<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of work experiences.
     */
    public function index()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $workExperiences = $candidateProfile->workExperiences()
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($exp) {
                return $this->transformWorkExperience($exp);
            });

        return Inertia::render('candidate/WorkExperience/Index', [
            'workExperiences' => $workExperiences,
        ]);
    }

    /**
     * Show the form for creating a new work experience.
     */
    public function create()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        return Inertia::render('candidate/WorkExperience/Create');
    }

    /**
     * Store a newly created work experience in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship,freelance'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $candidateProfile->workExperiences()->create($validated);

        return redirect()->route('candidate.work-experiences.index')
            ->with('success', 'Work experience added successfully!');
    }

    /**
     * Show the form for editing the specified work experience.
     */
    public function edit(WorkExperience $workExperience)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $workExperience->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('candidate/WorkExperience/Edit', [
            'workExperience' => $this->transformWorkExperience($workExperience),
        ]);
    }

    /**
     * Update the specified work experience in storage.
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $workExperience->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship,freelance'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $workExperience->update($validated);

        return redirect()->route('candidate.work-experiences.index')
            ->with('success', 'Work experience updated successfully!');
    }

    /**
     * Remove the specified work experience from storage.
     */
    public function destroy(WorkExperience $workExperience)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $workExperience->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        $workExperience->delete();

        return redirect()->route('candidate.work-experiences.index')
            ->with('success', 'Work experience deleted successfully!');
    }

    /**
     * Transform work experience data for response.
     */
    private function transformWorkExperience($experience)
    {
        return [
            'id' => $experience->id,
            'company_name' => $experience->company_name,
            'job_title' => $experience->job_title,
            'employment_type' => $experience->employment_type,
            'start_date' => $experience->start_date?->format('Y-m-d'),
            'end_date' => $experience->end_date?->format('Y-m-d'),
            'is_current' => $experience->is_current,
            'description' => $experience->description,
            'period' => $experience->getPeriod(),
            'duration_months' => $experience->getDurationInMonths(),
        ];
    }
}
