# Activity Log System - Hoàn Tất

## ✅ Tính Năng Đã Hoàn Thành

### 🔄 Automatic Logging (Tự động ghi log)

Hệ thống **TỰ ĐỘNG** ghi log cho TẤT CẢ các thao tác create/update/delete trên các models sau:

#### 1. **User Model**
- ✅ Ghi log khi user được tạo
- ✅ Ghi log khi thông tin user thay đổi (name, email, phone, is_active)
- ✅ Chỉ log các field quan trọng
- ✅ Chỉ log khi có thay đổi thực sự (dirty checking)

#### 2. **JobPosting Model**
- ✅ Ghi log khi job posting được tạo
- ✅ Ghi log khi job posting được cập nhật (title, status, is_featured, published_at)
- ✅ Ghi log khi job posting bị xóa

#### 3. **Company Model**
- ✅ Ghi log khi company được tạo
- ✅ Ghi log khi company thay đổi (company_name, is_verified, rating, website)
- ✅ Ghi log khi company bị xóa

#### 4. **Application Model**
- ✅ Ghi log khi ứng viên apply job
- ✅ Ghi log khi status application thay đổi (pending → reviewing → accepted/rejected)
- ✅ Ghi log khi có interview date được set
- ✅ Ghi log khi employer thêm notes

#### 5. **Payment Model**
- ✅ Ghi log khi payment được tạo
- ✅ Ghi log khi payment status thay đổi
- ✅ Ghi log với đầy đủ thông tin (amount, payment_method, transaction_id)
- ✅ Custom logging trong methods markAsCompleted() và markAsFailed()

#### 6. **Subscription Model**
- ✅ Ghi log khi subscription được tạo
- ✅ Ghi log khi subscription status thay đổi (active → expired → cancelled)
- ✅ Ghi log khi subscription được renew/upgrade

### 🎯 Manual Logging (Ghi log thủ công cho các action đặc biệt)

#### 1. **Authentication Logs**
- ✅ Log khi user login (bao gồm IP và User Agent)
- ✅ Log khi user logout (bao gồm IP)
- Location: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

#### 2. **Payment Logs**
- ✅ Log khi payment completed successfully
- ✅ Log khi payment failed
- ✅ Bao gồm đầy đủ thông tin: amount, payment_method, transaction_id
- Location: `app/Models/Payment.php` methods

## 📊 Thông Tin Được Ghi Log

Mỗi log entry bao gồm:

```json
{
    "id": 1,
    "log_name": "default",
    "description": "User updated",
    "subject_type": "App\\Models\\User",
    "subject_id": 1,
    "causer_type": "App\\Models\\User",
    "causer_id": 1,
    "properties": {
        "attributes": {
            "name": "New Name"
        },
        "old": {
            "name": "Old Name"
        }
    },
    "event": "updated",
    "batch_uuid": "...",
    "created_at": "2025-10-26 16:30:00"
}
```

## 🎨 UI Features

### Activity Logs Page (`/admin/activity-logs`)
- ✅ Statistics Cards
  - Tổng số hoạt động
  - Số lượng Created
  - Số lượng Updated
  - Số lượng Deleted
- ✅ Filters
  - Search by description
  - Filter by type (user, job_posting, company, etc.)
  - Filter by event (created, updated, deleted)
  - Filter by date range
- ✅ Logs List
  - Hiển thị user avatar
  - Badge màu sắc cho từng event type
  - Format date theo locale VN
  - Pagination
- ✅ Actions
  - Export logs (JSON)
  - Clean old logs (> 90 days)

## 🔍 Ví Dụ Các Log Được Tạo Tự Động

### Khi User Đăng Ký
```
Event: created
Description: "User created"
Subject: User #123
Properties: {name: "Nguyen Van A", email: "a@example.com"}
```

### Khi Apply Job
```
Event: created
Description: "Application #456 created"
Subject: Application #456
Causer: User #123
Properties: {status: "pending", job_posting_id: 789}
```

