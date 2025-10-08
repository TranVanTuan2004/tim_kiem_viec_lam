<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_slug',
        'description',
        'website',
        'logo',
        'address',
        'city',
        'province',
        'size',
        'industry',
        'rating',
        'total_reviews',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
            'is_verified' => 'boolean',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class, 'company_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class, 'company_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'company_id');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByLocation($query, $city, $province = null)
    {
        $query->where('city', $city);
        if ($province) {
            $query->where('province', $province);
        }
        return $query;
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    // Helper methods
    public function getRouteKeyName(): string
    {
        return 'company_slug';
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->where('status', 'approved')->avg('rating') ?? 0;
    }

    public function getTotalReviewsCount(): int
    {
        return $this->reviews()->where('status', 'approved')->count();
    }
}
