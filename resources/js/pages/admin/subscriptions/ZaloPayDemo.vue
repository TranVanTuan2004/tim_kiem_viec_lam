<template>
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-gray-900">
                    ZaloPay Sandbox Demo
                </h1>
                <p class="text-gray-600">
                    Test thanh toán ZaloPay trong môi trường sandbox
                </p>
            </div>

            <!-- Test Payment Form -->
            <div class="mb-8 rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-xl font-semibold">Tạo Test Payment</h2>

                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Số tiền (VND)</label
                        >
                        <input
                            v-model="testForm.amount"
                            type="number"
                            class="w-full rounded-lg border px-3 py-2"
                            placeholder="2000000"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Mô tả</label
                        >
                        <input
                            v-model="testForm.description"
                            type="text"
                            class="w-full rounded-lg border px-3 py-2"
                            placeholder="Test payment description"
                        />
                    </div>
                </div>

                <button
                    @click="createTestPayment"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                >
                    Tạo Test Payment
                </button>
            </div>

            <!-- Payment Result -->
            <div
                v-if="paymentResult"
                class="mb-8 rounded-lg border bg-white p-6"
            >
                <h2 class="mb-4 text-xl font-semibold">Kết Quả Payment</h2>

                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-600">App Trans ID</p>
                        <p class="font-medium">
                            {{ paymentResult.app_trans_id }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Order Token</p>
                        <p class="font-medium">
                            {{ paymentResult.order_token }}
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">Order URL</p>
                    <div class="rounded bg-gray-100 p-2 text-sm break-all">
                        {{ paymentResult.order_url }}
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">ZP Trans Token</p>
                    <div class="rounded bg-gray-100 p-2 text-sm break-all">
                        {{ paymentResult.zp_trans_token }}
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="simulatePayment"
                        class="rounded-lg bg-green-600 px-6 py-2 text-white transition-colors hover:bg-green-700"
                    >
                        Simulate Payment Success
                    </button>
                    <button
                        @click="simulatePaymentFailure"
                        class="rounded-lg bg-red-600 px-6 py-2 text-white transition-colors hover:bg-red-700"
                    >
                        Simulate Payment Failure
                    </button>
                    <button
                        @click="paymentResult = null"
                        class="rounded-lg bg-gray-300 px-6 py-2 text-gray-700 transition-colors hover:bg-gray-400"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Environment Info -->
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-6">
                <h2 class="mb-4 text-xl font-semibold text-blue-900">
                    Thông Tin Môi Trường
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-blue-600">Environment</p>
                        <p class="font-medium text-blue-900">Sandbox (Test)</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-600">API Endpoint</p>
                        <p class="font-medium text-blue-900">Mock Service</p>
                    </div>
                </div>

                <div class="mt-4 rounded-lg bg-blue-100 p-4">
                    <p class="text-sm text-blue-800">
                        <strong>Lưu ý:</strong> Đây là môi trường sandbox để
                        test. Các URL và token được tạo giả lập để demo flow
                        thanh toán. Trong production, bạn cần đăng ký tài khoản
                        ZaloPay Business và sử dụng credentials thật.
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Gói Dịch Vụ', href: '/admin/subscriptions' },
    { title: 'ZaloPay Demo', href: '/admin/subscriptions/zalopay-demo' },
];

const testForm = ref({
    amount: 2000000,
    description: 'Test payment for subscription upgrade',
});

const paymentResult = ref(null);

const createTestPayment = () => {
    if (!testForm.value.amount || !testForm.value.description) {
        alert('Vui lòng nhập đầy đủ thông tin');
        return;
    }

    router.post(
        '/admin/subscriptions/test-zalopay',
        {
            amount: testForm.value.amount,
            description: testForm.value.description,
        },
        {
            onSuccess: (page) => {
                if (page.props.payment_result) {
                    paymentResult.value = page.props.payment_result;
                }
            },
            onError: (errors) => {
                console.error('Test payment error:', errors);
                alert('Có lỗi xảy ra khi tạo test payment');
            },
        },
    );
};

const simulatePayment = () => {
    if (!paymentResult.value) {
        alert('Vui lòng tạo payment trước');
        return;
    }

    router.post(
        '/admin/subscriptions/simulate-payment',
        {
            payment_id: paymentResult.value.payment_id || 'test',
            status: 'success',
        },
        {
            onSuccess: () => {
                alert('Payment thành công! Subscription đã được nâng cấp.');
                paymentResult.value = null;
                router.reload();
            },
            onError: (errors) => {
                console.error('Simulate payment error:', errors);
                alert('Có lỗi xảy ra khi simulate payment');
            },
        },
    );
};

const simulatePaymentFailure = () => {
    if (!paymentResult.value) {
        alert('Vui lòng tạo payment trước');
        return;
    }

    router.post(
        '/admin/subscriptions/simulate-payment',
        {
            payment_id: paymentResult.value.payment_id || 'test',
            status: 'failed',
        },
        {
            onSuccess: () => {
                alert('Payment thất bại! Vui lòng thử lại.');
                paymentResult.value = null;
            },
            onError: (errors) => {
                console.error('Simulate payment error:', errors);
                alert('Có lỗi xảy ra khi simulate payment');
            },
        },
    );
};
</script>
