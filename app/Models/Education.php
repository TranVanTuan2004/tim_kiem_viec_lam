<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations';

    protected $fillable = [
        'candidate_id',
        'institution',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'gpa',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'gpa' => 'decimal:2',
        ];
    }

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    // Scopes
    public function scopeByInstitution($query, $institution)
    {
        return $query->where('institution', 'like', "%{$institution}%");
    }

    public function scopeByDegree($query, $degree)
    {
        return $query->where('degree', 'like', "%{$degree}%");
    }

    public function scopeByFieldOfStudy($query, $field)
    {
        return $query->where('field_of_study', 'like', "%{$field}%");
    }

    public function scopeByGpaRange($query, $minGpa, $maxGpa)
    {
        return $query->whereBetween('gpa', [$minGpa, $maxGpa]);
    }

    public function scopeHighGpa($query, $minGpa = 3.5)
    {
        return $query->where('gpa', '>=', $minGpa);
    }

    // Helper methods
    public function getFormattedGpa(): string
    {
        return $this->gpa ? number_format($this->gpa, 2) . '/4.0' : 'Không có';
    }

    public function getPeriod(): string
    {
        $start = $this->start_date->format('Y');
        
        if ($this->end_date) {
            $end = $this->end_date->format('Y');
            return "{$start} - {$end}";
        }
        
        return $start;
    }

    public function getDegreeLevel(): string
    {
        $degree = strtolower($this->degree);
        
        if (str_contains($degree, 'tiến sĩ') || str_contains($degree, 'phd')) {
            return 'Tiến sĩ';
        } elseif (str_contains($degree, 'thạc sĩ') || str_contains($degree, 'master')) {
            return 'Thạc sĩ';
        } elseif (str_contains($degree, 'cử nhân') || str_contains($degree, 'bachelor')) {
            return 'Cử nhân';
        } elseif (str_contains($degree, 'cao đẳng') || str_contains($degree, 'associate')) {
            return 'Cao đẳng';
        } elseif (str_contains($degree, 'trung cấp') || str_contains($degree, 'diploma')) {
            return 'Trung cấp';
        } else {
            return 'Khác';
        }
    }

    public function isCompleted(): bool
    {
        return $this->end_date !== null;
    }

    public function isGraduated(): bool
    {
        return $this->isCompleted() && $this->end_date->isPast();
    }
}
