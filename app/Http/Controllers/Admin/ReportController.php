<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display a listing of all reports.
     */
    public function index(Request $request)
    {
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

        // Search by description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reports = $query->paginate(20)->through(function ($report) {
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
            ];
        });

        $stats = [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'reviewing' => Report::reviewing()->count(),
            'resolved' => Report::resolved()->count(),
            'dismissed' => Report::dismissed()->count(),
        ];

        return Inertia::render('admin/Reports/Index', [
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
     */
    public function show($id)
    {
        $report = Report::with(['user', 'reportable', 'reviewer'])->findOrFail($id);

        return Inertia::render('admin/Reports/Show', [
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
            ],
        ]);
    }

    /**
     * Update the specified report.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Cập nhật báo cáo thành công!');
    }

    /**
     * Remove the specified report.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Xóa báo cáo thành công!');
    }
}
