<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Subscription;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Chỉ kiểm tra với Employer
        if ($user && $user->hasRole('Employer')) {
            $company = $user->company;
            
            if (!$company) {
                return redirect()->route('employer.subscriptions')
                    ->with('error', 'Bạn cần tạo công ty trước khi sử dụng tính năng này.');
            }
            
            // Kiểm tra subscription active
            $subscription = Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();
                
            if (!$subscription) {
                return redirect()->route('employer.subscriptions')
                    ->with('error', 'Bạn cần đăng ký gói dịch vụ để sử dụng tính năng này.');
            }
            
            // Kiểm tra subscription sắp hết hạn (7 ngày)
            if ($subscription->expires_at->diffInDays(now()) <= 7) {
                $request->session()->flash('warning', 
                    'Gói dịch vụ của bạn sẽ hết hạn vào ' . $subscription->expires_at->format('d/m/Y') . 
                    '. Vui lòng gia hạn để tiếp tục sử dụng.'
                );
            }
        }
        
        return $next($request);
    }
}