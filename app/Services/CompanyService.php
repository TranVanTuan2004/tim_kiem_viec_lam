<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService
{
    /**
     * Get paginated companies with filters
     */
    public function getFilteredCompanies(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Company::with(['user', 'jobPostings'])
            ->verified()
            ->withCount('jobPostings')
            ->select([
                'id',
                'company_name',
                'company_slug',
                'description',
                'website',
                'logo',
                'address',
                'city',
                'province',
                'size',
                'industry',
                'rating',
                'total_reviews',
                'is_verified',
                'created_at',
                'updated_at'
            ]);

        // Filter companies that have posted jobs
        if (!empty($filters['has_jobs'])) {
            $query->has('jobPostings');
        }

        // Search by keyword (company name, description, industry)
        if (!empty($filters['q'])) {
            $keyword = $filters['q'];
            $query->where(function($q) use ($keyword) {
                $q->where('company_name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('industry', 'like', "%{$keyword}%")
                  ->orWhere('city', 'like', "%{$keyword}%")
                  ->orWhere('province', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['industry'])) {
            $query->byIndustry($filters['industry']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['size'])) {
            $query->bySize($filters['size']);
        }

        return $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get top companies for homepage
     */
    public function getTopCompanies(int $limit = 4): Collection
    {
        return Company::withCount([
            'jobPostings' => function ($query) {
                $query->where('status', 'approved')->whereNotNull('published_at');
            }
        ])
            ->verified()
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get company detail with relations
     */
    public function getCompanyDetail(Company $company): Company
    {
        return $company->load(['user']);
    }

    /**
     * Get recent jobs for company
     */
    public function getRecentJobs(Company $company, int $limit = 6): Collection
    {
        return $company->jobPostings()
            ->published()
            ->with(['skills', 'industry'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get paginated jobs for company
     */
    public function getCompanyJobs(Company $company, int $perPage = 12): LengthAwarePaginator
    {
        return $company->jobPostings()
            ->published()
            ->with(['skills', 'industry'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get approved reviews for company (and pending reviews from current user)
     */
    public function getCompanyReviews(Company $company, int $perPage = 10): LengthAwarePaginator
    {
        $query = $company->reviews()
            ->with(['candidate.user'])
            ->where(function ($q) {
                $q->where('status', 'approved');
                
                // Also show pending reviews if it's the current user's review
                if (auth()->check() && auth()->user()->candidateProfile) {
                    $q->orWhere(function ($subQ) {
                        $subQ->where('status', 'pending')
                            ->where('candidate_id', auth()->user()->candidateProfile->id);
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get rating statistics for company
     */
    public function getRatingStats(Company $company): array
    {
        $stats = $company->reviews()
            ->where('status', 'approved')
            ->selectRaw('
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return [
            'average_rating' => round($stats->average_rating ?? 0, 1),
            'total_reviews' => $stats->total_reviews ?? 0,
            'five_star' => $stats->five_star ?? 0,
            'four_star' => $stats->four_star ?? 0,
            'three_star' => $stats->three_star ?? 0,
            'two_star' => $stats->two_star ?? 0,
            'one_star' => $stats->one_star ?? 0,
        ];
    }

    /**
     * Transform company for homepage display
     */
    public function transformForHome(Company $company): array
    {
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
    }

    /**
     * Transform company for detail display
     */
    public function transformForDetail(Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->company_name,
            'slug' => $company->company_slug,
            'description' => $company->description,
            'website' => $company->website,
            'logo' => $company->logo,
            'address' => $company->address,
            'city' => $company->city,
            'province' => $company->province,
            'size' => $company->size,
            'industry' => $company->industry,
            'is_verified' => $company->is_verified,
            'created_at' => $company->created_at,
        ];
    }

    /**
     * Transform company for jobs page
     */
    public function transformForJobsPage(Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->company_name,
            'slug' => $company->company_slug,
            'logo' => $company->logo,
            'description' => $company->description,
        ];
    }
}

