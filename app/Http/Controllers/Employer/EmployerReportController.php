<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateProfile;
use App\Events\NewReportCreated;

class EmployerReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidate_profiles,id',
            'type' => 'required|string',
            'reason' => 'required|min:10|max:1000',
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
        // event(new NewReportCreated($report));

        return back()->with('success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }
}

