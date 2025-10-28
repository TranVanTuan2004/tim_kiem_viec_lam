# Service Layer Architecture

## 📐 Kiến trúc Service Layer Pattern

Dự án đã được refactor để áp dụng **Service Layer Pattern** - một best practice trong phát triển ứng dụng Laravel.

### Cấu trúc phân tầng:

```
Request → Controller → Service → Model → Database
              ↓           ↓        ↓
            View  ←  Response ← Data
```

## 🗂️ Cấu trúc thư mục

```
app/
├── Http/Controllers/
│   └── Client/
│       ├── JobPostingController.php    (14 dòng)
│       ├── HomeController.php          (41 dòng)
│       ├── CompanyController.php       (80 dòng)
│       └── ApplicationController.php   (91 dòng)
│
├── Services/
│   ├── JobPostingService.php          (280+ dòng business logic)
│   ├── CompanyService.php             (180+ dòng business logic)
│   ├── ApplicationService.php         (180+ dòng business logic)
│   ├── VNPayPaymentService.php
│   └── ZaloPayPaymentService.php
│
└── Models/
    ├── JobPosting.php                 (với scopes)
    ├── Company.php
    ├── Application.php
    └── ...
```

## ✨ Lợi ích

### 1. **Controllers gọn gàng và tập trung**

**Trước:**

```php
public function index() {
    $query = JobPosting::published()
        ->with(['company', 'skills'])
        ->orderBy('published_at', 'desc');

    if (request('featured')) {
        $query->where('is_featured', true);
    }

    if (request('q')) {
        $keyword = request('q');
        $query->where(function($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    // ... 50+ dòng code nữa
}
```

**Sau:**

```php
public function index() {
    $filters = $this->jobPostingService->validateFilters([
        'featured' => request('featured', false),
        'q' => request('q', ''),
        'location' => request('location', ''),
    ]);

    $jobs = $this->jobPostingService->getFilteredJobs($filters, 12)
        ->through(fn($job) => $this->jobPostingService->transformForListing($job));

    return Inertia::render('client/JobsIndex', [
        'jobs' => $jobs,
        'filters' => $filters,
    ]);
}
```

### 2. **Business Logic tập trung tại Service**

- Validation logic
- Query phức tạp
- Data transformation
- Business rules
- Third-party integrations

### 3. **Model chỉ lo về Data Structure**

- Relationships
- Scopes (query helpers)
- Accessors/Mutators
- Casts

### 4. **Dễ test và maintain**

```php
// Unit test cho Service
public function test_can_filter_jobs_by_keyword() {
    $service = new JobPostingService();
    $jobs = $service->getFilteredJobs(['q' => 'PHP Developer']);

    $this->assertNotEmpty($jobs);
}
```

## 📚 Các Services đã tạo

### 1. **JobPostingService**

**Query Methods:**

- `getFilteredJobs(array $filters, int $perPage)` - Lấy jobs với filters
- `getFeaturedJobs(int $limit)` - Lấy featured jobs
- `getJobDetail(JobPosting $job)` - Chi tiết job + tăng views

**Transform Methods:**

- `transformForListing($job)` - Format cho trang danh sách
- `transformForHome($job)` - Format cho homepage
- `transformForDetail($job)` - Format cho chi tiết
- `transformForCompany($job)` - Format cho trang company
- `transformForApplication($job)` - Format cho form ứng tuyển

**Validation:**

- `validateFilters(array $filters)` - Validate và sanitize filters

### 2. **CompanyService**

**Query Methods:**

- `getFilteredCompanies(array $filters, int $perPage)`
- `getTopCompanies(int $limit)`
- `getCompanyDetail(Company $company)`
- `getRecentJobs(Company $company, int $limit)`
- `getCompanyJobs(Company $company, int $perPage)`
- `getCompanyReviews(Company $company, int $perPage)`
- `getRatingStats(Company $company)`

**Transform Methods:**

- `transformForHome($company)`
- `transformForDetail($company)`
- `transformForJobsPage($company)`

### 3. **ApplicationService**

**Business Logic:**

- `canApply(User $user, JobPosting $job)` - Kiểm tra điều kiện ứng tuyển
- `createApplication()` - Tạo đơn ứng tuyển
- `validateApplicationData(array $data)` - Validate dữ liệu
- `getCandidateProfileData(User $user)` - Lấy thông tin ứng viên
- `sendApplicationNotifications()` - Gửi thông báo

**Helper Methods:**

