<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Report extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'type',
        'reason',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at', 
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    // Relationships
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', 'reviewing');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeByReportableType($query, $type)
    {
        return $query->where('reportable_type', $type);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReviewing(): bool
    {
        return $this->status === 'reviewing';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ xử lý',
            'reviewing' => 'Đang xem xét',
            'resolved' => 'Đã xử lý',
            'dismissed' => 'Đã bỏ qua',
            default => 'Không xác định',
        };
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'spam' => 'Spam',
            'fraud' => 'Lừa đảo',
            'inappropriate' => 'Nội dung không phù hợp',
            'fake' => 'Giả mạo',
            'other' => 'Khác',
            default => 'Không xác định',
        };
    }

    public function getReportableTypeLabel(): string
    {
        return match ($this->reportable_type) {
            'App\\Models\\JobPosting' => 'Tin tuyển dụng',
            'App\\Models\\Company' => 'Công ty',
            'App\\Models\\Interview' => 'Lịch phỏng vấn',
            'App\\Models\\CandidateProfile' => 'Ứng viên',
            default => 'Không xác định',
        };
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Report #{$this->id} {$eventName}");
    }

    public function user(): BelongsTo
    {
        return $this->reporter();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getReasonLabel(): string
    {
        return match ($this->reason) {
            'spam' => 'Spam',
            'fraud' => 'Lừa đảo',
            'inappropriate' => 'Nội dung không phù hợp',
            'fake' => 'Giả mạo',
            'other' => 'Khác',
            default => $this->reason,
        };
    }
}
