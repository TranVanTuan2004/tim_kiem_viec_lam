<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateProfile;
use App\Events\NewReportCreated;
use Inertia\Inertia;

class EmployerReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Validate status filter parameter
        $validStatuses = ['all', 'pending', 'reviewing', 'resolved', 'dismissed'];
        if ($request->has('status') && !in_array($request->status, $validStatuses)) {
            return redirect()->route('employer.reports.index')
                ->with('error', 'Danh mục không tồn tại.');
        }

        $query = Report::where('reporter_id', $user->id)
            ->where('reportable_type', CandidateProfile::class) // EMPLOYER báo cáo ứng viên
            ->with('reportable')
            ->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search by reason
                $q->where('reason', 'like', "%{$search}%")
                    // Search by candidate name - use whereHasMorph to explicitly target CandidateProfile
                    ->orWhereHasMorph('reportable', [CandidateProfile::class], function ($rpQuery) use ($search) {
                        $rpQuery->whereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                    });
            });
        }

        $reports = $query->paginate(15)->through(function ($report) {
            return [
                'id' => $report->id,
                'reason' => $report->reason,
                'status' => $report->status,
                'status_label' => $report->getStatusLabel(),
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                'candidate' => [
                    'id' => $report->reportable->id,
                    'name' => $report->reportable->user->name,
                ]
            ];
        });

        return Inertia::render('Employer/Reports/Index', [
            'reports' => $reports,
            'filters' => [
                'status' => $request->status ?? 'all',
                'search' => $request->search ?? '',
            ]
        ]);
    }

    public function show(Report $report)
    {
        abort_if($report->reporter_id !== Auth::id(), 403);

        return Inertia::render('Employer/Reports/Show', [
            'report' => [
                'id' => $report->id,
                'reason' => $report->reason,
                'status' => $report->status,
                'status_label' => $report->getStatusLabel(),
                'admin_notes' => $report->admin_notes,
                'created_at' => $report->created_at?->format('Y-m-d H:i:s'),
                'candidate' => [
                    'id' => $report->reportable->id,
                    'name' => $report->reportable->user->name,
                    'email' => $report->reportable->user->email,
                ],
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidate_profiles,id',
            'type' => 'required|in:spam,fraud,inappropriate,fake,other',
            'reason' => 'required|min:10|max:1000',
        ], [
            'type.in' => 'Danh mục không tồn tại.',
            'reason.required' => 'Vui lòng nhập lý do báo cáo.',
            'reason.min' => 'Lý do báo cáo phải có ít nhất 10 ký tự.',
            'reason.max' => 'Lý do báo cáo không được vượt quá 1000 ký tự.',
        ]);

        $candidate = CandidateProfile::find($validated['candidate_id']);
        if (!$candidate) {
            return back()->with('error', 'Không tìm thấy hồ sơ ứng viên.');
        }

        $existingReport = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', CandidateProfile::class)
            ->where('reportable_id', $validated['candidate_id'])
            ->where('status', '!=', 'dismissed')
            ->first();

        if ($existingReport) {
            return back()->with('error', 'Bạn đã báo cáo ứng viên này trước đó.');
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
            'reportable_type' => CandidateProfile::class,
            'reportable_id' => $request->candidate_id,
        ]);

        \Log::info('Report created', ['report_id' => $report->id]);

        // Tạm tắt broadcast event để tránh lỗi Reverb
        // Bật lại khi đã cấu hình Reverb server
        event(new NewReportCreated($report));

        return back()->with('success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }


}

