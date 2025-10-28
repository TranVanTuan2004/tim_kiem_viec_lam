<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SavedJobController extends Controller
{
    /**
     * Display a listing of saved jobs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create')
                ->with('info', 'Please complete your profile first.');
        }

        $query = $candidateProfile->savedJobPostings()
            ->with(['company', 'industry']);

        // Search by job title or company name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by job type
        if ($request->has('job_type') && $request->job_type !== 'all') {
            $query->where('job_type', $request->job_type);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $savedJobs = $query->latest('saved_jobs.created_at')
            ->paginate(12)
            ->through(function ($job) use ($candidateProfile) {
                return $this->transformJobPosting($job, $candidateProfile);
            });

        return Inertia::render('candidate/SavedJobs/Index', [
            'savedJobs' => $savedJobs,
            'filters' => [
                'search' => $request->get('search', ''),
                'job_type' => $request->get('job_type', 'all'),
                'location' => $request->get('location', ''),
            ],
        ]);
    }

    /**
     * Toggle save status of a job.
     */
    public function toggle($jobId)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $job = JobPosting::findOrFail($jobId);

        if ($candidateProfile->savedJobPostings()->where('job_posting_id', $jobId)->exists()) {
            // Unsave
            $candidateProfile->savedJobPostings()->detach($jobId);
            $isSaved = false;
            $message = 'Job removed from saved list.';
        } else {
            // Save
            $candidateProfile->savedJobPostings()->attach($jobId);
            $isSaved = true;
            $message = 'Job saved successfully!';
        }

        return response()->json([
            'success' => true,
            'is_saved' => $isSaved,
            'message' => $message,
        ]);
    }

    /**
     * Remove a job from saved list.
     */
    public function destroy($jobId)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $candidateProfile->savedJobPostings()->detach($jobId);

        return redirect()->route('candidate.saved-jobs.index')
            ->with('success', 'Job removed from saved list.');
    }

    /**
     * Transform job posting data for response.
     */
    private function transformJobPosting($job, $candidateProfile)
    {
        $hasApplied = $candidateProfile->applications()
            ->where('job_posting_id', $job->id)
            ->exists();

        return [
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'location' => $job->location,
            'job_type' => $job->job_type,
            'experience_level' => $job->experience_level,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'deadline' => $job->deadline ? $job->deadline->format('Y-m-d') : null,
            'status' => $job->status,
            'saved_at' => $job->pivot?->created_at?->format('Y-m-d H:i:s'),
            'has_applied' => $hasApplied,
            'company' => [
                'id' => $job->company->id,
                'name' => $job->company->name,
                'logo' => $job->company->logo,
                'location' => $job->company->location,
            ],
            'industry' => $job->industry ? [
                'id' => $job->industry->id,
                'name' => $job->industry->name,
            ] : null,
        ];
    }
}

