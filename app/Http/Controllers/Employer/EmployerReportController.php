<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployerReport;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateProfile;

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

        $existingReport = EmployerReport::where('employer_id', Auth::id())
            ->where('candidate_id', $validated['candidate_id'])
            ->where('status', '!=', 'dismissed')
            ->first();
        
        if ($existingReport) {
            return back()->with('error', 'Bạn đã báo cáo ứng viên này trước đó.');
        }
        EmployerReport::create([
            'employer_id' => Auth::id(),
            'candidate_id' => $request->candidate_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }
}

