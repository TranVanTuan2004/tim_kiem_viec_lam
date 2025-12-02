# VNPay Configuration

## Thông tin cấu hình từ VNPay (Sandbox)

Thông tin này đã được cấu hình sẵn trong `config/services.php` với giá trị mặc định:

```env
VNPAY_TMN_CODE=NVGDTU1Q
VNPAY_HASH_SECRET=IFW65CX0UFDOY4RV2NB0JTEU8YUODMUY
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/admin/subscriptions/vnpay/return
VNPAY_CALLBACK_URL=http://localhost:8000/admin/subscriptions/vnpay/callback
VNPAY_ENVIRONMENT=sandbox
```

## Cấu hình trong file .env

Thêm các dòng sau vào file `.env` của bạn (hoặc để sử dụng giá trị mặc định từ config):

```env
VNPAY_TMN_CODE=NVGDTU1Q
VNPAY_HASH_SECRET=IFW65CX0UFDOY4RV2NB0JTEU8YUODMUY
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/admin/subscriptions/vnpay/return
VNPAY_CALLBACK_URL=http://localhost:8000/admin/subscriptions/vnpay/callback
VNPAY_ENVIRONMENT=sandbox
```

**Lưu ý:** 
- Thay đổi `VNPAY_RETURN_URL` và `VNPAY_CALLBACK_URL` thành domain thật của bạn khi deploy
- URL callback phải là URL công khai có thể truy cập từ internet (VNPay sẽ gọi URL này)

## Thẻ test (Sandbox)

- **Ngân hàng:** NCB
- **Số thẻ:** 9704198526191432198
- **Tên chủ thẻ:** NGUYEN VAN A
- **Ngày phát hành:** 07/15
- **Mật khẩu OTP:** 123456

## Cấu hình Production

Khi chuyển sang production, cập nhật:

```env
VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
VNPAY_ENVIRONMENT=production
VNPAY_TMN_CODE=<TMN_CODE_PRODUCTION>
VNPAY_HASH_SECRET=<HASH_SECRET_PRODUCTION>
```

## IPN URL (Callback URL)

VNPay sẽ gọi URL callback để thông báo kết quả thanh toán. Đảm bảo:
1. URL phải là HTTPS (trong production)
2. URL phải có thể truy cập công khai từ internet
3. URL không yêu cầu authentication (route đã được cấu hình public)

## Tài liệu tham khảo

- Tài liệu tích hợp: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
- Code demo: https://sandbox.vnpayment.vn/apis/vnpay-demo/code-demo-tích-hợp
- Merchant Admin (Sandbox): https://sandbox.vnpayment.vn/merchantv2/

