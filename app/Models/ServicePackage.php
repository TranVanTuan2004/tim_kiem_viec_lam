<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'max_jobs',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'package_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'package_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeByDuration($query, $durationDays)
    {
        return $query->where('duration_days', $durationDays);
    }

    // Helper methods
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price) . ' VNĐ';
    }

    public function getDurationInMonths(): float
    {
        return round($this->duration_days / 30, 1);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    protected static function booted()
    {
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }
}
