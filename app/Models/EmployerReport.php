<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CandidateProfile;

class EmployerReport extends Model
{
    protected $fillable = [
        'employer_id',
        'candidate_id',
        'type',
        'reason',
        'status',
        'reportable_type',
        'reportable_id',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function candidate()
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    public function reportable()
    {
        return $this->morphTo(null, 'reportable_type', 'reportable_id');
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'reviewing' => 'Đang xem xét',
            'resolved' => 'Đã xử lý',
            'dismissed' => 'Đã bỏ qua',
            default => ucfirst($this->status),
        };
    }

    public function getReportableTypeLabel(): string
    {
        if (!$this->reportable_type) {
            return 'Không xác định';
        }

        return match($this->reportable_type) {
            'App\\Models\\JobPosting' => 'Công việc',
            'App\\Models\\CandidateProfile' => 'Ứng viên',
            default => $this->reportable_type,
        };
    }
}