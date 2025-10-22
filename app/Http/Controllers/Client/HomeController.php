<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        $featuredJobs = JobPosting::published()
            ->featured()
            ->with(['company', 'skills'])
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'title' => $job->title,
                    'company' => $job->company->company_name ?? 'Công ty',
                    'company_logo' => $job->company->logo ?? null,
                    'logo' => '🏢', // Default logo emoji as fallback
                    'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
                    'salary' => $job->getSalaryRange(),
                    'type' => $job->employment_type ? str_replace('_', ' ', ucfirst($job->employment_type)) : 'Full-time',
                    'skills' => $job->skills->take(3)->pluck('name')->toArray(),
                    'posted' => $job->published_at ? $job->published_at->diffForHumans() : 'Vừa đăng',
                ];
            });

        // Get top companies with job counts
        $topCompanies = \App\Models\Company::withCount([
            'jobPostings' => function ($query) {
                $query->where('status', 'approved')->whereNotNull('published_at');
            }
        ])
            ->where('is_verified', true)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->company_name,
                    'slug' => $company->company_slug,
                    'logo' => $company->logo,
                    'rating' => $company->rating ?? 0,
                    'reviews' => $company->total_reviews ?? 0,
                    'jobs' => $company->job_postings_count ?? 0,
                    'location' => $company->city ?? $company->province ?? 'Việt Nam',
                    'employees' => $company->size ?? 'N/A',
                    'description' => $company->description ?? '',
                ];
            });

        return Inertia::render('client/Home', [
            'featuredJobs' => $featuredJobs,
            'topCompanies' => $topCompanies,
        ]);
    }
}
