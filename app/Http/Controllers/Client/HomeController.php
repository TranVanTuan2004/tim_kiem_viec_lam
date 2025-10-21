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
            ->limit(4)
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'title' => $job->title,
                    'company' => $job->company->company_name ?? 'Công ty',
                    'logo' => '🏢', // Default logo, có thể thay đổi sau
                    'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
                    'salary' => $job->getSalaryRange(),
                    'type' => $job->employment_type ? str_replace('_', ' ', $job->employment_type) : 'Full-time',
                    'skills' => $job->skills->take(3)->pluck('name')->toArray(),
                    'posted' => $job->published_at ? $job->published_at->diffForHumans() : 'Vừa đăng',
                ];
            });

        return Inertia::render('client/Home', [
            'featuredJobs' => $featuredJobs,
        ]);
    }
}
