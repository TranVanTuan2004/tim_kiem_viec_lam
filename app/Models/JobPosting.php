<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JobPosting extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'industry_id',
        'location',
        'city',
        'province',
        'employment_type',
        'experience_level',
        'min_salary',
        'max_salary',
        'salary_type',
        'application_deadline',
        'quantity',
        'status',
        'is_featured',
        'is_active',
        'views',
        'applications_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'application_deadline' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',

        ];
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_posting_id', 'skill_id')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_posting_id');
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'saved_jobs', 'job_posting_id', 'candidate_id')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'job_posting_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'approved')
            ->whereNotNull('published_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLocation($query, $city, $province = null)
    {
        $query->where('city', $city);
        if ($province) {
            $query->where('province', $province);
        }
        return $query;
    }

    public function scopeByIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeBySalaryRange($query, $minSalary, $maxSalary)
    {
        return $query->where(function ($q) use ($minSalary, $maxSalary) {
            $q->whereBetween('min_salary', [$minSalary, $maxSalary])
                ->orWhereBetween('max_salary', [$minSalary, $maxSalary])
                ->orWhere(function ($subQ) use ($minSalary, $maxSalary) {
                    $subQ->where('min_salary', '<=', $minSalary)
                        ->where('max_salary', '>=', $maxSalary);
                });
        });
    }

    public function scopeWithSkills($query, $skillIds)
    {
        return $query->whereHas('skills', function ($q) use ($skillIds) {
            $q->whereIn('skill_id', $skillIds);
        });
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    public function scopeFilterByLocation($query, $location)
    {
        if (!$location) {
            return $query;
        }

        return $query->where(function($q) use ($location) {
            $q->where('location', 'like', "%{$location}%")
              ->orWhere('city', 'like', "%{$location}%")
              ->orWhere('province', 'like', "%{$location}%");
        });
    }

    // Helper methods
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function incrementApplicationsCount(): void
    {
        $this->increment('applications_count');
    }

    public function isExpired(): bool
    {
        return $this->application_deadline && $this->application_deadline->isPast();
    }

    public function getSalaryRange(): string
    {
        if (!$this->min_salary && !$this->max_salary) {
            return 'Thỏa thuận';
        }

        if ($this->min_salary && $this->max_salary) {
            return number_format($this->min_salary) . ' - ' . number_format($this->max_salary) . ' ' . $this->salary_type;
        }

        if ($this->min_salary) {
            return 'Từ ' . number_format($this->min_salary) . ' ' . $this->salary_type;
        }

        return 'Đến ' . number_format($this->max_salary) . ' ' . $this->salary_type;
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()  // Log tất cả các field
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Job Posting {$eventName}");
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

}
