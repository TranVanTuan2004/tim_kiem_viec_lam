<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the candidate's applications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create')
                ->with('info', 'Please complete your profile first.');
        }

        $query = $candidateProfile->applications()
            ->with(['jobPosting.company', 'jobPosting.industry']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by job title or company name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('jobPosting', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $applications = $query->paginate(12)->through(function ($application) {
            return $this->transformApplication($application);
        });

        $stats = [
            'total' => $candidateProfile->applications()->count(),
            'pending' => $candidateProfile->applications()->where('status', 'pending')->count(),
            'reviewing' => $candidateProfile->applications()->where('status', 'reviewing')->count(),
            'interview' => $candidateProfile->applications()->where('status', 'interview')->count(),
            'rejected' => $candidateProfile->applications()->where('status', 'rejected')->count(),
            'accepted' => $candidateProfile->applications()->where('status', 'accepted')->count(),
        ];

        return Inertia::render('candidate/Applications/Index', [
            'applications' => $applications,
            'stats' => $stats,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'search' => $request->get('search', ''),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        $application = Application::with([
            'jobPosting.company',
            'jobPosting.industry',
            'jobPosting.skills',
        ])->findOrFail($id);

        // Check ownership
        if ($application->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('candidate/Applications/Show', [
            'application' => $this->transformApplication($application),
        ]);
    }

    /**
     * Withdraw an application.
     */
    public function withdraw($id)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        $application = Application::findOrFail($id);

        // Check ownership
        if ($application->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        // Only allow withdrawal of pending or reviewed applications
        if (!in_array($application->status, ['pending', 'reviewing'])) {
            return back()->with('error', 'Cannot withdraw this application.');
        }

        $application->update(['status' => 'withdrawn']);

        return redirect()->route('candidate.applications.index')
            ->with('success', 'Application withdrawn successfully!');
    }

    /**
     * Transform application data for response.
     */
    private function transformApplication($application)
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'cover_letter' => $application->cover_letter,
            'applied_at' => $application->created_at ? $application->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $application->updated_at ? $application->updated_at->format('Y-m-d H:i:s') : null,
            'job_posting' => [
                'id' => $application->jobPosting->id,
                'title' => $application->jobPosting->title,
                'description' => $application->jobPosting->description,
                'location' => $application->jobPosting->location,
                'job_type' => $application->jobPosting->job_type,
                'experience_level' => $application->jobPosting->experience_level,
                'salary_min' => $application->jobPosting->salary_min,
                'salary_max' => $application->jobPosting->salary_max,
                'deadline' => $application->jobPosting->application_deadline ? $application->jobPosting->application_deadline->format('Y-m-d') : null,
                'status' => $application->jobPosting->status,
                'company' => [
                    'id' => $application->jobPosting->company->id,
                    'name' => $application->jobPosting->company->name,
                    'logo' => $application->jobPosting->company->logo,
                    'website' => $application->jobPosting->company->website,
                    'location' => $application->jobPosting->company->location,
                ],
                'industry' => $application->jobPosting->industry ? [
                    'id' => $application->jobPosting->industry->id,
                    'name' => $application->jobPosting->industry->name,
                ] : null,
                'skills' => $application->jobPosting->skills?->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                    ];
                }),
            ],
        ];
    }
}

