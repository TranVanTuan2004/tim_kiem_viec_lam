# Khắc phục lỗi 500 - Banner & Homepage Management

## Nguyên nhân

Lỗi 500 xảy ra vì **bảng `banners` và `homepage_sections` chưa tồn tại trong database**.

```
SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'tim-kiem-viec-lam.banners' doesn't exist
```

## Giải pháp

### Bước 1: Kiểm tra MySQL Server

Đảm bảo MySQL/MariaDB đang chạy:

**Nếu dùng XAMPP:**
- Mở XAMPP Control Panel
- Start MySQL service

**Nếu dùng Laragon:**
- Start All services

**Nếu dùng MySQL standalone:**
```bash
# Kiểm tra MySQL service
net start MySQL
```

### Bước 2: Kiểm tra cấu hình Database

Mở file `.env` và kiểm tra các thông tin sau:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tim-kiem-viec-lam
DB_USERNAME=root
DB_PASSWORD=
```

**Lưu ý:** 
- `DB_DATABASE` phải trùng với tên database đã tạo
- `DB_USERNAME` và `DB_PASSWORD` phải đúng

### Bước 3: Tạo Database (nếu chưa có)

Mở phpMyAdmin hoặc MySQL client và chạy:

```sql
CREATE DATABASE IF NOT EXISTS `tim-kiem-viec-lam` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 4: Chạy Migration

Sau khi đã cấu hình database đúng, chạy lệnh:

```bash
php artisan migrate
```

Lệnh này sẽ tạo các bảng:
- ✅ `banners`
- ✅ `homepage_sections`

### Bước 5: (Tùy chọn) Seed dữ liệu mẫu

Tạo các homepage section mặc định:

```bash
php artisan db:seed --class=HomepageSectionSeeder
```

### Bước 6: Tạo Symbolic Link cho Storage

Để upload hình ảnh banner hoạt động:

```bash
php artisan storage:link
```

## Kiểm tra

Sau khi hoàn thành các bước trên:

1. Refresh lại trang `/admin/banners`
2. Trang sẽ hiển thị danh sách banner (trống nếu chưa có dữ liệu)
3. Click "Tạo Banner Mới" để thêm banner đầu tiên

## Nếu vẫn gặp lỗi

### Lỗi: "No connection could be made"

**Nguyên nhân:** MySQL server chưa chạy

**Giải pháp:** Start MySQL service trong XAMPP/Laragon

### Lỗi: "Access denied for user"

**Nguyên nhân:** Username/password không đúng

**Giải pháp:** Kiểm tra lại `DB_USERNAME` và `DB_PASSWORD` trong file `.env`

### Lỗi: "Unknown database"

**Nguyên nhân:** Database chưa được tạo

**Giải pháp:** Tạo database bằng phpMyAdmin hoặc MySQL client

## Kiểm tra nhanh kết nối Database

Chạy lệnh sau để test kết nối:

```bash
php artisan tinker
```

Sau đó trong tinker:

```php
DB::connection()->getPdo();
```

Nếu không có lỗi, kết nối database đã OK!

## Tóm tắt các lệnh cần chạy

```bash
# 1. Chạy migration
php artisan migrate

# 2. Seed dữ liệu mẫu (tùy chọn)
php artisan db:seed --class=HomepageSectionSeeder

# 3. Tạo symbolic link
php artisan storage:link

# 4. Clear cache (nếu cần)
php artisan config:clear
php artisan cache:clear
```

Sau khi chạy xong, truy cập lại `/admin/banners` sẽ hoạt động bình thường!
