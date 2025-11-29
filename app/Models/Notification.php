<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function markAsUnread(): void
    {
        $this->update(['is_read' => false]);
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'job_match' => 'Công việc phù hợp',
            'application_update' => 'Cập nhật đơn ứng tuyển',
            'interview_invite' => 'Mời phỏng vấn',
            'application_accepted' => 'Đơn ứng tuyển được chấp nhận',
            'application_rejected' => 'Đơn ứng tuyển bị từ chối',
            'job_alert' => 'Cảnh báo việc làm',
            'message_received' => 'Tin nhắn mới',
            'system_notification' => 'Thông báo hệ thống',
            'admin_notification' => 'Thông báo quản trị',
            default => 'Thông báo',
        };
    }
}
