<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $companies = Company::with(['user', 'jobPostings'])
            ->verified()
            ->withCount('jobPostings')
            ->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->paginate(12);

        return Inertia::render('client/CompaniesIndex', [
            'companies' => $companies,
        ]);
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        $company->load([
            'user',
            'jobPostings' => function ($query) {
                $query->where('status', 'published')
                      ->orderBy('published_at', 'desc')
                      ->limit(10);
            },
            'reviews' => function ($query) {
                $query->where('status', 'approved')
                      ->orderBy('created_at', 'desc')
                      ->limit(5);
            }
        ]);

        // Get recent job postings
        $recentJobs = $company->jobPostings()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Get approved reviews with pagination
        $reviews = $company->reviews()
            ->where('status', 'approved')
            ->with('candidate')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate rating statistics
        $ratingStats = $company->reviews()
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

        return Inertia::render('client/CompanyDetail', [
            'company' => [
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
            ],
            'recentJobs' => $recentJobs->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'location' => $job->location,
                    'employment_type' => $job->employment_type,
                    'experience_level' => $job->experience_level,
                    'min_salary' => $job->min_salary,
                    'max_salary' => $job->max_salary,
                    'salary_type' => $job->salary_type,
                    'published_at' => $job->published_at,
                ];
            }),
            'reviews' => $reviews,
            'ratingStats' => [
                'average_rating' => round($ratingStats->average_rating ?? 0, 1),
                'total_reviews' => $ratingStats->total_reviews ?? 0,
                'five_star' => $ratingStats->five_star ?? 0,
                'four_star' => $ratingStats->four_star ?? 0,
                'three_star' => $ratingStats->three_star ?? 0,
                'two_star' => $ratingStats->two_star ?? 0,
                'one_star' => $ratingStats->one_star ?? 0,
            ],
        ]);
    }
}
