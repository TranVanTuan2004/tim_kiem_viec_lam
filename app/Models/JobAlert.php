<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'name',
        'keywords',
        'industries',
        'locations',
        'skills',
        'min_salary',
        'max_salary',
        'frequency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'industries' => 'array',
            'locations' => 'array',
            'skills' => 'array',
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    public function scopeDaily($query)
    {
        return $query->where('frequency', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('frequency', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('frequency', 'monthly');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getFrequencyLabel(): string
    {
        return match ($this->frequency) {
            'daily' => 'Hàng ngày',
            'weekly' => 'Hàng tuần',
            'monthly' => 'Hàng tháng',
            default => 'Không xác định',
        };
    }

    public function matchesJobPosting(JobPosting $jobPosting): bool
    {
        // Check keywords
        if ($this->keywords && !empty($this->keywords)) {
            $jobText = strtolower($jobPosting->title . ' ' . $jobPosting->description);
            $hasKeyword = false;
            foreach ($this->keywords as $keyword) {
                if (str_contains($jobText, strtolower($keyword))) {
                    $hasKeyword = true;
                    break;
                }
            }
            if (!$hasKeyword) {
                return false;
            }
        }

        // Check industries
        if ($this->industries && !empty($this->industries)) {
            if (!in_array($jobPosting->industry_id, $this->industries)) {
                return false;
            }
        }

        // Check locations
        if ($this->locations && !empty($this->locations)) {
            $jobLocation = $jobPosting->city . ', ' . $jobPosting->province;
            $hasLocation = false;
            foreach ($this->locations as $location) {
                if (str_contains($jobLocation, $location)) {
                    $hasLocation = true;
                    break;
                }
            }
            if (!$hasLocation) {
                return false;
            }
        }

        // Check skills
        if ($this->skills && !empty($this->skills)) {
            $jobSkillIds = $jobPosting->skills()->pluck('skill_id')->toArray();
            if (empty(array_intersect($this->skills, $jobSkillIds))) {
                return false;
            }
        }

        // Check salary range
        if ($this->min_salary || $this->max_salary) {
            if ($this->min_salary && $jobPosting->max_salary && $jobPosting->max_salary < $this->min_salary) {
                return false;
            }
            if ($this->max_salary && $jobPosting->min_salary && $jobPosting->min_salary > $this->max_salary) {
                return false;
            }
        }

        return true;
    }
}
