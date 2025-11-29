<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\JobPosting;
use App\Models\Company;
use App\Events\NewReportCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display a listing of the user's reports.
     */
    public function index(Request $request)
    {
        // Validate page parameter
        if ($request->has('page')) {
            $page = $request->input('page');
            if (!is_numeric($page) || $page < 1) {
                return redirect()->route('candidate.reports.index', ['page' => 1])
                    ->with('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
            }
        }

        // Validate status filter parameter
        $validStatuses = ['all', 'pending', 'reviewing', 'resolved', 'dismissed'];
        if ($request->has('status') && !in_array($request->status, $validStatuses)) {
            return redirect()->route('candidate.reports.index')
                ->with('error', 'Danh mục không tồn tại.');
        }

        $user = Auth::user();

        $query = Report::where('reporter_id', $user->id)
            ->with(['reportable'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by reason or reportable title
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHasMorph('reportable', ['App\Models\JobPosting', 'App\Models\Company'], function ($q, $type) use ($search) {
                        if ($type === 'App\Models\JobPosting') {
                            $q->where('title', 'like', "%{$search}%");
                        } else {
                            $q->where('company_name', 'like', "%{$search}%");
                        }
                    });
            });
        }

        $reports = $query->paginate(15)->through(function ($report) {
            return [
                'id' => $report->id,
                'type' => $report->type,
                'type_label' => $report->getTypeLabel(),
                'reason' => $report->reason,
                'status' => $report->status,
                'status_label' => $report->getStatusLabel(),
                'reportable_type' => $report->reportable_type,
                'reportable_type_label' => $report->getReportableTypeLabel(),
                'reportable' => $report->reportable ? [
                    'id' => $report->reportable->id,
                    'title' => $report->reportable_type === 'App\Models\JobPosting'
                        ? $report->reportable->title
                        : $report->reportable->company_name,
                ] : null,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('candidate/Reports/Index', [
            'reports' => $reports,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'search' => $request->get('search', ''),
            ],
        ]);
    }

    public function show($id)
    {
        // Check if ID is numeric
        if (!is_numeric($id)) {
            return redirect()->route('candidate.reports.index')
                ->with('error', 'Không tìm thấy trang.');
        }

        try {
            $report = Report::findOrFail($id);

            // Check authorization
            if ($report->reporter_id !== Auth::id()) {
                abort(403);
            }

            return Inertia::render('candidate/Reports/Show', [
                'report' => [
                    'id' => $report->id,
                    'type_label' => $report->getTypeLabel(),
                    'reason' => $report->reason,
                    'status' => $report->status,
                    'status_label' => $report->getStatusLabel(),
                    'reportable_type_label' => $report->getReportableTypeLabel(),
                    'reportable' => $report->reportable ? [
                        'id' => $report->reportable->id,
                        'title' =>
                            $report->reportable_type === 'App\\Models\\JobPosting'
                            ? $report->reportable->title
                            : $report->reportable->company_name,
                    ] : null,
                    'admin_notes' => $report->admin_notes,
                    'created_at' => $report->created_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('candidate.reports.index')
                ->with('error', 'Không tìm thấy trang.');
        }
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => 'required|in:App\Models\JobPosting,App\Models\Company',
            'reportable_id' => 'required|integer',
            'type' => 'required|in:spam,fraud,inappropriate,fake,other',
            'reason' => ['required', 'string', 'min:10', 'max:1000', new \App\Rules\NoWhitespaceOnly],
        ], [
            'type.required' => 'Vui lòng chọn loại vi phạm.',
            'type.in' => 'Danh mục không tồn tại.',
            'reportable_type.required' => 'Vui lòng chọn đối tượng báo cáo.',
            'reportable_type.in' => 'Danh mục không tồn tại.',
            'reportable_id.required' => 'Vui lòng chọn đối tượng báo cáo.',
            'reportable_id.integer' => 'ID đối tượng báo cáo không hợp lệ.',
            'reason.required' => 'Vui lòng nhập lý do báo cáo.',
            'reason.min' => 'Lý do báo cáo phải có ít nhất 10 ký tự.',
            'reason.max' => 'Lý do báo cáo không được vượt quá 1000 ký tự.',
        ]);

        // Verify the reportable exists
        $reportableClass = $validated['reportable_type'];
        $reportable = $reportableClass::find($validated['reportable_id']);

        if (!$reportable) {
            return back()->with('error', 'Không tìm thấy đối tượng báo cáo.');
        }

        // Check if user already reported this item
        $existingReport = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', $validated['reportable_type'])
            ->where('reportable_id', $validated['reportable_id'])
            ->where('status', '!=', 'dismissed')
            ->first();

        if ($existingReport) {
            return back()->with('error', 'Bạn đã báo cáo đối tượng này trước đó.');
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Broadcast event để admin nhận real-time
        event(new NewReportCreated($report));

        return back()->with('success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }
}
