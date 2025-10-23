<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Show the application form for a specific job
     */
    public function create(JobPosting $job_posting)
    {
        $user = Auth::user()->load('candidateProfile');

        // Check if user has candidate profile
        if (!$user->candidateProfile) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('error', 'Vui lòng hoàn thiện hồ sơ ứng viên của bạn trước khi ứng tuyển.');
        }

        // Check if user already applied for this job
        $existingApplication = Application::where('job_posting_id', $job_posting->id)
            ->where('candidate_id', $user->candidateProfile->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('info', 'Bạn đã ứng tuyển vào vị trí này rồi. Trạng thái: ' . $existingApplication->getStatusLabel());
        }

        // Check if application deadline has passed
        if ($job_posting->isDeadlinePassed()) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('error', 'Hạn nộp đơn ứng tuyển đã kết thúc.');
        }

        // Load job with relations
        $job = $job_posting->load(['company', 'skills']);

        return Inertia::render('client/JobApplication', [
            'job' => [
                'id' => $job->id,
                'slug' => $job->slug,
                'title' => $job->title,
                'company' => [
                    'name' => $job->company->company_name ?? 'Công ty',
                    'logo' => $job->company->logo ?? null,
                ],
                'location' => $job->location ?? $job->city ?? 'Nơi làm việc',
                'salary' => $job->getSalaryRange(),
                'employment_type' => $job->employment_type,
                'experience_level' => $job->experience_level,
            ],
            'candidateProfile' => [
                'cv_file' => $user->candidateProfile->cv_file,
            ],
        ]);
    }

    /**
     * Store a new job application
     */
    public function store(Request $request, JobPosting $job_posting)
    {
        $user = Auth::user()->load('candidateProfile');

        // Check if user has candidate profile
        if (!$user->candidateProfile) {
            return back()->with('error', 'Vui lòng hoàn thiện hồ sơ ứng viên của bạn trước khi ứng tuyển.');
        }

        // Check if user already applied
        $existingApplication = Application::where('job_posting_id', $job_posting->id)
            ->where('candidate_id', $user->candidateProfile->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('info', 'Bạn đã ứng tuyển vào vị trí này rồi.');
        }

        // Check if application deadline has passed
        if ($job_posting->isDeadlinePassed()) {
            return redirect()->route('jobs.show', $job_posting->slug)
                ->with('error', 'Hạn nộp đơn ứng tuyển đã kết thúc.');
        }

        // Validate the request
        $validated = $request->validate([
            'cover_letter' => 'nullable|string|max:5000',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        // Handle CV file upload if provided
        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('applications/cvs', 'public');
        } else {
            // Use CV from candidate profile if available
            $cvPath = $user->candidateProfile->cv_file;
        }

        // Ensure we have a CV
        if (!$cvPath) {
            return back()->withErrors(['cv_file' => 'Vui lòng tải lên CV của bạn.']);
        }

        // Create the application
        $application = Application::create([
            'job_posting_id' => $job_posting->id,
            'candidate_id' => $user->candidateProfile->id,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_file' => $cvPath,
            'status' => 'pending',
        ]);

        // Increment applications count
        $job_posting->incrementApplicationsCount();

        // TODO: Send notification to employer
        // TODO: Send confirmation email to candidate

        return redirect()->route('jobs.show', $job_posting->slug)
            ->with('success', 'Ứng tuyển thành công! Nhà tuyển dụng sẽ liên hệ với bạn sớm.');
    }
}

