<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CandidateProfile;
use App\Models\Skill;
use App\Models\City; // giả sử bạn có model City
use Inertia\Inertia;

class CandidateSearchController extends Controller
{
    public function index(Request $request)
    {
        // ----- FILTERS -----
        $keyword = $request->input('keyword');
        $skills = $request->input('skills'); // ví dụ: "1,2,5"
        $skillExperience = $request->input('skill_experience'); // ví dụ: "1:3,2:5"
        $experienceLevel = $request->input('experience_level');
        $city = $request->input('city');
        $province = $request->input('province');

        $query = CandidateProfile::with(['user', 'skills'])
            ->when($keyword, fn($q) => $q->where(function ($q2) use ($keyword) {
                $q2->whereHas('user', fn($u) => $u->where('name', 'like', "%$keyword%"))
                    ->orWhere('current_position', 'like', "%$keyword%")
                    ->orWhere('current_company', 'like', "%$keyword%");
            }))
            ->when($experienceLevel, fn($q) => $q->where('experience_level', $experienceLevel))
            ->when($city, fn($q) => $q->where('city', $city))
            ->when($province, fn($q) => $q->where('province', $province));

        // ----- Filter theo skills + năm kinh nghiệm -----
        if ($skills) {
            $skillsArray = explode(',', $skills); // [1,2,3]
            $skillExpArr = [];
            if ($skillExperience) {
                foreach (explode(',', $skillExperience) as $item) {
                    [$id, $yrs] = explode(':', $item);
                    $skillExpArr[$id] = (int)$yrs;
                }
            }

            $query->whereHas('skills', function ($q) use ($skillsArray, $skillExpArr) {
                foreach ($skillsArray as $skillId) {
                    $yrs = $skillExpArr[$skillId] ?? 0;
                    $q->where(function ($q2) use ($skillId, $yrs) {
                        $q2->where('skills.id', $skillId)
                            ->wherePivot('years_experience', '>=', $yrs);
                    });
                }
            });
        }

        // ----- PAGINATION -----
        $candidates = $query->paginate(10)->withQueryString();

        // ----- ALL SKILLS + CITIES (dùng cho dropdown) -----
        $allSkills = Skill::active()->get();
        $allCities = [
            ['id' => 1, 'name' => 'Hà Nội', 'province' => 'Hà Nội'],
            ['id' => 2, 'name' => 'TP. Hồ Chí Minh', 'province' => 'TP.HCM'],
            ['id' => 3, 'name' => 'Đà Nẵng', 'province' => 'Đà Nẵng'],
            ['id' => 4, 'name' => 'Cần Thơ', 'province' => 'Cần Thơ'],
            // ...thêm các thành phố khác nếu muốn
        ];

        return Inertia::render('Employer/Search/CandidateSearch', [
            'candidates' => $candidates,
            'filters' => $request->only(['keyword', 'skills', 'skill_experience', 'experience_level', 'city', 'province']),
            'allSkills' => $allSkills,
            'allCities' => $allCities,
        ]);
    }
}
