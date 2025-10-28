<template>
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-gray-900">
                    Gói Dịch Vụ
                </h1>
                <p class="text-gray-600">
                    Chọn gói dịch vụ phù hợp với nhu cầu tuyển dụng của bạn
                </p>
            </div>

            <!-- Current Subscription Card -->
            <div v-if="currentSubscription" class="mb-8">
                <div
                    class="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-6"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-blue-900">
                            Gói Hiện Tại
                        </h2>
                        <span
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800"
                        >
                            {{ getStatusText(currentSubscription.status) }}
                        </span>
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <p class="text-sm text-gray-600">Gói dịch vụ</p>
                            <p class="font-semibold">
                                {{ currentSubscription.package.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Giá</p>
                            <p class="font-semibold">
                                {{
                                    formatPrice(
                                        currentSubscription.package.price,
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Hết hạn</p>
                            <p class="font-semibold">
                                {{ formatDate(currentSubscription.expires_at) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Còn lại</p>
                            <p class="font-semibold">
                                {{
                                    getDaysRemaining(
                                        currentSubscription.expires_at,
                                    )
                                }}
                                ngày
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div
                            class="mb-1 flex justify-between text-sm text-gray-600"
                        >
                            <span>Tiến độ sử dụng</span>
                            <span
                                >{{
                                    getProgressPercentage(currentSubscription)
                                }}%</span
                            >
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-blue-600 transition-all duration-300"
                                :style="{
                                    width:
                                        getProgressPercentage(
                                            currentSubscription,
                                        ) + '%',
                                }"
                            ></div>
                        </div>
                    </div>

                    <!-- Action Buttons - Chỉ hiện khi không phải gói Free -->
                    <div
                        v-if="currentSubscription.package.price > 0"
                        class="flex gap-3"
                    >
                        <button
                            @click="showRenewModal = true"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                        >
                            Gia Hạn
                        </button>
                        <button
                            @click="showUpgradeModal = true"
                            class="rounded-lg bg-green-600 px-4 py-2 text-white transition-colors hover:bg-green-700"
                        >
                            Nâng Cấp
                        </button>
                        <button
                            @click="cancelSubscription"
                            class="rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700"
                        >
                            Hủy Gói
                        </button>
                    </div>

                    <!-- Thông báo cho gói Free -->
                    <div v-else class="py-4 text-center">
                        <p class="mb-2 text-gray-600">
                            🎉 Bạn đang sử dụng gói miễn phí!
                        </p>
                        <p class="text-sm text-gray-500">
                            Để nâng cấp lên gói Premium, hãy chọn gói bên dưới
                        </p>
                    </div>
                </div>
            </div>

            <!-- Packages Grid -->
            <div
                class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="packageItem in packages"
                    :key="packageItem.id"
                    class="flex h-full flex-col rounded-xl border bg-white p-6 transition-all duration-300 hover:shadow-xl"
                    :class="{
                        'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 ring-2 ring-blue-200':
                            isCurrentPackage(packageItem),
                        'border-gray-200 hover:border-gray-300':
                            !isCurrentPackage(packageItem),
                    }"
                >
                    <div class="mb-6 flex-shrink-0 text-center">
                        <h3 class="mb-3 text-2xl font-bold text-gray-800">
                            {{ packageItem.name }}
                        </h3>
                        <div class="mb-3 text-4xl font-bold text-blue-600">
                            {{ formatPrice(packageItem.price) }}
                        </div>
                        <p class="mb-3 text-gray-600">
                            {{ packageItem.duration_days }} ngày
                        </p>

                        <!-- Giới hạn đăng bài -->
                        <div class="mb-4">
                            <span
                                class="inline-block rounded-full bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm"
                            >
                                {{
                                    packageItem.max_jobs === 0
                                        ? 'Không giới hạn'
                                        : `${packageItem.max_jobs} bài`
                                }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-6 flex-grow">
                        <p class="mb-4 text-center text-gray-700">
                            {{ packageItem.description }}
                        </p>

                        <ul class="space-y-3">
                            <li
                                v-for="feature in packageItem.features"
                                :key="feature"
                                class="flex items-start text-sm text-gray-600"
                            >
                                <Check
                                    class="mt-0.5 mr-3 h-5 w-5 flex-shrink-0 text-green-500"
                                />
                                <span>{{ feature }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-auto">
                        <button
                            @click="subscribe(packageItem)"
                            class="w-full transform rounded-lg px-6 py-3 font-semibold shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl"
                            :class="{
                                'cursor-not-allowed bg-gray-300 text-gray-500':
                                    isCurrentPackage(packageItem),
                                'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800':
                                    !isCurrentPackage(packageItem),
                            }"
                            :disabled="isCurrentPackage(packageItem)"
                        >
                            {{
                                isCurrentPackage(packageItem)
                                    ? 'Gói Hiện Tại'
                                    : 'Nâng Cấp'
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div
                v-if="paymentHistory.length > 0"
                class="rounded-lg border bg-white p-6"
            >
                <h3 class="mb-4 text-lg font-semibold">Lịch Sử Thanh Toán</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">Gói</th>
                                <th class="py-2 text-left">Số tiền</th>
                                <th class="py-2 text-left">Phương thức</th>
                                <th class="py-2 text-left">Trạng thái</th>
                                <th class="py-2 text-left">Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="payment in paymentHistory"
                                :key="payment.id"
                                class="border-b"
                            >
                                <td class="py-2">
                                    {{ payment.package?.name || 'N/A' }}
                                </td>
                                <td class="py-2">
                                    {{ formatPrice(payment.amount) }}
                                </td>
                                <td class="py-2">
                                    {{
                                        getPaymentMethodText(
                                            payment.payment_method,
                                        )
                                    }}
                                </td>
                                <td class="py-2">
                                    <span
                                        class="rounded px-2 py-1 text-xs"
                                        :class="getStatusClass(payment.status)"
                                    >
                                        {{ getStatusText(payment.status) }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    {{ formatDate(payment.created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Renew Modal -->
            <div
                v-if="showRenewModal"
                class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
            >
                <div class="w-full max-w-md rounded-lg bg-white p-6">
                    <h3 class="mb-4 text-lg font-semibold">
                        Gia Hạn Gói Dịch Vụ
                    </h3>
                    <p class="mb-4 text-gray-600">
                        Bạn sẽ gia hạn gói
                        <strong>{{
                            currentSubscription?.package?.name
                        }}</strong>
                        với giá
                        <strong>{{
                            formatPrice(currentSubscription?.package?.price)
                        }}</strong>
                    </p>

                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium"
                            >Phương thức thanh toán</label
                        >
                        <select
                            v-model="renewForm.payment_method"
                            class="w-full rounded-lg border px-3 py-2"
                        >
                            <option value="bank_transfer">
                                Chuyển khoản ngân hàng
                            </option>
                            <option value="credit_card">Thẻ tín dụng</option>
                            <option value="momo">Ví MoMo</option>
                            <option value="vnpay">VNPay</option>
                            <option value="zalopay">ZaloPay</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="renewSubscription"
                            class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                        >
                            Xác Nhận Gia Hạn
                        </button>
                        <button
                            @click="showRenewModal = false"
                            class="flex-1 rounded-lg bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                        >
                            Hủy
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upgrade Modal -->
            <div
                v-if="showUpgradeModal"
                class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
            >
                <div class="w-full max-w-md rounded-lg bg-white p-6">
                    <h3 class="mb-4 text-lg font-semibold">
                        Nâng Cấp Gói Dịch Vụ
                    </h3>
                    <p class="mb-4 text-gray-600">
                        Chọn gói dịch vụ để nâng cấp:
                    </p>

                    <div class="mb-4 space-y-3">
                        <label
                            v-for="packageItem in availableUpgrades"
                            :key="packageItem.id"
                            class="flex cursor-pointer items-center rounded-lg border p-3 hover:bg-gray-50"
                        >
                            <input
                                type="radio"
                                v-model="upgradeForm.package_id"
                                :value="packageItem.id"
                                class="mr-3"
                            />
                            <div class="flex-1">
                                <div class="font-medium">
                                    {{ packageItem.name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ formatPrice(packageItem.price) }}
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium"
                            >Phương thức thanh toán</label
                        >
                        <select
                            v-model="upgradeForm.payment_method"
                            class="w-full rounded-lg border px-3 py-2"
                        >
                            <option value="bank_transfer">
                                Chuyển khoản ngân hàng
                            </option>
                            <option value="credit_card">Thẻ tín dụng</option>
                            <option value="momo">Ví MoMo</option>
                            <option value="vnpay">VNPay</option>
                            <option value="zalopay">ZaloPay</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="upgradeSubscription"
                            class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                        >
                            Xác Nhận Nâng Cấp
                        </button>
                        <button
                            @click="showUpgradeModal = false"
                            class="flex-1 rounded-lg bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                        >
                            Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Modal -->
        <div
            v-if="showPaymentModal"
            class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
        >
            <div class="w-full max-w-md rounded-lg bg-white p-6">
                <h3 class="mb-4 text-lg font-semibold">Nâng Cấp Gói Dịch Vụ</h3>

                <div class="mb-4">
                    <p class="mb-2 text-sm text-gray-600">
                        Gói dịch vụ:
                        <strong>{{ selectedPackage?.name }}</strong>
                    </p>
                    <p class="mb-4 text-sm text-gray-600">
                        Số tiền:
                        <strong>{{
                            formatPrice(selectedPackage?.price)
                        }}</strong>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium"
                        >Phương thức thanh toán</label
                    >
                    <select
                        v-model="selectedPaymentMethod"
                        class="w-full rounded-lg border px-3 py-2"
                    >
                        <option value="zalopay">Ví ZaloPay</option>
                        <option value="bank_transfer">
                            Chuyển khoản ngân hàng
                        </option>
                        <option value="credit_card">Thẻ tín dụng</option>
                        <option value="vnpay">VNPay</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="confirmPayment"
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Nâng Cấp Ngay
                    </button>
                    <button
                        @click="showPaymentModal = false"
                        class="flex-1 rounded-lg bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                    >
                        Hủy
                    </button>
                </div>
            </div>
        </div>

        <!-- ZaloPay Payment Modal -->
        <div
            v-if="showQRModal"
            class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
        >
            <div class="w-full max-w-md rounded-lg bg-white p-6">
                <h3 class="mb-4 text-center text-lg font-semibold">
                    Thanh Toán ZaloPay
                </h3>

                <div class="mb-4 text-center">
                    <p class="mb-2 text-sm text-gray-600">
                        {{ paymentOrderInfo }}
                    </p>
                    <p class="text-lg font-bold text-blue-600">
                        {{ formatPrice(paymentAmount) }}
                    </p>
                </div>

                <div class="mb-4 text-center">
                    <div
                        class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white"
                    >
                        <p class="mb-2 text-sm">
                            📱 Mở ứng dụng ZaloPay để thanh toán
                        </p>
                        <a
                            :href="qrCodeUrl"
                            target="_blank"
                            class="inline-block rounded-lg bg-white px-6 py-2 font-semibold text-blue-600 transition-colors hover:bg-gray-100"
                        >
                            Mở ZaloPay App
                        </a>
                    </div>
                </div>

                <div class="mb-4 text-center">
                    <p class="text-xs text-gray-500">
                        Hoặc copy link này vào trình duyệt:
                    </p>
                    <div class="rounded bg-gray-100 p-2 text-xs break-all">
                        {{ qrCodeUrl }}
                    </div>
                </div>

                <div class="text-center">
                    <button
                        @click="showQRModal = false"
                        class="rounded-lg bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400"
                    >
                        Đóng
                    </button>
                </div>
            </div>
        </div>

        <!-- Demo Links Section -->
        <div class="mt-8 rounded-lg border bg-gray-50 p-6">
            <h3 class="mb-4 text-lg font-semibold">Payment Gateway Demo</h3>
            <p class="mb-4 text-gray-600">
                Test các phương thức thanh toán trong môi trường sandbox:
            </p>

            <div class="flex flex-wrap gap-3">
                <a
                    href="/admin/subscriptions/zalopay-demo"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                >
                    <span class="mr-2">💳</span>
                    ZaloPay Demo
                </a>
                <a
                    href="/admin/subscriptions/vnpay-demo"
                    class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-white transition-colors hover:bg-green-700"
                >
                    <span class="mr-2">🏦</span>
                    VNPay Demo
                </a>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    packages: Array,
    currentSubscription: Object,
    paymentHistory: Array,
    company: Object,
});

// Breadcrumbs
const breadcrumbs = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Gói Dịch Vụ', href: '/admin/subscriptions' },
];

const showRenewModal = ref(false);
const showUpgradeModal = ref(false);
const showPaymentModal = ref(false);
const showQRModal = ref(false);
const selectedPackage = ref(null);
const selectedPaymentMethod = ref('zalopay');
const qrCodeUrl = ref('');
const paymentAmount = ref(0);
const paymentOrderInfo = ref('');

const renewForm = ref({
    payment_method: 'bank_transfer',
});

const upgradeForm = ref({
    package_id: null,
    payment_method: 'bank_transfer',
});

// Computed
const availableUpgrades = computed(() => {
    if (!props.currentSubscription) return props.packages;
    return props.packages.filter(
        (pkg) => pkg.price > props.currentSubscription.package.price,
    );
});

// Methods
const subscribe = (packageItem) => {
    // Kiểm tra xem có phải gói hiện tại không
    if (isCurrentPackage(packageItem)) {
        alert('Bạn đang sử dụng gói này rồi!');
        return;
    }

    // Kiểm tra xem có phải downgrade không
    if (
        props.currentSubscription &&
        packageItem.price < props.currentSubscription.package.price
    ) {
        alert('Không thể hạ cấp gói dịch vụ. Vui lòng liên hệ admin.');
        return;
    }

    // Hiển thị modal chọn phương thức thanh toán
    showPaymentModal.value = true;
    selectedPackage.value = packageItem;
};

const confirmPayment = () => {
    if (!selectedPackage.value || !selectedPaymentMethod.value) {
        alert('Vui lòng chọn phương thức thanh toán');
        return;
    }

    if (selectedPaymentMethod.value === 'zalopay') {
        // Xử lý thanh toán ZaloPay
        router.post(
            '/admin/subscriptions/subscribe',
            {
                package_id: selectedPackage.value.id,
                payment_method: selectedPaymentMethod.value,
            },
            {
                onSuccess: () => {
                    showPaymentModal.value = false;
                    // Lấy payment data từ API
                    fetchPaymentData();
                },
                onError: (errors) => {
                    console.error('Subscription error:', errors);
                    alert(
                        'Có lỗi xảy ra khi tạo thanh toán ZaloPay. Vui lòng thử lại.',
                    );
                },
            },
        );
    } else if (selectedPaymentMethod.value === 'vnpay') {
        // Xử lý thanh toán VNPay
        router.post(
            '/admin/subscriptions/subscribe',
            {
                package_id: selectedPackage.value.id,
                payment_method: selectedPaymentMethod.value,
            },
            {
                onSuccess: () => {
                    showPaymentModal.value = false;
                    // Lấy payment data từ API
                    fetchPaymentData();
                },
                onError: (errors) => {
                    console.error('Subscription error:', errors);
                    alert(
                        'Có lỗi xảy ra khi tạo thanh toán VNPay. Vui lòng thử lại.',
                    );
                },
            },
        );
    } else {
        // Các phương thức thanh toán khác
        router.post(
            '/admin/subscriptions/subscribe',
            {
                package_id: selectedPackage.value.id,
                payment_method: selectedPaymentMethod.value,
            },
            {
                onSuccess: () => {
                    showPaymentModal.value = false;
                    router.reload();
                },
                onError: (errors) => {
                    console.error('Subscription error:', errors);
                    alert(
                        'Có lỗi xảy ra khi đăng ký gói dịch vụ. Vui lòng thử lại.',
                    );
                },
            },
        );
    }
};

const fetchPaymentData = async () => {
    try {
        const response = await fetch('/admin/subscriptions/qr/data');
        const data = await response.json();

        if (data.error) {
            alert('Không tìm thấy payment data');
            return;
        }

        paymentAmount.value = data.amount;
        paymentOrderInfo.value = data.order_info;

        // Hiển thị modal với link đến ZaloPay
        showPaymentModal.value = false;
        showQRModal.value = true;

        // Lưu URL để có thể redirect
        qrCodeUrl.value = data.order_url;
    } catch (error) {
        console.error('Error fetching payment data:', error);
        alert('Có lỗi khi lấy payment data');
    }
};

const renewSubscription = () => {
    router.post('/admin/subscriptions/renew', renewForm.value, {
        onSuccess: () => {
            showRenewModal.value = false;
            router.reload();
        },
        onError: (errors) => {
            console.error('Renew error:', errors);
            alert('Có lỗi xảy ra khi gia hạn gói dịch vụ. Vui lòng thử lại.');
        },
    });
};

const upgradeSubscription = () => {
    if (!upgradeForm.value.package_id) {
        alert('Vui lòng chọn gói dịch vụ để nâng cấp');
        return;
    }

    router.post('/admin/subscriptions/upgrade', upgradeForm.value, {
        onSuccess: () => {
            showUpgradeModal.value = false;
            router.reload();
        },
        onError: (errors) => {
            console.error('Upgrade error:', errors);
            alert('Có lỗi xảy ra khi nâng cấp gói dịch vụ. Vui lòng thử lại.');
        },
    });
};

const cancelSubscription = () => {
    if (confirm('Bạn có chắc chắn muốn hủy gói dịch vụ?')) {
        router.post(
            '/admin/subscriptions/cancel',
            {},
            {
                onSuccess: () => {
                    router.reload();
                },
                onError: (errors) => {
                    console.error('Cancel error:', errors);
                    alert(
                        'Có lỗi xảy ra khi hủy gói dịch vụ. Vui lòng thử lại.',
                    );
                },
            },
        );
    }
};

const isCurrentPackage = (packageItem) => {
    return props.currentSubscription?.package_id === packageItem.id;
};

const formatPrice = (price) => {
    if (!price) return '0 ₫';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(price);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('vi-VN');
};

const getDaysRemaining = (expiresAt) => {
    if (!expiresAt) return 0;
    const now = new Date();
    const expiry = new Date(expiresAt);
    const diffTime = expiry - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return Math.max(0, diffDays);
};

const getProgressPercentage = (subscription) => {
    if (!subscription) return 0;
    const start = new Date(subscription.starts_at);
    const end = new Date(subscription.expires_at);
    const now = new Date();

    const totalDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    const usedDays = Math.ceil((now - start) / (1000 * 60 * 60 * 24));

    return Math.min(100, Math.max(0, Math.round((usedDays / totalDays) * 100)));
};

const getStatusText = (status) => {
    const statusMap = {
        active: 'Hoạt động',
        expired: 'Hết hạn',
        cancelled: 'Đã hủy',
        pending: 'Chờ thanh toán',
        completed: 'Đã thanh toán',
        failed: 'Thất bại',
        refunded: 'Đã hoàn tiền',
    };
    return statusMap[status] || status;
};

const getStatusClass = (status) => {
    const classMap = {
        active: 'bg-green-100 text-green-800',
        expired: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-blue-100 text-blue-800',
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentMethodText = (method) => {
    const methodMap = {
        bank_transfer: 'Chuyển khoản',
        credit_card: 'Thẻ tín dụng',
        zalopay: 'ZaloPay',
        vnpay: 'VNPay',
    };
    return methodMap[method] || method;
};
</script>
