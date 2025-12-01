<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'author_id',
        'category',
        'tags',
        'featured_image',
        'is_published',
        'is_featured',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the author of the blog post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope to get only published blogs
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to get featured blogs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
