<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\JobMatchingService;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostingController extends Controller
{
    /**
     * Hiển thị form tạo mới tin tuyển dụng.
     */

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id ?? 1;

        // Lọc theo trạng thái (active = 1, inactive = 0, all)
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = JobPosting::where('company_id', $companyId);

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Lọc theo trạng thái duyệt
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }
        if ($status === 'active') {
            $query->where('is_active', true)
                ->where('status', 'approved'); // PHẢI ĐƯỢC DUYỆT TRƯỚC
        } elseif ($status === 'inactive') {
            $query->where('is_active', false)
                ->where('status', 'approved'); // PHẢI ĐƯỢC DUYỆT TRƯỚC
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        return inertia('Employer/Posting/Index', [
            'jobs' => $jobs,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    public function toggleVisibility($id)
    {
        $job = JobPosting::findOrFail($id);

        // Kiểm tra quyền (chỉ cho phép sửa tin của công ty mình)
        if ($job->company_id !== Auth::user()->company_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa tin này.');
        }

        $job->is_active = !$job->is_active;
        $job->save();

        return back()->with('success', 'Cập nhật trạng thái hiển thị thành công!');
    }

    public function create()
    {
        $industries = \App\Models\Industry::all();
        return inertia('Employer/Posting/Create', [
            'industries' => $industries
        ]);
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
                'experience_level' => 'required|in:intern,fresher,junior,middle,senior,lead',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'location' => 'required|string|max:255',
                'min_salary' => 'required|numeric|min:0|max:999999999',
                'max_salary' => 'required|numeric|min:0|max:999999999|gte:min_salary',

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
                'max_salary.numeric' => 'Lương phải là số.',
                'min_salary.numeric' => 'Lương phải là số.',
                'min_salary.max' => 'Lương tối thiểu quá lớn, vui lòng nhập số hợp lệ (tối đa 9 số).',
                'max_salary.max' => 'Lương tối đa quá lớn, vui lòng nhập số hợp lệ (tối đa 9 số).',
                'max_salary.gte' => 'Lương tối đa phải lớn hơn hoặc bằng lương tối thiểu.',

            ]
        );

        // Lưu tin mới
        $job = JobPosting::create([
            ...$validated,
            'slug' => Str::slug($validated['title']),
            'company_id' => Auth::user()->company_id ?? 1, // giả sử employer thuộc 1 công ty
        ]);

        // Only send notifications if the job is approved
        if ($job->status === 'approved') {
            try {
                $matchingService = new JobMatchingService();
                $matches = $matchingService->findMatchingCandidates($job);
                $notificationCount = $matchingService->sendJobMatchNotifications($job, $matches);

                \Log::info("Job matching completed for job #{$job->id}: {$notificationCount} notifications sent");
            } catch (\Exception $e) {
                \Log::error("Job matching failed for job #{$job->id}: " . $e->getMessage());
            }
        }

        return redirect()->route('employer.postings.show', $job->id)
            ->with('success', 'Tin tuyển dụng đã được tạo thành công và chờ phê duyệt.');
    }

    /**
     * Hiển thị chi tiết tin tuyển dụng.
     */
    public function show($id)
{
    $job = JobPosting::with([
        'company:id,company_name,logo,address,city,province,industry,size,is_verified',
        'industry:id,name',
    ])->findOrFail($id);

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
        $industries = \App\Models\Industry::all();
        return inertia('Employer/Posting/Edit', [
            'job' => $job,
            'industries' => $industries
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
                'min_salary' => 'required|numeric|min:0|max:999999999',
                'max_salary' => 'required|numeric|min:0|max:999999999|gte:min_salary',

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
                'max_salary.numeric' => 'Lương phải là số.',
                'min_salary.numeric' => 'Lương phải là số.',
                'min_salary.max' => 'Lương tối thiểu quá lớn, vui lòng nhập số hợp lệ( tối đa 9 số).',
                'max_salary.max' => 'Lương tối đa quá lớn, vui lòng nhập số hợp lệ (tối đa 9 số).',
                'max_salary.gte' => 'Lương tối đa phải lớn hơn hoặc bằng lương tối thiểu.',

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
