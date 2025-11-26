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

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $packages = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return inertia('admin/service-package-pay/Index', [
            'packages' => [
                'data' => $packages->items(),
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'total' => $packages->total(),
            ],
            'filters' => $request->only(['search', 'status']),
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:service_packages,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'features' => 'nullable|string',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!isset($data['description'])) {
            $data['description'] = '';
        }

        if (!isset($data['features'])) {
            $data['features'] = '';
        }

        ServicePackage::create($data);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Tạo gói dịch vụ thành công');
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:service_packages,slug,{$package->id}",
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'features' => 'nullable|string',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!isset($data['description'])) {
            $data['description'] = $package->description ?? '';
        }

        if (!isset($data['features'])) {
            $data['features'] = $package->features ?? '';
        }

        $package->update($data);

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
