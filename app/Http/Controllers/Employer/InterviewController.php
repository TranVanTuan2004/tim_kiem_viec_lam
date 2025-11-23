<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InterviewController extends Controller
{
    /**
     * Display a listing of interviews.
     */
    public function index(Request $request)
    {
        $employer = Auth::user();

        $query = Interview::with(['application.jobPosting', 'candidate.candidateProfile'])
            ->forEmployer($employer->id)
            ->latest('scheduled_at');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('scheduled_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('scheduled_at', '<=', $request->date_to);
        }

        $interviews = $query->paginate(15)->through(function ($interview) {
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
                'candidate' => [
                    'id' => $interview->candidate->id,
                    'name' => $interview->candidate->name,
                    'email' => $interview->candidate->email,
                ],
                'job_posting' => [
                    'id' => $interview->application->jobPosting->id,
                    'title' => $interview->application->jobPosting->title,
                ],
                'created_at' => $interview->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $stats = [
            'total' => Interview::forEmployer($employer->id)->count(),
            'upcoming' => Interview::forEmployer($employer->id)->upcoming()->count(),
            'pending' => Interview::forEmployer($employer->id)->byStatus('pending')->count(),
            'confirmed' => Interview::forEmployer($employer->id)->byStatus('confirmed')->count(),
        ];

        return Inertia::render('Employer/Interviews/Index', [
            'interviews' => $interviews,
            'stats' => $stats,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new interview.
     */
    public function create(Request $request)
    {
        $applicationId = $request->get('application_id');
        $application = null;

        if ($applicationId) {
            $application = Application::with(['candidate', 'jobPosting'])
                ->findOrFail($applicationId);
        }

        return Inertia::render('Employer/Interviews/Create', [
            'application' => $application ? [
                'id' => $application->id,
                'candidate' => [
                    'id' => $application->candidate->id,
                    'name' => $application->candidate->name,
                    'email' => $application->candidate->email,
                ],
                'job_posting' => [
                    'id' => $application->jobPosting->id,
                    'title' => $application->jobPosting->title,
                ],
            ] : null,
        ]);
    }

    /**
     * Store a newly created interview.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:phone,video,in_person',
            'location' => 'nullable|string|max:500',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'timezone' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['application_id']);

        // Check if company is blocked from creating interviews
        $company = $application->jobPosting->company;
        if (!$company->can_create_interviews) {
            return back()->with('error', 'Công ty của bạn đã bị khóa quyền tạo lịch phỏng vấn. Vui lòng liên hệ quản trị viên.');
        }

        $candidateUserId = $application->candidate->user_id;

        $interview = Interview::create([
            'application_id' => $application->id,
            'employer_id' => Auth::id(),
            'candidate_id' => $candidateUserId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'location' => $validated['location'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'duration' => $validated['duration'],
            'timezone' => $validated['timezone'] ?? 'Asia/Ho_Chi_Minh',
            'status' => 'pending',
        ]);

        // Create notification for candidate
        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'interview_invite',
            'title' => 'Lời mời phỏng vấn',
            'message' => "Bạn đã được mời phỏng vấn cho vị trí {$application->jobPosting->title}",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $application->jobPosting->title,
                'company_name' => $application->jobPosting->company->company_name ?? 'N/A',
                'scheduled_at' => $interview->scheduled_at->format('d/m/Y H:i'),
                'type' => $interview->getTypeLabel(),
                'location' => $interview->location,
            ],
            'is_read' => false,
        ]);

        // Update application status to interview
        $application->update([
            'status' => 'interview',
        ]);

        // TODO: Send email invitation to candidate
        // TODO: Sync to calendar if integrated

        return redirect()->route('employer.interviews.index')
            ->with('success', 'Lịch phỏng vấn đã được tạo và gửi lời mời đến ứng viên!');
    }

    /**
     * Display the specified interview.
     */
    public function show($id)
    {
        $interview = Interview::with([
            'application.jobPosting',
            'candidate.candidateProfile',
            'employer'
        ])->findOrFail($id);

        // Check authorization
        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Employer/Interviews/Show', [
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
                'candidate' => [
                    'id' => $interview->candidate->id,
                    'name' => $interview->candidate->name,
                    'email' => $interview->candidate->email,
                    'phone' => $interview->candidate->candidateProfile->phone ?? null,
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
     * Update the specified interview.
     */
    public function update(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:phone,video,in_person',
            'location' => 'nullable|string|max:500',
            'scheduled_at' => 'sometimes|date',
            'duration' => 'sometimes|integer|min:15|max:480',
            'employer_notes' => 'nullable|string',
        ]);

        $interview->update($validated);

        // Notify candidate about interview update
        // Ensure we get the User ID, not the CandidateProfile ID
        $candidateUserId = $interview->application->candidate->user_id;
        
        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Cập nhật lịch phỏng vấn',
            'message' => "Lịch phỏng vấn của bạn đã được cập nhật",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
            ],
            'is_read' => false,
        ]);

        // TODO: Send update notification email to candidate
        // TODO: Update calendar event if integrated

        return back()->with('success', 'Cập nhật lịch phỏng vấn thành công!');
    }

    /**
     * Remove the specified interview.
     */
    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Notify candidate about cancellation
        // Ensure we get the User ID
        $candidateUserId = $interview->application->candidate->user_id;

        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Lịch phỏng vấn đã bị hủy',
            'message' => "Lịch phỏng vấn cho vị trí {$interview->application->jobPosting->title} đã bị hủy",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
            ],
            'is_read' => false,
        ]);

        $interview->cancel();
        $interview->delete();

        // TODO: Send cancellation email
        // TODO: Delete calendar event

        return redirect()->route('employer.interviews.index')
            ->with('success', 'Đã hủy lịch phỏng vấn!');
    }

    /**
     * Mark interview as completed.
     */
    public function complete($id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $interview->complete();

        return back()->with('success', 'Đã đánh dấu phỏng vấn hoàn thành!');
    }

    /**
     * Reschedule interview.
     */
    public function reschedule(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        // Check authorization
        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'sometimes|integer|min:15|max:480',
        ]);

        $interview->update([
            'scheduled_at' => $validated['scheduled_at'],
            'duration' => $validated['duration'] ?? $interview->duration,
            'status' => 'rescheduled',
        ]);

        // Notify candidate about reschedule
        // Ensure we get the User ID
        $candidateUserId = $interview->application->candidate->user_id;

        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Lịch phỏng vấn đã được đổi',
            'message' => "Lịch phỏng vấn của bạn đã được đổi sang thời gian mới",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
                'new_scheduled_at' => $interview->scheduled_at->format('d/m/Y H:i'),
            ],
            'is_read' => false,
        ]);

        // TODO: Send reschedule notification email
        // TODO: Update calendar event

        return back()->with('success', 'Đã đổi lịch phỏng vấn thành công!');
    }

    /**
     * Accept reschedule proposal.
     */
    public function acceptReschedule(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date',
        ]);

        $interview->update([
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'confirmed',
            'proposed_times' => null,
            'confirmed_at' => now(),
        ]);

        // Notify candidate
        $candidateUserId = $interview->application->candidate->user_id;

        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Đề xuất đổi lịch được chấp nhận',
            'message' => "Nhà tuyển dụng đã chấp nhận đề xuất đổi lịch phỏng vấn của bạn.",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
                'scheduled_at' => $interview->scheduled_at->format('d/m/Y H:i'),
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'Đã chấp nhận lịch phỏng vấn mới!');
    }

    /**
     * Decline reschedule proposal.
     */
    public function declineReschedule($id)
    {
        $interview = Interview::findOrFail($id);

        if ($interview->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $interview->update([
            'status' => 'pending',
            'proposed_times' => null,
        ]);

        // Notify candidate
        $candidateUserId = $interview->application->candidate->user_id;

        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Đề xuất đổi lịch bị từ chối',
            'message' => "Nhà tuyển dụng đã từ chối đề xuất đổi lịch phỏng vấn của bạn. Vui lòng liên hệ lại.",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'Đã từ chối đề xuất đổi lịch!');
    }
}
