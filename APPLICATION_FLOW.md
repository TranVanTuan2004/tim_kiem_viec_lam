# Application Flow - Hệ thống ứng tuyển việc làm

## 📋 Tổng quan

Hệ thống ứng tuyển được thiết kế để đảm bảo chỉ người dùng đã đăng nhập mới có thể ứng tuyển vào công việc. Sau khi đăng nhập, họ sẽ được chuyển hướng về trang ứng tuyển.

## 🔐 Flow Authentication

### 1. User chưa đăng nhập

```
1. User vào trang chi tiết job → /jobs/{slug}
2. Click nút "Apply Now"
3. System phát hiện user chưa login (middleware auth)
4. Redirect đến /login với intended URL được lưu trong session
5. User nhập credentials và đăng nhập
6. Sau khi login thành công, redirect về intended URL (/jobs/{slug}/apply)
7. Hiển thị form ứng tuyển
```

### 2. User đã đăng nhập

```
1. User vào trang chi tiết job → /jobs/{slug}
2. Click nút "Apply Now"
3. Chuyển thẳng đến trang ứng tuyển → /jobs/{slug}/apply
4. Hiển thị form ứng tuyển
```

## 🗂️ Files đã tạo/sửa đổi

### Backend

#### 1. **ApplicationController** (`app/Http/Controllers/Client/ApplicationController.php`)

- `create()`: Hiển thị form ứng tuyển
    - Kiểm tra user có candidate profile chưa
    - Kiểm tra đã ứng tuyển vị trí này chưa
    - Kiểm tra deadline có hết hạn chưa
    - Render trang JobApplication.vue với dữ liệu job và candidate

- `store()`: Xử lý submit form ứng tuyển
    - Validate input (cover letter, CV file)
    - Upload CV nếu user upload mới
    - Hoặc sử dụng CV từ profile nếu có
    - Lưu application vào database
    - Tăng applications_count của job
    - Redirect về job detail với success message

#### 2. **Routes** (`routes/web.php`)

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/jobs/{job_posting}/apply', [ApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job_posting}/apply', [ApplicationController::class, 'store'])->name('jobs.apply.store');
});
```

#### 3. **Application Model** (`app/Models/Application.php`)

- Fixed fillable: `job_id` → `job_posting_id`

#### 4. **HandleInertiaRequests Middleware** (`app/Http/Middleware/HandleInertiaRequests.php`)

- Thêm flash messages vào shared props:

```php
'flash' => [
    'success' => $request->session()->get('success'),
    'error' => $request->session()->get('error'),
    'info' => $request->session()->get('info'),
]
```

### Frontend

#### 1. **JobApplication.vue** (`resources/js/pages/client/JobApplication.vue`)

Form ứng tuyển với các features:

- Progress indicator (3 bước)
- Hiển thị thông tin job
- Form với 2 fields:
    - **Cover Letter** (optional): Textarea cho thư xin việc
    - **CV Upload**:
        - Hiển thị thông báo nếu đã có CV trong profile
        - Cho phép upload CV mới (PDF, DOC, DOCX, max 5MB)
        - Preview file đã chọn
- Validation errors
- Submit/Cancel buttons
- Tips section

#### 2. **FlashMessage.vue** (`resources/js/components/FlashMessage.vue`)

Component hiển thị toast notifications:

- Hỗ trợ 3 loại: success, error, info
- Auto hide sau 5 giây
- Có nút đóng manual
- Hiển thị ở góc trên bên phải
- Smooth animations

#### 3. **ClientLayout.vue** (`resources/js/layouts/ClientLayout.vue`)

- Thêm FlashMessage component

## 🎯 Các trường hợp xử lý

### ✅ Success Cases

1. **User đăng nhập → Apply → Success**
    - Hiển thị form ứng tuyển
    - Submit thành công
    - Redirect về job detail với message "Ứng tuyển thành công!"

### ⚠️ Edge Cases được xử lý

1. **User chưa có Candidate Profile**
    - Redirect về job detail
    - Error: "Vui lòng hoàn thiện hồ sơ ứng viên của bạn trước khi ứng tuyển."

2. **User đã ứng tuyển vị trí này rồi**
    - Redirect về job detail
    - Info: "Bạn đã ứng tuyển vào vị trí này rồi. Trạng thái: {status}"

3. **Application deadline đã hết**
    - Redirect về job detail
    - Error: "Hạn nộp đơn ứng tuyển đã kết thúc."

4. **Không có CV**
    - Không cho submit form
    - Error: "Vui lòng tải lên CV của bạn."

## 📝 Validation Rules

### Cover Letter

- Type: string
- Max: 5000 characters
- Optional

### CV File

- Type: file
- MIME types: pdf, doc, docx
- Max size: 5MB (5120KB)
- Optional nếu đã có CV trong profile

## 🚀 How to Test

### Test 1: Apply khi chưa login

```
1. Logout (nếu đang login)
2. Vào /jobs/{any-job-slug}
3. Click "Apply Now"
4. Bạn sẽ bị redirect đến /login
5. Đăng nhập với credentials
6. Sau khi login, tự động redirect đến /jobs/{slug}/apply
7. Xem form ứng tuyển
```

### Test 2: Apply khi đã login

```
1. Login với account có candidate profile
2. Vào /jobs/{any-job-slug}
3. Click "Apply Now"
4. Thấy form ứng tuyển ngay lập tức
5. Điền form và submit
6. Kiểm tra thông báo success
```

### Test 3: Apply lần 2 cho cùng job

```
1. Login và apply cho một job
2. Sau khi thành công, vào lại job detail
3. Click "Apply Now" lần nữa
4. Bạn sẽ bị redirect với message "Bạn đã ứng tuyển vào vị trí này rồi"
```

## 🔜 TODO (Future Enhancements)

- [ ] Send email notification to employer khi có ứng tuyển mới
- [ ] Send confirmation email to candidate
- [ ] Trang quản lý applications cho candidate (My Applications)
- [ ] Trang quản lý applications cho employer (Review Applications)
- [ ] Real-time notifications khi có update về application
- [ ] Application tracking với status updates
- [ ] Interview scheduling

## 📊 Database Schema

### applications table

```sql
- id (bigint)
- job_posting_id (bigint)
- candidate_id (bigint)
- cover_letter (text, nullable)
- cv_file (string, nullable)
- status (enum: pending, reviewing, interview, accepted, rejected)
- notes (text, nullable)
- interview_date (timestamp, nullable)
- created_at
- updated_at

Indexes:
- Unique: [job_posting_id, candidate_id]
- Index: [status, created_at]
- Index: [candidate_id, status]
- Index: job_posting_id
- Index: candidate_id
```

## 💡 Tips

1. **User Experience**: Flow authentication này đảm bảo user không bị mất context khi bị yêu cầu đăng nhập
2. **Security**: Middleware auth bảo vệ route, không cho phép bypass
3. **Data Integrity**: Unique constraint đảm bảo không apply trùng
4. **Feedback**: Flash messages giúp user biết được kết quả của actions
