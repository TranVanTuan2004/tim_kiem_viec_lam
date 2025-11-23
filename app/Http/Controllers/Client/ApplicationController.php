<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\ApplicationService;
use App\Services\JobPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;
    protected JobPostingService $jobPostingService;

    public function __construct(
        ApplicationService $applicationService,
        JobPostingService $jobPostingService
    ) {
        $this->applicationService = $applicationService;
        $this->jobPostingService = $jobPostingService;
    }

    /**
     * Show the application form for a specific job
     */
    public function create(JobPosting $job_posting)
    {
        $user = Auth::user()->load('candidateProfile');

        // Check if user can apply
        $canApply = $this->applicationService->canApply($user, $job_posting);
        
        if (!$canApply['can_apply']) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with($canApply['type'], $canApply['message']);
        }

        $job_posting->load(['company', 'skills']);

        return Inertia::render('client/JobApplication', [
            'job' => $this->jobPostingService->transformForApplication($job_posting),
            'candidateProfile' => $this->applicationService->getCandidateProfileData($user),
        ]);
    }

    /**
     * Store a new job application
     */
    public function store(Request $request, JobPosting $job_posting)
    {
        $user = Auth::user()->load('candidateProfile');

        // Check if user can apply
        $canApply = $this->applicationService->canApply($user, $job_posting);
        
        if (!$canApply['can_apply']) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with($canApply['type'], $canApply['message']);
        }

        try {
            // Validate the request
            $validated = $this->applicationService->validateApplicationData([
                'cover_letter' => $request->input('cover_letter'),
                'cv_file' => $request->file('cv_file'),
                'cv_id' => $request->input('cv_id'),
            ]);

            // Create the application
            $application = $this->applicationService->createApplication(
                $user,
                $job_posting,
                $validated['cover_letter'] ?? null,
                $validated['cv_file'] ?? null,
                $validated['cv_id'] ?? null
            );

            // Send notifications
            $this->applicationService->sendApplicationNotifications($application);

            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('success', 'Ứng tuyển thành công! Nhà tuyển dụng sẽ liên hệ với bạn sớm.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

