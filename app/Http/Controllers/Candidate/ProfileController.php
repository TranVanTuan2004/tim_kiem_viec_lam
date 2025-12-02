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
        $candidate->avatar_url = storage_url($candidate->avatar);
        $candidate->cv_url = storage_url($candidate->cv_file);
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
            'current_position' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'current_company' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'summary' => 'nullable|string',
            'experience_level' => 'nullable|string|max:100',
            'expected_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:100|regex:/^[\p{L}\s\-]+$/u',
            'province' => 'required|string|max:100|regex:/^[\p{L}\s\-]+$/u',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'required|regex:/^[0-9]{1,15}$/|max:15',
            'avatar' => 'nullable|image|max:2048',
            'cv_file' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'skills' => 'required|array|min:1',
            'skills.*.name' => 'required|string|max:255',
            'work_experiences' => 'nullable|array',
            'work_experiences.*.company_name' => 'required_with:work_experiences|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'work_experiences.*.position' => 'required_with:work_experiences|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'work_experiences.*.description' => 'nullable|string',
            'work_experiences.*.start_date' => 'required_with:work_experiences|date',
            'work_experiences.*.end_date' => 'nullable|date|after:work_experiences.*.start_date',
            'educations' => 'nullable|array',
            'educations.*.institution' => 'required_with:educations|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'educations.*.degree' => 'required_with:educations|string|max:255|regex:/^[\p{L}\p{N}\s\-\.]+$/u',
            'educations.*.field_of_study' => 'required_with:educations|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'educations.*.start_date' => 'required_with:educations|date',
            'educations.*.end_date' => 'nullable|date|after:educations.*.start_date',
            'educations.*.gpa' => 'nullable|numeric|min:0|max:4',
        ], [
            'phone.regex' => 'Số điện thoại chỉ được chứa số và tối đa 15 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'city.regex' => 'Tên thành phố chỉ được chứa chữ cái, khoảng trắng và dấu gạch ngang.',
            'province.regex' => 'Tên tỉnh chỉ được chứa chữ cái, khoảng trắng và dấu gạch ngang.',
            'current_position.regex' => 'Vị trí hiện tại chỉ được chứa chữ cái, số và các ký tự: - / ( )',
            'current_company.regex' => 'Tên công ty chỉ được chứa chữ cái, số và các ký tự: - . &',
            'birth_date.before' => 'Ngày sinh phải trước ngày hôm nay.',
            'work_experiences.*.company_name.regex' => 'Tên công ty chỉ được chứa chữ cái, số và các ký tự: - . &',
            'work_experiences.*.position.regex' => 'Vị trí công việc chỉ được chứa chữ cái, số và các ký tự: - / ( )',
            'educations.*.institution.regex' => 'Tên trường học chỉ được chứa chữ cái, số và các ký tự: - . &',
            'educations.*.degree.regex' => 'Bằng cấp chỉ được chứa chữ cái, số và các ký tự: - .',
            'educations.*.field_of_study.regex' => 'Chuyên ngành chỉ được chứa chữ cái, số và các ký tự: - / ( )',
        ]);

        DB::transaction(function () use ($user, $request, $validated) {
            // Update User Phone if provided
            if ($request->filled('phone')) {
                $user->update(['phone' => $request->phone]);
            }

            // Create Profile
            $profileData = collect($validated)->except(['avatar', 'cv_file', 'skills', 'work_experiences', 'educations', 'phone'])->toArray();
            $profileData['user_id'] = $user->id;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $profileData['avatar'] = $avatarPath;
            }

            // Handle CV file upload
            if ($request->hasFile('cv_file')) {
                $cvPath = $request->file('cv_file')->store('cvs', 'public');
                $profileData['cv_file'] = $cvPath;
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
        $candidate->avatar_url = storage_url($candidate->avatar);
        $candidate->cv_url = storage_url($candidate->cv_file);

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
            'current_position' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'current_company' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'summary' => 'nullable|string',
            'experience_level' => 'nullable|string|max:100',
            'expected_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100|regex:/^[\p{L}\s\-]+$/u',
            'province' => 'nullable|string|max:100|regex:/^[\p{L}\s\-]+$/u',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'nullable|regex:/^[0-9]{1,15}$/|max:15',
            'avatar' => 'nullable|image|max:2048',
            'skills' => 'nullable|array',
            'skills.*.name' => 'nullable|string|max:255',
            'cv_file' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'work_experiences' => 'nullable|array',
            'work_experiences.*.company_name' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'work_experiences.*.position' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'work_experiences.*.description' => 'nullable|string',
            'work_experiences.*.start_date' => 'nullable|date',
            'work_experiences.*.end_date' => 'nullable|date|after:work_experiences.*.start_date',
            'educations' => 'nullable|array',
            'educations.*.institution' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\.\&]+$/u',
            'educations.*.degree' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\.]+$/u',
            'educations.*.field_of_study' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\s\-\/\(\)]+$/u',
            'educations.*.start_date' => 'nullable|date',
            'educations.*.end_date' => 'nullable|date|after:educations.*.start_date',
            'educations.*.gpa' => 'nullable|numeric|min:0|max:4',
        ], [
            'phone.regex' => 'Số điện thoại chỉ được chứa số và tối đa 15 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'city.regex' => 'Tên thành phố chỉ được chứa chữ cái, khoảng trắng và dấu gạch ngang.',
            'province.regex' => 'Tên tỉnh chỉ được chứa chữ cái, khoảng trắng và dấu gạch ngang.',
            'current_position.regex' => 'Vị trí hiện tại chỉ được chứa chữ cái, số và các ký tự: - / ( )',
            'current_company.regex' => 'Tên công ty chỉ được chứa chữ cái, số và các ký tự: - . &',
            'birth_date.before' => 'Ngày sinh phải trước ngày hôm nay.',
            'work_experiences.*.company_name.regex' => 'Tên công ty chỉ được chứa chữ cái, số và các ký tự: - . &',
            'work_experiences.*.position.regex' => 'Vị trí công việc chỉ được chứa chữ cái, số và các ký tự: - / ( )',
            'educations.*.institution.regex' => 'Tên trường học chỉ được chứa chữ cái, số và các ký tự: - . &',
            'educations.*.degree.regex' => 'Bằng cấp chỉ được chứa chữ cái, số và các ký tự: - .',
            'educations.*.field_of_study.regex' => 'Chuyên ngành chỉ được chứa chữ cái, số và các ký tự: - / ( )',
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

        // Handle Skills - convert names to IDs
        if ($request->has('skills') && is_array($request->skills)) {
            $skillIds = [];
            foreach ($request->skills as $skillData) {
                // If skillData is just a string (skill name)
                if (is_string($skillData)) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillData],
                        ['slug' => \Illuminate\Support\Str::slug($skillData)]
                    );
                    $skillIds[] = $skill->id;
                }
                // If skillData is an array with 'name' key
                elseif (is_array($skillData) && isset($skillData['name'])) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillData['name']],
                        ['slug' => \Illuminate\Support\Str::slug($skillData['name'])]
                    );
                    $skillIds[] = $skill->id;
                }
                // If skillData already has skill_id
                elseif (is_array($skillData) && isset($skillData['skill_id'])) {
                    $skillIds[] = $skillData['skill_id'];
                }
            }
            
            // Sync only the skill IDs (not the names)
            $candidate->skills()->sync($skillIds);
        }

        // Handle Work Experiences
        if ($request->has('work_experiences') && is_array($request->work_experiences)) {
            // Delete existing work experiences
            $candidate->workExperiences()->delete();
            
            // Create new work experiences
            foreach ($request->work_experiences as $exp) {
                if (isset($exp['company_name']) && isset($exp['position'])) {
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
        }

        // Handle Educations
        if ($request->has('educations') && is_array($request->educations)) {
            // Delete existing educations
            $candidate->educations()->delete();
            
            // Create new educations
            foreach ($request->educations as $edu) {
                if (isset($edu['institution']) && isset($edu['degree'])) {
                    $candidate->educations()->create([
                        'institution' => $edu['institution'],
                        'degree' => $edu['degree'],
                        'field_of_study' => $edu['field_of_study'] ?? null,
                        'start_date' => $edu['start_date'],
                        'end_date' => $edu['end_date'] ?? null,
                        'gpa' => $edu['gpa'] ?? null,
                        'description' => $edu['description'] ?? null,
                    ]);
                }
            }
        }

        // Update user phone if provided
        if ($request->filled('phone')) {
            $user->update(['phone' => $request->phone]);
        }

        return redirect()->route('candidate.profile.index')
            ->with('success', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Toggle candidate availability status.
     */
    public function toggleAvailability()
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile;

        if (!$candidate) {
            return back()->with('error', 'Hồ sơ không tồn tại!');
        }

        // Toggle the is_available status
        $candidate->update([
            'is_available' => !$candidate->is_available
        ]);

        return back()->with('success', $candidate->is_available 
            ? 'Đã bật trạng thái tìm việc!' 
            : 'Đã tắt trạng thái tìm việc!');
    }
}
