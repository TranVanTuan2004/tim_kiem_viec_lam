<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'industry_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($skill) {
            if (empty($skill->slug)) {
                $skill->slug = \Illuminate\Support\Str::slug($skill->name);
            }
        });

        static::updating(function ($skill) {
            if ($skill->isDirty('name') && empty($skill->slug)) {
                $skill->slug = \Illuminate\Support\Str::slug($skill->name);
            }
        });
    }

    // Relationships
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function jobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'job_skills', 'skill_id', 'job_posting_id')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'candidate_skills', 'skill_id', 'candidate_id')
            ->withPivot(['level', 'years_experience'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    // Helper methods
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
