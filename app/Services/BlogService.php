<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BlogService
{
    /**
     * Get paginated published blogs with filters
     */
    public function getPublishedBlogs(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Blog::with(['author'])
            ->published()
            ->select([
                'id',
                'author_id',
                'title',
                'slug',
                'excerpt',
                'featured_image',
                'published_at',
                'views',
                'is_featured',
                'created_at',
            ]);

        // Search by keyword
        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        // Filter by featured
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        // Order by featured first, then published date
        $query->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get featured blogs
     */
    public function getFeaturedBlogs(int $limit = 6): Collection
    {
        return Blog::with(['author'])
            ->published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent blogs
     */
    public function getRecentBlogs(int $limit = 6): Collection
    {
        return Blog::with(['author'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get blog detail
     */
    public function getBlogDetail(Blog $blog): Blog
    {
        return $blog->load(['author']);
    }

    /**
     * Get blog by slug
     */
    public function getBlogBySlug(string $slug): ?Blog
    {
        return Blog::with(['author'])
            ->where('slug', $slug)
            ->published()
            ->first();
    }

    /**
     * Increment blog views
     */
    public function incrementViews(Blog $blog): void
    {
        $blog->incrementViews();
    }

    /**
     * Get all blogs for admin (including drafts)
     */
    public function getAllBlogs(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Blog::with(['author'])
            ->select([
                'id',
                'author_id',
                'title',
                'slug',
                'excerpt',
                'featured_image',
                'status',
                'published_at',
                'views',
                'is_featured',
                'created_at',
                'updated_at',
            ]);

        // Search
        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by featured
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Filter by author
        if (!empty($filters['author_id'])) {
            $query->byAuthor($filters['author_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new blog
     */
    public function createBlog(int $authorId, array $data): Blog
    {
        $data['author_id'] = $authorId;
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        } else {
            $data['slug'] = $this->generateUniqueSlug($data['slug']);
        }

        // Set published_at if status is published
        if ($data['status'] === 'published') {
            // If published_at is empty or null, set it to now
            if (empty($data['published_at']) || $data['published_at'] === '') {
                $data['published_at'] = now();
            } else {
                // Ensure published_at is a valid datetime
                $data['published_at'] = \Carbon\Carbon::parse($data['published_at']);
            }
        } else {
            // If status is draft, set published_at to null
            $data['published_at'] = null;
        }

        return Blog::create($data);
    }

    /**
     * Update blog
     */
    public function updateBlog(Blog $blog, array $data): Blog
    {
        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $blog->title) {
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $blog->id);
            }
        } elseif (isset($data['slug']) && $data['slug'] !== $blog->slug) {
            $data['slug'] = $this->generateUniqueSlug($data['slug'], $blog->id);
        }

        // Set published_at if status changed to published
        if (isset($data['status'])) {
            if ($data['status'] === 'published') {
                // If published_at is empty or null, set it to now
                if (empty($data['published_at']) || $data['published_at'] === '') {
                    $data['published_at'] = now();
                } else {
                    // Ensure published_at is a valid datetime
                    $data['published_at'] = \Carbon\Carbon::parse($data['published_at']);
                }
            } else {
                // If status is draft, set published_at to null
                $data['published_at'] = null;
            }
        }

        $blog->update($data);
        return $blog->fresh();
    }

    /**
     * Delete blog
     */
    public function deleteBlog(Blog $blog): bool
    {
        return $blog->delete();
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Blog $blog): Blog
    {
        $blog->update(['is_featured' => !$blog->is_featured]);
        return $blog->fresh();
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Transform blog for list display
     */
    public function transformForList(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => $blog->excerpt,
            'featured_image' => $blog->featured_image,
            'published_at' => $blog->published_at,
            'views' => $blog->views,
            'is_featured' => $blog->is_featured,
            'author' => [
                'id' => $blog->author->id,
                'name' => $blog->author->name,
            ],
        ];
    }

    /**
     * Transform blog for detail display
     */
    public function transformForDetail(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => $blog->excerpt,
            'content' => $blog->content,
            'featured_image' => $blog->featured_image,
            'status' => $blog->status,
            'published_at' => $blog->published_at,
            'views' => $blog->views,
            'is_featured' => $blog->is_featured,
            'meta_title' => $blog->meta_title,
            'meta_description' => $blog->meta_description,
            'author' => [
                'id' => $blog->author->id,
                'name' => $blog->author->name,
                'email' => $blog->author->email,
            ],
            'created_at' => $blog->created_at,
            'updated_at' => $blog->updated_at,
        ];
    }
}

