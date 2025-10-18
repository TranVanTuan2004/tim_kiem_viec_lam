<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Inertia\Inertia;

class JobPostingController extends Controller // <-- Tên class phải chính xác
{
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
                'company' => $job->company ? [
                    'id' => $job->company->id,
                    'name' => $job->company->name,
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
