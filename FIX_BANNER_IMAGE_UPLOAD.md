# 🔧 Hướng Dẫn Sửa Lỗi Upload Ảnh Banner

## ❌ Vấn Đề

Khi sửa banner và upload ảnh mới, ảnh cũ vẫn được giữ nguyên, ảnh mới không được cập nhật.

## ✅ Nguyên Nhân

Form Edit đang dùng `form.post()` nhưng **chưa bật chế độ FormData** để gửi file.

## 🛠️ Giải Pháp

### Cách 1: Sửa trong Edit.vue (Khuyến nghị)

Mở file `resources/js/pages/admin/banners/Edit.vue`, tìm function `submit()` (khoảng dòng 67-71):

**Trước (Lỗi):**
```typescript
function submit() {
  form.post(`/admin/banners/${props.banner.id}`, {
    preserveScroll: true,
  });
}
```

**Sau (Đúng):**
```typescript
function submit() {
  form.post(`/admin/banners/${props.banner.id}`, {
    preserveScroll: true,
    forceFormData: true,  // ← Thêm dòng này
  });
}
```

### Cách 2: Hard Refresh Browser

Sau khi upload ảnh mới, browser có thể cache ảnh cũ:

**Windows:**
```
Ctrl + Shift + R
hoặc
Ctrl + F5
```

**Mac:**
```
Cmd + Shift + R
```

## 🧪 Cách Test

### Bước 1: Sửa code
Thêm `forceFormData: true` vào function submit()

### Bước 2: Rebuild
```bash
npm run dev
```

### Bước 3: Test upload
1. Vào `/admin/banners`
2. Click "Chỉnh sửa" banner bất kỳ
3. Upload ảnh mới
4. Click "Cập nhật Banner"
5. ✅ Kiểm tra: Ảnh mới đã được cập nhật

### Bước 4: Verify
- Xem trong danh sách banner → Ảnh mới hiển thị
- Xem trên trang chủ → Ảnh mới hiển thị
- Check trong `storage/app/public/banners/` → File mới tồn tại

## 📝 Giải Thích Kỹ Thuật

### Tại sao cần `forceFormData: true`?

**Không có `forceFormData`:**
- Inertia gửi data dạng JSON
- File không được gửi đi
- Backend không nhận được `$request->hasFile('image')`
- Ảnh cũ được giữ nguyên

**Có `forceFormData: true`:**
- Inertia gửi data dạng FormData (multipart/form-data)
- File được gửi kèm theo
- Backend nhận được file qua `$request->hasFile('image')`
- Ảnh cũ bị xóa, ảnh mới được lưu

## 🔍 Debug

Nếu vẫn không work, check:

### 1. Console Browser
```javascript
// Mở DevTools (F12) → Console
// Xem có lỗi gì không
```

### 2. Laravel Log
```bash
tail -f storage/logs/laravel.log
```

### 3. Check File Upload
```php
// Trong BannerController@update, thêm:
dd($request->hasFile('image')); // Phải return true
```

### 4. Check Storage Link
```bash
php artisan storage:link
```

## ✨ Kết Quả

Sau khi sửa:
- ✅ Upload ảnh mới → Ảnh được cập nhật
- ✅ Ảnh cũ tự động bị xóa khỏi storage
- ✅ URL ảnh mới được lưu vào database
- ✅ Hiển thị ảnh mới trên trang chủ

## 💡 Lưu Ý

- File ảnh cũ sẽ bị **xóa vĩnh viễn** khỏi server
- Nếu không upload ảnh mới → Giữ nguyên ảnh cũ
- Ảnh được lưu trong `storage/app/public/banners/`
- URL dạng: `/storage/banners/filename.jpg`

Chúc bạn thành công! 🎉
