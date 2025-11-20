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
            'skills.*.name' => ['required', 'string', 'max:255'],
            'work_experiences' => ['nullable', 'array'],
            'work_experiences.*.company_name' => ['required', 'string', 'max:255'],
            'work_experiences.*.position' => ['required', 'string', 'max:255'],
            'work_experiences.*.description' => ['nullable', 'string'],
            'work_experiences.*.start_date' => ['required', 'date'],
            'work_experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:work_experiences.*.start_date'],
            'work_experiences.*.is_current' => ['boolean'],
            'educations' => ['nullable', 'array'],
            'educations.*.institution' => ['required', 'string', 'max:255'],
            'educations.*.degree' => ['required', 'string', 'max:255'],
            'educations.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'educations.*.start_date' => ['required', 'date'],
            'educations.*.end_date' => ['nullable', 'date', 'after_or_equal:educations.*.start_date'],
            'educations.*.gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'educations.*.description' => ['nullable', 'string'],
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
            $skillIds = [];
            foreach ($validated['skills'] as $skill) {
                if (isset($skill['name']) && !empty($skill['name'])) {
                    // Create or find skill by name
                    $skillModel = \App\Models\Skill::firstOrCreate(
                        ['name' => trim($skill['name'])],
                        [
                            'slug' => \Illuminate\Support\Str::slug(trim($skill['name'])),
                            'is_active' => true,
                        ]
                    );
                    $skillIds[] = $skillModel->id;
                }
            }
            
            if (!empty($skillIds)) {
                // Attach skills without pivot data (no level, no years_experience)
                $profile->skills()->sync($skillIds);
            }
        }

        // Create work experiences if provided
        if (!empty($validated['work_experiences'])) {
            foreach ($validated['work_experiences'] as $exp) {
                $expData = [
                    'company_name' => $exp['company_name'],
                    'position' => $exp['position'],
                    'description' => $exp['description'] ?? null,
                    'start_date' => $exp['start_date'],
                    'is_current' => $exp['is_current'] ?? false,
                ];
                // If is_current is true, set end_date to null
                if ($expData['is_current']) {
                    $expData['end_date'] = null;
                } else {
                    $expData['end_date'] = $exp['end_date'] ?? null;
                }
                $profile->workExperiences()->create($expData);
            }
        }

        // Create educations if provided
        if (!empty($validated['educations'])) {
            foreach ($validated['educations'] as $edu) {
                $eduData = [
                    'institution' => $edu['institution'],
                    'degree' => $edu['degree'],
                    'field_of_study' => $edu['field_of_study'] ?? null,
                    'start_date' => $edu['start_date'],
                    'end_date' => $edu['end_date'] ?? null,
                    'gpa' => $edu['gpa'] ?? null,
                    'description' => $edu['description'] ?? null,
                ];
                // If is_current was true (end_date should be null), ensure it's null
                // Note: is_current is removed in frontend transform, so we check end_date
                // If end_date is explicitly null or empty, it means current
                if (empty($eduData['end_date'])) {
                    $eduData['end_date'] = null;
                }
                $profile->educations()->create($eduData);
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

        $candidateProfile->load([
            'skills',
            'workExperiences' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'educations' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
        ]);
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
        \Log::info('=== Profile Update Request ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Has avatar file: ' . ($request->hasFile('avatar') ? 'YES' : 'NO'));
        \Log::info('All request data keys: ' . implode(', ', array_keys($request->all())));
        
        if ($request->hasFile('avatar')) {
            \Log::info('Avatar file details', [
                'name' => $request->file('avatar')->getClientOriginalName(),
                'size' => $request->file('avatar')->getSize(),
                'mime' => $request->file('avatar')->getMimeType(),
            ]);
        }
        
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
            'skills.*.name' => ['required', 'string', 'max:255'],
            'work_experiences' => ['nullable', 'array'],
            'work_experiences.*.id' => ['nullable', 'integer', 'exists:work_experiences,id'],
            'work_experiences.*.company_name' => ['required', 'string', 'max:255'],
            'work_experiences.*.position' => ['required', 'string', 'max:255'],
            'work_experiences.*.description' => ['nullable', 'string'],
            'work_experiences.*.start_date' => ['required', 'date'],
            'work_experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:work_experiences.*.start_date'],
            'work_experiences.*.is_current' => ['boolean'],
            'educations' => ['nullable', 'array'],
            'educations.*.id' => ['nullable', 'integer', 'exists:educations,id'],
            'educations.*.institution' => ['required', 'string', 'max:255'],
            'educations.*.degree' => ['required', 'string', 'max:255'],
            'educations.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'educations.*.start_date' => ['required', 'date'],
            'educations.*.end_date' => ['nullable', 'date', 'after_or_equal:educations.*.start_date'],
            'educations.*.gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'educations.*.description' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $profile = $user->candidateProfile;

        if (!$profile) {
            return redirect()->route('candidate.profile.create');
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            \Log::info('Avatar file received', [
                'original_name' => $request->file('avatar')->getClientOriginalName(),
                'size' => $request->file('avatar')->getSize(),
            ]);
            
            // Delete old avatar if exists
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
            
            \Log::info('Avatar saved', ['path' => $avatarPath]);
        } else {
            \Log::info('No avatar file in request');
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

        // Update profile (exclude relationships that are handled separately)
        $profileFields = array_diff_key($validated, [
            'skills' => true,
            'work_experiences' => true,
            'educations' => true,
        ]);
        
        // Update profile fields
        $profile->update($profileFields);

        // Sync skills - only if provided in request
        if ($request->has('skills')) {
            if (!empty($validated['skills'])) {
                $skillIds = [];
                foreach ($validated['skills'] as $skill) {
                    if (isset($skill['name']) && !empty($skill['name'])) {
                        // Create or find skill by name
                        $skillModel = \App\Models\Skill::firstOrCreate(
                            ['name' => trim($skill['name'])],
                            [
                                'slug' => \Illuminate\Support\Str::slug(trim($skill['name'])),
                                'is_active' => true,
                            ]
                        );
                        $skillIds[] = $skillModel->id;
                    }
                }
                
                // Attach skills without pivot data (no level, no years_experience)
                $profile->skills()->sync($skillIds);
            } else {
                // If empty array provided, clear all skills
                $profile->skills()->sync([]);
            }
        }
        // If skills not in request, keep existing data unchanged

        // Sync work experiences - only if provided in request
        if ($request->has('work_experiences')) {
            if (!empty($validated['work_experiences'])) {
                $existingIds = [];
                foreach ($validated['work_experiences'] as $exp) {
                    $expData = [
                        'company_name' => $exp['company_name'],
                        'position' => $exp['position'],
                        'description' => $exp['description'] ?? null,
                        'start_date' => $exp['start_date'],
                        'is_current' => $exp['is_current'] ?? false,
                    ];
                    // If is_current is true, set end_date to null
                    if ($expData['is_current']) {
                        $expData['end_date'] = null;
                    } else {
                        $expData['end_date'] = $exp['end_date'] ?? null;
                    }
                    
                    if (isset($exp['id']) && $exp['id']) {
                        // Update existing - use model instance to ensure proper update
                        $workExp = $profile->workExperiences()->find($exp['id']);
                        if ($workExp) {
                            $workExp->update($expData);
                            $existingIds[] = $workExp->id;
                        }
                    } else {
                        // Create new
                        $newExp = $profile->workExperiences()->create($expData);
                        $existingIds[] = $newExp->id;
                    }
                }
                // Delete removed work experiences
                $profile->workExperiences()->whereNotIn('id', $existingIds)->delete();
            } else {
                // If empty array provided, delete all
                $profile->workExperiences()->delete();
            }
        }
        // If work_experiences not in request, keep existing data unchanged

        // Sync educations - only if provided in request
        if ($request->has('educations')) {
            if (!empty($validated['educations'])) {
                $existingIds = [];
                foreach ($validated['educations'] as $edu) {
                    $eduData = [
                        'institution' => $edu['institution'],
                        'degree' => $edu['degree'],
                        'field_of_study' => $edu['field_of_study'] ?? null,
                        'start_date' => $edu['start_date'],
                        'gpa' => $edu['gpa'] ?? null,
                        'description' => $edu['description'] ?? null,
                    ];
                    // Handle end_date - if is_current was true (end_date should be null)
                    // Note: is_current is removed in frontend transform, so we check end_date
                    if (isset($edu['end_date']) && !empty($edu['end_date'])) {
                        $eduData['end_date'] = $edu['end_date'];
                    } else {
                        $eduData['end_date'] = null;
                    }
                    
                    if (isset($edu['id']) && $edu['id']) {
                        // Update existing - use model instance to ensure proper update
                        $education = $profile->educations()->find($edu['id']);
                        if ($education) {
                            $education->update($eduData);
                            $existingIds[] = $education->id;
                        }
                    } else {
                        // Create new
                        $newEdu = $profile->educations()->create($eduData);
                        $existingIds[] = $newEdu->id;
                    }
                }
                // Delete removed educations
                $profile->educations()->whereNotIn('id', $existingIds)->delete();
            } else {
                // If empty array provided, delete all
                $profile->educations()->delete();
            }
        }
        // If educations not in request, keep existing data unchanged

        // Reload the profile from database to ensure fresh data
        $user = Auth::user();
        $user->load('candidateProfile');
        $profile = $user->candidateProfile;
        
        // Reload all relationships
        $profile->load([
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
            'avatar_url' => $profile->avatar ? asset('storage/' . $profile->avatar) : null,
            'birth_date' => $profile->birth_date?->format('Y-m-d'),
            'age' => $profile->getAge(),
            'gender' => $profile->gender,
            'address' => $profile->address,
            'city' => $profile->city,
            'province' => $profile->province,
            'cv_file' => $profile->cv_file,
            'cv_url' => $profile->cv_file ? asset('storage/' . $profile->cv_file) : null,
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
                    'is_current' => !$edu->end_date, // Compute from end_date
                    'gpa' => $edu->gpa,
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

