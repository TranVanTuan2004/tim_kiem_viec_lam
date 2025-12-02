<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServicePackageController extends Controller
{
    // Hiển thị danh sách gói
    public function index(Request $request)
    {
        $query = ServicePackage::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status == 'active') {
            $query->active();
        } elseif ($request->status == 'inactive') {
            $query->where('is_active', false);
        }

        $packages = $query->orderBy('id','desc')->paginate(10)->withQueryString();

        return inertia('admin/service-package-pay/Index', [
            'packages' => $packages,
            'filters' => $request->only(['search','status']),
        ]);
    }
    

    // Form tạo mới
    public function create()
    {
        return inertia('admin/service-package-pay/Create');
    }

    // Lưu gói mới
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'description' => 'required|string|max:1000',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'features' => 'required|max:2000',
        'is_active' => 'boolean',
    ], [
        'name.required' => 'Tên gói không được để trống',
        'name.string' => 'Tên gói phải là chuỗi ký tự',
        'name.max' => 'Tên gói không được vượt quá 255 ký tự',
        'slug.string' => 'Slug phải là chuỗi ký tự',
        'slug.max' => 'Slug không được vượt quá 255 ký tự',
        'description.string' => 'Mô tả phải là chuỗi ký tự',
        'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
        'description.required' => 'Mô tả không được để trống',
        'price.required' => 'Giá không được để trống',
        'price.numeric' => 'Giá phải là số',
        'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
        'duration_days.required' => 'Số ngày sử dụng không được để trống',
        'duration_days.integer' => 'Số ngày sử dụng phải là số nguyên',
        'duration_days.min' => 'Số ngày sử dụng phải lớn hơn 0',
        'features.required' => 'Tính năng không được để trống',
        'features.max' => 'Tính năng không được vượt quá 2000 ký tự',
    ]);

    // Nếu slug trống thì tự sinh từ tên
    $slug = $validated['slug'] ?: Str::slug($validated['name']);

    // Kiểm tra trùng lặp
    $originalSlug = $slug;
    $counter = 1;
    while (\App\Models\ServicePackage::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
   

    $validated['slug'] = $slug;

    $package = \App\Models\ServicePackage::create($validated);

    return redirect()->route('admin.service-packages.index')
                     ->with('success', 'Tạo gói dịch vụ thành công');
}
    public function show(ServicePackage $package)
    {
        return inertia('admin/service-package-pay/Show', [
            'package' => $package
        ]);
    }

    // Form chỉnh sửa
    public function edit(ServicePackage $package)
    {
        return inertia('admin/service-package-pay/Edit', [
            'package' => $package,
        ]);
    }

    // Cập nhật gói
    public function update(Request $request, ServicePackage $package)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'description' => 'required|string|max:1000',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'features' => 'required|max:2000',
        'is_active' => 'boolean',
    ], [
        'name.required' => 'Tên gói không được để trống',
        'name.string' => 'Tên gói phải là chuỗi ký tự',
        'name.max' => 'Tên gói không được vượt quá 255 ký tự',
        'slug.string' => 'Slug phải là chuỗi ký tự',
        'slug.max' => 'Slug không được vượt quá 255 ký tự',
        'description.string' => 'Mô tả phải là chuỗi ký tự',
        'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
        'description.required' => 'Mô tả không được để trống',
        'price.required' => 'Giá không được để trống',
        'price.numeric' => 'Giá phải là số',
        'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
        'duration_days.required' => 'Số ngày sử dụng không được để trống',
        'duration_days.integer' => 'Số ngày sử dụng phải là số nguyên',
        'duration_days.min' => 'Số ngày sử dụng phải lớn hơn 0',
        'features.required' => 'Tính năng không được để trống',
        'features.max' => 'Tính năng không được vượt quá 2000 ký tự',
    ]);

    // Nếu slug trống, tự sinh từ tên
    $slug = $validated['slug'] ?: Str::slug($validated['name']);
    $originalSlug = $slug;
    $counter = 1;

    // Kiểm tra trùng với các bản ghi khác
    while (ServicePackage::where('slug', $slug)
        ->where('id', '!=', $package->id)
        ->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    $validated['slug'] = $slug;

    $package->update($validated);

    return redirect()->route('admin.service-packages.index')
                     ->with('success', 'Cập nhật gói dịch vụ thành công');
}

    // Xóa gói
    public function destroy(ServicePackage $package)
    {
        try {
            $package->delete();
            return redirect()->route('admin.service-packages.index')
                ->with('success', 'Xóa gói dịch vụ thành công');
        } catch (\Exception $e) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Không thể xóa gói: ' . $e->getMessage());
        }
    }

    // Bật / Tắt gói
    public function toggle(ServicePackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Cập nhật trạng thái thành công');
    }
    
}
