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
        $user = $request->user();
        
        // Cho phép Admin hoặc Employer truy cập
        if (!$user->hasRole('Admin') && !$user->hasRole('Employer')) {
            abort(403, 'Unauthorized action.');
        }
        
        $isAdmin = $user->hasRole('Admin');

        $data = [
            'isAdmin' => $isAdmin,
        ];

        if ($isAdmin) {
            // Admin dashboard data
            $data['stats'] = [
                'total_users' => User::count(),
                'total_companies' => Company::count(),
                'total_jobs' => JobPosting::count(),
                'total_applications' => Application::count(),
                'total_payments' => Payment::count(),
                'active_subscriptions' => Subscription::where('status', 'active')->count(),
            ];

            $data['roleStats'] = [
                'admins' => User::withRole('Admin')->count(),
                'employers' => User::withRole('Employer')->count(),
                'candidates' => User::withRole('Candidate')->count(),
            ];

            $data['jobStats'] = [
                'published' => JobPosting::where('status', 'approved')->count(),
                'pending' => JobPosting::where('status', 'pending')->count(),
                'expired' => JobPosting::where('application_deadline', '<', now())->whereNull('deleted_at')->count(),
                'featured' => JobPosting::where('is_featured', true)->count(),
            ];

            $data['applicationStats'] = [
                'pending' => Application::where('status', 'pending')->count(),
                'accepted' => Application::where('status', 'accepted')->count(),
                'rejected' => Application::where('status', 'rejected')->count(),
                'interview' => Application::where('status', 'interview')->count(),
            ];

            $data['revenue'] = [
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

            $data['topJobs'] = JobPosting::with(['company'])
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get();

            $data['topCompanies'] = Company::withCount('jobPostings')
                ->orderBy('job_postings_count', 'desc')
                ->limit(5)
                ->get();

            $data['newUsers'] = User::where('created_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['newJobs'] = JobPosting::where('created_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $data['last30Days'] = collect(range(29, 0))->map(function ($days) {
                $date = now()->subDays($days)->startOfDay();
                return [
                    'date' => $date->format('Y-m-d'),
                    'display' => $date->format('d/m'),
                    'users' => User::whereDate('created_at', $date->toDateString())->count(),
                    'jobs' => JobPosting::whereDate('created_at', $date->toDateString())->count(),
                    'applications' => Application::whereDate('created_at', $date->toDateString())->count(),
                ];
            });

            $data['recentActivities'] = [
                'latest_users' => User::with('roles')->latest()->limit(5)->get(),
                'latest_jobs' => JobPosting::with('company')->latest()->limit(5)->get(),
                'latest_applications' => Application::with(['jobPosting.company', 'candidate.user'])->latest()->limit(5)->get(),
            ];
        } else {
            // Employer dashboard data
            $company = $user->company;

            if (!$company) {
                return redirect()->route('employer.company.create')
                    ->with('info', 'Please complete your company profile to access all features.');
            }

            $data['stats'] = [
                'total_jobs' => $company->jobPostings()->count(),
                'active_jobs' => $company->jobPostings()->where('status', 'active')->count(),
                'total_applications' => $company->jobPostings()->withCount('applications')->get()->sum('applications_count'),
                'pending_applications' => $company->jobPostings()
                    ->whereHas('applications', function ($query) {
                        $query->where('status', 'pending');
                    })->count(),
                'subscription_status' => $company->subscription ? $company->subscription->status : 'none',
            ];

            $data['recentJobs'] = $company->jobPostings()
                ->with(['industry', 'applications'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'status' => $job->status,
                        'location' => $job->location,
                        'job_type' => $job->job_type,
                        'applications_count' => $job->applications->count(),
                        'created_at' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                        'deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                    ];
                });

            $data['recentApplications'] = $company->jobPostings()
                ->with(['applications.candidateProfile.user'])
                ->get()
                ->pluck('applications')
                ->flatten()
                ->sortByDesc('created_at')
                ->take(10)
                ->values()
                ->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'status' => $application->status,
                        'applied_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
                        'candidate' => [
                            'name' => $application->candidateProfile->user->name,
                            'email' => $application->candidateProfile->user->email,
                            'avatar' => $application->candidateProfile->avatar,
                            'current_position' => $application->candidateProfile->current_position,
                        ],
                        'job_posting' => [
                            'id' => $application->jobPosting->id,
                            'title' => $application->jobPosting->title,
                        ],
                    ];
                });

            $data['company'] = [
                'id' => $company->id,
                'name' => $company->name,
                'logo' => $company->logo,
                'subscription' => $company->subscription ? [
                    'status' => $company->subscription->status,
                    'expires_at' => $company->subscription->expires_at,
                ] : null,
                'created_at' => $company->created_at ? $company->created_at->format('Y-m-d H:i:s') : null,
            ];
        }

        return Inertia::render('admin/dashboard', $data);
    }
}

