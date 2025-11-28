<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Inertia\Inertia;

class IndustryController extends Controller
{
    public function index()
    {
        $industries = Industry::active()
            ->withCount('jobPostings')
            ->orderBy('job_postings_count', 'desc')
            ->get()
            ->map(function ($industry) {
                return [
                    'id' => $industry->id,
                    'name' => $industry->name,
                    'slug' => $industry->slug,
                    'icon' => $industry->icon,
                    'description' => $industry->description,
                    'count' => $industry->job_postings_count,
                ];
            });

        return Inertia::render('client/IndustriesIndex', [
            'industries' => $industries,
        ]);
    }
}
