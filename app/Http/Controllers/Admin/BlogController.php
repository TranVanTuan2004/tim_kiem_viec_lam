<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Display a listing of blogs.
     */
    public function index(Request $request): Response
    {
        $filters = [
            'q' => $request->get('q', ''),
            'status' => $request->get('status', ''),
            'is_featured' => $request->get('is_featured', ''),
            'author_id' => $request->get('author_id', ''),
        ];

        $blogs = $this->blogService->getAllBlogs($filters, 15);

        return Inertia::render('admin/blogs/Index', [
            'blogs' => $blogs,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create(): Response
    {
        return Inertia::render('admin/blogs/Create');
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store('blogs', 'public');
            $validated['featured_image'] = Storage::url($path);
        }

        $blog = $this->blogService->createBlog($request->user()->id, $validated);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog): Response
    {
        $blog->load('author');

        return Inertia::render('admin/blogs/Edit', [
            'blog' => $this->blogService->transformForDetail($blog),
        ]);
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image_file')) {
            // Delete old image if exists
            if ($blog->featured_image && strpos($blog->featured_image, '/storage/') === 0) {
                $oldPath = str_replace('/storage/', '', $blog->featured_image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('featured_image_file')->store('blogs', 'public');
            $validated['featured_image'] = Storage::url($path);
        }

        $this->blogService->updateBlog($blog, $validated);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog đã được cập nhật thành công.');
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        $this->blogService->deleteBlog($blog);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Blog đã được xóa thành công.');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Blog $blog): RedirectResponse
    {
        $this->blogService->toggleFeatured($blog);

        return redirect()
            ->route('admin.blogs.index')
            ->with('success', 'Trạng thái nổi bật đã được cập nhật.');
    }
}

