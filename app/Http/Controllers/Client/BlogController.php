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

    public function index()
    {
        $filters = [
            'q' => request('q', ''),
            'featured' => request('featured') == '1',
        ];

        $perPage = (int) request('per_page', 6);
        if ($perPage <= 0) {
            $perPage = 6;
        }

        $blogs = $this->blogService->getPublishedBlogs($filters, $perPage)
            ->through(fn($blog) => $this->blogService->transformForList($blog));

        $featuredBlogs = $this->blogService->getFeaturedBlogs(5)
            ->map(fn($blog) => $this->blogService->transformForList($blog));

        return Inertia::render('client/blog/Index', [
            'blogs' => $blogs,
            'featuredBlogs' => $featuredBlogs,
            'filters' => [
                'q' => $filters['q'],
                'featured' => $filters['featured'],
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $blog = $this->blogService->getBlogBySlug($slug);

        if (!$blog) {
            abort(404);
        }

        $this->blogService->incrementViews($blog);

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
