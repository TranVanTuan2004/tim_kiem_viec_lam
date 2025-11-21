<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomepageSectionController extends Controller
{
    /**
     * Display a listing of homepage sections
     */
    public function index()
    {
        $sections = HomepageSection::ordered()->get();

        return Inertia::render('admin/homepage/Index', [
            'sections' => $sections,
        ]);
    }

    /**
     * Update the specified section
     */
    public function update(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section đã được cập nhật thành công!');
    }

    /**
     * Toggle section active status
     */
    public function toggleActive(HomepageSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);

        return back()->with('success', 'Trạng thái section đã được cập nhật!');
    }

    /**
     * Update sections order
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:homepage_sections,id',
            'sections.*.order' => 'required|integer',
        ]);

        foreach ($validated['sections'] as $sectionData) {
            HomepageSection::where('id', $sectionData['id'])
                ->update(['order' => $sectionData['order']]);
        }

        return back()->with('success', 'Thứ tự section đã được cập nhật!');
    }
}
