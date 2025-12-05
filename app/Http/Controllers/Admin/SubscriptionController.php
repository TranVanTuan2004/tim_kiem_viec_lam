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
    public function index(): Response
    {
        $user = Auth::user();
        $company = $user->company;
        
        $packages = ServicePackage::where('is_active', true)->get();
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->first();
        
        Log::info('Subscription query result', [
            'user_id' => $user->id,
            'found_subscription' => $currentSubscription ? true : false,
            'subscription_id' => $currentSubscription->id ?? null,
            'package_id' => $currentSubscription->package_id ?? null,
        ]);
        
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
        
        $paymentHistory = Payment::where('user_id', $user->id)
            ->with(['package', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($currentSubscription && !$currentSubscription->relationLoaded('package')) {
            $currentSubscription->load('package');
        }
        
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
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Không tìm thấy gói dịch vụ hiện tại.');
        }
        
        if ($package->price <= $currentSubscription->package->price) {
            return redirect()->back()->with('error', 'Chỉ có thể nâng cấp lên gói có giá cao hơn.');
        }
        
        if ($request->payment_method === 'vnpay') {
            return $this->processVNPayPayment($user, $company, $package, $currentSubscription);
        }
        
        return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ.');
    }
    

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
            $currentSubscription->update([
                'expires_at' => $currentSubscription->expires_at->addDays($package->duration_days),
            ]);
            
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


    public function vnpayPayment(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:service_packages,id',
                'payment_method' => 'required|in:vnpay',
            ]);
            
            $user = Auth::user();
            $package = ServicePackage::findOrFail($request->package_id);
            
            $currentSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('package')
                ->first();
            
            $vnp_TxnRef = 'ORDER_' . $user->id . '_' . $package->id . '_' . time();
            
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
            
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "NVGDTU1Q";
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
      
            
            $vnp_OrderInfo = $currentSubscription 
                ? "Nang cap tu {$currentSubscription->package->name} len {$package->name}"
                : "Dang ky goi {$package->name}";
            $vnp_OrderType = 'other';
            $vnp_Amount = $package->price * 100;
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
        
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
        
            ksort($inputData);
            
            $queryString = http_build_query($inputData);
            
            $vnpSecureHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            $paymentUrl = $vnp_Url . "?" . $queryString . '&vnp_SecureHash=' . $vnpSecureHash;
            
    
            return redirect()->away($paymentUrl);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


  


    public function vnpayReturn(Request $request)
    {
        
        try {
            $vnp_HashSecret = "LYHQBKQVVD6PC32DEB2F7IYOCXTJG4X3";
            $secureHash = $request->input('vnp_SecureHash', '');
            
            if (empty($secureHash)) {
                Log::error('VNPay Return: Missing SecureHash');
                return redirect('/admin/subscriptions')->with('error', 'Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ.');
            }
            
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);
            
            $inputData = array_filter($inputData, function($value) {
                return $value !== null && $value !== '';
            });
            
            ksort($inputData);
            
            $queryString = http_build_query($inputData);
            
            $expectedHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
            
            if (!hash_equals($expectedHash, $secureHash)) {
                Log::error('VNPay Return: Invalid signature');
                return redirect('/admin/subscriptions')->with('error', 'Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ.');
            }
            
            $orderId = $request->input('vnp_TxnRef');
            $responseCode = $request->input('vnp_ResponseCode');
            $responseMessage = $request->input('vnp_ResponseMessage', '');
            
            $payment = Payment::where('transaction_id', $orderId)
                ->where('payment_method', 'vnpay')
                ->where('status', 'pending')
                ->first();
            
            if (!$payment) {
                Log::error('VNPay Return: Payment not found', ['orderId' => $orderId]);
                return redirect('/admin/subscriptions')->with('error', 'Không tìm thấy giao dịch thanh toán.');
            }
            
            if ($responseCode == '00') {
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
                    
                    $package = $payment->package;
                    $user = $payment->user;
                    $company = $user->company;
                    
             
                    $paymentDetails = json_decode($payment->payment_details, true);
                    $currentSubscriptionId = $paymentDetails['current_subscription_id'] ?? null;
                    
                    if ($currentSubscriptionId) {
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
                        Subscription::where('user_id', $payment->user_id)
                            ->where('status', 'active')
                            ->update(['status' => 'expired']);
                        
                        $subscriptionData = [
                            'user_id' => $payment->user_id,
                            'package_id' => $package->id,
                            'status' => 'active',
                            'starts_at' => now(),
                            'expires_at' => now()->addDays($package->duration_days),
                        ];
                        $subscription = Subscription::create($subscriptionData);
                        
                        $payment->update(['subscription_id' => $subscription->id]);
                        
                        $subscription->load('package');
                    }
                    
                    DB::commit();
                    return redirect('/admin/subscriptions')->with('success', 'Thanh toán thành công! Gói dịch vụ ' . $package->name . ' đã được kích hoạt.');
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect('/admin/subscriptions')->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
                }
            } else {
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
}