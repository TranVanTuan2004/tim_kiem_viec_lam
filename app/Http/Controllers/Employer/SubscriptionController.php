<?php

namespace App\Http\Controllers\Employer;

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

class SubscriptionController extends Controller
{
    /**
     * Hiển thị danh sách gói dịch vụ và subscription hiện tại
     */
    public function index(): Response
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Lấy các gói dịch vụ đang hoạt động
        $packages = ServicePackage::active()
            ->orderBy('price')
            ->get();
            
        // Lấy subscription hiện tại của company
        $currentSubscription = null;
        if ($company) {
            $currentSubscription = Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->with('package')
                ->first();
        }
        
        // Lấy lịch sử payments
        $paymentHistory = Payment::where('user_id', $user->id)
            ->with(['package', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return Inertia::render('Employer/Subscriptions/Index', [
            'packages' => $packages,
            'currentSubscription' => $currentSubscription,
            'paymentHistory' => $paymentHistory,
            'company' => $company,
        ]);
    }
    
    /**
     * Đăng ký gói dịch vụ mới
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'payment_method' => 'required|in:vnpay',
            'auto_renew' => 'boolean',
        ]);
        
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi đăng ký gói dịch vụ.');
        }
        
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Kiểm tra xem đã có subscription active chưa
        $existingSubscription = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();
            
        if ($existingSubscription) {
            return redirect()->back()->with('error', 'Bạn đã có gói dịch vụ đang hoạt động. Vui lòng nâng cấp hoặc gia hạn.');
        }
        
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
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'package_id' => $package->id,
                'payment_method' => $request->payment_method,
                'amount' => $package->price,
                'currency' => 'VND',
                'status' => 'completed', // Tạm thời set completed, sau này tích hợp payment gateway
                'paid_at' => now(),
                'notes' => 'Đăng ký gói ' . $package->name,
            ]);
            
            DB::commit();
            
            return redirect()->route('employer.subscriptions')
                ->with('success', 'Đăng ký gói dịch vụ thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi đăng ký gói dịch vụ.');
        }
    }
    
    /**
     * Nâng cấp gói dịch vụ
     */
    public function upgrade(Request $request): RedirectResponse
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
        
        $newPackage = ServicePackage::findOrFail($request->package_id);
        $currentSubscription = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->with('package')
            ->first();
            
        if (!$currentSubscription) {
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ nào để nâng cấp.');
        }
        
        if ($currentSubscription->package_id == $newPackage->id) {
            return redirect()->back()->with('error', 'Bạn đã sử dụng gói này rồi.');
        }
        
        if ($newPackage->price <= $currentSubscription->package->price) {
            return redirect()->back()->with('error', 'Chỉ có thể nâng cấp lên gói có giá cao hơn.');
        }
        
        DB::beginTransaction();
        
        try {
            // Tính toán số tiền cần thanh toán (pro-rated)
            $remainingDays = $currentSubscription->getDaysRemaining();
            $dailyRate = $newPackage->price / $newPackage->duration_days;
            $upgradeAmount = $remainingDays * $dailyRate;
            
            // Hủy subscription cũ
            $currentSubscription->update(['status' => 'cancelled']);
            
            // Tạo subscription mới
            $newSubscription = Subscription::create([
                'company_id' => $company->id,
                'package_id' => $newPackage->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $currentSubscription->expires_at, // Giữ nguyên ngày hết hạn
            ]);
            
            // Tạo payment cho phần nâng cấp
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $newSubscription->id,
                'package_id' => $newPackage->id,
                'payment_method' => $request->payment_method,
                'amount' => $upgradeAmount,
                'currency' => 'VND',
                'status' => 'completed',
                'paid_at' => now(),
                'notes' => "Nâng cấp từ {$currentSubscription->package->name} lên {$newPackage->name}",
            ]);
            
            DB::commit();
            
            return redirect()->route('employer.subscriptions')
                ->with('success', 'Nâng cấp gói dịch vụ thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi nâng cấp gói dịch vụ.');
        }
    }
    
    /**
     * Gia hạn gói dịch vụ
     */
    public function renew(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay',
            'auto_renew' => 'boolean',
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
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ nào để gia hạn.');
        }
        
        DB::beginTransaction();
        
        try {
            // Gia hạn thêm thời gian
            $newExpiresAt = $currentSubscription->expires_at->addDays($currentSubscription->package->duration_days);
            $currentSubscription->update(['expires_at' => $newExpiresAt]);
            
            // Tạo payment cho gia hạn
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $currentSubscription->id,
                'package_id' => $currentSubscription->package_id,
                'payment_method' => $request->payment_method,
                'amount' => $currentSubscription->package->price,
                'currency' => 'VND',
                'status' => 'completed',
                'paid_at' => now(),
                'notes' => "Gia hạn gói {$currentSubscription->package->name}",
            ]);
            
            DB::commit();
            
            return redirect()->route('employer.subscriptions')
                ->with('success', 'Gia hạn gói dịch vụ thành công!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gia hạn gói dịch vụ.');
        }
    }
    
    /**
     * Hủy subscription
     */
    public function cancel(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->back()->with('error', 'Bạn cần tạo công ty trước khi hủy gói dịch vụ.');
        }
        
        $subscription = Subscription::where('company_id', $company->id)
            ->where('status', 'active')
            ->first();
            
        if (!$subscription) {
            return redirect()->back()->with('error', 'Bạn chưa có gói dịch vụ nào để hủy.');
        }
        
        $subscription->update(['status' => 'cancelled']);
        
        return redirect()->route('employer.subscriptions')
            ->with('success', 'Hủy gói dịch vụ thành công!');
    }
    
    /**
     * Xem chi tiết subscription
     */
    public function show(Subscription $subscription): Response
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        if ($subscription->company->user_id !== $user->id) {
            abort(403);
        }
        
        $subscription->load(['package', 'payments']);
        
        return Inertia::render('Employer/Subscriptions/Show', [
            'subscription' => $subscription,
        ]);
    }
}