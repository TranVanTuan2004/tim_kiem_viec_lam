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
            // Đảm bảo orderId là duy nhất
            $orderId = $orderId . '_' . time();

            // Kiểm tra cấu hình
            if (empty($this->tmnCode) || empty($this->hashSecret)) {
                Log::error('VNPay Configuration Missing', [
                    'tmn_code' => empty($this->tmnCode),
                    'hash_secret' => empty($this->hashSecret)
                ]);
                return [
                    'success' => false,
                    'message' => 'VNPay chưa được cấu hình. Vui lòng kiểm tra file .env',
                ];
            }

            $vnpParams = [
                'vnp_Version' => config('services.vnpay.version', '2.1.0'),
                'vnp_Command' => config('services.vnpay.command', 'pay'),
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_Amount' => $amount * 100, // VNPay tính bằng xu
                'vnp_CurrCode' => config('services.vnpay.curr_code', 'VND'),
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => $description,
                'vnp_OrderType' => 'other',
                'vnp_Locale' => config('services.vnpay.locale', 'vn'),
                'vnp_ReturnUrl' => $this->returnUrl,
                'vnp_IpAddr' => request()->ip() ?? '127.0.0.1',
                'vnp_CreateDate' => date('YmdHis'),
            ];

            // Thêm extra data nếu có
            if ($extraData) {
                $vnpParams['vnp_ExtraData'] = $extraData;
            }

            // Loại bỏ các tham số rỗng
            $vnpParams = array_filter($vnpParams, function($value) {
                return $value !== null && $value !== '';
            });

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
                'environment' => $this->environment,
                'paymentUrl' => $paymentUrl
            ]);

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'order_id' => $orderId,
                'amount' => $amount,
            ];

        } catch (\Exception $e) {
            Log::error('VNPay Payment Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Lỗi tạo thanh toán VNPay: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Xác thực callback từ VNPay
     */
    public function verifyCallback($data)
    {
        try {
            $secureHash = $data['vnp_SecureHash'] ?? '';
            
            if (empty($secureHash)) {
                Log::error('VNPay Callback: Missing SecureHash');
                return false;
            }

            // Tạo bản sao để không ảnh hưởng đến data gốc
            $inputData = $data;
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
            $expectedHash = hash_hmac('sha512', $queryString, $this->hashSecret);
            
            $isValid = hash_equals($expectedHash, $secureHash);
            
            if (!$isValid) {
                Log::warning('VNPay Callback Verification Failed', [
                    'expected' => $expectedHash,
                    'received' => $secureHash,
                    'data' => $data
                ]);
            }
            
            return $isValid;
        } catch (\Exception $e) {
            Log::error('VNPay Callback Verification Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Kiểm tra trạng thái payment từ VNPay
     */
    public function checkPaymentStatus($orderId, $transactionDate = null)
    {
        try {
            if (empty($this->tmnCode) || empty($this->hashSecret)) {
                return [
                    'success' => false,
                    'message' => 'VNPay chưa được cấu hình',
                ];
            }

            $apiUrl = $this->environment === 'sandbox' 
                ? 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'
                : 'https://vnpayment.vn/merchant_webapi/api/transaction';

            $vnpParams = [
                'vnp_RequestId' => time(),
                'vnp_Version' => config('services.vnpay.version', '2.1.0'),
                'vnp_Command' => 'querydr',
                'vnp_TmnCode' => $this->tmnCode,
                'vnp_TxnRef' => $orderId,
                'vnp_OrderInfo' => 'Check payment status',
                'vnp_TransactionDate' => $transactionDate ?? date('YmdHis'),
            ];

            ksort($vnpParams);
            $queryString = http_build_query($vnpParams);
            $secureHash = hash_hmac('sha512', $queryString, $this->hashSecret);
            $vnpParams['vnp_SecureHash'] = $secureHash;

            $response = Http::timeout(30)->post($apiUrl, $vnpParams);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'status' => ($result['vnp_ResponseCode'] ?? '') == '00' ? 'success' : 'failed',
                    'message' => $result['vnp_ResponseMessage'] ?? '',
                    'data' => $result,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Lỗi kết nối với VNPay',
                ];
            }
        } catch (\Exception $e) {
            Log::error('VNPay Status Check Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Lỗi kiểm tra trạng thái: ' . $e->getMessage(),
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