- `getExistingApplication()` - Kiểm tra đơn đã tồn tại
- `handleCvUpload()` - Xử lý upload CV
- `getApplicationStats()` - Thống kê đơn ứng tuyển

## 🎯 Best Practices được áp dụng

### 1. **Dependency Injection**

```php
class JobPostingController extends Controller
{
    protected JobPostingService $jobPostingService;

    public function __construct(JobPostingService $jobPostingService)
    {
        $this->jobPostingService = $jobPostingService;
    }
}
```

### 2. **Single Responsibility Principle**

- Controller: Routing và Response
- Service: Business Logic
- Model: Data Structure

### 3. **DRY (Don't Repeat Yourself)**

Transform methods có thể được sử dụng nhiều nơi:

```php
// Trong HomeController
$jobs->map(fn($job) => $this->jobPostingService->transformForHome($job));

// Trong JobPostingController
$jobs->through(fn($job) => $this->jobPostingService->transformForListing($job));
```

### 4. **Type Hinting & Return Types**

```php
public function getFilteredJobs(array $filters = [], int $perPage = 12): LengthAwarePaginator
{
    // ...
}
```

### 5. **Validation tập trung**

```php
public function validateFilters(array $filters): array
{
    $validated = [];

    if (isset($filters['q']) && !empty($filters['q'])) {
        $validated['q'] = trim($filters['q']);
    }

    // ... validation logic

    return $validated;
}
```

## 📊 So sánh trước và sau

| Metrics                         | Trước     | Sau                      |
| ------------------------------- | --------- | ------------------------ |
| JobPostingController::index()   | ~70 dòng  | ~18 dòng                 |
| JobPostingController::show()    | ~45 dòng  | ~7 dòng                  |
| HomeController::index()         | ~70 dòng  | ~15 dòng                 |
| CompanyController::show()       | ~90 dòng  | ~20 dòng                 |
| ApplicationController::create() | ~50 dòng  | ~15 dòng                 |
| **Tổng Controllers**            | ~325 dòng | ~75 dòng                 |
| **Business Logic**              | Rải rác   | Tập trung trong Services |

## 🚀 Cách sử dụng

### Thêm Service mới:

1. **Tạo Service class:**

```php
// app/Services/YourService.php
namespace App\Services;

class YourService
{
    public function yourMethod()
    {
        // Business logic here
    }
}
```

2. **Inject vào Controller:**

```php
class YourController extends Controller
{
    protected YourService $yourService;

    public function __construct(YourService $yourService)
    {
        $this->yourService = $yourService;
    }

    public function index()
    {
        $data = $this->yourService->yourMethod();
        return Inertia::render('YourPage', ['data' => $data]);
    }
}
```

### Thêm Scope vào Model:

```php
// app/Models/YourModel.php
public function scopeYourScope($query, $param)
{
    return $query->where('column', $param);
}
```

### Gọi từ Service:

```php
// app/Services/YourService.php
public function getData($param)
{
    return YourModel::yourScope($param)->get();
}
```

## 🧪 Testing

Service Layer giúp testing dễ dàng hơn:

```php
// tests/Unit/Services/JobPostingServiceTest.php
class JobPostingServiceTest extends TestCase
{
    protected JobPostingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JobPostingService();
    }

    public function test_validate_filters_removes_empty_values()
    {
        $input = ['q' => '', 'location' => 'Hanoi'];
        $result = $this->service->validateFilters($input);

        $this->assertArrayNotHasKey('q', $result);
        $this->assertEquals('Hanoi', $result['location']);
    }
}
```

## 📖 Nguyên tắc khi viết code

1. **Controller**: Chỉ lo routing, validation request, và response
2. **Service**: Chứa tất cả business logic
3. **Model**: Chỉ lo data structure, relationships, và helper methods đơn giản
4. **Validation**: Nên đặt trong Service, không phải Controller
5. **Transform**: Nên đặt trong Service để reuse
6. **Query phức tạp**: Sử dụng Scopes trong Model, gọi từ Service

## 🔄 Migration Path

Nếu cần thêm tính năng mới:

1. **Tạo/Update Model** với relationships và scopes
2. **Tạo Service** với business logic
3. **Update Controller** để sử dụng Service
4. **Write Tests** cho Service

## 📝 Notes

- Services không được inject Models trực tiếp (để dễ test)
- Services có thể gọi Services khác nếu cần
- Transform methods nên return plain arrays, không return Models
- Validation nên throw Exception để Controller handle
