<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the employer dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('employer.company.create')
                ->with('info', 'Please complete your company profile to access all features.');
        }

        // Load employer statistics
        $stats = [
            'total_jobs' => $company->jobPostings()->count(),
            'active_jobs' => $company->jobPostings()->where('status', 'active')->count(),
            'total_applications' => $company->jobPostings()->withCount('applications')->get()->sum('applications_count'),
            'pending_applications' => $company->jobPostings()
                ->whereHas('applications', function ($query) {
                    $query->where('status', 'pending');
                })->count(),
            'subscription_status' => $company->subscription ? $company->subscription->status : 'none',
        ];

        // Get recent job postings
        $recentJobs = $company->jobPostings()
            ->with(['industry', 'applications'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'status' => $job->status,
                    'location' => $job->location,
                    'job_type' => $job->job_type,
                    'applications_count' => $job->applications->count(),
                    'created_at' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                    'deadline' => $job->deadline ? $job->deadline->format('Y-m-d') : null,
                ];
            });

        // Get recent applications
        $recentApplications = $company->jobPostings()
            ->with(['applications.candidateProfile.user'])
            ->get()
            ->pluck('applications')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applied_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                    'candidate' => [
                        'name' => $application->candidateProfile->user->name,
                        'email' => $application->candidateProfile->user->email,
                        'current_position' => $application->candidateProfile->current_position,
                    ],
                    'job_posting' => [
                        'id' => $application->jobPosting->id,
                        'title' => $application->jobPosting->title,
                    ],
                ];
            });

        return Inertia::render('employer/Dashboard', [
            'stats' => $stats,
            'recentJobs' => $recentJobs,
            'recentApplications' => $recentApplications,
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'logo' => $company->logo,
                'subscription' => $company->subscription ? [
                    'status' => $company->subscription->status,
                    'expires_at' => $company->subscription->expires_at,
                ] : null,
            ],
        ]);
    }
}
