<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\Company;
use App\Models\Application;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Load admin statistics
        $stats = [
            'total_users' => User::count(),
            'total_candidates' => User::role('Candidate')->count(),
            'total_employers' => User::role('Employer')->count(),
            'total_companies' => Company::count(),
            'total_jobs' => JobPosting::count(),
            'active_jobs' => JobPosting::where('status', 'active')->count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
        ];

        // Get recent users
        $recentUsers = User::with(['roles'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

        // Get recent job postings
        $recentJobs = JobPosting::with(['company', 'industry'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'status' => $job->status,
                    'location' => $job->location,
                    'company' => [
                        'id' => $job->company->id,
                        'name' => $job->company->name,
                    ],
                    'created_at' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

        // Get recent applications
        $recentApplications = Application::with(['jobPosting.company', 'candidateProfile.user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applied_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                    'candidate' => [
                        'name' => $application->candidateProfile->user->name,
                        'email' => $application->candidateProfile->user->email,
                    ],
                    'job_posting' => [
                        'id' => $application->jobPosting->id,
                        'title' => $application->jobPosting->title,
                        'company' => $application->jobPosting->company->name,
                    ],
                ];
            });

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentJobs' => $recentJobs,
            'recentApplications' => $recentApplications,
        ]);
    }
}
