<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\JobPostingService;
use App\Services\CompanyService;
use App\Models\Banner;
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
        // Get active banners
        $banners = Banner::active()
            ->orderBy('order')
            ->get(['id', 'title', 'description', 'image_url', 'link_url', 'button_text', 'type']);

        // Get featured jobs
        $featuredJobs = $this->jobPostingService->getFeaturedJobs(6)
            ->map(fn($job) => $this->jobPostingService->transformForHome($job));

        // Get popular categories (industries)
        $popularCategories = \App\Models\Industry::active()
            ->withCount('jobPostings')
            ->orderBy('job_postings_count', 'desc')
            ->take(6)
            ->get()
            ->map(function ($industry) {
                return [
                    'id' => $industry->id,
                    'name' => $industry->name,
                    'slug' => $industry->slug,
                    'icon' => $industry->icon, // Assuming icon is stored as string (e.g., 'Code', 'Database')
                    'count' => $industry->job_postings_count,
                ];
            });

        \Illuminate\Support\Facades\Log::info('Popular Categories:', $popularCategories->toArray());

        // Get top companies
        $topCompanies = $this->companyService->getTopCompanies(4)
            ->map(fn($company) => $this->companyService->transformForHome($company));

        // Get homepage sections configuration
        $sections = \App\Models\HomepageSection::active()
            ->ordered()
            ->get(['section_key', 'title', 'subtitle', 'content', 'is_active']);

        return Inertia::render('client/Home', [
            'banners' => $banners,
            'featuredJobs' => $featuredJobs,
            'topCompanies' => $topCompanies,
            'popularCategories' => $popularCategories,
            'sections' => $sections,
        ]);
    }
}
