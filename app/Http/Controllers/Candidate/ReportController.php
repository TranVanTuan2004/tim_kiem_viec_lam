<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\JobPosting;
use App\Models\Company;
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
        $user = Auth::user();

        $query = Report::where('reporter_id', $user->id)
            ->with(['reportable'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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
            ],
        ]);
    }

    public function show(Report $report)
    {
        abort_if($report->reporter_id !== Auth::id(), 403);

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
            'reason' => 'required|string|min:10|max:1000',
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

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }
}
