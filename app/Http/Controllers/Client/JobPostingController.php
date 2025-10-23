<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Inertia\Inertia;

class JobPostingController extends Controller // <-- Tên class phải chính xác
{
    /**
     * Display a listing of all jobs.
     */
    public function index()
    {
        $query = JobPosting::published()
            ->with(['company', 'skills'])
            ->orderBy('published_at', 'desc');

        // Filter by featured if requested
        if (request('featured')) {
            $query->where('is_featured', true);
        }

        // Search by keyword
        if (request('q')) {
            $keyword = request('q');
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Filter by location
        if (request('location')) {
            $location = request('location');
            $query->where(function($q) use ($location) {
                $q->where('location', 'like', "%{$location}%")
                  ->orWhere('city', 'like', "%{$location}%")
                  ->orWhere('province', 'like', "%{$location}%");
            });
        }

        $jobs = $query->paginate(12)
            ->withQueryString()
            ->through(function ($job) {
                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'title' => $job->title,
                    'company' => $job->company->company_name ?? 'Công ty',
                    'company_slug' => $job->company->company_slug ?? null,
                    'company_logo' => $job->company->logo ?? null,
                    'logo' => '🏢',
                    'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
                    'salary' => $job->getSalaryRange(),
                    'type' => $job->employment_type ? str_replace('_', ' ', ucfirst($job->employment_type)) : 'Full-time',
                    'skills' => $job->skills->take(3)->pluck('name')->toArray(),
                    'posted' => $job->published_at ? $job->published_at->diffForHumans() : 'Vừa đăng',
                    'is_featured' => $job->is_featured,
                ];
            });

        return Inertia::render('client/JobsIndex', [
            'jobs' => $jobs,
            'filters' => [
                'featured' => request('featured', false),
                'q' => request('q', ''),
                'location' => request('location', ''),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPosting $job_posting)
    {
        $job = $job_posting->load(['company', 'industry', 'skills']);
        $job->incrementViews();

        return Inertia::render('client/JobDetail', [
            'job' => [
                'id' => $job->id,
                'slug' => $job->slug,
                'title' => $job->title,
                'description' => $job->description,
                'requirements' => $job->requirements,
                'benefits' => $job->benefits,
                'employment_type' => $job->employment_type,
                'experience_level' => $job->experience_level,
                'min_salary' => $job->min_salary,
                'max_salary' => $job->max_salary,
                'salary_type' => $job->salary_type,
                'salary_text' => $job->getSalaryRange(),
                'application_deadline' => $job->application_deadline,
                'quantity' => $job->quantity,
                'location' => $job->location,
                'city' => $job->city,
                'province' => $job->province,
                'published_at' => $job->published_at,
                'views' => $job->views,
                'applications_count' => $job->applications_count,
                'company' => $job->company ? [
                    'id' => $job->company->id,
                    'name' => $job->company->company_name,
                    'slug' => $job->company->company_slug,
                ] : null,
                'industry' => $job->industry ? [
                    'id' => $job->industry->id,
                    'name' => $job->industry->name,
                ] : null,
                'skills' => $job->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                    ];
                }),
            ],
        ]);
    }
}
