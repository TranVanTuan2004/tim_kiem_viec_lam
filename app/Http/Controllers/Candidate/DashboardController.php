<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the candidate dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create')
                ->with('info', 'Please complete your profile to access all features.');
        }

        // Load candidate statistics
        $stats = [
            'total_applications' => $candidateProfile->applications()->count(),
            'pending_applications' => $candidateProfile->applications()->where('status', 'pending')->count(),
            'shortlisted_applications' => $candidateProfile->applications()->where('status', 'shortlisted')->count(),
            'saved_jobs' => $candidateProfile->savedJobPostings()->count(),
            'portfolios' => $candidateProfile->portfolios()->count(),
            'profile_views' => 0, // Placeholder for future feature
        ];

        // Get recent applications
        $recentApplications = $candidateProfile->applications()
            ->with(['jobPosting.company', 'jobPosting.industry'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applied_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                    'cover_letter' => $application->cover_letter,
                    'job_posting' => [
                        'id' => $application->jobPosting->id,
                        'title' => $application->jobPosting->title,
                        'location' => $application->jobPosting->location,
                        'salary_min' => $application->jobPosting->salary_min,
                        'salary_max' => $application->jobPosting->salary_max,
                        'company' => [
                            'id' => $application->jobPosting->company->id,
                            'name' => $application->jobPosting->company->name,
                            'logo' => $application->jobPosting->company->logo,
                        ],
                    ],
                ];
            });

        // Get recommended jobs (based on skills and preferences)
        $recommendedJobs = $this->getRecommendedJobs($candidateProfile);

        return Inertia::render('candidate/Dashboard', [
            'stats' => $stats,
            'recentApplications' => $recentApplications,
            'recommendedJobs' => $recommendedJobs,
            'candidateProfile' => [
                'id' => $candidateProfile->id,
                'current_position' => $candidateProfile->current_position,
                'current_company' => $candidateProfile->current_company,
                'is_available' => $candidateProfile->is_available,
                'experience_level' => $candidateProfile->experience_level,
                'expected_salary' => $candidateProfile->expected_salary,
            ],
        ]);
    }

    /**
     * Get recommended jobs for the candidate.
     */
    private function getRecommendedJobs($candidateProfile)
    {
        $query = \App\Models\JobPosting::with(['company', 'industry'])
            ->where('status', 'approved')
            ->where('application_deadline', '>=', now());

        // Filter by candidate's skills
        if ($candidateProfile->skills()->count() > 0) {
            $skillIds = $candidateProfile->skills()->pluck('skill_id')->toArray();
            $query->whereHas('skills', function ($q) use ($skillIds) {
                $q->whereIn('skill_id', $skillIds);
            });
        }

        // Filter by experience level
        if ($candidateProfile->experience_level) {
            $query->where('experience_level', $candidateProfile->experience_level);
        }

        return $query->latest()
            ->take(6)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'location' => $job->location,
                    'job_type' => $job->job_type,
                    'experience_level' => $job->experience_level,
                    'salary_min' => $job->salary_min,
                    'salary_max' => $job->salary_max,
                    'deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                    'company' => [
                        'id' => $job->company->id,
                        'name' => $job->company->name,
                        'logo' => $job->company->logo,
                    ],
                    'industry' => $job->industry ? [
                        'id' => $job->industry->id,
                        'name' => $job->industry->name,
                    ] : null,
                ];
            });
    }
}

