# 🎨 Hướng Dẫn Áp Dụng Banner Vào Trang Chủ

## ✅ Đã Hoàn Thành!

Tôi đã tích hợp banner vào trang chủ với các thay đổi sau:

### 📝 Files Đã Cập Nhật

#### 1. **HomeController.php**
```php
// Thêm import
use App\Models\Banner;

// Trong method index()
$banners = Banner::active()
    ->orderBy('order')
    ->get(['id', 'title', 'description', 'image_url', 'link_url', 'button_text', 'type']);

return Inertia::render('client/Home', [
    'banners' => $banners,  // ← Thêm dòng này
    'featuredJobs' => $featuredJobs,
    'topCompanies' => $topCompanies,
]);
```

#### 2. **BannerCarousel.vue** (Component mới)
Tạo component carousel đẹp mắt với:
- ✅ Autoplay (tự động chuyển slide mỗi 5 giây)
- ✅ Navigation arrows (nút trái/phải)
- ✅ Dots indicator (chấm chỉ báo)
- ✅ Smooth transitions (chuyển cảnh mượt mà)
- ✅ Responsive design
- ✅ Gradient overlay theo loại banner
- ✅ Hover effects

#### 3. **client/Home.vue**
```vue
// Thêm import
import BannerCarousel from '@/components/BannerCarousel.vue';

// Thêm prop
banners: {
    type: Array as () => any[],
    default: () => [],
},

// Trong template
<BannerCarousel v-if="props.banners.length > 0" :banners="props.banners" />
<HeroSection v-else />
```

## 🎯 Tính Năng Banner Carousel

### Hiển Thị
- **Hero Banner**: Gradient xanh-tím
- **Promotional**: Gradient cam-đỏ
- **Announcement**: Gradient xanh-cyan

### Tương Tác
- **Autoplay**: Tự động chuyển sau 5 giây
- **Pause on Hover**: Dừng khi hover vào nút
- **Click Navigation**: Click mũi tên hoặc chấm để chuyển
- **Smooth Animation**: Hiệu ứng slide mượt mà

### Responsive
- **Mobile**: Chiều cao 400px
- **Desktop**: Chiều cao 500px
- **Text**: Tự động scale theo màn hình

## 🚀 Cách Test

### Bước 1: Chạy Server
```bash
php artisan serve
npm run dev
```

### Bước 2: Tạo Banner Mẫu (Nếu Chưa Có)
```bash
php artisan db:seed --class=BannerSeeder
```

### Bước 3: Truy Cập Trang Chủ
```
http://localhost:8000
```

### Bước 4: Kiểm Tra
- ✅ Banner carousel hiển thị ở đầu trang
- ✅ Tự động chuyển slide sau 5 giây
- ✅ Click mũi tên trái/phải để chuyển
- ✅ Click chấm để nhảy đến slide cụ thể
- ✅ Hover vào nút để tạm dừng autoplay
- ✅ Click nút CTA để chuyển đến link

## 🎨 Tùy Chỉnh

### Thay Đổi Thời Gian Autoplay
```vue
<BannerCarousel 
  :banners="props.banners" 
  :interval="3000"  <!-- 3 giây -->
/>
```

### Tắt Autoplay
```vue
<BannerCarousel 
  :banners="props.banners" 
  :autoplay="false" 
/>
```

### Thay Đổi Chiều Cao
Sửa trong `BannerCarousel.vue`:
```vue
<div class="relative h-[600px] md:h-[700px]">
```

## 📊 Cấu Trúc Hiển Thị

```
Trang Chủ
├── Banner Carousel (nếu có banner)
│   ├── Slide 1: Hero Banner
│   ├── Slide 2: Promotional
│   └── Slide 3: Announcement
├── Job Search Section
├── Stats Section
├── Featured Jobs
└── Top Companies
```

## 🔧 Quản Lý Banner

### Thêm Banner Mới
1. Vào `/admin/banners`
2. Click "Tạo Banner Mới"
3. Upload ảnh, điền thông tin
4. Bật "Kích hoạt ngay"
5. Lưu → Banner tự động hiện trên trang chủ

### Sắp Xếp Thứ Tự
- Banner hiển thị theo field `order` (ASC)
- Số nhỏ hơn → Hiển thị trước

### Bật/Tắt Banner
- Chỉ banner có `is_active = true` mới hiển thị
- Tắt banner trong admin → Biến mất khỏi trang chủ ngay lập tức

## ✨ Kết Quả

Bây giờ trang chủ của bạn có:
- 🎨 Banner carousel đẹp mắt, chuyên nghiệp
- 🔄 Tự động chuyển slide
- 📱 Responsive trên mọi thiết bị
- ⚡ Hiệu ứng mượt mà
- 🎯 CTA button rõ ràng

Hoàn hảo! 🎉
