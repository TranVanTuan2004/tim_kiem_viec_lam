# Hướng Dẫn Sử Dụng Banner Mẫu

## 📸 Banner Đã Tạo

Tôi đã tạo 3 banner mẫu cho website tuyển dụng của bạn:

### 1. Hero Banner - "Tìm Công Việc Mơ Ước"
![Hero Banner](file:///C:/Users/quynh/.gemini/antigravity/brain/fc9b905c-4e70-4f33-8c63-8a26741c2351/hero_banner_sample_1763735429510.png)

**Thông tin:**
- **Loại:** Hero Banner (Banner chính)
- **Tiêu đề:** Tìm Công Việc Mơ Ước Của Bạn
- **Mô tả:** Hàng nghìn cơ hội việc làm từ các công ty hàng đầu
- **Nút CTA:** Khám Phá Ngay
- **Link:** /jobs

### 2. Promotional Banner - "Khuyến Mãi Đặc Biệt"
![Promotional Banner](file:///C:/Users/quynh/.gemini/antigravity/brain/fc9b905c-4e70-4f33-8c63-8a26741c2351/promotional_banner_sample_1763735451032.png)

**Thông tin:**
- **Loại:** Promotional (Khuyến mãi)
- **Tiêu đề:** Khuyến Mãi Đặc Biệt - Đăng Tin Miễn Phí
- **Mô tả:** Đăng tin tuyển dụng MIỄN PHÍ trong 30 ngày
- **Nút CTA:** Đăng Ký Ngay
- **Link:** /employer/register
- **Thời hạn:** 30 ngày

### 3. Announcement Banner - "Nâng Cấp AI"
![Announcement Banner](file:///C:/Users/quynh/.gemini/antigravity/brain/fc9b905c-4e70-4f33-8c63-8a26741c2351/announcement_banner_sample_1763735469980.png)

**Thông tin:**
- **Loại:** Announcement (Thông báo)
- **Tiêu đề:** Nâng Cấp Tính Năng Mới - AI
- **Mô tả:** Tìm việc thông minh hơn với công nghệ AI
- **Nút CTA:** Tìm Hiểu Thêm
- **Link:** /features/ai-job-matching

## 🚀 Cách Sử Dụng

### Bước 1: Copy Hình Ảnh

1. Tạo thư mục banners trong storage:
```bash
mkdir storage/app/public/banners
```

2. Copy 3 file ảnh đã tạo vào thư mục:
   - `hero_banner_sample_*.png` → `storage/app/public/banners/hero-banner.jpg`
   - `promotional_banner_sample_*.png` → `storage/app/public/banners/promotional-banner.jpg`
   - `announcement_banner_sample_*.png` → `storage/app/public/banners/announcement-banner.jpg`

### Bước 2: Chạy Seeder

```bash
php artisan db:seed --class=BannerSeeder
```

### Bước 3: Kiểm Tra

1. Truy cập `/admin/banners`
2. Bạn sẽ thấy 3 banner mẫu đã được tạo
3. Có thể chỉnh sửa, bật/tắt hoặc xóa theo ý muốn

## 📝 Lưu Ý

- Các hình ảnh đã được tạo ở định dạng PNG
- Bạn có thể convert sang JPG nếu muốn giảm dung lượng
- Đường dẫn trong database đã được cấu hình sẵn
- Tất cả banner đều đang ở trạng thái "Hoạt động"

## 🎨 Tùy Chỉnh

Bạn có thể:
- Thay đổi hình ảnh bằng cách upload ảnh mới
- Chỉnh sửa tiêu đề, mô tả
- Thay đổi link và text nút CTA
- Đặt lịch hiển thị
- Sắp xếp thứ tự hiển thị

Chúc bạn sử dụng thành công! 🎉
