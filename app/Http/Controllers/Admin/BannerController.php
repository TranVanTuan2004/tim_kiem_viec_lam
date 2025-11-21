<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Display a listing of banners
     */
    public function index(Request $request)
    {
        $banners = Banner::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->ordered()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/banners/Index', [
            'banners' => $banners,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        return Inertia::render('admin/banners/Create');
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'type' => 'required|in:hero,promotional,announcement',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        // Get the highest order and add 1
        $validated['order'] = Banner::max('order') + 1;

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified banner
     */
    public function edit(Banner $banner)
    {
        return Inertia::render('admin/banners/Edit', [
            'banner' => $banner,
        ]);
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'type' => 'required|in:hero,promotional,announcement',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Handle image upload if new image provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_url) {
                $oldPath = str_replace('/storage/', '', $banner->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('banners', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công!');
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner)
    {
        // Delete image file
        if ($banner->image_url) {
            $path = str_replace('/storage/', '', $banner->image_url);
            Storage::disk('public')->delete($path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công!');
    }

    /**
     * Toggle banner active status
     */
    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return back()->with('success', 'Trạng thái banner đã được cập nhật!');
    }

    /**
     * Update banners order
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.order' => 'required|integer',
        ]);

        foreach ($validated['banners'] as $bannerData) {
            Banner::where('id', $bannerData['id'])
                ->update(['order' => $bannerData['order']]);
        }

        return back()->with('success', 'Thứ tự banner đã được cập nhật!');
    }
}
