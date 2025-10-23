<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Company;
use App\Services\ZaloPayPaymentService;
use App\Services\VNPayPaymentService;
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
        
        if (!$company) {
            return Inertia::render('admin/subscriptions/Index', [
                'packages' => ServicePackage::where('is_active', true)->get(),
                'currentSubscription' => null,
                'paymentHistory' => [],
                'company' => null,
            ]);
        }
        
        $packages = ServicePackage::where('is_active', true)->get();
        
        // Lấy subscription hiện tại hoặc tạo Free mặc định
            $currentSubscription = Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->with('package')
                ->first();
            
        // Nếu chưa có subscription, tạo Free mặc định
        if (!$currentSubscription) {
            $freePackage = ServicePackage::where('slug', 'free')->first();
            if ($freePackage) {
                $currentSubscription = Subscription::create([
                    'company_id' => $company->id,
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
            'payment_method' => 'required|in:bank_transfer,credit_card,zalopay,vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi nâng cấp gói dịch vụ.');
        }
        
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Lấy subscription hiện tại
        $currentSubscription = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Không tìm thấy gói dịch vụ hiện tại.');
        }
        
        // Kiểm tra xem có đang upgrade không
        if ($package->price <= $currentSubscription->package->price) {
            return redirect()->back()->with('error', 'Chỉ có thể nâng cấp lên gói có giá cao hơn.');
        }
        
        // Xử lý thanh toán cho gói trả phí
        if ($request->payment_method === 'zalopay') {
            return $this->processZaloPayPayment($user, $company, $package, $currentSubscription);
        }
        
        // Các phương thức thanh toán khác
        return $this->createPendingPayment($user, $company, $package, $request->payment_method, $currentSubscription);
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
                'company_id' => $company->id,
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
     * Xử lý thanh toán ZaloPay cho upgrade
     */
    private function processZaloPayPayment($user, $company, $package, $currentSubscription)
    {
        try {
            $zalopayService = new ZaloPayPaymentService();

            $orderId = 'UPGRADE_' . $company->id . '_' . $package->id;
            $orderInfo = "Nang cap tu {$currentSubscription->package->name} len {$package->name} - {$company->company_name}";

            $result = $zalopayService->createPayment(
                $orderId,
                $package->price,
                $orderInfo,
                json_encode([
                    'company_id' => $company->id,
                    'package_id' => $package->id,
                    'user_id' => $user->id,
                    'current_subscription_id' => $currentSubscription->id
                ])
            );

            if ($result['success']) {
                // Lưu thông tin payment
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'payment_method' => 'zalopay',
                    'amount' => $package->price,
                    'status' => 'pending',
                    'transaction_id' => $result['app_trans_id'],
                    'payment_details' => json_encode([
                        'zalopay_order_token' => $result['order_token'],
                        'zalopay_order_url' => $result['order_url'],
                        'zp_trans_token' => $result['zp_trans_token'],
                        'current_subscription_id' => $currentSubscription->id,
                        'upgrade_type' => 'zalopay',
                        'order_info' => $orderInfo,
                    ]),
                ]);

                Log::info('ZaloPay Payment Created', [
                    'payment_id' => $payment->id,
                    'order_url' => $result['order_url']
                ]);

                return redirect()->back()->with('success', 'Đã tạo thanh toán ZaloPay');
            } else {
                return redirect()->back()->with('error', 'Lỗi tạo thanh toán ZaloPay: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Tạo payment pending cho các phương thức khác
     */
    private function createPendingPayment($user, $company, $package, $paymentMethod, $currentSubscription)
    {
        try {
            Payment::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'payment_method' => $paymentMethod,
                'amount' => $package->price,
                'status' => 'pending',
                'notes' => "Nâng cấp từ {$currentSubscription->package->name} lên {$package->name} - " . $this->getPaymentMethodText($paymentMethod),
            ]);
            
            return redirect()->back()->with('success', 'Đã tạo yêu cầu nâng cấp. Vui lòng liên hệ admin để hoàn tất.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    // TODO: Implement MoMo payment service
    /*
    public function momoCallback(Request $request)
    {
        // MoMo callback implementation
    }
    
    public function momoReturn(Request $request)
    {
        // MoMo return implementation
    }
    */
    
    /**
     * Gia hạn subscription
     */
    public function renew(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,credit_card,zalopay,vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi gia hạn gói dịch vụ.');
        }
        
        $currentSubscription = Subscription::where('company_id', $company->id)
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
    public function upgrade(Request $request): RedirectResponse
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'payment_method' => 'required|in:bank_transfer,credit_card,zalopay,vnpay',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi nâng cấp gói dịch vụ.');
        }
        
        $currentSubscription = Subscription::where('company_id', $company->id)
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
        
        // Xử lý thanh toán theo phương thức được chọn
        switch ($request->payment_method) {
            case 'zalopay':
                return $this->processZaloPayPayment($user, $company, $newPackage, $currentSubscription);
            case 'vnpay':
                return $this->processVNPayPayment($user, $company, $newPackage, $currentSubscription);
            case 'bank_transfer':
            case 'credit_card':
            default:
                // Xử lý thanh toán truyền thống (ngay lập tức)
                DB::beginTransaction();
                
                try {
                    // Cập nhật subscription
                    $currentSubscription->update([
                        'package_id' => $newPackage->id,
                        'expires_at' => $currentSubscription->expires_at->addDays($newPackage->duration_days),
                    ]);
                    
                    // Tạo payment record
                    Payment::create([
                        'user_id' => $user->id,
                        'subscription_id' => $currentSubscription->id,
                        'package_id' => $newPackage->id,
                        'payment_method' => $request->payment_method,
                        'amount' => $newPackage->price - $currentSubscription->package->price,
                        'status' => 'completed',
                        'paid_at' => now(),
                        'notes' => 'Nâng cấp từ ' . $currentSubscription->package->name . ' lên ' . $newPackage->name,
                    ]);
                    
                    DB::commit();
                    
                    return redirect()->back()->with('success', 'Nâng cấp gói dịch vụ thành công!');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
                }
        }
    }
    
    /**
     * Hủy subscription
     */
    public function cancel(): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi hủy gói dịch vụ.');
        }
        
        $currentSubscription = Subscription::where('company_id', $company->id)
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
     * Lấy text cho payment method
     */
    private function getPaymentMethodText($method)
    {
        $methodMap = [
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng',
            'zalopay' => 'Ví ZaloPay',
            'vnpay' => 'VNPay',
        ];
        
        return $methodMap[$method] ?? $method;
    }
    
    /**
     * Lấy payment data từ database
     */
    public function getPaymentData()
    {
        $user = Auth::user();
        
        // Lấy payment pending gần nhất của user
        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('payment_method', 'zalopay')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$payment || !$payment->payment_details) {
            Log::info('No pending ZaloPay payment found', ['user_id' => $user->id]);
            return response()->json(['error' => 'No payment data found'], 404);
        }
        
        $paymentDetails = json_decode($payment->payment_details, true);
        
        $responseData = [
            'payment_id' => $payment->id,
            'order_url' => $paymentDetails['zalopay_order_url'] ?? null,
            'order_token' => $paymentDetails['zalopay_order_token'] ?? null,
            'amount' => $payment->amount,
            'order_info' => $paymentDetails['order_info'] ?? 'Thanh toán ZaloPay',
        ];
        
        Log::info('Payment Data Retrieved from Database', $responseData);
        
        return response()->json($responseData);
    }

    /**
     * Test ZaloPay sandbox payment
     */
    public function testZaloPay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        try {
            $zalopayService = new ZaloPayPaymentService();
            
            $orderId = 'TEST_' . time();
            $result = $zalopayService->createPayment(
                $orderId,
                $request->amount,
                $request->description,
                json_encode(['test' => true])
            );

            if ($result['success']) {
                // Tạo payment record để có thể simulate
                $user = Auth::user();
                $package = ServicePackage::where('slug', 'premium')->first();
                
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'payment_method' => 'zalopay',
                    'amount' => $request->amount,
                    'status' => 'pending',
                    'transaction_id' => $result['app_trans_id'],
                    'payment_details' => json_encode([
                        'zalopay_order_url' => $result['order_url'],
                        'zalopay_order_token' => $result['order_token'],
                        'order_info' => $request->description,
                        'test_mode' => true,
                    ]),
                ]);

                $result['payment_id'] = $payment->id;

                return redirect()->back()->with([
                    'success' => true,
                    'payment_result' => $result
                ]);
            } else {
                return redirect()->back()->with('error', 'Lỗi tạo test payment: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang demo ZaloPay sandbox
     */
    public function zaloPayDemo(): Response
    {
        return Inertia::render('admin/subscriptions/ZaloPayDemo');
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
            
            // Tìm payment pending gần nhất
            $payment = Payment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('payment_method', 'zalopay')
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
     * Xử lý payment thành công
     */
    private function processSuccessfulPayment($payment)
    {
        $paymentDetails = json_decode($payment->payment_details, true);
        
        if (isset($paymentDetails['current_subscription_id'])) {
            // Upgrade subscription hiện tại
            $currentSubscription = Subscription::find($paymentDetails['current_subscription_id']);
            if ($currentSubscription) {
                $currentSubscription->update([
                    'package_id' => $payment->package_id,
                    'expires_at' => $currentSubscription->expires_at->addDays($payment->package->duration_days),
                ]);
                $payment->update(['subscription_id' => $currentSubscription->id]);
            }
        } else {
            // Tạo subscription mới
            $subscription = Subscription::create([
                'company_id' => $payment->user->company->id,
                'package_id' => $payment->package_id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addDays($payment->package->duration_days),
            ]);
            $payment->update(['subscription_id' => $subscription->id]);
        }
    }

    /**
     * Xử lý thanh toán VNPay
     */
    private function processVNPayPayment($user, $company, $package, $currentSubscription)
    {
        try {
            $vnpayService = new VNPayPaymentService();

            $orderId = 'UPGRADE_' . $company->id . '_' . $package->id;
            $orderInfo = "Nang cap tu {$currentSubscription->package->name} len {$package->name} - {$company->company_name}";

            $result = $vnpayService->createPayment(
                $orderId,
                $package->price,
                $orderInfo,
                json_encode([
                    'company_id' => $company->id,
                    'package_id' => $package->id,
                    'user_id' => $user->id,
                    'current_subscription_id' => $currentSubscription->id
                ])
            );

            if ($result['success']) {
                // Lưu thông tin payment
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'payment_method' => 'vnpay',
                    'amount' => $package->price,
                    'status' => 'pending',
                    'transaction_id' => $result['order_id'],
                    'payment_details' => json_encode([
                        'vnpay_payment_url' => $result['payment_url'],
                        'current_subscription_id' => $currentSubscription->id,
                        'upgrade_type' => 'vnpay',
                        'order_info' => $orderInfo,
                    ]),
                ]);

                Log::info('VNPay Payment Created', [
                    'payment_id' => $payment->id,
                    'payment_url' => $result['payment_url']
                ]);

                return redirect()->to($result['payment_url']);
            } else {
                return redirect()->back()->with('error', 'Lỗi tạo thanh toán VNPay: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('VNPay Payment Error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Callback từ VNPay
     */
    public function vnpayCallback(Request $request)
    {
        try {
            $vnpayService = new VNPayPaymentService();
            
            if (!$vnpayService->verifyCallback($request->all())) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }
            
            $orderId = $request->input('vnp_TxnRef');
            $responseCode = $request->input('vnp_ResponseCode');
            
            $payment = Payment::where('transaction_id', $orderId)->first();
            
            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }
            
            DB::beginTransaction();
            
            if ($responseCode == '00') {
                // Thanh toán thành công
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
                
                // Xử lý subscription
                $this->processSuccessfulPayment($payment);
                
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Payment successful']);
            } else {
                // Thanh toán thất bại
                $payment->update(['status' => 'failed']);
                DB::commit();
                return response()->json(['success' => false, 'message' => 'Payment failed']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Return URL từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $orderId = $request->input('vnp_TxnRef');
        $responseCode = $request->input('vnp_ResponseCode');
        
        if ($responseCode == '00') {
            return redirect('/admin/subscriptions')->with('success', 'Thanh toán thành công! Gói dịch vụ đã được kích hoạt.');
        } else {
            return redirect('/admin/subscriptions')->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
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
            $vnpayService = new VNPayPaymentService();
            
            $orderId = 'TEST_' . time();
            $result = $vnpayService->createPayment(
                $orderId,
                $request->amount,
                $request->description,
                json_encode(['test' => true])
            );

            if ($result['success']) {
                // Tạo payment record để có thể simulate
                $user = Auth::user();
                $package = ServicePackage::where('slug', 'premium')->first();
                
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'payment_method' => 'vnpay',
                    'amount' => $request->amount,
                    'status' => 'pending',
                    'transaction_id' => $result['order_id'],
                    'payment_details' => json_encode([
                        'vnpay_payment_url' => $result['payment_url'],
                        'order_info' => $request->description,
                        'test_mode' => true,
                    ]),
                ]);

                $result['payment_id'] = $payment->id;

                return redirect()->back()->with([
                    'success' => true,
                    'payment_result' => $result
                ]);
            } else {
                return redirect()->back()->with('error', 'Lỗi tạo test payment: ' . $result['message']);
            }
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