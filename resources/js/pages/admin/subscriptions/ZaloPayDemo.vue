<template>
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto py-8 px-4">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">ZaloPay Sandbox Demo</h1>
                <p class="text-gray-600">Test thanh toán ZaloPay trong môi trường sandbox</p>
            </div>

            <!-- Test Payment Form -->
            <div class="bg-white rounded-lg border p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Tạo Test Payment</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Số tiền (VND)</label>
                        <input 
                            v-model="testForm.amount" 
                            type="number" 
                            class="w-full border rounded-lg px-3 py-2"
                            placeholder="2000000"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Mô tả</label>
                        <input 
                            v-model="testForm.description" 
                            type="text" 
                            class="w-full border rounded-lg px-3 py-2"
                            placeholder="Test payment description"
                        >
                    </div>
                </div>

                <button 
                    @click="createTestPayment"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Tạo Test Payment
                </button>
            </div>

            <!-- Payment Result -->
            <div v-if="paymentResult" class="bg-white rounded-lg border p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Kết Quả Payment</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">App Trans ID</p>
                        <p class="font-medium">{{ paymentResult.app_trans_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Order Token</p>
                        <p class="font-medium">{{ paymentResult.order_token }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">Order URL</p>
                    <div class="bg-gray-100 p-2 rounded text-sm break-all">
                        {{ paymentResult.order_url }}
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">ZP Trans Token</p>
                    <div class="bg-gray-100 p-2 rounded text-sm break-all">
                        {{ paymentResult.zp_trans_token }}
                    </div>
                </div>

                <div class="flex gap-3">
                    <button 
                        @click="simulatePayment"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Simulate Payment Success
                    </button>
                    <button 
                        @click="simulatePaymentFailure"
                        class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Simulate Payment Failure
                    </button>
                    <button 
                        @click="paymentResult = null"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Environment Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-900">Thông Tin Môi Trường</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-blue-600">Environment</p>
                        <p class="font-medium text-blue-900">Sandbox (Test)</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-600">API Endpoint</p>
                        <p class="font-medium text-blue-900">Mock Service</p>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-100 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Lưu ý:</strong> Đây là môi trường sandbox để test. 
                        Các URL và token được tạo giả lập để demo flow thanh toán.
                        Trong production, bạn cần đăng ký tài khoản ZaloPay Business và sử dụng credentials thật.
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Gói Dịch Vụ', href: '/admin/subscriptions' },
    { title: 'ZaloPay Demo', href: '/admin/subscriptions/zalopay-demo' },
];

const testForm = ref({
    amount: 2000000,
    description: 'Test payment for subscription upgrade'
});

const paymentResult = ref(null);

const createTestPayment = () => {
    if (!testForm.value.amount || !testForm.value.description) {
        alert('Vui lòng nhập đầy đủ thông tin');
        return;
    }

    router.post('/admin/subscriptions/test-zalopay', {
        amount: testForm.value.amount,
        description: testForm.value.description
    }, {
        onSuccess: (page) => {
            if (page.props.payment_result) {
                paymentResult.value = page.props.payment_result;
            }
        },
        onError: (errors) => {
            console.error('Test payment error:', errors);
            alert('Có lỗi xảy ra khi tạo test payment');
        }
    });
};

const simulatePayment = () => {
    if (!paymentResult.value) {
        alert('Vui lòng tạo payment trước');
        return;
    }

    router.post('/admin/subscriptions/simulate-payment', {
        payment_id: paymentResult.value.payment_id || 'test',
        status: 'success'
    }, {
        onSuccess: () => {
            alert('Payment thành công! Subscription đã được nâng cấp.');
            paymentResult.value = null;
            router.reload();
        },
        onError: (errors) => {
            console.error('Simulate payment error:', errors);
            alert('Có lỗi xảy ra khi simulate payment');
        }
    });
};

const simulatePaymentFailure = () => {
    if (!paymentResult.value) {
        alert('Vui lòng tạo payment trước');
        return;
    }

    router.post('/admin/subscriptions/simulate-payment', {
        payment_id: paymentResult.value.payment_id || 'test',
        status: 'failed'
    }, {
        onSuccess: () => {
            alert('Payment thất bại! Vui lòng thử lại.');
            paymentResult.value = null;
        },
        onError: (errors) => {
            console.error('Simulate payment error:', errors);
            alert('Có lỗi xảy ra khi simulate payment');
        }
    });
};
</script>

