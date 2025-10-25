<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\JobPostingService;
use App\Services\CompanyService;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected JobPostingService $jobPostingService;
    protected CompanyService $companyService;

    public function __construct(
        JobPostingService $jobPostingService,
        CompanyService $companyService
    ) {
        $this->jobPostingService = $jobPostingService;
        $this->companyService = $companyService;
    }

    /**
     * Display the homepage.
     */
    public function index()
    {
        // Get featured jobs
        $featuredJobs = $this->jobPostingService->getFeaturedJobs(6)
            ->map(fn($job) => $this->jobPostingService->transformForHome($job));

        // Get top companies
        $topCompanies = $this->companyService->getTopCompanies(4)
            ->map(fn($company) => $this->companyService->transformForHome($company));

        return Inertia::render('client/Home', [
            'featuredJobs' => $featuredJobs,
            'topCompanies' => $topCompanies,
        ]);
    }
}
