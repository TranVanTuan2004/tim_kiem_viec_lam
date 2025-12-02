<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\JobPosting;
use App\Models\Company;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:system,user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        $apiKey = config('services.openai.api_key') ?: env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OPENAI_API_KEY is not configured'], 500);
        }

        $user = $request->user();
        $candidate = $user?->candidateProfile;

        $skills = [];
        if ($candidate) {
            $candidate->loadMissing('skills');
            foreach ($candidate->skills as $skill) {
                $years = $skill->pivot->years_experience ? ($skill->pivot->years_experience . 'y') : null;
                $skills[] = $years ? ($skill->name . ' (' . $years . ')') : $skill->name;
            }
        }

        $preferredLocations = $candidate?->preferred_locations;
        if (is_string($preferredLocations)) {
            // Some databases may store JSON strings
            $decoded = json_decode($preferredLocations, true);
            $preferredLocations = is_array($decoded) ? $decoded : [$preferredLocations];
        }

        $contextParts = array_filter([
            $candidate?->summary ? ('Tóm tắt: ' . $candidate->summary) : null,
            $candidate?->current_position ? ('Vị trí hiện tại: ' . $candidate->current_position) : null,
            $candidate?->current_company ? ('Công ty hiện tại: ' . $candidate->current_company) : null,
            $candidate?->experience_level ? ('Cấp độ kinh nghiệm: ' . $candidate->experience_level) : null,
            !empty($skills) ? ('Kỹ năng: ' . implode(', ', $skills)) : null,
            is_array($preferredLocations) && count($preferredLocations) ? ('Khu vực mong muốn: ' . implode(', ', $preferredLocations)) : null,
        ]);

        $systemPrompt = trim(implode(' ', [
            'Bạn là Trợ lý tuyển dụng cho cổng việc làm IT. Nhiệm vụ: tư vấn nghề nghiệp, CV, phỏng vấn và gợi ý việc làm phù hợp.',
            $contextParts ? ('Thông tin ứng viên: ' . implode(' | ', $contextParts) . '.') : 'Chưa có thông tin ứng viên. Hãy hỏi làm rõ trước khi gợi ý.',
            'Chỉ sử dụng dữ liệu từ database được cung cấp, không bịa thêm công ty hay việc.',
            'Khi người dùng hỏi gợi ý việc, đề xuất 3-5 vị trí cụ thể (kèm thành phố nếu có), nêu lý do phù hợp dựa trên kỹ năng/kinh nghiệm, liệt kê kỹ năng nên bổ sung, và đưa ra bước tiếp theo (xem JD, cập nhật CV, luyện phỏng vấn). Trả lời ngắn gọn, rõ ràng, có tính hành động.',
        ]));

        $skillIds = $candidate ? $candidate->skills->pluck('id')->all() : [];

        $preferredLocations = $candidate?->preferred_locations;
        if (is_string($preferredLocations)) {
            $decoded = json_decode($preferredLocations, true);
            $preferredLocations = is_array($decoded) ? $decoded : [$preferredLocations];
        }

        $jobsQuery = JobPosting::query()
            ->published()
            ->active()
            ->with(['company', 'skills']);

        if (!empty($skillIds)) {
            $jobsQuery->withSkills($skillIds);
        }

        if (is_array($preferredLocations) && count($preferredLocations)) {
            $jobsQuery->where(function ($q) use ($preferredLocations) {
                foreach ($preferredLocations as $loc) {
                    if (!is_string($loc) || $loc === '') { continue; }
                    $q->orWhere('city', 'like', "%{$loc}%")
                      ->orWhere('province', 'like', "%{$loc}%")
                      ->orWhere('location', 'like', "%{$loc}%");
                }
            });
        }

        $jobs = $jobsQuery->latest('published_at')->take(30)->get();

        $scored = $jobs->map(function ($job) use ($skillIds) {
            $jobSkillIds = $job->skills->pluck('id');
            $matched = $jobSkillIds->intersect($skillIds)->values();
            return [
                'job' => $job,
                'score' => $matched->count(),
                'matched_skill_ids' => $matched->all(),
            ];
        })->sortByDesc('score')->values();

        $top = $scored->take(5);

        $recommendedJobs = $top->map(function ($item) {
            $job = $item['job'];
            $matchedIds = $item['matched_skill_ids'];
            $matchedNames = $job->skills->whereIn('id', $matchedIds)->pluck('name')->values()->all();
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company?->company_name,
                'city' => $job->city,
                'province' => $job->province,
                'experience_level' => $job->experience_level,
                'salary' => [
                    'min' => $job->min_salary,
                    'max' => $job->max_salary,
                    'type' => $job->salary_type,
                ],
                'skills_match' => $matchedNames,
            ];
        })->values()->all();

        $recommendedCompanies = collect($top)->map(function ($item) {
            return $item['job']->company;
        })->filter()->unique('id')->map(function ($c) {
            return [
                'id' => $c->id,
                'company_name' => $c->company_name,
                'city' => $c->city,
                'province' => $c->province,
                'rating' => $c->rating,
                'is_verified' => $c->is_verified,
            ];
        })->values()->all();

        $dbContext = [
            'candidate' => [
                'current_position' => $candidate?->current_position,
                'current_company' => $candidate?->current_company,
                'experience_level' => $candidate?->experience_level,
                'skills' => $skills,
                'preferred_locations' => is_array($preferredLocations) ? $preferredLocations : [],
            ],
            'recommended_jobs' => $recommendedJobs,
            'recommended_companies' => $recommendedCompanies,
        ];

        // Remove any incoming system messages from client; backend controls the system prompt
        $incoming = array_values(array_filter($validated['messages'], function ($m) {
            return ($m['role'] ?? 'user') !== 'system';
        }));

        $payload = [
            'model' => config('services.openai.model'),
            'messages' => array_merge([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'system', 'content' => 'Dữ liệu từ database (JSON). Chỉ sử dụng dữ liệu này để trả lời: ' . json_encode($dbContext, JSON_UNESCAPED_UNICODE)],
            ], $incoming),
            'temperature' => 0.7,
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if ($response->failed()) {
            return response()->json([
                'error' => 'OpenAI API request failed',
                'details' => $response->json(),
            ], $response->status());
        }

        $reply = data_get($response->json(), 'choices.0.message.content');

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
