<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'company_name',
        'position',
        'description',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopePast($query)
    {
        return $query->where('is_current', false);
    }

    public function scopeByCompany($query, $companyName)
    {
        return $query->where('company_name', 'like', "%{$companyName}%");
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', 'like', "%{$position}%");
    }

    // Helper methods
    public function isCurrent(): bool
    {
        return $this->is_current;
    }

    public function getDurationInMonths(): int
    {
        if ($this->is_current) {
            return $this->start_date->diffInMonths(now());
        }
        
        if ($this->end_date) {
            return $this->start_date->diffInMonths($this->end_date);
        }
        
        return 0;
    }

    public function getDurationInYears(): float
    {
        return round($this->getDurationInMonths() / 12, 1);
    }

    public function getFormattedDuration(): string
    {
        $months = $this->getDurationInMonths();
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) {
            return "{$years} năm {$remainingMonths} tháng";
        } elseif ($years > 0) {
            return "{$years} năm";
        } else {
            return "{$months} tháng";
        }
    }

    public function getPeriod(): string
    {
        $start = $this->start_date->format('m/Y');
        
        if ($this->is_current) {
            return "{$start} - Hiện tại";
        }
        
        if ($this->end_date) {
            $end = $this->end_date->format('m/Y');
            return "{$start} - {$end}";
        }
        
        return $start;
    }
}
