<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    /**
     * Get the user who performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Get the subject of the activity
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('log_name', $type);
    }

    /**
     * Scope to filter by event
     */
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $userName = $this->causer ? $this->causer->name : 'System';
        
        $subjectName = match ($this->subject_type) {
            'App\Models\User' => 'User',
            'App\Models\JobPosting' => 'Job Posting',
            'App\Models\Application' => 'Application',
            'App\Models\Company' => 'Company',
            'App\Models\Payment' => 'Payment',
            default => 'Record',
        };

        return match ($this->event) {
            'created' => "{$userName} created {$subjectName}",
            'updated' => "{$userName} updated {$subjectName}",
            'deleted' => "{$userName} deleted {$subjectName}",
            default => "{$userName} performed action on {$subjectName}",
        };
    }
}
