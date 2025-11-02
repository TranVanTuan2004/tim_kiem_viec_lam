<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\CompanyUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Hiển thị form cài đặt công ty.
     */
    public function edit()
    {
        // Lấy thông tin công ty liên kết với người dùng đang đăng nhập
        $company = Auth::user()->company; 

        if (!$company) {
            // Trường hợp người dùng chưa liên kết với công ty nào (nên được xử lý ở bước đăng ký)
            // Tạm thời chuyển hướng hoặc trả về lỗi
            return redirect()->route('employer.dashboard')->with('error', 'Bạn chưa liên kết với công ty nào.');
        }

        // Tên component Inertia phải là 'Employer/Company' để khớp với đường dẫn file Vue
        return inertia('Employer/Settings/Company', [ 
            'company' => $company,
        ]);
    }

    /**
     * Xử lý cập nhật thông tin và logo công ty.
     */
    public function update(CompanyUpdateRequest $request)
    {
        $company = Auth::user()->company; 
        
        // Kiểm tra quyền lần nữa, mặc dù FormRequest đã kiểm tra
        if (!$company) {
            abort(403, 'Không tìm thấy công ty để cập nhật hoặc bạn không có quyền.');
        }

        $validated = $request->validated();
        
        // CỘT TRONG DB LÀ 'logo'
        $logoPath = $company->logo; 

        // 1. Xử lý Upload Logo (Sử dụng tên input file 'logo_file')
        if ($request->hasFile('logo_file')) { 
            // Xóa logo cũ nếu tồn tại
            if ($company->logo) { 
                Storage::disk('public')->delete($company->logo);
            }

            // Lưu file mới vào thư mục 'logos' trong public disk
            $logoPath = $request->file('logo_file')->store('logos', 'public');
        } else {
            // Xử lý xóa logo
            if ($request->input('remove_logo')) {
                 if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                 }
                 $logoPath = null; // Gán null cho logo
            }
        }
        
        // 2. Lọc và Cập nhật thông tin (Chỉ cập nhật các trường an toàn)
        $company->update([
            'company_name' => $validated['company_name'],
            'description' => $validated['description'],
            'website' => $validated['website'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'size' => $validated['size'],
            'industry' => $validated['industry'],
            
            // Tự động tạo slug từ tên công ty mới
            'company_slug' => Str::slug($validated['company_name']), 
            
            // Cập nhật đường dẫn logo đã xử lý
            'logo' => $logoPath, 
        ]);

        return redirect()->back()->with('success', 'Thông tin công ty đã được cập nhật thành công!');
    }
}