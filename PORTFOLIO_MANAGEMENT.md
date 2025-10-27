# Portfolio & Personal Project Management

Tài liệu hướng dẫn sử dụng chức năng Quản lý Portfolio & Dự án cá nhân cho ứng viên.

## Tổng quan

Chức năng Portfolio Management cho phép ứng viên:

- Tạo và quản lý các dự án cá nhân
- Upload hình ảnh minh họa cho dự án
- Thêm công nghệ sử dụng trong dự án
- Liên kết với GitHub, Demo, Website
- Đánh dấu dự án nổi bật (Featured)
- Kiểm soát quyền riêng tư (Public/Private)
- Sắp xếp thứ tự hiển thị

## Cấu trúc Database

### Bảng `portfolios`

```sql
- id (primary key)
- candidate_id (foreign key -> candidate_profiles)
- title (string) - Tên dự án
- description (text) - Mô tả chi tiết
- project_url (string) - Link website
- github_url (string) - Link GitHub
- demo_url (string) - Link demo
- images (json) - Mảng đường dẫn hình ảnh
- technologies (json) - Mảng công nghệ sử dụng
- start_date (date) - Ngày bắt đầu
- end_date (date) - Ngày kết thúc
- is_ongoing (boolean) - Dự án đang thực hiện
- is_featured (boolean) - Dự án nổi bật
- display_order (integer) - Thứ tự hiển thị
- is_public (boolean) - Hiển thị công khai
- created_at, updated_at, deleted_at
```

## Backend Components

### 1. Model: `App\Models\Portfolio`

**Location:** `app/Models/Portfolio.php`

**Relationships:**

- `candidate()` - BelongsTo CandidateProfile

**Scopes:**

- `public()` - Lọc portfolio công khai
- `featured()` - Lọc portfolio nổi bật
- `ongoing()` - Lọc dự án đang thực hiện
- `completed()` - Lọc dự án đã hoàn thành
- `ordered()` - Sắp xếp theo display_order và created_at
- `byCandidate($candidateId)` - Lọc theo candidate

**Helper Methods:**

- `getDuration()` - Lấy chuỗi thời gian thực hiện
- `getMainImage()` - Lấy ảnh chính (ảnh đầu tiên)
- `hasImages()` - Kiểm tra có ảnh hay không
- `getTechnologyList()` - Lấy danh sách công nghệ

### 2. Service: `App\Services\PortfolioService`

**Location:** `app/Services/PortfolioService.php`

**Methods:**

#### Quản lý Portfolio

- `getCandidatePortfolios($candidateId, $perPage)` - Lấy danh sách portfolio
- `getPublicPortfolios($candidateId, $perPage)` - Lấy portfolio công khai
- `getFeaturedPortfolios($candidateId, $limit)` - Lấy portfolio nổi bật
- `getPortfolio($portfolioId)` - Lấy chi tiết portfolio

#### CRUD Operations

- `createPortfolio($candidateId, $data)` - Tạo portfolio mới
- `updatePortfolio($portfolioId, $data)` - Cập nhật portfolio
- `deletePortfolio($portfolioId)` - Xóa portfolio

#### Tính năng nâng cao

- `reorderPortfolios($candidateId, $portfolioIds)` - Sắp xếp lại thứ tự
- `toggleFeatured($portfolioId)` - Bật/tắt featured
- `togglePublic($portfolioId)` - Bật/tắt công khai
- `transformForResponse($portfolio)` - Transform dữ liệu cho API
- `userOwnsPortfolio($userId, $portfolioId)` - Kiểm tra quyền sở hữu

### 3. Controller: `App\Http\Controllers\Candidate\PortfolioController`

**Location:** `app/Http/Controllers/Candidate/PortfolioController.php`

**Routes:**

```php
GET    /candidate/portfolios              -> index()
GET    /candidate/portfolios/create       -> create()
POST   /candidate/portfolios              -> store()
GET    /candidate/portfolios/{id}         -> show()
GET    /candidate/portfolios/{id}/edit    -> edit()
PUT    /candidate/portfolios/{id}         -> update()
DELETE /candidate/portfolios/{id}         -> destroy()
POST   /candidate/portfolios/reorder      -> reorder()
POST   /candidate/portfolios/{id}/toggle-featured -> toggleFeatured()
POST   /candidate/portfolios/{id}/toggle-public   -> togglePublic()
```

### 4. Request Validation

#### StorePortfolioRequest

**Location:** `app/Http/Requests/Portfolio/StorePortfolioRequest.php`

**Validation Rules:**

- title: required, max:255
- description: nullable, max:5000
- project_url, github_url, demo_url: nullable, url
- images: array, max:10 items, each 5MB max
- technologies: array, max:50 items
- dates: valid dates với logic checks

#### UpdatePortfolioRequest

**Location:** `app/Http/Requests/Portfolio/UpdatePortfolioRequest.php`

Tương tự StorePortfolioRequest nhưng các field là optional.

## Frontend Components (Vue.js)

### 1. Index Page

**Location:** `resources/js/Pages/candidate/Portfolio/Index.vue`

**Features:**

- Grid hiển thị tất cả portfolio
- Badge hiển thị trạng thái (Featured, Private)
- Dropdown menu với các actions
- Empty state khi chưa có portfolio
- Pagination

### 2. Create Page

**Location:** `resources/js/Pages/candidate/Portfolio/Create.vue`

**Features:**

- Form tạo portfolio mới
- Upload multiple images với preview
- Thêm/xóa technologies
- Date pickers cho timeline
- Checkboxes cho settings

### 3. Edit Page

**Location:** `resources/js/Pages/candidate/Portfolio/Edit.vue`

**Features:**

