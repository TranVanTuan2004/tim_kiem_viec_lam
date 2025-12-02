<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Hiển thị danh sách hồ sơ ứng tuyển của công ty
     * Hỗ trợ lọc theo trạng thái, công việc, tìm kiếm, ngày tháng
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Lấy công ty của nhà tuyển dụng
        $company = $user->company;
        
        // Nếu chưa có công ty, trả về trang trống
        if (!$company) {
            return inertia('Employer/Applications/Index', [
                'applications' => [],
                'statistics' => $this->getEmptyStatistics(),
                'jobPostings' => [],
                'filters' => $request->only(['status', 'job_posting_id', 'search', 'date_from', 'date_to']),
            ]);
        }

        // Tạo query lấy hồ sơ của công ty, kèm thông tin ứng viên và công việc
        $query = Application::whereHas('jobPosting', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })
        ->with(['candidate.user', 'jobPosting']);

        // Áp dụng các bộ lọc
        // Lọc theo trạng thái (pending, reviewing, interview, accepted, rejected)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo công việc cụ thể
        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        // Tìm kiếm theo tên hoặc email ứng viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('candidate.user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo khoảng thời gian ứng tuyển
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sắp xếp (mặc định: mới nhất trước)
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang 15 hồ sơ/trang
        $applications = $query->paginate(15)->withQueryString();

        // Lấy thống kê số lượng theo trạng thái
        $statistics = $this->getStatistics($company->id);

        // Lấy danh sách công việc để lọc
        $jobPostings = JobPosting::where('company_id', $company->id)
        ->select('id', 'title')
        ->orderBy('title')
        ->get();

        return inertia('Employer/Applications/Index', [
            'applications' => $applications,
            'statistics' => $statistics,
            'jobPostings' => $jobPostings,
            'filters' => $request->only(['status', 'job_posting_id', 'search', 'date_from', 'date_to', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Hiển thị chi tiết hồ sơ ứng viên
     * Bao gồm thông tin cá nhân, kỹ năng, kinh nghiệm, học vấn
     */
    public function show($id)
    {
        // Lấy hồ sơ với đầy đủ thông tin ứng viên
        $application = Application::with([
            'candidate.user',
            'candidate.skills',
            'candidate.workExperiences',
            'candidate.educations',
            'jobPosting'
        ])
        ->whereHas('jobPosting', function ($q) {
            $q->where('company_id', auth()->user()->company->id);
        })
        ->findOrFail($id);

        return inertia('Employer/Applications/Show', [
            'application' => tap($application, function($app) {
                $app->cv_file = storage_url($app->cv_file);
            }),
        ]);
    }

    /**
     * Cập nhật trạng thái hồ sơ và ghi chú
     * Gửi thông báo cho ứng viên khi trạng thái thay đổi
     */
    public function updateStatus(Request $request, $id)
    {
        // Validate dữ liệu (status không bắt buộc để cho phép chỉ lưu ghi chú)
        $request->validate([
            'status' => 'nullable|in:pending,reviewing,interview,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
            'interview_date' => 'nullable|date|after:now',
        ]);

        // Kiểm tra quyền truy cập (chỉ công ty sở hữu công việc mới được cập nhật)
        $application = Application::whereHas('jobPosting', function ($q) {
            $q->where('company_id', auth()->user()->company->id);
        })->findOrFail($id);

        $oldStatus = $application->status;
        
        // Chuẩn bị dữ liệu cập nhật (chỉ cập nhật các trường được gửi lên)
        $updateData = [];
        
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        
        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }
        
        if ($request->filled('interview_date')) {
            $updateData['interview_date'] = $request->interview_date;
        }
        
        $application->update($updateData);

        // Gửi thông báo cho ứng viên nếu trạng thái thay đổi
        if ($request->filled('status') && $oldStatus !== $request->status) {
            $this->sendStatusChangeNotification($application, $request->status);
        }

        // Thông báo thành công (khác nhau tùy vào cập nhật status hay chỉ notes)
        $message = $request->filled('status') ? 'Cập nhật trạng thái thành công!' : 'Ghi chú đã được lưu thành công!';
        return back()->with('success', $message);
    }

    /**
     * Gửi thông báo cho ứng viên khi trạng thái hồ sơ thay đổi
     * Các loại: accepted (chấp nhận), rejected (từ chối), interview (phỏng vấn), reviewing (xem xét)
     */
    private function sendStatusChangeNotification($application, $newStatus)
    {
        $candidate = $application->candidate;
        $jobTitle = $application->jobPosting->title;
        $companyName = $application->jobPosting->company->name ?? $application->jobPosting->company->company_name;

        // Dữ liệu đính kèm thông báo để ứng viên có thể click xem chi tiết
        $notificationData = [
            'application_id' => $application->id,
            'job_posting_id' => $application->job_posting_id,
            'job_title' => $jobTitle,
            'company_name' => $companyName,
            'status' => $newStatus,
        ];

        // Tạo thông báo tương ứng với từng trạng thái
        switch ($newStatus) {
            case 'accepted': // Hồ sơ được chấp nhận
                \App\Models\Notification::create([
                    'user_id' => $candidate->user_id,
                    'type' => 'application_accepted',
                    'title' => 'Chúc mừng! Hồ sơ của bạn đã được chấp nhận',
                    'message' => "Hồ sơ ứng tuyển vị trí \"{$jobTitle}\" tại {$companyName} đã được chấp nhận. Nhà tuyển dụng sẽ liên hệ với bạn sớm.",
                    'data' => $notificationData,
                ]);
                break;

            case 'rejected': // Hồ sơ bị từ chối
                \App\Models\Notification::create([
                    'user_id' => $candidate->user_id,
                    'type' => 'application_rejected',
                    'title' => 'Cập nhật trạng thái hồ sơ',
                    'message' => "Rất tiếc, hồ sơ ứng tuyển vị trí \"{$jobTitle}\" tại {$companyName} chưa phù hợp lúc này. Đừng nản lòng, hãy tiếp tục tìm kiếm cơ hội khác!",
                    'data' => $notificationData,
                ]);
                break;

            case 'interview': // Mời phỏng vấn
                \App\Models\Notification::create([
                    'user_id' => $candidate->user_id,
                    'type' => 'interview_invite',
                    'title' => 'Mời phỏng vấn',
                    'message' => "Bạn đã được mời phỏng vấn cho vị trí \"{$jobTitle}\" tại {$companyName}. Vui lòng kiểm tra email để biết thêm chi tiết.",
                    'data' => $notificationData,
                ]);
                break;

            case 'reviewing': // Đang xem xét
                \App\Models\Notification::create([
                    'user_id' => $candidate->user_id,
                    'type' => 'application_update',
                    'title' => 'Hồ sơ đang được xem xét',
                    'message' => "Hồ sơ ứng tuyển vị trí \"{$jobTitle}\" tại {$companyName} đang được xem xét. Chúng tôi sẽ thông báo kết quả sớm nhất.",
                    'data' => $notificationData,
                ]);
                break;
        }
    }

    /**
     * Lấy thống kê số lượng hồ sơ theo từng trạng thái
     */
    private function getStatistics($companyId)
    {
        // Đếm số lượng hồ sơ theo từng trạng thái
        $stats = Application::whereHas('jobPosting', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

        return [
            'total' => array_sum($stats),
            'pending' => $stats['pending'] ?? 0,
            'reviewing' => $stats['reviewing'] ?? 0,
            'interview' => $stats['interview'] ?? 0,
            'accepted' => $stats['accepted'] ?? 0,
            'rejected' => $stats['rejected'] ?? 0,
        ];
    }

    /**
     * Trả về thống kê rỗng (dùng khi công ty chưa có hồ sơ nào)
     */
    private function getEmptyStatistics()
    {
        return [
            'total' => 0,
            'pending' => 0,
            'reviewing' => 0,
            'interview' => 0,
            'accepted' => 0,
            'rejected' => 0,
        ];
    }
}
