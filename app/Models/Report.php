<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'type',
        'reason',
        'status',
    ];

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

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSpam($query)
    {
        return $query->where('type', 'spam');
    }

    public function scopeFraud($query)
    {
        return $query->where('type', 'fraud');
    }

    public function scopeInappropriate($query)
    {
        return $query->where('type', 'inappropriate');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
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
            'inappropriate' => 'Không phù hợp',
            'fake' => 'Giả mạo',
            'other' => 'Khác',
            default => 'Không xác định',
        };
    }
}
