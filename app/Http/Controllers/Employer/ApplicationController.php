<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get employer's company
        $company = $user->company;
        
        if (!$company) {
            return inertia('Employer/Applications/Index', [
                'applications' => [],
                'statistics' => $this->getEmptyStatistics(),
                'jobPostings' => [],
                'filters' => $request->only(['status', 'job_posting_id', 'search', 'date_from', 'date_to']),
            ]);
        }

        // Build query
        $query = Application::whereHas('jobPosting.company', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['candidate.user', 'jobPosting']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('candidate.user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
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
        $applications = $query->paginate(15)->withQueryString();

        // Get statistics
        $statistics = $this->getStatistics($user->id);

        // Get job postings for filter
        $jobPostings = JobPosting::whereHas('company', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->select('id', 'title')
        ->orderBy('title')
        ->get();

        return inertia('Employer/Applications/Index', [
            'applications' => $applications,
            'statistics' => $statistics,
            'jobPostings' => $jobPostings,
            'filters' => $request->only(['status', 'job_posting_id', 'search', 'date_from', 'date_to', 'sort_by', 'sort_order']),
        ]);
    }

    public function show($id)
    {
        $application = Application::with([
            'candidate.user',
            'candidate.skills',
            'candidate.workExperiences',
            'candidate.educations',
            'jobPosting'
        ])
        ->whereHas('jobPosting.company', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->findOrFail($id);

        return inertia('Employer/Applications/Show', [
            'application' => $application,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,interview,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
            'interview_date' => 'nullable|date|after:now',
        ]);

        $application = Application::whereHas('jobPosting.company', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($id);

        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'interview_date' => $request->interview_date,
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    private function getStatistics($userId)
    {
        $stats = Application::whereHas('jobPosting.company', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->select('status', DB::raw('count(*) as count'))
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

    private function getEmptyStatistics()
    {
        return [
            'total' => 0,
            'pending' => 0,
            'reviewing' => 0,
            'interview' => 0,
            'accepted' => 0,
            'rejected' => 0,
        ];
    }
}
