# Hệ Thống Theo Dõi Hoạt Động (Activity Log System)

## Tổng Quan
Hệ thống Activity Log được xây dựng để theo dõi và ghi lại tất cả các hoạt động trong hệ thống tìm kiếm việc làm. Hệ thống này giúp admin có thể:
- Theo dõi mọi thay đổi trong hệ thống
- Kiểm tra hoạt động của người dùng
- Phân tích các xu hướng sử dụng
- Kiểm tra an ninh và phát hiện các hành vi bất thường
- Audit trail (vết kiểm toán) đầy đủ

## Kiến Trúc

### 1. Package được sử dụng
- **Spatie Laravel Activity Log** v4.10: Package chuyên nghiệp và phổ biến cho việc logging trong Laravel
- Tự động log các event: created, updated, deleted
- Hỗ trợ logging các thuộc tính cụ thể
- Hỗ trợ log nhiều model cùng lúc

### 2. Database
- **Bảng**: `activity_log`
- **Các trường chính**:
  - `log_name`: Tên loại log (user, job_posting, company, etc.)
  - `description`: Mô tả hoạt động
  - `subject_id` & `subject_type`: Đối tượng bị thay đổi
  - `causer_id` & `causer_type`: Người thực hiện hành động
  - `properties`: JSON chứa dữ liệu chi tiết
  - `event`: Loại sự kiện (created, updated, deleted)
  - `batch_uuid`: ID nhóm cho các hoạt động liên quan

### 3. Models với Activity Logging
- **User**: Log các thay đổi thông tin user, activation status
- **JobPosting**: Log các thay đổi về job postings
- Có thể mở rộng thêm cho các model khác

### 4. Service Layer
**ActivityLogService** cung cấp các chức năng:
- `getLogs()`: Lấy danh sách logs với filter
- `getStatistics()`: Lấy thống kê về hoạt động
- `getRecentActivities()`: Lấy các hoạt động gần đây
- `getTopActiveUsers()`: Top user hoạt động nhiều nhất
- `cleanOldLogs()`: Xóa logs cũ
- `exportLogs()`: Xuất logs ra file
- `getActivityTimeline()`: Timeline hoạt động

### 5. Controller
**ActivityLogController** cung cấp các endpoints:
- GET `/admin/activity-logs`: Trang chính với danh sách logs
- GET `/admin/activity-logs/statistics`: API lấy thống kê
- GET `/admin/activity-logs/recent`: API lấy hoạt động gần đây
- GET `/admin/activity-logs/top-users`: API top active users
- GET `/admin/activity-logs/export`: Export logs
- POST `/admin/activity-logs/clean`: Xóa logs cũ

## Features

### 1. Filtering & Search
- Filter theo user
- Filter theo type (log_name)
- Filter theo event (created, updated, deleted)
- Filter theo khoảng thời gian
- Search theo description

### 2. Statistics Dashboard
- Tổng số hoạt động
- Phân loại theo event type
- Thống kê theo type
- Thống kê theo user
- Thống kê theo ngày (30 ngày gần nhất)

### 3. Recent Activities
- Hiển thị 10 hoạt động gần đây nhất
- Kèm thông tin user và đối tượng bị thay đổi

### 4. Top Active Users
- Hiển thị top 10 user có nhiều hoạt động nhất
- Dùng để phân tích usage patterns

### 5. Export & Cleanup
- Export logs ra JSON
- Xóa logs cũ (mặc định > 90 ngày)
- Tránh database quá tải

## Security & Permissions
- Chỉ admin mới có quyền truy cập
- Sử dụng Spatie Permission middleware
- Tất cả các hoạt động đều được log lại

## Performance
- Index trên các cột quan trọng
- Pagination để tránh load quá nhiều dữ liệu
- Caching có thể được thêm vào sau
- Auto cleanup old logs

## Usage Example

### Trong Controller
```php
// Activity log tự động được tạo khi model được update
$user = User::find(1);
$user->update(['name' => 'New Name']);
// => Log sẽ được tạo tự động

// Xem logs
$logs = ActivityLogService::getLogs(['user_id' => 1]);
```

### Manual Logging
```php
activity()
    ->performedOn($model)
    ->causedBy(auth()->user())
    ->withProperties(['key' => 'value'])
    ->log('description');
```

## Migration
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

## Configuration
File: `config/activitylog.php`
- Có thể cấu hình các options khác nhau
- Có thể disable logging cho một số model
- Có thể set log name mặc định

## Best Practices
1. **Log Only Important Changes**: Không log các field ít quan trọng
2. **Use Dirty Checking**: Chỉ log các thay đổi thực sự
3. **Regular Cleanup**: Xóa logs cũ định kỳ
4. **Monitor Performance**: Theo dõi ảnh hưởng đến performance
5. **Backup Strategy**: Có kế hoạch backup logs quan trọng

## Future Enhancements
- [ ] Real-time notifications cho admin
- [ ] Advanced analytics và charts
- [ ] Export to Excel/PDF
- [ ] Activity timeline visualization
- [ ] Alert system cho suspicious activities
- [ ] Integration với monitoring tools
