<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;
use App\Services\JobPostingService;
use Inertia\Inertia;

class CompanyController extends Controller
{
    protected CompanyService $companyService;
    protected JobPostingService $jobPostingService;

    public function __construct(
        CompanyService $companyService,
        JobPostingService $jobPostingService
    ) {
        $this->companyService = $companyService;
        $this->jobPostingService = $jobPostingService;
    }

    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $filters = [
            'has_jobs' => request('has_jobs') == '1',
        ];

        $companies = $this->companyService->getFilteredCompanies($filters, 12);

        return Inertia::render('client/CompaniesIndex', [
            'companies' => $companies,
            'hasJobsFilter' => $filters['has_jobs'],
            // 'filters' => request()->only('q'),
        ]);
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        $company = $this->companyService->getCompanyDetail($company);

        // Get recent jobs
        $recentJobs = $this->companyService->getRecentJobs($company, 6)
            ->map(fn($job) => $this->jobPostingService->transformForCompany($job));

        // Get reviews
        $reviews = $this->companyService->getCompanyReviews($company, 10);

        // Get rating statistics
        $ratingStats = $this->companyService->getRatingStats($company);

        return Inertia::render('client/CompanyDetail', [
            'company' => $this->companyService->transformForDetail($company),
            'recentJobs' => $recentJobs,
            'reviews' => $reviews,
            'ratingStats' => $ratingStats,
        ]);
    }

    /**
     * Display all jobs for a specific company.
     */
    public function jobs(Company $company)
    {
        $jobs = $this->companyService->getCompanyJobs($company, 12)
            ->through(fn($job) => $this->jobPostingService->transformForCompany($job));

        return Inertia::render('client/CompanyJobs', [
            'company' => $this->companyService->transformForJobsPage($company),
            'jobs' => $jobs,
        ]);
    }
}
