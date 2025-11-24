<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'avatar',
        'birth_date',
        'gender',
        'address',
        'city',
        'province',
        'cv_file',
        'summary',
        'current_position',
        'current_company',
        'expected_salary',
        'experience_level',
        'is_available',
        'job_alert_enabled',
        'preferred_locations',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'expected_salary' => 'decimal:2',
            'is_available' => 'boolean',
            'job_alert_enabled' => 'boolean',
            'preferred_locations' => 'array',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'candidate_id', 'skill_id')
            ->withPivot(['level', 'years_experience'])
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function savedJobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'saved_jobs', 'candidate_id', 'job_posting_id')
            ->withTimestamps();
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class, 'candidate_id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'candidate_id');
    }

    public function jobAlerts(): HasMany
    {
        return $this->hasMany(JobAlert::class, 'candidate_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class, 'candidate_id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'candidate_id');
    }

    public function cvs(): HasMany
    {
        return $this->hasMany(CandidateCv::class, 'candidate_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByLocation($query, $city, $province = null)
    {
        $query->where('city', $city);
        if ($province) {
            $query->where('province', $province);
        }
        return $query;
    }

    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    public function scopeBySalaryRange($query, $minSalary, $maxSalary)
    {
        return $query->whereBetween('expected_salary', [$minSalary, $maxSalary]);
    }

    // Helper methods
    public function getAge(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function hasSkill($skillId): bool
    {
        return $this->skills()->where('skill_id', $skillId)->exists();
    }

    public function getSkillLevel($skillId): ?string
    {
        $skill = $this->skills()->where('skill_id', $skillId)->first();
        return $skill ? $skill->pivot->level : null;
    }
}
