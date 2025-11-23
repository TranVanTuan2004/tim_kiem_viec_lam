<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApplicationService
{
    /**
     * Check if user can apply for job
     */
    public function canApply(User $user, JobPosting $jobPosting): array
    {
        // Check if user has candidate profile
        if (!$user->candidateProfile) {
            return [
                'can_apply' => false,
                'message' => 'Vui lòng hoàn thiện hồ sơ ứng viên của bạn trước khi ứng tuyển.',
                'type' => 'error'
            ];
        }

        // Check if user already applied
        $existingApplication = $this->getExistingApplication($user, $jobPosting);
        if ($existingApplication) {
            return [
                'can_apply' => false,
                'message' => 'Bạn đã ứng tuyển vào vị trí này rồi. Trạng thái: ' . $existingApplication->getStatusLabel(),
                'type' => 'info'
            ];
        }

        // Check if application deadline has passed
        if ($jobPosting->isExpired()) {
            return [
                'can_apply' => false,
                'message' => 'Hạn nộp đơn ứng tuyển đã kết thúc.',
                'type' => 'error'
            ];
        }

        return [
            'can_apply' => true,
            'message' => 'Bạn có thể ứng tuyển cho vị trí này.',
            'type' => 'success'
        ];
    }

    /**
     * Get existing application
     */
    public function getExistingApplication(User $user, JobPosting $jobPosting): ?Application
    {
        if (!$user->candidateProfile) {
            return null;
        }

        return Application::where('job_posting_id', $jobPosting->id)
            ->where('candidate_id', $user->candidateProfile->id)
            ->first();
    }

    /**
     * Create new application
     */
    public function createApplication(
        User $user,
        JobPosting $jobPosting,
        ?string $coverLetter,
        ?UploadedFile $cvFile,
        ?int $cvId = null
    ): Application {
        // Handle CV file upload
        $cvPath = $this->handleCvUpload($user, $cvFile, $cvId);

        if (!$cvPath) {
            throw new \Exception('Vui lòng tải lên CV của bạn hoặc chọn CV có sẵn.');
        }

        // Create the application
        $application = Application::create([
            'job_posting_id' => $jobPosting->id,
            'candidate_id' => $user->candidateProfile->id,
            'cover_letter' => $coverLetter,
            'cv_file' => $cvPath,
            'status' => 'pending',
        ]);

        // Increment applications count
        $jobPosting->incrementApplicationsCount();

        return $application;
    }

    /**
     * Handle CV file upload
     */
    private function handleCvUpload(User $user, ?UploadedFile $cvFile, ?int $cvId = null): ?string
    {
        if ($cvFile) {
            // Upload new CV
            return $cvFile->store('applications/cvs', 'public');
        }

        if ($cvId) {
            $cv = $user->candidateProfile->cvs()->find($cvId);
            if ($cv) {
                return $cv->file_path;
            }
        }

        // Prefer default CV from candidate_cvs if available
        if ($user->candidateProfile) {
            $candidate = $user->candidateProfile;
            
            // Check for default CV in new table
            $defaultCv = $candidate->cvs()->where('is_default', true)->first();
            if ($defaultCv) {
                return $defaultCv->file_path;
            }

            // Fallback to legacy single cv_file on candidate_profiles
            if (!empty($candidate->cv_file)) {
                return $candidate->cv_file;
            }
        }

        return null;
    }

    /**
     * Validate application data
     */
    public function validateApplicationData(array $data): array
    {
        $validated = [];

        if (isset($data['cover_letter'])) {
            $validated['cover_letter'] = trim($data['cover_letter']);
            
            if (strlen($validated['cover_letter']) > 5000) {
                throw new \Exception('Thư xin việc không được vượt quá 5000 ký tự.');
            }
        }

        if (isset($data['cv_file']) && $data['cv_file'] instanceof UploadedFile) {
            $file = $data['cv_file'];
            
            // Validate file type
            $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw new \Exception('CV phải là file PDF, DOC hoặc DOCX.');
            }

            // Validate file size (5MB max)
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('Kích thước CV không được vượt quá 5MB.');
            }

            $validated['cv_file'] = $file;
        }

        if (isset($data['cv_id'])) {
            $validated['cv_id'] = $data['cv_id'];
        }

        return $validated;
    }

    /**
     * Get candidate profile data for application form
     */
    public function getCandidateProfileData(User $user): array
    {
        if (!$user->candidateProfile) {
            return [];
        }

        return [
            'cv_file' => $user->candidateProfile->cv_file,
            'cvs' => $user->candidateProfile->cvs()->orderBy('created_at', 'desc')->get()->map(function ($cv) {
                return [
                    'id' => $cv->id,
                    'name' => $cv->name,
                    'file_path' => $cv->file_path,
                    'is_default' => $cv->is_default,
                ];
            }),
            'full_name' => $user->candidateProfile->full_name ?? $user->name,
            'email' => $user->email,
            'phone' => $user->candidateProfile->phone,
        ];
    }

    /**
     * Send notification after successful application
     */
    public function sendApplicationNotifications(Application $application): void
    {
        // TODO: Send notification to employer
        // TODO: Send confirmation email to candidate
    }

    /**
     * Get application statistics for a job
     */
    public function getApplicationStats(JobPosting $jobPosting): array
    {
        $applications = Application::where('job_posting_id', $jobPosting->id)->get();

        return [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'reviewing' => $applications->where('status', 'reviewing')->count(),
            'shortlisted' => $applications->where('status', 'shortlisted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
        ];
    }
}

