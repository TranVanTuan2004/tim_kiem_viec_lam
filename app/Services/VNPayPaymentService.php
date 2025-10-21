<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VNPayPaymentService
{
    protected $tmnCode;
    protected $hashSecret;
    protected $url;
    protected $returnUrl;
    protected $callbackUrl;
    protected $environment;

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code');
        $this->hashSecret = config('services.vnpay.hash_secret');
        $this->url = config('services.vnpay.url');
        $this->returnUrl = config('services.vnpay.return_url');
        $this->callbackUrl = config('services.vnpay.callback_url');
        $this->environment = config('services.vnpay.environment');
    }

    /**
     * Tạo payment với VNPay
     */
    public function createPayment($orderId, $amount, $description, $extraData = '')
    {
        try {
            $orderId = $orderId . '_' . time(); // Ensure unique orderId
            
            // Kiểm tra môi trường sandbox
            if ($this->environment === 'sandbox') {
                return $this->createMockPayment($orderId, $amount, $description);
            }

            $vnpParams = [
                'vnp_Version' => config('services.vnpay.version'),
                'vnp_Command' => config('services.vnpay.command'),
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_Amount' => $amount * 100, // VNPay tính bằng xu
                'vnp_CurrCode' => config('services.vnpay.curr_code'),
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => $description,
                'vnp_OrderType' => 'other',
                'vnp_Locale' => config('services.vnpay.locale'),
                'vnp_ReturnUrl' => $this->returnUrl,
                'vnp_IpAddr' => request()->ip(),
                'vnp_CreateDate' => date('YmdHis'),
            ];

            // Thêm extra data nếu có
            if ($extraData) {
                $vnpParams['vnp_ExtraData'] = $extraData;
            }

            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($vnpParams);

            // Tạo query string
            $queryString = http_build_query($vnpParams);

            // Tạo secure hash
            $secureHash = hash_hmac('sha512', $queryString, $this->hashSecret);

            // Thêm secure hash vào query string
            $queryString .= '&vnp_SecureHash=' . $secureHash;

            // Tạo URL thanh toán
            $paymentUrl = $this->url . '?' . $queryString;

            Log::info('VNPay Payment Created', [
                'orderId' => $orderId,
                'amount' => $amount,
                'description' => $description,
                'paymentUrl' => $paymentUrl
            ]);

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'order_id' => $orderId,
                'amount' => $amount,
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Payment Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tạo mock payment cho sandbox environment
     */
    private function createMockPayment($orderId, $amount, $description)
    {
        Log::info('VNPay Sandbox Mock Payment', [
            'orderId' => $orderId,
            'amount' => $amount,
            'description' => $description
        ]);

        // Tạo mock URL với các tham số giả lập
        $mockParams = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => 'MOCK_TMN',
            'vnp_Amount' => $amount * 100,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $orderId,
            'vnp_OrderInfo' => $description,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_IpAddr' => '127.0.0.1',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_SecureHash' => 'mock_hash_' . $orderId,
        ];

        $queryString = http_build_query($mockParams);
        $paymentUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?' . $queryString;

        return [
            'success' => true,
            'payment_url' => $paymentUrl,
            'order_id' => $orderId,
            'amount' => $amount,
        ];
    }

    /**
     * Xác thực callback từ VNPay
     */
    public function verifyCallback($data)
    {
        try {
            $secureHash = $data['vnp_SecureHash'] ?? '';
            unset($data['vnp_SecureHash']);
            
            // Sắp xếp các tham số theo thứ tự alphabet
            ksort($data);
            
            // Tạo query string
            $queryString = http_build_query($data);
            
            // Tạo secure hash
            $expectedHash = hash_hmac('sha512', $queryString, $this->hashSecret);
            
            return hash_equals($expectedHash, $secureHash);
        } catch (\Exception $e) {
            Log::error('VNPay Callback Verification Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kiểm tra trạng thái payment từ VNPay
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            // Trong môi trường sandbox, trả về mock status
            if ($this->environment === 'sandbox') {
                return [
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Mock payment status check',
                ];
            }

            // API để check status từ VNPay (cần implement theo VNPay API)
            $vnpParams = [
                'vnp_Version' => config('services.vnpay.version'),
                'vnp_Command' => 'querydr',
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => 'Check payment status',
                'vnp_TransactionDate' => date('YmdHis'),
            ];

            ksort($vnpParams);
            $queryString = http_build_query($vnpParams);
            $secureHash = hash_hmac('sha512', $queryString, $this->hashSecret);

            $response = Http::timeout(30)->post('https://sandbox.vnpayment.vn/merchant_webapi/api/transaction', [
                'vnp_RequestId' => time(),
                'vnp_Version' => config('services.vnpay.version'),
                'vnp_Command' => 'querydr',
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => 'Check payment status',
                'vnp_TransactionDate' => date('YmdHis'),
                'vnp_SecureHash' => $secureHash,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'status' => $result['vnp_ResponseCode'] == '00' ? 'success' : 'failed',
                    'message' => $result['vnp_ResponseMessage'] ?? '',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Network error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('VNPay Status Check Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Status check error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Hoàn thành payment và cập nhật subscription
     */
    public function markAsCompleted($payment)
    {
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * Đánh dấu payment thất bại
     */
    public function markAsFailed($payment)
    {
        $payment->update([
            'status' => 'failed',
        ]);
    }

    /**
     * Tạo QR Code cho VNPay (nếu cần)
     */
    public function generateQRCode($orderId, $amount, $description)
    {
        try {
            $vnpParams = [
                'vnp_Version' => config('services.vnpay.version'),
                'vnp_Command' => 'pay',
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_Amount' => $amount * 100,
                'vnp_CurrCode' => config('services.vnpay.curr_code'),
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => $description,
                'vnp_OrderType' => 'other',
                'vnp_Locale' => config('services.vnpay.locale'),
                'vnp_IpAddr' => request()->ip(),
                'vnp_CreateDate' => date('YmdHis'),
            ];

            ksort($vnpParams);
            $queryString = http_build_query($vnpParams);
            $secureHash = hash_hmac('sha512', $queryString, $this->hashSecret);

            return [
                'success' => true,
                'qr_data' => $queryString . '&vnp_SecureHash=' . $secureHash,
                'order_id' => $orderId,
            ];
        } catch (\Exception $e) {
            Log::error('VNPay QR Code Generation Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'QR Code generation error: ' . $e->getMessage(),
            ];
        }
    }
}

