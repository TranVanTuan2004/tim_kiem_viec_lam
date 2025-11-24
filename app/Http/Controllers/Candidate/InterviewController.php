<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InterviewController extends Controller
{
    /**
     * Display a listing of candidate's interviews.
     */
    public function index(Request $request)
    {
        $candidate = Auth::user();

        $query = Interview::with(['application.jobPosting.company', 'employer'])
            ->forCandidate($candidate->id)
            ->latest('scheduled_at');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Separate upcoming and past
        $upcomingInterviews = (clone $query)->upcoming()->get()->map(function ($interview) {
            return $this->transformInterview($interview);
        });

        $pastInterviews = (clone $query)->past()->get()->map(function ($interview) {
            return $this->transformInterview($interview);
        });

        return Inertia::render('candidate/Interviews/Index', [
            'upcoming' => $upcomingInterviews,
            'past' => $pastInterviews,
            'filters' => [
                'status' => $request->get('status', 'all'),
            ],
        ]);
    }

    /**
     * Display the specified interview.
     */
    public function show($id)
    {
        $interview = Interview::with([
            'application.jobPosting.company',
            'employer.company'
        ])->findOrFail($id);

        // Check authorization
        if ($interview->candidate_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('candidate/Interviews/Show', [
            'interview' => [
                'id' => $interview->id,
                'title' => $interview->title,
                'description' => $interview->description,
                'type' => $interview->type,
                'type_label' => $interview->getTypeLabel(),
                'location' => $interview->location,
                'scheduled_at' => $interview->scheduled_at->format('Y-m-d H:i'),
                'duration' => $interview->duration,
                'timezone' => $interview->timezone,
                'status' => $interview->status,
                'status_label' => $interview->getStatusLabel(),
                'employer_notes' => $interview->employer_notes,
                'candidate_notes' => $interview->candidate_notes,
                'proposed_times' => $interview->proposed_times,
                'company' => [
                    'id' => $interview->application->jobPosting->company->id,
                    'name' => $interview->application->jobPosting->company->company_name,
                    'logo' => $interview->application->jobPosting->company->logo 
                        ? asset('storage/' . $interview->application->jobPosting->company->logo) 
                        : null,
                ],
                'job_posting' => [
                    'id' => $interview->application->jobPosting->id,
                    'title' => $interview->application->jobPosting->title,
                ],
                'created_at' => $interview->created_at->format('Y-m-d H:i:s'),
                'confirmed_at' => $interview->confirmed_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Confirm interview attendance.
     */
    public function confirm($id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->candidate_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($interview->status !== 'pending') {
            return back()->with('error', 'Không thể xác nhận lịch phỏng vấn này!');
        }

        $interview->confirm();

        // Send confirmation notification to employer
        Notification::create([
            'user_id' => $interview->employer_id,
            'type' => 'application_update',
            'title' => 'Ứng viên xác nhận phỏng vấn',
            'message' => "Ứng viên đã xác nhận tham gia phỏng vấn cho vị trí {$interview->application->jobPosting->title}",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
            ],
            'is_read' => false,
        ]);

        // TODO: Send confirmation email to employer
        // TODO: Add to candidate's calendar

        return back()->with('success', 'Đã xác nhận tham gia phỏng vấn!');
    }

    /**
     * Decline interview invitation.
     */
    public function decline(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->candidate_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($interview->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Không thể từ chối lịch phỏng vấn này!');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $interview->update([
            'status' => 'declined',
            'candidate_notes' => $validated['notes'] ?? null,
        ]);

        // Send decline notification to employer
        Notification::create([
            'user_id' => $interview->employer_id,
            'type' => 'application_update',
            'title' => 'Ứng viên từ chối phỏng vấn',
            'message' => "Ứng viên đã từ chối lời mời phỏng vấn cho vị trí {$interview->application->jobPosting->title}",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
                'notes' => $validated['notes'] ?? null,
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'Đã từ chối lời mời phỏng vấn!');
    }

    /**
     * Propose reschedule with alternative times.
     */
    public function proposeReschedule(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->candidate_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'proposed_times' => 'required|array|min:1|max:3',
            'proposed_times.*' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        $interview->update([
            'proposed_times' => $validated['proposed_times'],
            'candidate_notes' => $validated['notes'] ?? $interview->candidate_notes,
            'status' => 'rescheduled',
        ]);

        // TODO: Send reschedule proposal to employer

        return back()->with('success', 'Đã gửi đề xuất đổi lịch phỏng vấn!');
    }

    /**
     * Transform interview for response.
     */
    private function transformInterview($interview)
    {
        return [
            'id' => $interview->id,
            'title' => $interview->title,
            'type' => $interview->type,
            'type_label' => $interview->getTypeLabel(),
            'status' => $interview->status,
            'status_label' => $interview->getStatusLabel(),
            'scheduled_at' => $interview->scheduled_at->format('Y-m-d H:i'),
            'duration' => $interview->duration,
            'location' => $interview->location,
            'company' => [
                'name' => $interview->application->jobPosting->company->company_name,
                'logo' => $interview->application->jobPosting->company->logo 
                    ? asset('storage/' . $interview->application->jobPosting->company->logo) 
                    : null,
            ],
            'job_posting' => [
                'title' => $interview->application->jobPosting->title,
            ],
        ];
    }
}
