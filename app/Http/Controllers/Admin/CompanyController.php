<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CompanyController extends Controller
{
    // Hiển thị danh sách công ty
    public function index(Request $request)
{
    $search = $request->query('search'); // lấy từ URL ?search=

    $companies = Company::with('user')
        ->when($search, fn($q) => $q->where('company_name', 'like', "%{$search}%"))
        ->paginate(10)
        ->withQueryString(); // giữ query string để pagination không mất search

    return Inertia::render('admin/company/Index', [
        'companies' => $companies,
        'users' => User::all(),
        'filters' => ['search' => $search],
    ]);
}

    // Form tạo công ty mới
    public function create()
    {
        return Inertia::render('admin/company/Create', [
            'users' => User::all(),
        ]);
    }

    // Lưu công ty mới
    public function store(Request $request)
{
    // Validate dữ liệu cơ bản
    $validated = $request->validate([
        'company_name' => 'required|string|max:255',
        'province'     => 'required|string|max:255',
        'city'         => 'required|string|max:255',
        'user_id'      => 'required|exists:users,id',
        'description'  => 'required|string|max:1000',
        'industry'     => 'required|string|max:255',
        'is_active'    => 'boolean',
    ], [
        'company_name.required' => 'Tên công ty không được để trống',
        'company_name.string' => 'Tên công ty phải là chuỗi ký tự',
        'company_name.max' => 'Tên công ty không được vượt quá 255 ký tự',
        'province.required' => 'Tỉnh/Thành phố không được để trống',
        'province.string' => 'Tỉnh/Thành phố phải là chuỗi ký tự',
        'province.max' => 'Tỉnh/Thành phố không được vượt quá 255 ký tự',
        'city.required' => 'Quận/Huyện không được để trống',
        'city.string' => 'Quận/Huyện phải là chuỗi ký tự',
        'city.max' => 'Quận/Huyện không được vượt quá 255 ký tự',
        'user_id.required' => 'Hãy chọn người quản lý công ty',
        'user_id.exists' => 'Người dùng không tồn tại',
        'description.required' => 'Mô tả không được để trống',
        'description.string' => 'Mô tả phải là chuỗi ký tự',
        'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
        'industry.required' => 'Ngành nghề không được để trống',
        'industry.string' => 'Ngành nghề phải là chuỗi ký tự',
        'industry.max' => 'Ngành nghề không được vượt quá 255 ký tự',
    ]);

    // Tạo slug từ tên
    $slug = Str::slug(trim($validated['company_name']));

    // Nếu đã tồn tại slug => báo lỗi
    if (\App\Models\Company::where('company_slug', $slug)->exists()) {
        return back()->withErrors(['company_name' => 'Tên công ty đã tồn tại, vui lòng nhập tên khác.'])
                     ->withInput();
    }

    // Xử lý is_active mặc định true
    $validated['is_active'] = $request->boolean('is_active', true);
    $validated['company_slug'] = $slug;

    // Tạo công ty mới
    \App\Models\Company::create($validated);

    return redirect()->route('admin.companies.index')
                     ->with('success', 'Tạo công ty thành công!');
}

    

    // Xem chi tiết công ty
    public function show(Company $company)
{
    $company->load('user'); // load luôn user

    return Inertia::render('admin/company/Show', [
        'company' => $company, // không chuyển thành array
    ]);
}

    // Form sửa công ty
    public function edit(Company $company)
    {
        return Inertia::render('admin/company/Edit', [
            'company' => $company,
            'users' => User::all(),
        ]);
    }

    // Cập nhật công ty
    public function update(Request $request, Company $company)
{
    // Validate dữ liệu cơ bản
    $validated = $request->validate([
        'company_name' => 'required|string|max:255',
        'province'     => 'required|string|max:255',
        'city'         => 'required|string|max:255',
        'user_id'      => 'required|exists:users,id',
        'description'  => 'nullable|string|max:1000',
        'industry'     => 'nullable|string|max:255',
        'is_active'    => 'boolean',
    ], [
        'company_name.required' => 'Tên công ty không được để trống',
        'company_name.string' => 'Tên công ty phải là chuỗi ký tự',
        'company_name.max' => 'Tên công ty không được vượt quá 255 ký tự',
        'province.required' => 'Tỉnh/Thành phố không được để trống',
        'province.string' => 'Tỉnh/Thành phố phải là chuỗi ký tự',
        'province.max' => 'Tỉnh/Thành phố không được vượt quá 255 ký tự',
        'city.required' => 'Quận/Huyện không được để trống',
        'city.string' => 'Quận/Huyện phải là chuỗi ký tự',
        'city.max' => 'Quận/Huyện không được vượt quá 255 ký tự',
        'user_id.required' => 'Hãy chọn người quản lý công ty',
        'user_id.exists' => 'Người dùng không tồn tại',
        'description.string' => 'Mô tả phải là chuỗi ký tự',
        'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
        'industry.string' => 'Ngành nghề phải là chuỗi ký tự',
        'industry.max' => 'Ngành nghề không được vượt quá 255 ký tự',
    ]);

    // Nếu tên công ty đổi => kiểm tra trùng slug
    $newSlug = Str::slug(trim($validated['company_name']));
    if ($company->company_name !== $validated['company_name'] &&
        \App\Models\Company::where('company_slug', $newSlug)
                            ->where('id', '!=', $company->id)
                            ->exists()) {
        return back()->withErrors(['company_name' => 'Tên công ty đã tồn tại, vui lòng nhập tên khác.'])
                     ->withInput();
    }

    // Xử lý is_active
    $validated['is_active'] = $request->boolean('is_active', $company->is_active);
    $validated['company_slug'] = $newSlug;

    // Cập nhật công ty
    $company->update($validated);

    return redirect()->route('admin.companies.index')
                     ->with('success', 'Cập nhật công ty thành công!');
}


    // Xóa công ty
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->back()
            ->with('success', 'Xóa công ty thành công!');
    }
}
