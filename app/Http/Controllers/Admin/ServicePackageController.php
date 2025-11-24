<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServicePackageController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách gói dịch vụ với filter / sort
        $query = ServicePackage::query();

        // Search theo tên gói
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        if ($request->sort) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'duration_asc':
                    $query->orderBy('duration_days', 'asc');
                    break;
                case 'duration_desc':
                    $query->orderBy('duration_days', 'desc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc'); // mặc định
        }

        $packages = $query->get();

        // Gói hiện tại (ví dụ lấy gói mới nhất)
        $currentSubscription = Subscription::with('package')
            ->latest()
            ->first();

        // Lịch sử thanh toán
        $paymentHistory = Payment::with('package')
            ->latest()
            ->get();

        return Inertia::render('admin/service-package-pay/Index', [
            'packages' => $packages,
            'currentSubscription' => $currentSubscription ? $currentSubscription->package : null,
            'paymentHistory' => $paymentHistory,
        ]);
    }

    public function create()
{
    // Nếu cần danh sách gói để chọn nâng cấp, truyền packages
    $packages = ServicePackage::all();
    return Inertia::render('admin/service-package-pay/Create', [
        'packages' => $packages,
    ]);
}


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'duration_days' => 'required|integer',
        'max_jobs' => 'nullable|integer',
    ]);

    ServicePackage::create($request->all());

    return redirect()->route('admin.service-packages.index')->with('success', 'Thêm gói dịch vụ thành công');
}

public function edit(ServicePackage $package)
{
    return Inertia::render('admin/service-package-pay/Edit', [
        'package' => $package
    ]);
}

public function update(Request $request, ServicePackage $package)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'duration_days' => 'required|integer',
        'max_jobs' => 'nullable|integer',
    ]);

    $package->update($request->all());

    return redirect()->route('admin.service-packages.index')->with('success', 'Cập nhật gói dịch vụ thành công');
}

public function destroy(ServicePackage $package)
{
    $package->delete();
    return redirect()->route('admin.service-packages.index')->with('success', 'Xóa gói dịch vụ thành công');
}

public function toggleActive(ServicePackage $package)
{
    $package->is_active = !$package->is_active;
    $package->save();

    return redirect()->route('admin.service-packages.index')->with('success', 'Cập nhật trạng thái thành công');
}

    // ... các method create, store, edit, update, destroy, toggleActive như mình viết trước
}
