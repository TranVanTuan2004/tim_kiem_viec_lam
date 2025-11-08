<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'package_id',
        'status',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', now()->addDays($days));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->expires_at->isPast();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Đang hoạt động',
            'expired' => 'Đã hết hạn',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getProgressPercentage(): float
    {
        $totalDays = $this->starts_at->diffInDays($this->expires_at);
        $remainingDays = $this->getDaysRemaining();
        return round((($totalDays - $remainingDays) / $totalDays) * 100, 2);
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()  // Log tất cả các field
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Subscription #{$this->id} {$eventName}");
    }
}
