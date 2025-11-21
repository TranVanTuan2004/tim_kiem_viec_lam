# Hướng dẫn cài đặt - Hệ thống Quản lý Banner & Nội dung Trang chủ

## Bước 1: Chạy Migration

Tạo các bảng `banners` và `homepage_sections` trong database:

```bash
php artisan migrate
```

## Bước 2: Tạo Symbolic Link cho Storage

Để có thể truy cập hình ảnh banner đã upload:

```bash
php artisan storage:link
```

## Bước 3: Seed dữ liệu mẫu (Tùy chọn)

Tạo các homepage section mặc định:

```bash
php artisan db:seed --class=HomepageSectionSeeder
```

## Bước 4: Truy cập trang quản lý

### Quản lý Banner
- URL: `http://your-domain/admin/banners`
- Tạo banner mới: Click "Tạo Banner Mới"

### Quản lý Homepage Sections
- URL: `http://your-domain/admin/homepage`
- Chỉnh sửa section: Click "Chỉnh sửa"

## Lưu ý

- Đảm bảo database đã được cấu hình đúng trong file `.env`
- Thư mục `storage/app/public` phải có quyền ghi
- Hình ảnh banner tối đa 2MB
- Chỉ tài khoản Admin mới có quyền truy cập

## Kiểm tra

Sau khi cài đặt, kiểm tra:
1. ✅ Truy cập `/admin/banners` không bị lỗi
2. ✅ Có thể tạo banner mới với upload ảnh
3. ✅ Có thể chỉnh sửa và xóa banner
4. ✅ Truy cập `/admin/homepage` hiển thị danh sách sections
5. ✅ Có thể chỉnh sửa và bật/tắt sections
