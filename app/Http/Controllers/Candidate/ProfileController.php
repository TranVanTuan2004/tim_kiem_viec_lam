<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Education;
use App\Models\Skill;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the candidate's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile()->with([
            'skills',
            'workExperiences' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'educations' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
            'portfolios'
        ])->first();

        if (!$candidate) {
            // Create empty candidate profile if doesn't exist
            $candidate = CandidateProfile::create([
                'user_id' => $user->id,
            ]);
        }

        // Append attributes for frontend
        $candidate->avatar_url = $candidate->avatar ? asset('storage/' . $candidate->avatar) : null;
        $candidate->cv_url = $candidate->cv_file ? asset('storage/' . $candidate->cv_file) : null;
        $candidate->age = $candidate->getAge();

        return Inertia::render('candidate/Profile/Index', [
            'profile' => $candidate,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new profile.
     */
    public function create()
    {
        return Inertia::render('candidate/Profile/Create', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created profile in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_position' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'experience_level' => 'nullable|string|max:100',
            'expected_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'skills' => 'required|array|min:1',
            'skills.*.name' => 'required|string|max:255',
            'work_experiences' => 'nullable|array',
            'educations' => 'nullable|array',
        ]);

        DB::transaction(function () use ($user, $request, $validated) {
            // Update User Phone if provided
            if ($request->filled('phone')) {
                $user->update(['phone' => $request->phone]);
            }

            // Create Profile
            $profileData = collect($validated)->except(['avatar', 'skills', 'work_experiences', 'educations', 'phone'])->toArray();
            $profileData['user_id'] = $user->id;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $profileData['avatar'] = $avatarPath;
            }

            $candidate = CandidateProfile::create($profileData);

            // Handle Skills
            if ($request->has('skills')) {
                $skillIds = [];
                foreach ($request->skills as $skillData) {
                    $skill = Skill::firstOrCreate(['name' => $skillData['name']]);
                    $skillIds[] = $skill->id;
                }
                $candidate->skills()->sync($skillIds);
            }

            // Handle Work Experiences
            if ($request->has('work_experiences')) {
                foreach ($request->work_experiences as $exp) {
                    $candidate->workExperiences()->create([
                        'company_name' => $exp['company_name'],
                        'position' => $exp['position'],
                        'description' => $exp['description'] ?? null,
                        'start_date' => $exp['start_date'],
                        'end_date' => $exp['end_date'] ?? null,
                        'is_current' => $exp['is_current'] ?? false,
                    ]);
                }
            }

            // Handle Educations
            if ($request->has('educations')) {
                foreach ($request->educations as $edu) {
                    $candidate->educations()->create([
                        'institution' => $edu['institution'],
                        'degree' => $edu['degree'],
                        'field_of_study' => $edu['field_of_study'],
                        'start_date' => $edu['start_date'],
                        'end_date' => $edu['end_date'] ?? null,
                        'gpa' => $edu['gpa'] ?? null,
                        'description' => $edu['description'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('candidate.dashboard')
            ->with('success', 'Hồ sơ đã được tạo thành công!');
    }

    /**
     * Show the form for editing the candidate's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile()->with([
            'skills',
            'workExperiences',
            'educations'
        ])->first();

        if (!$candidate) {
            $candidate = CandidateProfile::create([
                'user_id' => $user->id,
            ]);
        }

        // Append attributes for frontend
        $candidate->avatar_url = $candidate->avatar ? asset('storage/' . $candidate->avatar) : null;
        $candidate->cv_url = $candidate->cv_file ? asset('storage/' . $candidate->cv_file) : null;

        return Inertia::render('candidate/Profile/Edit', [
            'profile' => $candidate,
            'user' => $user,
        ]);
    }

    /**
     * Update the candidate's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile;

        if (!$candidate) {
            $candidate = CandidateProfile::create([
                'user_id' => $user->id,
            ]);
        }

        $validated = $request->validate([
            'current_position' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'experience_level' => 'nullable|string|max:100',
            'expected_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|max:2048',
            'skills' => 'nullable|array',
            'cv_file' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($candidate->avatar) {
                Storage::disk('public')->delete($candidate->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Handle CV upload
        if ($request->hasFile('cv_file')) {
            // Delete old CV if exists
            if ($candidate->cv_file) {
                Storage::disk('public')->delete($candidate->cv_file);
            }

            $cvPath = $request->file('cv_file')->store('cvs', 'public');
            $validated['cv_file'] = $cvPath;
        }

        // Update candidate profile
        $candidate->update($validated);

        // Sync skills if provided
        if ($request->has('skills')) {
            $candidate->skills()->sync($request->skills);
        }

        return redirect()->route('candidate.profile.index')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
