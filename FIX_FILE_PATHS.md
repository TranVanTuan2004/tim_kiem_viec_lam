# Hướng dẫn di chuyển files Banner & Homepage

## Vấn đề

Các file Vue đã được tạo ở sai thư mục:
- ❌ `resources/js/Pages/admin/banners/` (chữ P hoa - SAI)
- ✅ `resources/js/pages/admin/banners/` (chữ p thường - ĐÚNG)

## Cách khắc phục

### Bước 1: Tạo thư mục mới

Trong VS Code, tạo 2 thư mục:
```
resources/js/pages/admin/banners/
resources/js/pages/admin/homepage/
```

### Bước 2: Di chuyển files

**Banner files** (3 files):
- Copy `Index.vue`, `Create.vue`, `Edit.vue` 
- Từ: `resources/js/Pages/admin/banners/`
- Đến: `resources/js/pages/admin/banners/`

**Homepage file** (1 file):
- Copy `Index.vue`
- Từ: `resources/js/Pages/admin/homepage/`
- Đến: `resources/js/pages/admin/homepage/`

### Bước 3: Xóa thư mục cũ

Xóa:
- `resources/js/Pages/admin/banners/`
- `resources/js/Pages/admin/homepage/`

### Bước 4: Kiểm tra

Refresh trang `/admin/banners` - sẽ hoạt động!

## Hoặc dùng lệnh PowerShell

```powershell
# Tạo thư mục
New-Item -ItemType Directory -Path "resources\js\pages\admin\banners" -Force
New-Item -ItemType Directory -Path "resources\js\pages\admin\homepage" -Force

# Copy files
Copy-Item "resources\js\Pages\admin\banners\*" "resources\js\pages\admin\banners\" -Recurse
Copy-Item "resources\js\Pages\admin\homepage\*" "resources\js\pages\admin\homepage\" -Recurse

# Xóa thư mục cũ
Remove-Item "resources\js\Pages\admin\banners" -Recurse -Force
Remove-Item "resources\js\Pages\admin\homepage" -Recurse -Force
```
