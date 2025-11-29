<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Danh sách ứng tuyển với tìm kiếm & filter
     */
    public function index(Request $request)
    {
        // Query cơ bản với eager load
        $query = Application::with(['candidate.user', 'jobPosting.company'])
            ->orderByDesc('created_at');

        // Tìm kiếm theo tên/email ứng viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('candidate.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo công ty
        if ($request->filled('company_id')) {
            $query->whereHas('jobPosting.company', function ($q) use ($request) {
                $q->where('id', $request->company_id);
            });
        }

        // Lọc theo vị trí tuyển dụng
        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        // Paginate
        $applicationsPaginate = $query->paginate(15)->withQueryString();

        // Map applications để gửi Inertia
        $applications = $applicationsPaginate->getCollection()->map(function ($app) {
            return [
                'id' => $app->id,
                'status' => $app->status,
                'created_at' => $app->created_at,
                'candidate' => $app->candidate ? [
                    'id' => $app->candidate->id,
                    'avatar' => $app->candidate->avatar,
                    'user' => $app->candidate->user ? [
                        'name' => $app->candidate->user->name,
                        'email' => $app->candidate->user->email,
                        'avatar' => $app->candidate->user->avatar,
                    ] : null,
                ] : null,
                'jobPosting' => $app->jobPosting ? [
                    'id' => $app->jobPosting->id,
                    'title' => $app->jobPosting->title,
                    'company' => $app->jobPosting->company ? [
                        'company_name' => $app->jobPosting->company->company_name,
                    ] : null,
                ] : null,
            ];
        });

        // Gửi lại pagination với collection đã map
        $applicationsPaginate->setCollection($applications);

        // Dữ liệu filter select
        $companies = Company::orderBy('company_name')->get(['id', 'company_name']);
        $jobPostings = JobPosting::with('company')->orderBy('title')->get()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? ['company_name' => $job->company->company_name] : null,
            ];
        });

        return inertia('admin/applications/ApplicationIndex', [
            'applications' => $applicationsPaginate,
            'companies' => $companies,
            'jobPostings' => $jobPostings,
            'filters' => $request->only(['search', 'status', 'company_id', 'job_posting_id']),
        ]);
    }

    /**
     * Xem chi tiết ứng viên
     */
    public function show(Application $application)
{
    // Load tất cả quan hệ cần thiết
    $application->load([
        'candidate.user',        // user chứa name, email, avatar
        'candidate.skills',
        'candidate.educations',
        'candidate.workExperiences',
        'jobPosting.company',
    ]);

    $candidate = $application->candidate;
    $user = $candidate?->user;

    // Chuẩn bị dữ liệu trả về
    $applicationData = [
        'id' => $application->id,
        'status' => $application->status,
        'notes' => $application->notes,
        'created_at' => $application->created_at,
        'candidate' => $candidate ? [
            'id' => $candidate->id,
            'name' => $user?->name ?? 'Chưa có user',    // lấy từ user
            'email' => $user?->email ?? '-',            // lấy từ user
            'avatar' => $candidate->avatar ?? $user?->avatar ?? null,  // ưu tiên avatar candidate, fallback user
            'skills' => $candidate->skills->map(fn($s) => ['name' => $s->name]),
            'educations' => $candidate->educations->map(fn($e) => [
                'school' => $e->school,
                'degree' => $e->degree,
                'year' => $e->year,
            ]),
            'workExperiences' => $candidate->workExperiences->map(fn($w) => [
                'company' => $w->company,
                'position' => $w->position,
                'from' => $w->from,
                'to' => $w->to,
            ]),
        ] : null,
        'jobPosting' => $application->jobPosting ? [
            'id' => $application->jobPosting->id,
            'title' => $application->jobPosting->title ?? 'Chưa cập nhật',
            'company' => $application->jobPosting->company ? [
                'id' => $application->jobPosting->company->id,
                'company_name' => $application->jobPosting->company->company_name ?? 'Chưa cập nhật',
            ] : null,
        ] : null,
    ];

    return inertia('admin/applications/ApplicationShow', [
        'application' => $applicationData,
    ]);
}

    /**
     * Xóa ứng tuyển
     */


    /**
     * Update application status
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,interview,accepted,rejected,withdrawn',
            'notes' => 'nullable|string|max:1000',
            'interview_date' => 'nullable|date|after:now',
        ]);

        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'interview_date' => $request->interview_date,
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * Xóa ứng tuyển
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return back()->with('success', 'Ứng tuyển đã được xóa.');
    }

    /**
     * Get statistics for all applications
     */
    private function getStatistics()
    {
        $stats = Application::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => array_sum($stats),
            'pending' => $stats['pending'] ?? 0,
            'reviewing' => $stats['reviewing'] ?? 0,
            'interview' => $stats['interview'] ?? 0,
            'accepted' => $stats['accepted'] ?? 0,
            'rejected' => $stats['rejected'] ?? 0,
        ];
    }

    /**
     * Transform application data for frontend
     */
    private function transformApplication($application)
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'created_at' => $application->created_at,
            'notes' => $application->notes,
            'interview_date' => $application->interview_date,
            'candidate' => [
                'id' => $application->candidate->id,
                'avatar' => $application->candidate->avatar,
                'user' => [
                    'name' => $application->candidate->user->name,
                    'email' => $application->candidate->user->email,
                ],
            ],
            'jobPosting' => $application->jobPosting ? [
                'id' => $application->jobPosting->id,
                'title' => $application->jobPosting->title,
                'company' => $application->jobPosting->company ? [
                    'id' => $application->jobPosting->company->id,
                    'company_name' => $application->jobPosting->company->company_name,
                ] : null,
            ] : null,
        ];
    }

    /**
     * Transform detailed application data for show page
     */
    private function transformApplicationDetail($application)
    {
        $candidate = $application->candidate;
        $user = $candidate?->user;

        return [
            'id' => $application->id,
            'status' => $application->status,
            'notes' => $application->notes,
            'created_at' => $application->created_at,
            'candidate' => $candidate ? [
                'id' => $candidate->id,
                'name' => $user?->name ?? 'Chưa có user',
                'email' => $user?->email ?? '-',
                'avatar' => $candidate->avatar ?? $user?->avatar ?? null,
                'skills' => $candidate->skills->map(fn($s) => ['name' => $s->name]),
                'educations' => $candidate->educations->map(fn($e) => [
                    'school' => $e->school,
                    'degree' => $e->degree,
                    'year' => $e->year,
                ]),
                'workExperiences' => $candidate->workExperiences->map(fn($w) => [
                    'company' => $w->company,
                    'position' => $w->position,
                    'from' => $w->from,
                    'to' => $w->to,
                ]),
            ] : null,
            'jobPosting' => $application->jobPosting ? [
                'id' => $application->jobPosting->id,
                'title' => $application->jobPosting->title ?? 'Chưa cập nhật',
                'company' => $application->jobPosting->company ? [
                    'id' => $application->jobPosting->company->id,
                    'company_name' => $application->jobPosting->company->company_name ?? 'Chưa cập nhật',
                ] : null,
            ] : null,
        ];
    }
}
