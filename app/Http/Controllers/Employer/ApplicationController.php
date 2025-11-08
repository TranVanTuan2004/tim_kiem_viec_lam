<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::whereHas('jobPosting.company', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->with(['candidate.user', 'jobPosting'])
            ->latest()
            ->get();

        return inertia('Employer/Applications/Index', [
            'applications' => $applications,
        ]);
    }

    // Xem chi tiết 1 ứng viên
    public function show($id)
    {
        $application = Application::with([
            'candidate.user',
            'candidate.skills',
            'candidate.workExperiences',
            'candidate.educations',
            'jobPosting'
        ])
        ->whereHas('jobPosting.company', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->findOrFail($id);

        return inertia('Employer/Applications/Show', [
            'application' => $application,
        ]);
    }
}
