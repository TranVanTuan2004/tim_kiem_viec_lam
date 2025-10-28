<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\JobPostingService;
use Inertia\Inertia;

class JobPostingController extends Controller 
{
    protected JobPostingService $jobPostingService;

    public function __construct(JobPostingService $jobPostingService)
    {
        $this->jobPostingService = $jobPostingService;
    }

    /**
     * Display a listing of all jobs.
     */
    public function index()
    {
        // Get and validate filters from request
        $filters = $this->jobPostingService->validateFilters([
            'featured' => request('featured', false),
            'q' => request('q', ''),
            'location' => request('location', ''),
        ]);

        // Get filtered jobs
        $jobs = $this->jobPostingService->getFilteredJobs($filters, 12)
            ->through(fn($job) => $this->jobPostingService->transformForListing($job));

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
        $job = $this->jobPostingService->getJobDetail($job_posting);

        return Inertia::render('client/JobDetail', [
            'job' => $this->jobPostingService->transformForDetail($job),
        ]);
    }
}
