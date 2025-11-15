<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EducationController extends Controller
{
    /**
     * Display a listing of educations.
     */
    public function index()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $educations = $candidateProfile->educations()
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($edu) {
                return $this->transformEducation($edu);
            });

        return Inertia::render('candidate/Education/Index', [
            'educations' => $educations,
        ]);
    }

    /**
     * Show the form for creating a new education.
     */
    public function create()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        return Inertia::render('candidate/Education/Create');
    }

    /**
     * Store a newly created education in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['required', 'string', 'max:255'],
            'field_of_study' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'description' => ['nullable', 'string'],
        ]);

        $candidateProfile->educations()->create($validated);

        return redirect()->route('candidate.educations.index')
            ->with('success', 'Education added successfully!');
    }

    /**
     * Show the form for editing the specified education.
     */
    public function edit(Education $education)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $education->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('candidate/Education/Edit', [
            'education' => $this->transformEducation($education),
        ]);
    }

    /**
     * Update the specified education in storage.
     */
    public function update(Request $request, Education $education)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $education->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['required', 'string', 'max:255'],
            'field_of_study' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'description' => ['nullable', 'string'],
        ]);

        $education->update($validated);

        return redirect()->route('candidate.educations.index')
            ->with('success', 'Education updated successfully!');
    }

    /**
     * Remove the specified education from storage.
     */
    public function destroy(Education $education)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile || $education->candidate_id !== $candidateProfile->id) {
            abort(403, 'Unauthorized access');
        }

        $education->delete();

        return redirect()->route('candidate.educations.index')
            ->with('success', 'Education deleted successfully!');
    }

    /**
     * Transform education data for response.
     */
    private function transformEducation($education)
    {
        return [
            'id' => $education->id,
            'institution' => $education->institution,
            'degree' => $education->degree,
            'field_of_study' => $education->field_of_study,
            'start_date' => $education->start_date?->format('Y-m-d'),
            'end_date' => $education->end_date?->format('Y-m-d'),
            'gpa' => $education->gpa,
            'description' => $education->description,
            'period' => $education->getPeriod(),
            'degree_level' => $education->getDegreeLevel(),
            'is_completed' => $education->isCompleted(),
            'is_graduated' => $education->isGraduated(),
        ];
    }
}
