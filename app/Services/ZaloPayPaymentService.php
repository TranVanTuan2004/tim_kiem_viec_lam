<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZaloPayPaymentService
{
    protected $appId;
    protected $key1;
    protected $key2;
    protected $endpoint;
    protected $callbackUrl;
    protected $redirectUrl;

    public function __construct()
    {
        $this->appId = config('services.zalopay.app_id', 'ZALOPAY_APP_ID');
        $this->key1 = config('services.zalopay.key1', 'ZALOPAY_KEY1');
        $this->key2 = config('services.zalopay.key2', 'ZALOPAY_KEY2');
        $this->endpoint = config('services.zalopay.endpoint', 'https://sb-openapi.zalopay.vn/v2/create');
        $this->callbackUrl = config('services.zalopay.callback_url', url('/admin/subscriptions/payment/callback'));
        $this->redirectUrl = config('services.zalopay.redirect_url', url('/admin/subscriptions/payment/return'));
    }

    /**
     * Tạo payment với ZaloPay
     */
    public function createPayment($orderId, $amount, $description, $extraData = '')
    {
        try {
            $orderId = $orderId . '_' . time(); // Ensure unique orderId
            
            // Kiểm tra môi trường sandbox
            if (config('services.zalopay.environment') === 'sandbox') {
                return $this->createMockPayment($orderId, $amount, $description);
            }

            $data = [
                'app_id' => (int) $this->appId,
                'app_trans_id' => $orderId,
                'app_user' => 'demo',
                'amount' => (int) $amount,
                'app_time' => time() * 1000, // ZaloPay uses milliseconds
                'bank_code' => '',
                'description' => $description,
                'embed_data' => $extraData,
                'item' => '[]',
                'callback_url' => $this->callbackUrl,
            ];

            // Tạo mac (signature)
            $data['mac'] = $this->createMac($data);

            Log::info('ZaloPay Payment Request', $data);

            $response = Http::timeout(30)->post($this->endpoint, $data);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['return_code'] == 1) {
                    return [
                        'success' => true,
                        'order_token' => $result['order_token'],
                        'order_url' => $result['order_url'],
                        'app_trans_id' => $orderId,
                        'zp_trans_token' => $result['zp_trans_token'] ?? null,
                    ];
                } else {
                    Log::error('ZaloPay Payment Error', $result);
                    return [
                        'success' => false,
                        'message' => $result['return_message'] ?? 'Payment failed',
                        'return_code' => $result['return_code'],
                    ];
                }
            } else {
                Log::error('ZaloPay HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
                return [
                    'success' => false,
                    'message' => 'Network error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('ZaloPay Payment Exception', ['error' => $e->getMessage()]);
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
        Log::info('ZaloPay Sandbox Mock Payment', [
            'orderId' => $orderId,
            'amount' => $amount,
            'description' => $description
        ]);

        return [
            'success' => true,
            'order_token' => 'mock_token_' . $orderId,
            'order_url' => 'https://sandbox.zalopay.vn/pay/' . $orderId,
            'app_trans_id' => $orderId,
            'zp_trans_token' => 'mock_zp_token_' . $orderId,
        ];
    }

    /**
     * Tạo MAC (Message Authentication Code) cho ZaloPay
     */
    private function createMac($data)
    {
        $dataStr = $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $data['app_user'] . '|' . $data['amount'] . '|' . $data['app_time'] . '|' . $data['embed_data'] . '|' . $data['item'];
        return hash_hmac('sha256', $dataStr, $this->key1);
    }

    /**
     * Xác thực callback từ ZaloPay
     */
    public function verifyCallback($data)
    {
        try {
            $mac = $data['mac'] ?? '';
            unset($data['mac']);
            
            $dataStr = $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $data['zp_trans_id'] . '|' . $data['amount'] . '|' . $data['status'] . '|' . $data['app_time'];
            $expectedMac = hash_hmac('sha256', $dataStr, $this->key2);
            
            return hash_equals($expectedMac, $mac);
        } catch (\Exception $e) {
            Log::error('ZaloPay Callback Verification Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kiểm tra trạng thái payment
     */
    public function checkPaymentStatus($appTransId)
    {
        try {
            $data = [
                'app_id' => $this->appId,
                'app_trans_id' => $appTransId,
            ];

            $data['mac'] = hash_hmac('sha256', $data['app_id'] . '|' . $data['app_trans_id'] . '|' . $this->key1, $this->key1);

            $response = Http::timeout(30)->post('https://sb-openapi.zalopay.vn/v2/query', $data);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'status' => $result['status'] ?? 0,
                    'return_code' => $result['return_code'] ?? 0,
                    'return_message' => $result['return_message'] ?? '',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Network error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('ZaloPay Status Check Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Status check error: ' . $e->getMessage(),
            ];
        }
    }
}
