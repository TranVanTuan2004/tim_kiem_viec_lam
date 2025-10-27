<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostingController extends Controller
{
    /**
     * Hiển thị form tạo mới tin tuyển dụng.
     */

    public function index()
    {
        $companyId = Auth::user()->company_id ?? 1;

        $jobs = \App\Models\JobPosting::where('company_id', $companyId)
            ->latest()
            ->paginate(10);

        return inertia('Employer/Posting/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function create()
    {
        return inertia('Employer/Posting/Create');
    }

    /**
     * Lưu tin tuyển dụng mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'benefits' => 'required|string',
                'industry_id' => 'required',
                'employment_type' => 'required|string',
                'experience_level' => 'required|string',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'location' => 'required|string|max:255',
                'min_salary' => 'required|numeric|min:0',
                'max_salary' => 'required|numeric|min:0',
            ],
            [
                'title.required' => 'Vui lòng nhập tiêu đề cho tin tuyển dụng.',
                'title.max' => 'Tiêu đề không được dài quá 255 ký tự.',
                'description.required' => 'Hãy nhập mô tả chi tiết công việc.',
                'requirements.required' => 'Hãy nêu các yêu cầu ứng viên.',
                'benefits.required' => 'Hãy nêu quyền lợi mà ứng viên sẽ nhận được.',
                'industry_id.required' => 'Chọn ngành nghề phù hợp.',
                'employment_type.required' => 'Chọn hình thức làm việc.',
                'experience_level.required' => 'Chọn cấp bậc kinh nghiệm.',
                'city.required' => 'Vui lòng chọn quận, huyện.',
                'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
                'location.required' => 'Nhập địa điểm cụ thể của công việc.',
                'min_salary.required' => 'Nhập mức lương tối thiểu.',
                'max_salary.required' => 'Nhập mức lương tối đa.',
                'max_salary.gte' => 'Mức lương tối đa phải lớn hơn hoặc bằng mức lương tối thiểu.',
            ]
        );

        // Lưu tin mới
        $job = JobPosting::create([
            ...$validated,
            'slug' => Str::slug($validated['title']),
            'company_id' => Auth::user()->company_id ?? 1, // giả sử employer thuộc 1 công ty
        ]);

        return redirect()->route('employer.postings.show', $job->id)
            ->with('success', 'Tin tuyển dụng đã được tạo thành công và chờ phê duyệt.');
    }

    /**
     * Hiển thị chi tiết tin tuyển dụng.
     */
    public function show($id)
    {
        $job = JobPosting::findOrFail($id);

        return inertia('Employer/Posting/Show', [
            'job' => $job,
        ]);
    }

    public function destroy($id)
{
    $job = JobPosting::findOrFail($id);
    $job->delete();
    return redirect()->route('employer.postings.index')
        ->with('success', 'Tin tuyển dụng đã được xóa.');
}
public function edit($id)
{
    $job = JobPosting::findOrFail($id);
    return inertia('Employer/Posting/Edit', [
        'job' => $job,
    ]);
}

public function update(Request $request, $id)
{
    $job = JobPosting::findOrFail($id);

    $validated = $request->validate(
        [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'required|string',
            'industry_id' => 'required',
            'employment_type' => 'required|string',
            'experience_level' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gte:min_salary',
        ],
        [
            'title.required' => 'Vui lòng nhập tiêu đề cho tin tuyển dụng.',
            'title.max' => 'Tiêu đề không được dài quá 255 ký tự.',
            'description.required' => 'Hãy nhập mô tả chi tiết công việc.',
            'requirements.required' => 'Hãy nêu các yêu cầu ứng viên.',
            'benefits.required' => 'Hãy nêu quyền lợi mà ứng viên sẽ nhận được.',
            'industry_id.required' => 'Chọn ngành nghề phù hợp.',
            'employment_type.required' => 'Chọn hình thức làm việc.',
            'experience_level.required' => 'Chọn cấp bậc kinh nghiệm.',
            'city.required' => 'Vui lòng chọn quận, huyện.',
            'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'location.required' => 'Nhập địa điểm cụ thể của công việc.',
            'min_salary.required' => 'Nhập mức lương tối thiểu.',
            'max_salary.required' => 'Nhập mức lương tối đa.',
            'max_salary.gte' => 'Mức lương tối đa phải lớn hơn hoặc bằng mức lương tối thiểu.',
        ]
    );

    $job->update([
        ...$validated,
        'slug' => Str::slug($validated['title']),
    ]);

    return redirect()->route('employer.postings.index')
        ->with('success', 'Tin tuyển dụng đã được cập nhật thành công.');
}

// Ẩn/Hiện tin
public function toggle($id)
{
    $job = JobPosting::findOrFail($id);
    $job->is_active = !$job->is_active;
    $job->save();

    return redirect()->back()->with('success', 'Tin tuyển dụng đã được cập nhật.');
}
    
}
