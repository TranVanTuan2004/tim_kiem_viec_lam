<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the candidate profile.
     */
    public function index()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        // Load all related data
        $candidateProfile->load([
            'skills',
            'workExperiences' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'educations' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'portfolios' => function ($query) {
                $query->orderBy('display_order', 'asc');
            },
        ]);

        return Inertia::render('candidate/Profile/Index', [
            'profile' => $this->transformProfile($candidateProfile),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ],
        ]);
    }

    /**
     * Show the form for creating a new profile.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->candidateProfile) {
            return redirect()->route('candidate.profile.index');
        }

        $allSkills = Skill::orderBy('name')->get();

        return Inertia::render('candidate/Profile/Create', [
            'allSkills' => $allSkills,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * Store a newly created profile in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'current_position' => ['nullable', 'string', 'max:100'],
            'current_company' => ['nullable', 'string', 'max:100'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'experience_level' => ['nullable', 'in:fresher,junior,middle,senior,lead'],
            'is_available' => ['boolean'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ]);

        $user = Auth::user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
            $validated['cv_file'] = $cvPath;
        }

        // Create profile
        $profile = $user->candidateProfile()->create($validated);

        // Attach skills if provided
        if (!empty($validated['skills'])) {
            foreach ($validated['skills'] as $skillId) {
                $profile->skills()->attach($skillId, [
                    'level' => 'intermediate',
                    'years_experience' => 1,
                ]);
            }
        }

        return redirect()->route('candidate.dashboard')
            ->with('success', 'Profile created successfully!');
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create');
        }

        $candidateProfile->load(['skills']);
        $allSkills = Skill::orderBy('name')->get();

        return Inertia::render('candidate/Profile/Edit', [
            'profile' => $this->transformProfile($candidateProfile),
            'allSkills' => $allSkills,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ],
        ]);
    }

    /**
     * Update the profile in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'current_position' => ['nullable', 'string', 'max:100'],
            'current_company' => ['nullable', 'string', 'max:100'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'experience_level' => ['nullable', 'in:fresher,junior,middle,senior,lead'],
            'is_available' => ['boolean'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['required', 'integer', 'exists:skills,id'],
            'skills.*.level' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'skills.*.years_experience' => ['required', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $profile = $user->candidateProfile;

        if (!$profile) {
            return redirect()->route('candidate.profile.create');
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            // Delete old CV if exists
            if ($profile->cv_file) {
                Storage::disk('public')->delete($profile->cv_file);
            }
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
            $validated['cv_file'] = $cvPath;
        }

        // Update profile
        $profile->update($validated);

        // Sync skills with pivot data
        if (isset($validated['skills'])) {
            $skillsData = [];
            foreach ($validated['skills'] as $skill) {
                $skillsData[$skill['id']] = [
                    'level' => $skill['level'],
                    'years_experience' => $skill['years_experience'],
                ];
            }
            $profile->skills()->sync($skillsData);
        }

        return redirect()->route('candidate.profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Toggle availability status.
     */
    public function toggleAvailability()
    {
        $user = Auth::user();
        $profile = $user->candidateProfile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $profile->update([
            'is_available' => !$profile->is_available,
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $profile->is_available,
            'message' => 'Availability status updated successfully!',
        ]);
    }

    /**
     * Transform profile data for response.
     */
    private function transformProfile($profile)
    {
        return [
            'id' => $profile->id,
            'avatar' => $profile->avatar,
            'avatar_url' => $profile->avatar ? Storage::url($profile->avatar) : null,
            'birth_date' => $profile->birth_date?->format('Y-m-d'),
            'age' => $profile->getAge(),
            'gender' => $profile->gender,
            'address' => $profile->address,
            'city' => $profile->city,
            'province' => $profile->province,
            'cv_file' => $profile->cv_file,
            'cv_url' => $profile->cv_file ? Storage::url($profile->cv_file) : null,
            'summary' => $profile->summary,
            'current_position' => $profile->current_position,
            'current_company' => $profile->current_company,
            'expected_salary' => $profile->expected_salary,
            'experience_level' => $profile->experience_level,
            'is_available' => $profile->is_available,
            'skills' => $profile->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'slug' => $skill->slug,
                    'level' => $skill->pivot->level,
                    'years_experience' => $skill->pivot->years_experience,
                ];
            }),
            'work_experiences' => $profile->workExperiences?->map(function ($exp) {
                return [
                    'id' => $exp->id,
                    'company_name' => $exp->company_name,
                    'position' => $exp->position,
                    'start_date' => $exp->start_date ? $exp->start_date->format('Y-m-d') : null,
                    'end_date' => $exp->end_date?->format('Y-m-d'),
                    'is_current' => $exp->is_current,
                    'description' => $exp->description,
                ];
            }),
            'educations' => $profile->educations?->map(function ($edu) {
                return [
                    'id' => $edu->id,
                    'institution' => $edu->institution,
                    'degree' => $edu->degree,
                    'field_of_study' => $edu->field_of_study,
                    'start_date' => $edu->start_date ? $edu->start_date->format('Y-m-d') : null,
                    'end_date' => $edu->end_date?->format('Y-m-d'),
                    'is_current' => $edu->is_current,
                    'description' => $edu->description,
                ];
            }),
            'portfolios' => $profile->portfolios?->map(function ($portfolio) {
                return [
                    'id' => $portfolio->id,
                    'title' => $portfolio->title,
                    'description' => $portfolio->description,
                    'project_url' => $portfolio->project_url,
                    'thumbnail' => $portfolio->thumbnail,
                    'is_featured' => $portfolio->is_featured,
                    'is_public' => $portfolio->is_public,
                ];
            }),
        ];
    }
}

