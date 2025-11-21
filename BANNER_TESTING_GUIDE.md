# 🧪 Hướng Dẫn Test Banner - Nhanh & Đơn Giản

## ✅ Test Cơ Bản (5 phút)

### Bước 1: Chạy Server
```bash
php artisan serve
```

### Bước 2: Truy Cập Admin
```
http://localhost:8000/admin/banners
```

### Bước 3: Kiểm Tra
- ✅ Thấy danh sách banner
- ✅ Hình ảnh hiển thị
- ✅ Badge trạng thái (xanh = hoạt động, đỏ = tắt)
- ✅ Nút "Chỉnh sửa", "Bật/Tắt", "Xóa" hoạt động

## 🎯 Test Chi Tiết

### 1. Test CRUD

**Tạo Banner:**
1. Click "Tạo Banner Mới"
2. Điền form:
   - Tiêu đề: "Test Banner"
   - Mô tả: "Đây là banner test"
   - Upload ảnh bất kỳ
   - Chọn loại: Hero Banner
   - Bật "Kích hoạt ngay"
3. Click "Tạo Banner"
4. ✅ Kiểm tra: Banner mới xuất hiện trong danh sách

**Sửa Banner:**
1. Click "Chỉnh sửa" banner bất kỳ
2. Thay đổi tiêu đề
3. Click "Cập nhật Banner"
4. ✅ Kiểm tra: Thay đổi được lưu

**Xóa Banner:**
1. Click "Xóa" banner test
2. Xác nhận xóa
3. ✅ Kiểm tra: Banner biến mất

### 2. Test Bật/Tắt

1. Click nút "Tắt" trên banner đang hoạt động
2. ✅ Kiểm tra:
   - Badge đổi từ xanh → đỏ
   - Text đổi "Hoạt động" → "Đã tắt"
   - Nút đổi "Tắt" → "Bật"

### 3. Test Filter

**Tìm kiếm:**
1. Nhập tên banner vào ô tìm kiếm
2. Click "Tìm"
3. ✅ Chỉ hiển thị banner phù hợp

**Lọc theo loại:**
1. Chọn "Hero Banner" trong dropdown
2. Click "Tìm"
3. ✅ Chỉ hiển thị hero banner

**Lọc theo trạng thái:**
1. Chọn "Đang hoạt động"
2. Click "Tìm"
3. ✅ Chỉ hiển thị banner active

### 4. Test Upload Ảnh

1. Vào "Tạo Banner Mới"
2. Click chọn file ảnh
3. ✅ Kiểm tra:
   - Preview ảnh hiển thị ngay
   - Có thể upload jpg, png, gif, webp
   - File size < 2MB

## 🚀 Test Nâng Cao

### Test API Endpoint

**PowerShell:**
```powershell
# Lấy danh sách banner active
Invoke-RestMethod -Uri "http://localhost:8000/api/banners" -Method GET | ConvertTo-Json
```

**Kết quả mong đợi:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Tìm Công Việc Mơ Ước",
      "image_url": "/storage/banners/hero-banner.jpg",
      "type": "hero"
    }
  ]
}
```

### Test Database

```bash
php artisan tinker
```

```php
// Lấy tất cả banner
\App\Models\Banner::all();

// Lấy banner active
\App\Models\Banner::active()->get();

// Đếm số banner
\App\Models\Banner::count();
```

## ✅ Checklist Hoàn Chỉnh

### Admin Panel
- [ ] Truy cập `/admin/banners` thành công
- [ ] Danh sách banner hiển thị
- [ ] Hình ảnh hiển thị đúng
- [ ] Tạo banner mới thành công
- [ ] Upload ảnh thành công
- [ ] Chỉnh sửa banner thành công
- [ ] Bật/tắt banner thành công
- [ ] Xóa banner thành công
- [ ] Filter hoạt động
- [ ] Pagination hoạt động

### File System
- [ ] `storage/app/public/banners/` tồn tại
- [ ] Symbolic link hoạt động: `php artisan storage:link`
- [ ] Truy cập `/storage/banners/[file]` hiển thị ảnh

### Database
- [ ] Bảng `banners` có dữ liệu
- [ ] Seeder chạy thành công
- [ ] Dữ liệu lưu đúng format

## 🐛 Lỗi Thường Gặp

### Không thấy hình ảnh
```bash
php artisan storage:link
```

### Lỗi 404 khi upload
Kiểm tra quyền thư mục:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Lỗi database
```bash
php artisan migrate:fresh
php artisan db:seed --class=BannerSeeder
```

## 🎉 Kết Luận

Nếu tất cả checklist đều ✅, banner đã hoạt động hoàn hảo!

**Bước tiếp theo:** Tạo component hiển thị banner trên trang chủ để user thấy được.
