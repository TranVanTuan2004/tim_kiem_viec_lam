<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\CandidateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Display a listing of all applications (admin view)
     */
    public function index(Request $request)
    {
        // Build query for ALL applications in the system
        $query = Application::with(['candidate.user', 'jobPosting.company']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('jobPosting', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('candidate.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $applications = $query->paginate(20)->withQueryString();

        // Transform the paginated data
        $applications->getCollection()->transform(function ($application) {
            return $this->transformApplication($application);
        });

        // Get filter options
        $companies = Company::select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        $jobPostings = JobPosting::select('id', 'title', 'company_id')
            ->with('company:id,company_name')
            ->orderBy('title')
            ->get();

        return Inertia::render('admin/applications/Index', [
            'applications' => $applications,
            'companies' => $companies,
            'jobPostings' => $jobPostings,
            'filters' => $request->only([
                'status', 
                'company_id', 
                'job_posting_id', 
                'candidate_id', 
                'search', 
                'date_from', 
                'date_to', 
                'sort_by', 
                'sort_order'
            ]),
        ]);
    }

    /**
     * Display the specified application (admin view)
     */
    public function show(Application $application)
    {
        $application->load([
            'candidate.user',
            'candidate.skills',
            'candidate.workExperiences',
            'candidate.educations',
            'jobPosting.company'
        ]);

        return Inertia::render('admin/applications/ApplicationShow', [
            'application' => $this->transformApplicationDetail($application),
        ]);
    }

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
