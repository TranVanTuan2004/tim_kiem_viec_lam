<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Payment;
use App\Models\Subscription;    
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'total_jobs' => JobPosting::count(),
            'total_applications' => Application::count(),
            'total_payments' => Payment::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        $roleStats = [
            'admins' => User::withRole('Admin')->count(),
            'employers' => User::withRole('Employer')->count(),
            'candidates' => User::withRole('Candidate')->count(),
        ];

        $jobStats = [
            'published' => JobPosting::where('status', 'approved')->count(),
            'pending' => JobPosting::where('status', 'pending')->count(),
            'expired' => JobPosting::where('application_deadline', '<', now())->whereNull('deleted_at')->count(),
            'featured' => JobPosting::where('is_featured', true)->count(),
        ];

        $applicationStats = [
            'pending' => Application::where('status', 'pending')->count(),
            'accepted' => Application::where('status', 'accepted')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'interview' => Application::where('status', 'interview')->count(),
        ];

        $revenue = [
            'total' => Payment::where('status', 'completed')->sum('amount'),
            'this_month' => Payment::where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'last_month' => Payment::where('status', 'completed')
                ->whereMonth('paid_at', now()->subMonth()->month)
                ->whereYear('paid_at', now()->subMonth()->year)
                ->sum('amount'),
        ];

        $topJobs = JobPosting::with(['company'])
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        $topCompanies = Company::withCount('jobPostings')
            ->orderBy('job_postings_count', 'desc')
            ->limit(5)
            ->get();

        $newUsers = User::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $newJobs = JobPosting::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $last30Days = collect(range(29, 0))->map(function ($days) {
            $date = now()->subDays($days)->startOfDay();
            return [
                'date' => $date->format('Y-m-d'),
                'display' => $date->format('d/m'),
                'users' => User::whereDate('created_at', $date->toDateString())->count(),
                'jobs' => JobPosting::whereDate('created_at', $date->toDateString())->count(),
                'applications' => Application::whereDate('created_at', $date->toDateString())->count(),
            ];
        });

        $recentActivities = [
            'latest_users' => User::with('roles')->latest()->limit(5)->get(),
            'latest_jobs' => JobPosting::with('company')->latest()->limit(5)->get(),
            'latest_applications' => Application::with(['jobPosting.company', 'candidate.user'])->latest()->limit(5)->get(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'roleStats' => $roleStats,
            'jobStats' => $jobStats,
            'applicationStats' => $applicationStats,
            'revenue' => $revenue,
            'topJobs' => $topJobs,
            'topCompanies' => $topCompanies,
            'newUsers' => $newUsers,
            'newJobs' => $newJobs,
            'last30Days' => $last30Days,
            'recentActivities' => $recentActivities,
        ]);
    }
}

