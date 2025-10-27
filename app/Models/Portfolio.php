<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidate_id',
        'title',
        'description',
        'project_url',
        'github_url',
        'demo_url',
        'images',
        'technologies',
        'start_date',
        'end_date',
        'is_ongoing',
        'is_featured',
        'display_order',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'technologies' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_ongoing' => 'boolean',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    // Relationships
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOngoing($query)
    {
        return $query->where('is_ongoing', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_ongoing', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('created_at', 'desc');
    }

    public function scopeByCandidate($query, $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    // Helper methods
    public function getDuration(): string
    {
        if ($this->is_ongoing) {
            return $this->start_date 
                ? $this->start_date->format('M Y') . ' - Present'
                : 'Ongoing';
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('M Y') . ' - ' . $this->end_date->format('M Y');
        }

        return $this->start_date 
            ? $this->start_date->format('M Y')
            : 'N/A';
    }

    public function getMainImage(): ?string
    {
        return $this->images && count($this->images) > 0 
            ? $this->images[0] 
            : null;
    }

    public function hasImages(): bool
    {
        return $this->images && count($this->images) > 0;
    }

    public function getTechnologyList(): array
    {
        return $this->technologies ?? [];
    }
}

