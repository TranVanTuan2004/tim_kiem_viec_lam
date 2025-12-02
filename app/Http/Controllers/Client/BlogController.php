<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts
     */
    public function index(Request $request)
    {
        $query = Blog::with('author')
            ->published()
            ->latest('published_at');

        // Filter by category
        if ($request->category && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Search by title or content
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $blogs = $query->paginate(12)->withQueryString();

        // Get available categories
        $categories = Blog::published()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return Inertia::render('client/BlogIndex', [
            'blogs' => $blogs,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category ?? 'all',
            ],
        ]);
    }

    /**
     * Display the specified blog post
     */
    public function show($slug)
    {
        $blog = Blog::with('author')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $blog->incrementViews();

        // Get related posts
        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return Inertia::render('client/BlogShow', [
            'blog' => $blog,
            'relatedBlogs' => $relatedBlogs,
        ]);
    }
}
