<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InterviewController extends Controller
{
    /**
     * Display a listing of all interviews (admin view).
     */
    public function index(Request $request)
    {
        $query = Interview::with([
            'application.jobPosting.company',
            'application.candidate.user',
            'candidate',
            'employer'
        ])->latest('scheduled_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->whereHas('application.jobPosting', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        // Filter by candidate
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $interviews = $query->paginate(20)->through(function ($interview) {
            return [
                'id' => $interview->id,
                'title' => $interview->title,
                'type' => $interview->type,
                'type_label' => $interview->getTypeLabel(),
                'status' => $interview->status,
                'status_label' => $interview->getStatusLabel(),
                'scheduled_at' => $interview->scheduled_at->format('Y-m-d H:i'),
                'duration' => $interview->duration,
                'candidate' => [
                    'id' => $interview->candidate->id,
                    'name' => $interview->candidate->name,
                    'email' => $interview->candidate->email,
                ],
                'company' => [
                    'id' => $interview->application->jobPosting->company->id,
                    'name' => $interview->application->jobPosting->company->company_name,
                ],
                'job_posting' => [
                    'id' => $interview->application->jobPosting->id,
                    'title' => $interview->application->jobPosting->title,
                ],
                'created_at' => $interview->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $stats = $this->getStatistics();
        $companies = Company::select('id', 'company_name')->orderBy('company_name')->get();

        return Inertia::render('admin/interviews/Index', [
            'interviews' => $interviews,
            'stats' => $stats,
            'companies' => $companies,
            'filters' => [
                'status' => $request->get('status'),
                'company_id' => $request->get('company_id'),
                'candidate_id' => $request->get('candidate_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ],
        ]);
    }

    /**
     * Display the specified interview with activity log
     */
    public function show($id)
    {
        $interview = Interview::with([
            'application.jobPosting.company',
            'application.candidate.user',
            'candidate.candidateProfile',
            'employer'
        ])->findOrFail($id);

        return Inertia::render('admin/interviews/Show', [
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
                'employer' => [
                    'id' => $interview->employer->id,
                    'name' => $interview->employer->name,
                    'email' => $interview->employer->email,
                ],
                'company' => [
                    'id' => $interview->application->jobPosting->company->id,
                    'name' => $interview->application->jobPosting->company->company_name,
                    'can_create_interviews' => $interview->application->jobPosting->company->can_create_interviews,
                ],
                'job_posting' => [
                    'id' => $interview->application->jobPosting->id,
                    'title' => $interview->application->jobPosting->title,
                ],
                'created_at' => $interview->created_at->format('Y-m-d H:i:s'),
                'confirmed_at' => $interview->confirmed_at?->format('Y-m-d H:i:s'),
            ],
            'activityLogs' => $interview->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'properties' => $activity->properties,
                    'causer' => $activity->causer ? [
                        'id' => $activity->causer->id,
                        'name' => $activity->causer->name,
                    ] : null,
                    'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Delete interview (admin override)
     */
    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $interview = Interview::findOrFail($id);

        // Notify both candidate and employer
        $candidateUserId = $interview->application->candidate->user_id;
        $employerId = $interview->employer_id;

        Notification::create([
            'user_id' => $candidateUserId,
            'type' => 'application_update',
            'title' => 'Lịch phỏng vấn đã bị xóa',
            'message' => "Lịch phỏng vấn của bạn đã bị quản trị viên xóa. Lý do: {$validated['reason']}",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
                'reason' => $validated['reason'],
            ],
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $employerId,
            'type' => 'application_update',
            'title' => 'Lịch phỏng vấn đã bị xóa',
            'message' => "Lịch phỏng vấn đã bị quản trị viên xóa. Lý do: {$validated['reason']}",
            'data' => [
                'interview_id' => $interview->id,
                'job_title' => $interview->application->jobPosting->title,
                'reason' => $validated['reason'],
            ],
            'is_read' => false,
        ]);

        $interview->delete();

        return redirect()->route('admin.interviews.index')
            ->with('success', 'Đã xóa lịch phỏng vấn!');
    }

    /**
     * Block/unblock company from creating interviews
     */
    public function toggleBlock(Request $request, $companyId)
    {
        $company = Company::findOrFail($companyId);
        
        $company->update([
            'can_create_interviews' => !$company->can_create_interviews,
        ]);

        $status = $company->can_create_interviews ? 'mở khóa' : 'khóa';

        return back()->with('success', "Đã {$status} quyền tạo lịch phỏng vấn cho công ty {$company->company_name}!");
    }

    /**
     * Get interview statistics
     */
    private function getStatistics()
    {
        $stats = Interview::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => array_sum($stats),
            'pending' => $stats['pending'] ?? 0,
            'confirmed' => $stats['confirmed'] ?? 0,
            'rescheduled' => $stats['rescheduled'] ?? 0,
            'completed' => $stats['completed'] ?? 0,
            'cancelled' => $stats['cancelled'] ?? 0,
        ];
    }
}