- Form chỉnh sửa portfolio
- Quản lý ảnh hiện tại và thêm ảnh mới
- Cập nhật technologies
- Cập nhật timeline và settings

### 4. Show Page

**Location:** `resources/js/Pages/candidate/Portfolio/Show.vue`

**Features:**

- Hiển thị chi tiết portfolio
- Gallery ảnh
- Links đến project/github/demo
- Timeline và metadata
- Button chỉnh sửa

## Hướng dẫn sử dụng

### Cài đặt

1. **Chạy migration:**

```bash
php artisan migrate
```

2. **Tạo symbolic link cho storage (nếu chưa có):**

```bash
php artisan storage:link
```

3. **Build frontend assets:**

```bash
npm run build
# hoặc
npm run dev
```

### Sử dụng cho Candidate

#### 1. Tạo Portfolio mới

1. Truy cập `/candidate/portfolios`
2. Click "Thêm dự án mới"
3. Điền thông tin:
    - **Tên dự án** (bắt buộc)
    - **Mô tả** chi tiết về dự án
    - **URLs:** Website, GitHub, Demo
    - **Hình ảnh:** Upload tối đa 10 ảnh (mỗi ảnh max 5MB)
    - **Công nghệ:** Thêm các công nghệ đã sử dụng
    - **Timeline:** Ngày bắt đầu, kết thúc (hoặc đang thực hiện)
    - **Settings:** Featured, Public/Private
4. Click "Tạo dự án"

#### 2. Chỉnh sửa Portfolio

1. Từ danh sách portfolio, click "..." -> "Sửa"
2. Hoặc vào chi tiết portfolio, click "Chỉnh sửa"
3. Cập nhật thông tin cần thiết
4. Click "Cập nhật dự án"

#### 3. Xóa Portfolio

1. Từ danh sách, click "..." -> "Xóa"
2. Xác nhận xóa
3. Portfolio và tất cả ảnh sẽ bị xóa vĩnh viễn

#### 4. Toggle Featured/Public

1. Từ danh sách, click "..." -> "Đặt featured" hoặc "Ẩn/Hiện"
2. Trạng thái sẽ được cập nhật ngay lập tức

### Quyền truy cập

- **Candidate role:** Quản lý portfolio của mình
- **Employer/Admin:** Có thể xem portfolio công khai của candidates
- **Authorization:** Tự động check quyền sở hữu trong UpdatePortfolioRequest

## API Response Format

```json
{
    "id": 1,
    "title": "E-commerce Website",
    "description": "Full-stack e-commerce platform...",
    "project_url": "https://example.com",
    "github_url": "https://github.com/user/repo",
    "demo_url": "https://demo.example.com",
    "images": [
        "http://domain.com/storage/portfolios/image1.jpg",
        "http://domain.com/storage/portfolios/image2.jpg"
    ],
    "main_image": "http://domain.com/storage/portfolios/image1.jpg",
    "technologies": ["Laravel", "Vue.js", "MySQL", "Redis"],
    "start_date": "2024-01-01",
    "end_date": "2024-06-30",
    "duration": "Jan 2024 - Jun 2024",
    "is_ongoing": false,
    "is_featured": true,
    "is_public": true,
    "display_order": 1,
    "created_at": "2024-01-01 00:00:00",
    "updated_at": "2024-06-30 00:00:00"
}
```

## File Upload

### Storage Location

Images được lưu trong: `storage/app/public/portfolios/`

### Access URLs

Sau khi chạy `php artisan storage:link`:

- Physical path: `storage/app/public/portfolios/`
- URL path: `public/storage/portfolios/`

### Validation

- **Max files:** 10 images per portfolio
- **Max size:** 5MB per image
- **Formats:** JPG, JPEG, PNG, WEBP

## Security

### Authorization

- Middleware `role:Candidate` bảo vệ tất cả routes
- `UpdatePortfolioRequest` check ownership
- Controller methods double-check ownership

### File Upload Security

- Validated file types
- Size limits enforced
- Stored outside web root
- Served through Laravel storage

## Best Practices

### Cho Candidates:

1. Upload ảnh chất lượng cao, rõ nét
2. Viết mô tả chi tiết, đầy đủ
3. Thêm đầy đủ technologies
4. Link đến source code (GitHub)
5. Đặt 2-3 dự án tốt nhất là "Featured"
6. Sắp xếp thứ tự hiển thị hợp lý

### Cho Developers:

1. Luôn validate ownership trước khi action
2. Handle file uploads an toàn
3. Cleanup files khi delete portfolio
4. Use transactions cho complex operations
5. Cache public portfolios nếu cần

## Troubleshooting

### Upload ảnh không thành công

- Check `storage/app/public/portfolios` có writable permission
- Đảm bảo đã chạy `php artisan storage:link`
- Kiểm tra `upload_max_filesize` và `post_max_size` trong php.ini

### Ảnh không hiển thị

- Check symbolic link: `ls -la public/storage`
- Verify file tồn tại trong storage
- Check APP_URL trong .env

### Authorization errors

- Đảm bảo user có role "Candidate"
- Check candidate_profile đã được tạo
- Verify ownership trong database

## Future Enhancements

Các tính năng có thể bổ sung:

- [ ] Drag & drop để reorder portfolios
- [ ] Video demos
- [ ] Portfolio templates
- [ ] Public portfolio page cho mỗi candidate
- [ ] Share portfolio via link
- [ ] Portfolio analytics (views, clicks)
- [ ] Export portfolio as PDF
- [ ] Tags/categories cho portfolios

## Support

Nếu gặp vấn đề, vui lòng:

1. Check logs: `storage/logs/laravel.log`
2. Check browser console cho frontend errors
3. Verify database structure
4. Check file permissions
