<?php

namespace App\Services;

use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class JobPostingService
{
    /**
     * Get filtered and paginated job listings
     */
    public function getFilteredJobs(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $userId = auth()->check() ? auth()->id() : null;

        $query = JobPosting::published()
            ->with(['company', 'skills']);

        // Only load favoritedBy if user is authenticated
        if ($userId) {
            $query->with([
                'favoritedBy' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);
        }

        $query->orderBy('published_at', 'desc');

        // Apply filters
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        if (!empty($filters['location'])) {
            $query->filterByLocation($filters['location']);
        }

        if (!empty($filters['industry'])) {
            if (is_numeric($filters['industry'])) {
                $query->byIndustry($filters['industry']);
            } else {
                $industry = \App\Models\Industry::where('slug', $filters['industry'])->first();
                if ($industry) {
                    $query->byIndustry($industry->id);
                }
            }
        }

        if (!empty($filters['employment_type'])) {
            $query->byEmploymentType($filters['employment_type']);
        }

        if (!empty($filters['experience_level'])) {
            $query->byExperienceLevel($filters['experience_level']);
        }

        if (!empty($filters['min_salary']) && !empty($filters['max_salary'])) {
            $query->bySalaryRange($filters['min_salary'], $filters['max_salary']);
        }
        $paginated = $query->paginate($perPage)->withQueryString();

        return $paginated;
    }

    /**
     * Get featured jobs for homepage
     */
    public function getFeaturedJobs(int $limit = 6): Collection
    {
        $userId = auth()->id();

        $query = JobPosting::published()
            ->featured()
            ->with(['company', 'skills']);

        // Only load favoritedBy if user is authenticated
        if ($userId) {
            $query->with([
                'favoritedBy' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);
        }

        return $query->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }


    /**
     * Get job by slug with relations
     */
    public function getJobBySlug(string $slug): ?JobPosting
    {
        return JobPosting::where('slug', $slug)
            ->with(['company', 'industry', 'skills'])
            ->first();
    }

    /**
     * Get job for detail page
     */
    public function getJobDetail(JobPosting $jobPosting): JobPosting
    {
        $jobPosting->load(['company', 'industry', 'skills']);
        $jobPosting->incrementViews();

        return $jobPosting;
    }

    /**
     * Transform job for listing display
     */
    public function transformForListing(JobPosting $job): array
    {
        // Check if user is authenticated and has favorited this job
        $isFavorited = 0;
        if (auth()->check() && $job->relationLoaded('favoritedBy')) {
            $isFavorited = $job->favoritedBy->first()?->pivot->is_favorited ?? 0;
        }

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
            'is_favorited' => $isFavorited,
        ];
    }

    /**
     * Transform job for homepage display
     */
    public function transformForHome(JobPosting $job): array
    {
        // Check if user is authenticated and has favorited this job
        $isFavorited = 0;
        if (auth()->check() && $job->relationLoaded('favoritedBy')) {
            $isFavorited = $job->favoritedBy->first()?->pivot->is_favorited ?? 0;
        }

        return [
            'id' => $job->id,
            'slug' => $job->slug,
            'title' => $job->title,
            'company' => $job->company->company_name ?? 'Công ty',
            'company_logo' => $job->company->logo ?? null,
            'logo' => $this->getCompanyEmoji($job->company),
            'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
            'salary' => $job->getSalaryRange(),
            'type' => $job->employment_type ? str_replace('_', ' ', ucfirst($job->employment_type)) : 'Full-time',
            'skills' => $job->skills->take(3)->pluck('name')->toArray(),
            'posted' => $job->published_at ? $job->published_at->diffForHumans() : 'Vừa đăng',
            'is_favorited' => $isFavorited,
        ];
    }

    /**
     * Transform job for detail display
     */
    public function transformForDetail(JobPosting $job): array
    {
        return [
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
        ];
    }

    /**
     * Transform job for company page display
     */
    public function transformForCompany(JobPosting $job): array
    {
        return [
            'id' => $job->id,
            'title' => $job->title,
            'slug' => $job->slug,
            'location' => $job->location,
            'city' => $job->city,
            'province' => $job->province,
            'employment_type' => $job->employment_type,
            'experience_level' => $job->experience_level,
            'min_salary' => $job->min_salary,
            'max_salary' => $job->max_salary,
            'salary_type' => $job->salary_type,
            'published_at' => $job->published_at,
            'skills' => $job->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                ];
            }),
            'industry' => $job->industry ? [
                'id' => $job->industry->id,
                'name' => $job->industry->name,
            ] : null,
        ];
    }

    /**
     * Transform job for application form
     */
    public function transformForApplication(JobPosting $job): array
    {
        return [
            'id' => $job->id,
            'slug' => $job->slug,
            'title' => $job->title,
            'company' => [
                'name' => $job->company->company_name ?? 'Công ty',
                'logo' => $job->company->logo ?? null,
            ],
            'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
            'salary' => $job->getSalaryRange(),
            'employment_type' => $job->employment_type,
            'experience_level' => $job->experience_level,
        ];
    }

    /**
     * Get company emoji based on company name
     */
    private function getCompanyEmoji($company): string
    {
        if (!$company) {
            return '🏢';
        }

        $emojis = ['🏢', '💼', '🚀', '⚡', '🎯', '💻', '🔥', '⭐'];
        $index = abs(crc32($company->company_name ?? 'default')) % count($emojis);
        return $emojis[$index];
    }

    /**
     * Validate job filters
     */
    public function validateFilters(array $filters): array
    {
        $validated = [];

        if (isset($filters['featured'])) {
            $validated['featured'] = filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($filters['q']) && !empty($filters['q'])) {
            $validated['q'] = trim($filters['q']);
        }

        if (isset($filters['location']) && !empty($filters['location'])) {
            $validated['location'] = trim($filters['location']);
        }

        if (isset($filters['industry']) && !empty($filters['industry'])) {
            $validated['industry'] = $filters['industry'];
        }

        if (isset($filters['employment_type'])) {
            $validated['employment_type'] = $filters['employment_type'];
        }

        if (isset($filters['experience_level'])) {
            $validated['experience_level'] = $filters['experience_level'];
        }

        if (isset($filters['min_salary']) && is_numeric($filters['min_salary'])) {
            $validated['min_salary'] = (float) $filters['min_salary'];
        }

        if (isset($filters['max_salary']) && is_numeric($filters['max_salary'])) {
            $validated['max_salary'] = (float) $filters['max_salary'];
        }

        return $validated;
    }
}