<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\JobMatchingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobPostingController extends Controller
{
    private JobMatchingService $jobMatchingService;

    public function __construct(JobMatchingService $jobMatchingService)
    {
        $this->jobMatchingService = $jobMatchingService;
    }

    public function index(Request $request)
    {
        $query = JobPosting::with(['company']);

        // Search by title or company name
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('company', function ($q) use ($request) {
                      $q->where('company_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('admin/job-postings/Index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function approve(JobPosting $jobPosting)
    {
        $jobPosting->update([
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
        ]);

        // Tìm ứng viên phù hợp và gửi thông báo
        $jobPosting->load(['company', 'skills']);
        $matches = $this->jobMatchingService->findMatchingCandidates($jobPosting, 50);
        $notificationCount = $this->jobMatchingService->sendJobMatchNotifications($jobPosting, $matches);

        return back()->with('success', "Tin tuyển dụng đã được duyệt thành công. Đã gửi thông báo cho {$notificationCount} ứng viên phù hợp.");
    }

    public function reject(Request $request, JobPosting $jobPosting)
    {
        $jobPosting->update([
            'status' => 'rejected',
            'is_active' => false,
        ]);

        return back()->with('success', 'Tin tuyển dụng đã bị từ chối.');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();

        return back()->with('success', 'Tin tuyển dụng đã được xóa.');
    }
}
