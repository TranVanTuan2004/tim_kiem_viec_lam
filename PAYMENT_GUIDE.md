# Hệ thống Payment (Thanh toán)

## 📊 Cấu trúc bảng `payments`

```
payments
├── id
├── user_id → users.id
├── subscription_id → subscriptions.id (nullable)
├── package_id → service_packages.id
├── payment_method (credit_card, bank_transfer, momo, vnpay, zalopay, cash)
├── transaction_id (unique, từ payment gateway)
├── amount (số tiền)
├── currency (VND, USD, etc.)
├── status (pending, processing, completed, failed, refunded)
├── payment_details (JSON data từ gateway)
├── notes
├── paid_at
├── timestamps
└── deleted_at (soft delete)
```

## 🎯 Flow thanh toán

### 1. User chọn gói dịch vụ
```php
$package = ServicePackage::find(1); // Gói Premium
```

### 2. Tạo payment record
```php
$payment = Payment::create([
    'user_id' => auth()->id(),
    'package_id' => $package->id,
    'payment_method' => Payment::METHOD_VNPAY,
    'amount' => $package->price,
    'currency' => 'VND',
    'status' => Payment::STATUS_PENDING,
]);
```

### 3. Redirect đến payment gateway
```php
// VNPay, MoMo, ZaloPay, etc.
return redirect()->to($paymentGatewayUrl);
```

### 4. Callback từ payment gateway
```php
// Khi thanh toán thành công
$payment->markAsCompleted();

// Tạo subscription
$subscription = Subscription::create([
    'company_id' => $user->company->id,
    'package_id' => $payment->package_id,
    'status' => 'active',
    'starts_at' => now(),
    'expires_at' => now()->addDays($package->duration_days),
]);

// Cập nhật subscription_id vào payment
$payment->update(['subscription_id' => $subscription->id]);
```

## 💳 Payment Methods hỗ trợ

```php
Payment::METHOD_CREDIT_CARD    // Thẻ tín dụng
Payment::METHOD_BANK_TRANSFER  // Chuyển khoản ngân hàng
Payment::METHOD_MOMO          // Ví MoMo
Payment::METHOD_VNPAY         // VNPay
Payment::METHOD_ZALOPAY       // ZaloPay
Payment::METHOD_CASH          // Tiền mặt
```

## 📈 Payment Status

```php
Payment::STATUS_PENDING     // Chờ thanh toán
Payment::STATUS_PROCESSING  // Đang xử lý
Payment::STATUS_COMPLETED   // Đã thanh toán
Payment::STATUS_FAILED      // Thất bại
Payment::STATUS_REFUNDED    // Đã hoàn tiền
```

## 🔍 Query Examples

```php
// Lấy tất cả payments của user
$userPayments = auth()->user()->payments;

// Lấy payments đã hoàn thành
$completedPayments = Payment::completed()->get();

// Lấy payments theo phương thức
$momoPayments = Payment::byPaymentMethod('momo')->get();

// Lấy payments của 1 subscription
$subscription->payments;

// Tổng doanh thu
$totalRevenue = Payment::completed()->sum('amount');

// Doanh thu theo tháng
$monthlyRevenue = Payment::completed()
    ->whereMonth('paid_at', now()->month)
    ->sum('amount');
```

## 🛠️ Helper Methods

```php
$payment = Payment::find(1);

// Check status
$payment->isPending();
$payment->isCompleted();
$payment->isFailed();
$payment->isRefunded();

// Update status
$payment->markAsCompleted();
$payment->markAsFailed();
$payment->markAsRefunded();

// Get labels
$payment->getStatusLabel();        // "Đã thanh toán"
$payment->getPaymentMethodLabel(); // "Ví MoMo"
$payment->getFormattedAmount();    // "500,000 VND"
```

## 📊 Relationships

```php
// Payment → User
$payment->user;

// Payment → Subscription
$payment->subscription;

// Payment → Package
$payment->package;

// User → Payments
$user->payments;

// Subscription → Payments
$subscription->payments;

// Package → Payments
$package->payments;
```

## 🔐 Security Notes

1. **NEVER** lưu thông tin thẻ tín dụng vào database
2. Sử dụng HTTPS cho tất cả payment endpoints
3. Validate callback từ payment gateway (signature, hash)
4. Log tất cả payment transactions
5. Implement rate limiting cho payment endpoints

## 📝 Example Controller

```php
class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $package = ServicePackage::findOrFail($request->package_id);
        
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'package_id' => $package->id,
            'payment_method' => $request->payment_method,
            'amount' => $package->price,
            'currency' => 'VND',
            'status' => Payment::STATUS_PENDING,
        ]);
        
        // Redirect to payment gateway
        return $this->redirectToPaymentGateway($payment);
    }
    
    public function handleCallback(Request $request)
    {
        // Validate callback từ gateway
        if (!$this->validateCallback($request)) {
            abort(403);
        }
        
        $payment = Payment::where('transaction_id', $request->transaction_id)->first();
        
        if ($request->status === 'success') {
            $payment->markAsCompleted();
            
            // Tạo subscription
            $this->createSubscription($payment);
            
            return redirect()->route('payment.success');
        }
        
        $payment->markAsFailed();
        return redirect()->route('payment.failed');
    }
}