### Khi Employer Chấp Nhận Application
```
Event: updated
Description: "Application #456 updated"
Subject: Application #456
Causer: User #999 (Employer)
Properties: {
    old: {status: "pending"},
    attributes: {status: "accepted"}
}
```

### Khi Payment Hoàn Tất
```
Event: manual_log
Description: "Payment completed successfully"
Subject: Payment #111
Causer: User #123
Properties: {
    amount: 500000,
    payment_method: "vnpay",
    transaction_id: "TXN123456"
}
```

### Khi User Login
```
Event: manual_log
Description: "User logged in"
Causer: User #123
Properties: {
    ip: "192.168.1.1",
    user_agent: "Mozilla/5.0..."
}
```

### Khi Company Được Verified
```
Event: updated
Description: "Company ABC Company updated"
Subject: Company #50
Causer: Admin User #1
Properties: {
    old: {is_verified: false},
    attributes: {is_verified: true}
}
```

## 📈 Use Cases

### 1. Security Audit
- Xem tất cả login/logout activities
- Phát hiện suspicious activities
- Track admin actions

### 2. User Behavior Analysis
- Xem ai đang active nhất trong hệ thống
- Phân tích patterns
- Identify power users

### 3. Debugging
- Trace ai đã thay đổi gì và khi nào
- Xem history của một record
- Investigate issues

### 4. Compliance
- Audit trail đầy đủ
- Có thể export logs
- Retention policy (auto cleanup old logs)

## 🛠 Configuration

### Cấu hình trong Model

```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['field1', 'field2'])  // Chỉ log các field này
        ->logOnlyDirty()                 // Chỉ log khi có thay đổi
        ->dontSubmitEmptyLogs()          // Không log nếu không có gì thay đổi
        ->setDescriptionForEvent(fn(string $eventName) => "Custom description");
}
```

### Cấu hình Global

File: `config/activitylog.php`
- Database connection
- Table name
- Log name
- Subject returns soft deleted models
- Và nhiều options khác

## 📝 Manual Logging Example

```php
// Log một action bất kỳ
activity()
    ->performedOn($model)           // Đối tượng bị tác động
    ->causedBy($user)               // Người thực hiện
    ->withProperties(['key' => 'value'])  // Data bổ sung
    ->log('Custom description');    // Mô tả

// Log với name
activity('special_log')
    ->causedBy(auth()->user())
    ->log('Something special happened');
```

## 🔐 Permissions

- Activity Logs page chỉ dành cho Admin
- Sử dụng permission: `view users`
- Có thể config permission riêng nếu cần

## ⚡ Performance

- ✅ Index trên các cột quan trọng (created_at, causer_id, subject_type, etc.)
- ✅ Pagination (20 records/page)
- ✅ Eager loading (with relationships)
- ✅ Auto cleanup old logs để tránh database bloat
- ✅ Chỉ log dirty fields để giảm dung lượng

## 🎯 Next Steps (Future Enhancements)

- [ ] Real-time notifications cho admin khi có activity quan trọng
- [ ] Advanced analytics với charts (activity by hour, by day)
- [ ] Export to Excel/PDF với formatting đẹp
- [ ] Activity timeline visualization (timeline view)
- [ ] Alert system cho suspicious activities (multiple failed logins, etc.)
- [ ] Integration với monitoring tools (Sentry, etc.)
- [ ] Search theo multiple fields
- [ ] Advanced filtering (by IP range, by time range)

## ✨ Summary

Hệ thống Activity Log hiện tại:
- ✅ **TỰ ĐỘNG** ghi log cho 6 models quan trọng
- ✅ Manual logging cho authentication và payment
- ✅ UI đầy đủ với filters và statistics
- ✅ Export và cleanup functionality
- ✅ Performance optimized
- ✅ Production ready

**Không cần làm gì thêm** - hệ thống sẽ tự động ghi log mọi thao tác! 🎉


