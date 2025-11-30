<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\BlogService;
use Inertia\Inertia;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Display a listing of published blogs.
     */
    public function index()
    {
        $filters = [
            'q' => request('q', ''),
            'featured' => request('featured') == '1',
        ];

        $blogs = $this->blogService->getPublishedBlogs($filters, 12)
            ->through(fn($blog) => $this->blogService->transformForList($blog));

        // Get featured blogs for sidebar
        $featuredBlogs = $this->blogService->getFeaturedBlogs(5)
            ->map(fn($blog) => $this->blogService->transformForList($blog));

        return Inertia::render('client/blog/Index', [
            'blogs' => $blogs,
            'featuredBlogs' => $featuredBlogs,
            'filters' => [
                'q' => $filters['q'],
                'featured' => $filters['featured'],
            ],
        ]);
    }

    /**
     * Display the specified blog.
     */
    public function show(string $slug)
    {
        $blog = $this->blogService->getBlogBySlug($slug);

        if (!$blog) {
            abort(404);
        }

        // Increment views
        $this->blogService->incrementViews($blog);

        // Get related blogs
        $relatedBlogs = $this->blogService->getRecentBlogs(4)
            ->reject(fn($b) => $b->id === $blog->id)
            ->take(3)
            ->map(fn($b) => $this->blogService->transformForList($b));

        return Inertia::render('client/blog/Show', [
            'blog' => $this->blogService->transformForDetail($blog),
            'relatedBlogs' => $relatedBlogs,
        ]);
    }
}

