<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReportRequest;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display a listing of all reports.
     * 
     * Test Case 10: Validate URL parameters
     */
    public function index(Request $request)
    {
        // Validate page parameter
        $page = $request->get('page', 1);

        // Check if page is valid
        if (!is_numeric($page) || $page < 1) {
            return redirect()->route('admin.reports.index', ['page' => 1])
                ->with('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
        }

        // Validate status filter parameter
        $validStatuses = ['all', 'pending', 'reviewing', 'resolved', 'dismissed'];
        if ($request->has('status') && !in_array($request->status, $validStatuses)) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Danh mục không tồn tại.');
        }

        $query = Report::with(['user', 'reportable', 'reviewer'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by reportable type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('reportable_type', $request->type);
        }

        // Filter by reason
        if ($request->has('reason') && $request->reason !== 'all') {
            $query->where('reason', $request->reason);
        }

        // Search by reason, user name, or reportable content
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Tìm kiếm trong lý do báo cáo
                $q->where('reason', 'like', "%{$search}%")
                    // Tìm kiếm theo tên người báo cáo
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    // Tìm kiếm trong CandidateProfile
                    ->orWhere(function ($q3) use ($search) {
                        $q3->where('reportable_type', 'App\Models\CandidateProfile')
                            ->whereHasMorph('reportable', ['App\Models\CandidateProfile'], function ($q4) use ($search) {
                                $q4->where('current_position', 'like', "%{$search}%")
                                    ->orWhere('current_company', 'like', "%{$search}%");
                            });
                    })
                    // Tìm kiếm trong JobPosting
                    ->orWhere(function ($q5) use ($search) {
                        $q5->where('reportable_type', 'App\Models\JobPosting')
                            ->whereHasMorph('reportable', ['App\Models\JobPosting'], function ($q6) use ($search) {
                                $q6->where('title', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $reports = $query->paginate(20);

        // Test Case 10: Check if requested page exists
        if ($page > $reports->lastPage() && $reports->total() > 0) {
            return redirect()->route('admin.reports.index', array_merge(
                $request->except('page'),
                ['page' => 1]
            ))->with('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
        }

        $reports = $reports->through(function ($report) {
            return [
                'id' => $report->id,
                'user' => [
                    'id' => $report->user->id,
                    'name' => $report->user->name,
                    'email' => $report->user->email,
                ],
                'reason' => $report->reason,
                'reason_label' => $report->getReasonLabel(),
                'description' => $report->description,
                'status' => $report->status,
                'status_label' => $report->getStatusLabel(),
                'reportable_type' => $report->reportable_type,
                'reportable_type_label' => $report->getReportableTypeLabel(),
                'reportable' => $report->reportable ? [
                    'id' => $report->reportable->id,
                    'title' => $report->reportable_type === 'App\Models\JobPosting'
                        ? $report->reportable->title
                        : $report->reportable->company_name,
                    'slug' => $report->reportable_type === 'App\Models\JobPosting'
                        ? $report->reportable->slug
                        : $report->reportable->company_slug,
                ] : null,
                'admin_notes' => $report->admin_notes,
                'reviewer' => $report->reviewer ? [
                    'id' => $report->reviewer->id,
                    'name' => $report->reviewer->name,
                ] : null,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                'reviewed_at' => $report->reviewed_at?->format('Y-m-d H:i:s'),
                'updated_at' => $report->updated_at->format('Y-m-d H:i:s'), // For optimistic locking
            ];
        });

        $stats = [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'reviewing' => Report::reviewing()->count(),
            'resolved' => Report::resolved()->count(),
            'dismissed' => Report::dismissed()->count(),
        ];

        return Inertia::render('admin/reports/Index', [
            'reports' => $reports,
            'stats' => $stats,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'type' => $request->get('type', 'all'),
                'reason' => $request->get('reason', 'all'),
                'search' => $request->get('search', ''),
            ],
        ]);
    }

    /**
     * Display the specified report.
     * 
     * Test Case 3: Handle invalid IDs (both non-numeric and non-existent)
     */
    public function show($id)
    {
        // Check if ID is numeric
        if (!is_numeric($id)) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa.');
        }

        try {
            $report = Report::with(['user', 'reportable', 'reviewer'])->findOrFail($id);

            return Inertia::render('admin/reports/Show', [
                'report' => [
                    'id' => $report->id,
                    'user' => [
                        'id' => $report->user->id,
                        'name' => $report->user->name,
                        'email' => $report->user->email,
                    ],
                    'reason' => $report->reason,
                    'reason_label' => $report->getReasonLabel(),
                    'description' => $report->description,
                    'status' => $report->status,
                    'status_label' => $report->getStatusLabel(),
                    'reportable_type' => $report->reportable_type,
                    'reportable_type_label' => $report->getReportableTypeLabel(),
                    'reportable' => $report->reportable ? [
                        'id' => $report->reportable->id,
                        'title' => $report->reportable_type === 'App\Models\JobPosting'
                            ? $report->reportable->title
                            : $report->reportable->company_name,
                        'slug' => $report->reportable_type === 'App\Models\JobPosting'
                            ? $report->reportable->slug
                            : $report->reportable->company_slug,
                        'status' => $report->reportable_type === 'App\Models\JobPosting'
                            ? $report->reportable->status
                            : null,
                    ] : null,
                    'admin_notes' => $report->admin_notes,
                    'reviewer' => $report->reviewer ? [
                        'id' => $report->reviewer->id,
                        'name' => $report->reviewer->name,
                    ] : null,
                    'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    'reviewed_at' => $report->reviewed_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $report->updated_at->format('Y-m-d H:i:s'), // For optimistic locking
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa.');
        }
    }

    /**
     * Update the specified report.
     * 
     * Test Cases:
     * - #2: Optimistic locking (concurrent updates)
     * - #4: Form validation
     * - #5: Max length validation
     * - #6: Whitespace-only validation
     * - #8: Select option validation
     */
    public function update(UpdateReportRequest $request, $id)
    {
        try {
            $report = Report::findOrFail($id);

            // Test Case 2: Optimistic Locking - Check if data was updated by another user
            if ($request->has('updated_at')) {
                $requestUpdatedAt = $request->input('updated_at');
                $dbUpdatedAt = $report->updated_at->format('Y-m-d H:i:s');

                if ($requestUpdatedAt !== $dbUpdatedAt) {
                    return back()->withErrors([
                        'concurrent_update' => 'Dữ liệu đã được cập nhật bởi người dùng khác. Vui lòng tải lại trang trước khi cập nhật.'
                    ])->withInput();
                }
            }

            $validated = $request->validated();

            $report->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            return back()->with('success', 'Cập nhật báo cáo thành công!');

        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Không tìm thấy báo cáo này. Có thể đã bị xóa bởi người dùng khác.');
        } catch (\Exception $e) {
            Log::error('Error updating report: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra khi cập nhật báo cáo. Vui lòng thử lại.'
            ])->withInput();
        }
    }

    /**
     * Remove the specified report.
     * 
     * Test Case 1: Handle deletion of non-existent reports
     */
    public function destroy($id)
    {
        try {
            $report = Report::findOrFail($id);
            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa báo cáo thành công!'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy báo cáo này. Có thể đã bị xóa bởi người dùng khác.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa báo cáo. Vui lòng thử lại.'
            ], 500);
        }
    }
}
