<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Hiển thị danh sách gói dịch vụ và subscription hiện tại
     */
    public function index(): Response
    {
        $user = Auth::user();
        $company = $user->company;
        
        $packages = ServicePackage::where('is_active', true)->get();
        
        // Lấy subscription hiện tại theo user_id (lấy subscription mới nhất và active)
        // Không phụ thuộc vào company, chỉ cần user_id
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Debug: Log để kiểm tra
        Log::info('Subscription query result', [
            'user_id' => $user->id,
            'found_subscription' => $currentSubscription ? true : false,
            'subscription_id' => $currentSubscription->id ?? null,
            'package_id' => $currentSubscription->package_id ?? null,
        ]);
        
        // Nếu chưa có subscription, tạo Free mặc định
        if (!$currentSubscription) {
            $freePackage = ServicePackage::where('slug', 'free')->first();
            if ($freePackage) {
                $currentSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'package_id' => $freePackage->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($freePackage->duration_days),
                ]);
                
                // Tạo payment record cho gói Free
                Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $currentSubscription->id,
                    'package_id' => $freePackage->id,
                    'payment_method' => 'free',
                    'amount' => 0,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
                
                $currentSubscription->load('package');
            }
        }
        
        // Lấy lịch sử payments
        $paymentHistory = Payment::where('user_id', $user->id)
            ->with(['package', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Đảm bảo currentSubscription có đầy đủ thông tin package
        if ($currentSubscription && !$currentSubscription->relationLoaded('package')) {
            $currentSubscription->load('package');
        }
        
        // Debug: Log subscription info
        if ($currentSubscription) {
            Log::info('Sending subscription to frontend', [
                'subscription_id' => $currentSubscription->id,
                'user_id' => $user->id,
                'package_id' => $currentSubscription->package_id,
                'package_name' => $currentSubscription->package->name ?? 'N/A',
                'package_id_from_relation' => $currentSubscription->package->id ?? 'N/A',
                'status' => $currentSubscription->status,
                'has_package_relation' => $currentSubscription->relationLoaded('package'),
            ]);
        } else {
            Log::info('No subscription found for user', [
                'user_id' => $user->id,
            ]);
        }
        
        // Debug: Log trước khi render
        Log::info('Rendering subscription page', [
            'user_id' => $user->id,
            'has_subscription' => $currentSubscription ? true : false,
            'subscription_data' => $currentSubscription ? [
                'id' => $currentSubscription->id,
                'package_id' => $currentSubscription->package_id,
                'status' => $currentSubscription->status,
            ] : null,
        ]);
        
        return Inertia::render('admin/subscriptions/Index', [
            'packages' => $packages,
            'currentSubscription' => $currentSubscription,
            'paymentHistory' => $paymentHistory,
            'company' => $company,
        ]);
    }
    
    /**
     * Upgrade gói dịch vụ (từ Free lên Premium)
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'payment_method' => 'required|in:vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi nâng cấp gói dịch vụ.');
        }
        
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Lấy subscription hiện tại
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Không tìm thấy gói dịch vụ hiện tại.');
        }
        
        // Kiểm tra xem có đang upgrade không
        if ($package->price <= $currentSubscription->package->price) {
            return redirect()->back()->with('error', 'Chỉ có thể nâng cấp lên gói có giá cao hơn.');
        }
        
        // Xử lý thanh toán VNPay
        if ($request->payment_method === 'vnpay') {
            return $this->processVNPayPayment($user, $company, $package, $currentSubscription);
        }
        
        return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ.');
    }
    
    /**
     * Tạo subscription miễn phí
     */
    private function createFreeSubscription($user, $company, $package)
    {
        DB::beginTransaction();
        
        try {
            // Tạo subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addDays($package->duration_days),
            ]);
            
            // Tạo payment record
            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'package_id' => $package->id,
                'payment_method' => 'free',
                'amount' => 0,
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Đăng ký gói miễn phí thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    
    /**
     * Gia hạn subscription
     */
    public function renew(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi gia hạn gói dịch vụ.');
        }
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ đang hoạt động.');
        }
        
        $package = $currentSubscription->package;
        
        DB::beginTransaction();
        
        try {
            // Gia hạn subscription
            $currentSubscription->update([
                'expires_at' => $currentSubscription->expires_at->addDays($package->duration_days),
            ]);
            
            // Tạo payment record
            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $currentSubscription->id,
                'package_id' => $package->id,
                'payment_method' => $request->payment_method,
                'amount' => $package->price,
                'status' => 'completed',
                'paid_at' => now(),
                'notes' => 'Gia hạn gói ' . $package->name,
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Gia hạn gói dịch vụ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Nâng cấp subscription
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'payment_method' => 'required|in:vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi nâng cấp gói dịch vụ.');
        }
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ đang hoạt động.');
        }
        
        $newPackage = ServicePackage::findOrFail($request->package_id);
        
        if ($newPackage->price <= $currentSubscription->package->price) {
            return redirect()->back()->with('error', 'Gói dịch vụ mới phải có giá cao hơn gói hiện tại.');
        }
        
        // Xử lý thanh toán VNPay
        if ($request->payment_method === 'vnpay') {
            return $this->processVNPayPayment($user, $company, $newPackage, $currentSubscription);
        }
        
        return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ.');
    }
    
    /**
     * Hủy subscription
     */
    public function cancel(): RedirectResponse
    {
        $user = Auth::user();

        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ đang hoạt động.');
        }
        
        try {
            $currentSubscription->update(['status' => 'cancelled']);
            
            return redirect()->back()->with('success', 'Hủy gói dịch vụ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết subscription
     */
    public function show(Subscription $subscription): Response
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if ($subscription->company->user_id !== $user->id) {
            abort(403);
        }
        
        $subscription->load(['package', 'payments']);
        
        return Inertia::render('admin/subscriptions/Show', [
            'subscription' => $subscription,
        ]);
    }
    

    /**
     * Simulate payment callback (for testing)
     */
    public function simulatePayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required',
            'status' => 'required|in:success,failed',
        ]);

        try {
            $user = Auth::user();
            
            // Tìm payment theo ID hoặc transaction_id
            $payment = null;
            
            if (is_numeric($request->payment_id)) {
                // Tìm theo ID
                $payment = Payment::where('user_id', $user->id)
                    ->where('id', $request->payment_id)
                    ->where('status', 'pending')
                    ->first();
            } else {
                // Tìm theo transaction_id (có thể có hoặc không có timestamp)
                $payment = Payment::where('user_id', $user->id)
                    ->where(function($query) use ($request) {
                        $query->where('transaction_id', 'like', $request->payment_id . '%')
                              ->orWhere('transaction_id', $request->payment_id);
                    })
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
            
            // Nếu không tìm thấy theo ID, tìm payment pending gần nhất (fallback)
            if (!$payment) {
                $payment = Payment::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$payment) {
                return redirect()->back()->with('error', 'Không tìm thấy payment để simulate. Vui lòng kiểm tra payment_id hoặc đảm bảo có payment pending.');
            }

            DB::beginTransaction();

            if ($request->status === 'success') {
                // Simulate successful payment
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Upgrade subscription
                $paymentDetails = json_decode($payment->payment_details, true);
                if (isset($paymentDetails['current_subscription_id'])) {
                    $currentSubscription = Subscription::find($paymentDetails['current_subscription_id']);
                    if ($currentSubscription) {
                        $currentSubscription->update([
                            'package_id' => $payment->package_id,
                            'expires_at' => $currentSubscription->expires_at->addDays($payment->package->duration_days),
                        ]);
                        $payment->update(['subscription_id' => $currentSubscription->id]);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Payment simulation thành công! Subscription đã được nâng cấp.');
            } else {
                // Simulate failed payment
                $payment->update(['status' => 'failed']);
                DB::commit();
                return redirect()->back()->with('error', 'Payment simulation thất bại!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý payment thành công (được sử dụng bởi callback)
     */
    private function processSuccessfulPayment($payment)
    {
        $paymentDetails = json_decode($payment->payment_details, true);
        $package = $payment->package;
        $user = $payment->user;
        $company = $user ? $user->company : null;
        
        if (isset($paymentDetails['current_subscription_id'])) {
            // Nâng cấp subscription hiện tại
            $currentSubscription = Subscription::find($paymentDetails['current_subscription_id']);
            if ($currentSubscription && $package) {
                $currentSubscription->update([
                    'package_id' => $package->id,
                    'status' => 'active',
                    'expires_at' => now()->addDays($package->duration_days),
                ]);
                $payment->update(['subscription_id' => $currentSubscription->id]);
            }
        } else {
            // Deactivate tất cả subscription cũ của user này
            Subscription::where('user_id', $payment->user_id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
            
            // Tạo subscription mới cho user này
            if ($package) {
                $subscriptionData = [
                    'user_id' => $payment->user_id,
                    'package_id' => $package->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($package->duration_days),
                ];
                
                
                $subscription = Subscription::create($subscriptionData);
                $payment->update(['subscription_id' => $subscription->id]);
            
            }
        }
    }

    /**
     * Xử lý thanh toán VNPay - Redirect đến VNPay sandbox
     */
    public function vnpayPayment(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:service_packages,id',
                'payment_method' => 'required|in:vnpay',
            ]);
            
            $user = Auth::user();
            $package = ServicePackage::findOrFail($request->package_id);
            
            // Lấy subscription hiện tại (nếu có)
            $currentSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('package')
                ->first();
            
            // Tạo order ID duy nhất
            $vnp_TxnRef = 'ORDER_' . $user->id . '_' . $package->id . '_' . time();
            
            // Tạo payment record trước khi redirect
            $payment = Payment::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_method' => 'vnpay',
                'amount' => $package->price,
                'status' => 'pending',
                'transaction_id' => $vnp_TxnRef,
                'payment_details' => json_encode([
                    'current_subscription_id' => $currentSubscription ? $currentSubscription->id : null,
                ]),
            ]);
            
            // Cấu hình VNPay (sandbox)
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            // Sử dụng route name để đảm bảo URL đúng
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "NVGDTU1Q";
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
            
            // Log URL để debug
            Log::info('VNPay Payment: Creating payment with return URL', [
                'return_url' => $vnp_Returnurl,
                'return_url_full' => url('/admin/subscriptions/vnpay/return'),
                'payment_url' => $vnp_Url,
                'user_id' => $user->id,
                'package_id' => $package->id,
            ]);
            
            $vnp_OrderInfo = $currentSubscription 
                ? "Nang cap tu {$currentSubscription->package->name} len {$package->name}"
                : "Dang ky goi {$package->name}";
            $vnp_OrderType = 'other';
            $vnp_Amount = $package->price * 100; // VNPay tính bằng xu
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip() ?? '127.0.0.1';
            
            $inputData = [
              "vnp_Version" => "2.1.0",
              "vnp_TmnCode" => $vnp_TmnCode,
              "vnp_Amount" => $vnp_Amount,
              "vnp_Command" => "pay",
              "vnp_CreateDate" => date('YmdHis'),
              "vnp_CurrCode" => "VND",
              "vnp_IpAddr" => $vnp_IpAddr,
              "vnp_Locale" => $vnp_Locale,
              "vnp_OrderInfo" => $vnp_OrderInfo,
              "vnp_OrderType" => $vnp_OrderType,
              "vnp_ReturnUrl" => $vnp_Returnurl,
              "vnp_TxnRef" => $vnp_TxnRef,
            ];
        
            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
        
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($inputData);
            
            // Tạo query string
            $queryString = http_build_query($inputData);
            
            // Tạo secure hash
            $vnpSecureHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            // Tạo URL thanh toán
            $paymentUrl = $vnp_Url . "?" . $queryString . '&vnp_SecureHash=' . $vnpSecureHash;
            
            Log::info('VNPay Payment Created', [
                'payment_id' => $payment->id,
                'order_id' => $vnp_TxnRef,
            ]);
            
            // Redirect đến VNPay sandbox
            return redirect()->away($paymentUrl);
            
        } catch (\Exception $e) {
            Log::error('VNPay Payment Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý thanh toán VNPay
     */
    private function processVNPayPayment($user, $company, $package, $currentSubscription)
    {
        try {
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = "http://localhost:8000/admin/subscriptions/vnpay/return";
            $vnp_TmnCode = "NVGDTU1Q";
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";

            $orderId = 'UPGRADE_' . $user->id . '_' . $package->id . '_' . time();
            $orderInfo = "Nang cap tu {$currentSubscription->package->name} len {$package->name} - {$company->company_name}";
            $vnp_Amount = $package->price * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip() ?? '127.0.0.1';
            
            $inputData = array(
              "vnp_Version" => "2.1.0",
              "vnp_TmnCode" => $vnp_TmnCode,
              "vnp_Amount" => $vnp_Amount,
              "vnp_Command" => "pay",
              "vnp_CreateDate" => date('YmdHis'),
              "vnp_CurrCode" => "VND",
              "vnp_IpAddr" => $vnp_IpAddr,
              "vnp_Locale" => $vnp_Locale,
              "vnp_OrderInfo" => $orderInfo,
              "vnp_OrderType" => "other",
              "vnp_ReturnUrl" => $vnp_Returnurl,
              "vnp_TxnRef" => $orderId,
            );
        
            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
        
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($inputData);
            
            // Tạo query string bằng http_build_query
            $queryString = http_build_query($inputData);
            
            // Tạo secure hash từ query string
            $vnpSecureHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            // Thêm secure hash vào query string
            $queryString .= '&vnp_SecureHash=' . $vnpSecureHash;
            
            // Tạo URL thanh toán
            $paymentUrl = $vnp_Url . "?" . $queryString;

            // Lưu thông tin payment
            $payment = Payment::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_method' => 'vnpay',
                'amount' => $package->price,
                'status' => 'pending',
                'transaction_id' => $orderId,
                'payment_details' => json_encode([
                    'vnpay_payment_url' => $paymentUrl,
                    'current_subscription_id' => $currentSubscription->id,
                    'upgrade_type' => 'vnpay',
                    'order_info' => $orderInfo,
                ]),
            ]);

            Log::info('VNPay Payment Created', [
                'payment_id' => $payment->id,
                'payment_url' => $paymentUrl
            ]);

            // Trả về JSON với payment info để hiển thị QR code
            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'payment_url' => $paymentUrl,
                'amount' => $package->price,
                'order_info' => $orderInfo,
            ]);
        } catch (\Exception $e) {
            Log::error('VNPay Payment Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback từ VNPay (IPN - Instant Payment Notification)
     * VNPay sẽ gọi URL này để thông báo kết quả thanh toán
     */
    public function vnpayCallback(Request $request)
    {
        try {
            Log::info('VNPay Callback Received', $request->all());
            
            // Cấu hình VNPay
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
            $secureHash = $request->input('vnp_SecureHash', '');
            
            if (empty($secureHash)) {
                Log::error('VNPay Callback: Missing SecureHash');
                return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 400);
            }
            
            // Tạo bản sao để không ảnh hưởng đến data gốc
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);
            
            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($inputData);
            
            // Tạo query string
            $queryString = http_build_query($inputData);
            
            // Tạo secure hash
            $expectedHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            if (!hash_equals($expectedHash, $secureHash)) {
                Log::error('VNPay Callback: Invalid signature');
                return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature'], 400);
            }
            
            $orderId = $request->input('vnp_TxnRef');
            $responseCode = $request->input('vnp_ResponseCode');
            $amount = $request->input('vnp_Amount') / 100; // VNPay trả về bằng xu
            $transactionNo = $request->input('vnp_TransactionNo');
            
            // Tìm payment
            $payment = Payment::where('transaction_id', $orderId)
                ->where('payment_method', 'vnpay')
                ->where('status', 'pending')
                ->first();
            
            if (!$payment) {
                Log::error('VNPay Callback: Payment not found', ['orderId' => $orderId]);
                return response()->json(['RspCode' => '01', 'Message' => 'Payment not found'], 404);
            }
            
            // Kiểm tra số tiền
            if (abs($payment->amount - $amount) > 0.01) {
                Log::error('VNPay Callback: Amount mismatch', [
                    'expected' => $payment->amount,
                    'received' => $amount
                ]);
                return response()->json(['RspCode' => '04', 'Message' => 'Amount mismatch'], 400);
            }
            
            DB::beginTransaction();
            
            if ($responseCode == '00') {
                // Thanh toán thành công - Tạo subscription và active package
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'payment_details' => json_encode(array_merge(
                        json_decode($payment->payment_details, true) ?? [],
                        [
                            'vnpay_transaction_no' => $transactionNo,
                            'vnpay_response_code' => $responseCode,
                        ]
                    )),
                ]);
                
                // Xử lý subscription
                $this->processSuccessfulPayment($payment);
                
                DB::commit();
                
                Log::info('VNPay Callback: Payment completed successfully', [
                    'payment_id' => $payment->id,
                    'orderId' => $orderId
                ]);
                
                return response()->json(['RspCode' => '00', 'Message' => 'Success']);
            } else {
                // Thanh toán thất bại
                $payment->update([
                    'status' => 'failed',
                    'payment_details' => json_encode(array_merge(
                        json_decode($payment->payment_details, true) ?? [],
                        [
                            'vnpay_response_code' => $responseCode,
                        ]
                    )),
                ]);
                
                DB::commit();
                
                Log::info('VNPay Callback: Payment failed', [
                    'payment_id' => $payment->id,
                    'response_code' => $responseCode
                ]);
                
                return response()->json(['RspCode' => '00', 'Message' => 'Payment failed recorded']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VNPay Callback Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['RspCode' => '99', 'Message' => $e->getMessage()], 500);
        }
    }

    /**
     * Return URL từ VNPay (sau khi người dùng thanh toán xong)
     * Tạo subscription và active package khi thanh toán thành công
     */
    public function vnpayReturn(Request $request)
    {
        // Log ngay đầu hàm để đảm bảo hàm được gọi
        Log::info('=== VNPay Return URL Called ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'all_params' => $request->all(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);
        
        // Debug: Log để xem có vào hàm không
        error_log('VNPay Return called - ' . now()->toDateTimeString());
        
        try {
            
            // Cấu hình VNPay
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
            $secureHash = $request->input('vnp_SecureHash', '');
            
            if (empty($secureHash)) {
                Log::error('VNPay Return: Missing SecureHash');
                return redirect('/admin/subscriptions')->with('error', 'Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ.');
            }
            
            // Tạo bản sao để không ảnh hưởng đến data gốc
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);
            
            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($inputData);
            
            // Tạo query string
            $queryString = http_build_query($inputData);
            
            // Tạo secure hash
            $expectedHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            if (!hash_equals($expectedHash, $secureHash)) {
                Log::error('VNPay Return: Invalid signature');
                return redirect('/admin/subscriptions')->with('error', 'Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ.');
            }
            
            $orderId = $request->input('vnp_TxnRef');
            $responseCode = $request->input('vnp_ResponseCode');
            $responseMessage = $request->input('vnp_ResponseMessage', '');
            
            // Tìm payment
            $payment = Payment::where('transaction_id', $orderId)
                ->where('payment_method', 'vnpay')
                ->where('status', 'pending')
                ->first();
            
            if (!$payment) {
                Log::error('VNPay Return: Payment not found', ['orderId' => $orderId]);
                return redirect('/admin/subscriptions')->with('error', 'Không tìm thấy giao dịch thanh toán.');
            }
            
            if ($responseCode == '00') {
                // Thanh toán thành công - Tạo subscription và active package
                DB::beginTransaction();
                try {
                    // Cập nhật payment
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'payment_details' => json_encode(array_merge(
                            json_decode($payment->payment_details, true) ?? [],
                            [
                                'vnpay_transaction_no' => $request->input('vnp_TransactionNo'),
                                'vnpay_response_code' => $responseCode,
                            ]
                        )),
                    ]);
                    
                    // Lấy thông tin package và user
                    $package = $payment->package;
                    $user = $payment->user;
                    $company = $user->company;
                    
             
                    // Lấy subscription hiện tại (nếu có)
                    $paymentDetails = json_decode($payment->payment_details, true);
                    $currentSubscriptionId = $paymentDetails['current_subscription_id'] ?? null;
                    
                    if ($currentSubscriptionId) {
                        // Nâng cấp subscription hiện tại
                        $currentSubscription = Subscription::find($currentSubscriptionId);
                        if ($currentSubscription) {
                            $currentSubscription->update([
                                'package_id' => $package->id,
                                'status' => 'active',
                                'expires_at' => now()->addDays($package->duration_days),
                            ]);
                            $payment->update(['subscription_id' => $currentSubscription->id]);
                            
                            Log::info('VNPay Return: Subscription upgraded', [
                                'subscription_id' => $currentSubscription->id,
                                'package_id' => $package->id,
                            ]);
                        }
                    } else {
                        // Deactivate tất cả subscription cũ của user này
                        Subscription::where('user_id', $payment->user_id)
                            ->where('status', 'active')
                            ->update(['status' => 'expired']);
                        
                        // Tạo subscription mới cho user này
                        $subscriptionData = [
                            'user_id' => $payment->user_id,
                            'package_id' => $package->id,
                            'status' => 'active',
                            'starts_at' => now(),
                            'expires_at' => now()->addDays($package->duration_days),
                        ];
                        
                        // Thêm company_id nếu user có company
                     
                        
                    
                        $subscription = Subscription::create($subscriptionData);
                        
                        // Cập nhật payment với subscription_id
                        $payment->update(['subscription_id' => $subscription->id]);
                        
                        // Load lại subscription với package để đảm bảo có đầy đủ thông tin
                        $subscription->load('package');
                        
                  
               
                    }
                    
                    DB::commit();
                    
                    Log::info('VNPay Return: Payment completed and subscription activated', [
                        'payment_id' => $payment->id,
                        'package_id' => $package->id,
                    ]);
                    
                    return redirect('/admin/subscriptions')->with('success', 'Thanh toán thành công! Gói dịch vụ ' . $package->name . ' đã được kích hoạt.');
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('VNPay Return: Error processing payment', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id
                    ]);
                    return redirect('/admin/subscriptions')->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
                }
            } else {
                // Thanh toán thất bại
                $payment->update(['status' => 'failed']);
                
                $errorMessages = [
                    '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
                    '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
                    '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần',
                    '11' => 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch.',
                    '12' => 'Thẻ/Tài khoản bị khóa.',
                    '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Quá 3 lần',
                    '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
                    '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
                    '75' => 'Ngân hàng thanh toán đang bảo trì.',
                    '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
                ];
                
                $errorMessage = $errorMessages[$responseCode] ?? $responseMessage ?: 'Thanh toán thất bại. Vui lòng thử lại.';
                
                return redirect('/admin/subscriptions')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('VNPay Return Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/admin/subscriptions')->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Test VNPay payment
     */
    public function testVNPay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        try {
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = "http://localhost:8000/admin/subscriptions/vnpay/return";
            $vnp_TmnCode = "NVGDTU1Q";
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
            
            $orderId = 'TEST_' . time();
            $vnp_Amount = $request->amount * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip() ?? '127.0.0.1';
            
            $inputData = array(
              "vnp_Version" => "2.1.0",
              "vnp_TmnCode" => $vnp_TmnCode,
              "vnp_Amount" => $vnp_Amount,
              "vnp_Command" => "pay",
              "vnp_CreateDate" => date('YmdHis'),
              "vnp_CurrCode" => "VND",
              "vnp_IpAddr" => $vnp_IpAddr,
              "vnp_Locale" => $vnp_Locale,
              "vnp_OrderInfo" => $request->description,
              "vnp_OrderType" => "other",
              "vnp_ReturnUrl" => $vnp_Returnurl,
              "vnp_TxnRef" => $orderId,
            );
        
            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
        
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($inputData);
            
            // Tạo query string bằng http_build_query
            $queryString = http_build_query($inputData);
            
            // Tạo secure hash từ query string
            $vnpSecureHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            // Thêm secure hash vào query string
            $queryString .= '&vnp_SecureHash=' . $vnpSecureHash;
            
            // Tạo URL thanh toán
            $paymentUrl = $vnp_Url . "?" . $queryString;

            // Tạo payment record để có thể simulate
            $user = Auth::user();
            $package = ServicePackage::where('slug', 'premium')->first();
            
            $payment = Payment::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_method' => 'vnpay',
                'amount' => $request->amount,
                'status' => 'pending',
                'transaction_id' => $orderId,
                'payment_details' => json_encode([
                    'vnpay_payment_url' => $paymentUrl,
                    'order_info' => $request->description,
                    'test_mode' => true,
                ]),
            ]);

            return redirect()->back()->with([
                'success' => true,
                'payment_result' => [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'payment_url' => $paymentUrl,
                    'order_id' => $orderId,
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang demo VNPay sandbox
     */
    public function vnpayDemo(): Response
    {
        return Inertia::render('admin/subscriptions/VNPayDemo');
    }

    /**
     * Simulate VNPay payment callback (for testing)
     */
    public function simulateVNPayPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required',
            'status' => 'required|in:success,failed',
        ]);

        try {
            $user = Auth::user();
            
            // Tìm payment pending gần nhất
            $payment = Payment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('payment_method', 'vnpay')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$payment) {
                return redirect()->back()->with('error', 'Không tìm thấy payment để simulate');
            }

            DB::beginTransaction();

            if ($request->status === 'success') {
                // Simulate successful payment
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Upgrade subscription
                $paymentDetails = json_decode($payment->payment_details, true);
                if (isset($paymentDetails['current_subscription_id'])) {
                    $currentSubscription = Subscription::find($paymentDetails['current_subscription_id']);
                    if ($currentSubscription) {
                        $currentSubscription->update([
                            'package_id' => $payment->package_id,
                            'expires_at' => $currentSubscription->expires_at->addDays($payment->package->duration_days),
                        ]);
                        $payment->update(['subscription_id' => $currentSubscription->id]);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'VNPay payment simulation thành công! Subscription đã được nâng cấp.');
            } else {
                // Simulate failed payment
                $payment->update(['status' => 'failed']);
                DB::commit();
                return redirect()->back()->with('error', 'VNPay payment simulation thất bại!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    
}