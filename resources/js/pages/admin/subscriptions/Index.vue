<template>
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto py-8 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gói Dịch Vụ</h1>
            <p class="text-gray-600">Chọn gói dịch vụ phù hợp với nhu cầu tuyển dụng của bạn</p>
        </div>

        <!-- Current Subscription Card -->
        <div v-if="currentSubscription" class="mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-blue-900">Gói Hiện Tại</h2>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ getStatusText(currentSubscription.status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Gói dịch vụ</p>
                        <p class="font-semibold">{{ currentSubscription.package.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Giá</p>
                        <p class="font-semibold">{{ formatPrice(currentSubscription.package.price) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Hết hạn</p>
                        <p class="font-semibold">{{ formatDate(currentSubscription.expires_at) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Còn lại</p>
                        <p class="font-semibold">{{ getDaysRemaining(currentSubscription.expires_at) }} ngày</p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Tiến độ sử dụng</span>
                        <span>{{ getProgressPercentage(currentSubscription) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: getProgressPercentage(currentSubscription) + '%' }"
                        ></div>
                    </div>
                </div>
                
                <!-- Action Buttons - Chỉ hiện khi không phải gói Free -->
                <div v-if="currentSubscription.package.price > 0" class="flex gap-3">
                    <button 
                        @click="showRenewModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Gia Hạn
                    </button>
                    <button 
                        @click="showUpgradeModal = true"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                    >
                        Nâng Cấp
                    </button>
                    <button 
                        @click="cancelSubscription"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Hủy Gói
                    </button>
                </div>
                
                <!-- Thông báo cho gói Free -->
                <div v-else class="text-center py-4">
                    <p class="text-gray-600 mb-2">🎉 Bạn đang sử dụng gói miễn phí!</p>
                    <p class="text-sm text-gray-500">Để nâng cấp lên gói Premium, hãy chọn gói bên dưới</p>
                </div>
            </div>
        </div>

        <!-- Packages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div 
                v-for="packageItem in packages" 
                :key="packageItem.id"
                class="border rounded-xl p-6 hover:shadow-xl transition-all duration-300 flex flex-col h-full bg-white"
                :class="{
                    'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 ring-2 ring-blue-200': isCurrentPackage(packageItem),
                    'border-gray-200 hover:border-gray-300': !isCurrentPackage(packageItem)
                }"
            >
                <div class="text-center mb-6 flex-shrink-0">
                    <h3 class="text-2xl font-bold mb-3 text-gray-800">{{ packageItem.name }}</h3>
                    <div class="text-4xl font-bold text-blue-600 mb-3">
                        {{ formatPrice(packageItem.price) }}
                    </div>
                    <p class="text-gray-600 mb-3">{{ packageItem.duration_days }} ngày</p>
                    
                    <!-- Giới hạn đăng bài -->
                    <div class="mb-4">
                        <span class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium px-4 py-2 rounded-full shadow-sm">
                            {{ packageItem.max_jobs === 0 ? 'Không giới hạn' : `${packageItem.max_jobs} bài` }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-6 flex-grow">
                    <p class="text-gray-700 mb-4 text-center">{{ packageItem.description }}</p>
                    
                    <ul class="space-y-3">
                        <li 
                            v-for="feature in packageItem.features" 
                            :key="feature" 
                            class="flex items-start text-sm text-gray-600"
                        >
                            <Check class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" />
                            <span>{{ feature }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mt-auto">
                    <button 
                        @click="subscribe(packageItem)"
                        class="w-full py-3 px-6 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        :class="{
                            'bg-gray-300 text-gray-500 cursor-not-allowed': isCurrentPackage(packageItem),
                            'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800': !isCurrentPackage(packageItem)
                        }"
                        :disabled="isCurrentPackage(packageItem)"
                    >
                        {{ isCurrentPackage(packageItem) ? 'Gói Hiện Tại' : 'Nâng Cấp' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div v-if="paymentHistory.length > 0" class="bg-white rounded-lg border p-6">
            <h3 class="text-lg font-semibold mb-4">Lịch Sử Thanh Toán</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Gói</th>
                            <th class="text-left py-2">Số tiền</th>
                            <th class="text-left py-2">Phương thức</th>
                            <th class="text-left py-2">Trạng thái</th>
                            <th class="text-left py-2">Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="payment in paymentHistory" :key="payment.id" class="border-b">
                            <td class="py-2">{{ payment.package?.name || 'N/A' }}</td>
                            <td class="py-2">{{ formatPrice(payment.amount) }}</td>
                            <td class="py-2">{{ getPaymentMethodText(payment.payment_method) }}</td>
                            <td class="py-2">
                                <span 
                                    class="px-2 py-1 rounded text-xs"
                                    :class="getStatusClass(payment.status)"
                                >
                                    {{ getStatusText(payment.status) }}
                                </span>
                            </td>
                            <td class="py-2">{{ formatDate(payment.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Renew Modal -->
        <div v-if="showRenewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Gia Hạn Gói Dịch Vụ</h3>
                <p class="text-gray-600 mb-4">
                    Bạn sẽ gia hạn gói <strong>{{ currentSubscription?.package?.name }}</strong> 
                    với giá <strong>{{ formatPrice(currentSubscription?.package?.price) }}</strong>
                </p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Phương thức thanh toán</label>
                    <select v-model="renewForm.payment_method" class="w-full border rounded-lg px-3 py-2">
                        <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                        <option value="credit_card">Thẻ tín dụng</option>
                        <option value="momo">Ví MoMo</option>
                        <option value="vnpay">VNPay</option>
                        <option value="zalopay">ZaloPay</option>
                    </select>
                </div>
                
                <div class="flex gap-3">
                    <button 
                        @click="renewSubscription"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
                    >
                        Xác Nhận Gia Hạn
                    </button>
                    <button 
                        @click="showRenewModal = false"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                    >
                        Hủy
                    </button>
                </div>
            </div>
        </div>

        <!-- Upgrade Modal -->
        <div v-if="showUpgradeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Nâng Cấp Gói Dịch Vụ</h3>
                <p class="text-gray-600 mb-4">Chọn gói dịch vụ để nâng cấp:</p>
                
                <div class="space-y-3 mb-4">
                    <label 
                        v-for="packageItem in availableUpgrades" 
                        :key="packageItem.id"
                        class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                    >
                        <input 
                            type="radio" 
                            v-model="upgradeForm.package_id" 
                            :value="packageItem.id"
                            class="mr-3"
                        >
                        <div class="flex-1">
                            <div class="font-medium">{{ packageItem.name }}</div>
                            <div class="text-sm text-gray-600">{{ formatPrice(packageItem.price) }}</div>
                        </div>
                    </label>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Phương thức thanh toán</label>
                    <select v-model="upgradeForm.payment_method" class="w-full border rounded-lg px-3 py-2">
                        <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                        <option value="credit_card">Thẻ tín dụng</option>
                        <option value="momo">Ví MoMo</option>
                        <option value="vnpay">VNPay</option>
                        <option value="zalopay">ZaloPay</option>
                    </select>
                </div>
                
                <div class="flex gap-3">
                    <button 
                        @click="upgradeSubscription"
                        class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700"
                    >
                        Xác Nhận Nâng Cấp
                    </button>
                    <button 
                        @click="showUpgradeModal = false"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                    >
                        Hủy
                    </button>
                </div>
            </div>
        </div>
        </div>

        <!-- Payment Method Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Nâng Cấp Gói Dịch Vụ</h3>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Gói dịch vụ: <strong>{{ selectedPackage?.name }}</strong></p>
                <p class="text-sm text-gray-600 mb-4">Số tiền: <strong>{{ formatPrice(selectedPackage?.price) }}</strong></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Phương thức thanh toán</label>
                        <select v-model="selectedPaymentMethod" class="w-full border rounded-lg px-3 py-2">
                            <option value="zalopay">Ví ZaloPay</option>
                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                            <option value="credit_card">Thẻ tín dụng</option>
                            <option value="vnpay">VNPay</option>
                        </select>
            </div>
            
            <div class="flex gap-3">
                <button 
                    @click="confirmPayment"
                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
                >
                    Nâng Cấp Ngay
                </button>
                <button 
                    @click="showPaymentModal = false"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                >
                    Hủy
                </button>
            </div>
        </div>
    </div>
    
                <!-- ZaloPay Payment Modal -->
                <div v-if="showQRModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4 text-center">Thanh Toán ZaloPay</h3>

                        <div class="text-center mb-4">
                            <p class="text-sm text-gray-600 mb-2">{{ paymentOrderInfo }}</p>
                            <p class="text-lg font-bold text-blue-600">{{ formatPrice(paymentAmount) }}</p>
                        </div>

                        <div class="text-center mb-4">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-lg">
                                <p class="text-sm mb-2">📱 Mở ứng dụng ZaloPay để thanh toán</p>
                                <a 
                                    :href="qrCodeUrl" 
                                    target="_blank" 
                                    class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
                                >
                                    Mở ZaloPay App
                                </a>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <p class="text-xs text-gray-500">Hoặc copy link này vào trình duyệt:</p>
                            <div class="bg-gray-100 p-2 rounded text-xs break-all">
                                {{ qrCodeUrl }}
                            </div>
                        </div>

                        <div class="text-center">
                            <button
                                @click="showQRModal = false"
                                class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                            >
                                Đóng
                            </button>
                        </div>
                    </div>
                </div>

        <!-- Demo Links Section -->
        <div class="mt-8 bg-gray-50 border rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Payment Gateway Demo</h3>
            <p class="text-gray-600 mb-4">Test các phương thức thanh toán trong môi trường sandbox:</p>
            
            <div class="flex flex-wrap gap-3">
                <a 
                    href="/admin/subscriptions/zalopay-demo"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <span class="mr-2">💳</span>
                    ZaloPay Demo
                </a>
                <a 
                    href="/admin/subscriptions/vnpay-demo"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    <span class="mr-2">🏦</span>
                    VNPay Demo
                </a>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AppLayout.vue';

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
    return props.packages.filter(pkg => pkg.price > props.currentSubscription.package.price);
});

// Methods
const subscribe = (packageItem) => {
    // Kiểm tra xem có phải gói hiện tại không
    if (isCurrentPackage(packageItem)) {
        alert('Bạn đang sử dụng gói này rồi!');
        return;
    }
    
    // Kiểm tra xem có phải downgrade không
    if (props.currentSubscription && packageItem.price < props.currentSubscription.package.price) {
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
        router.post('/admin/subscriptions/subscribe', {
            package_id: selectedPackage.value.id,
            payment_method: selectedPaymentMethod.value,
        }, {
            onSuccess: () => {
                showPaymentModal.value = false;
                // Lấy payment data từ API
                fetchPaymentData();
            },
            onError: (errors) => {
                console.error('Subscription error:', errors);
                alert('Có lỗi xảy ra khi tạo thanh toán ZaloPay. Vui lòng thử lại.');
            }
        });
    } else if (selectedPaymentMethod.value === 'vnpay') {
        // Xử lý thanh toán VNPay
        router.post('/admin/subscriptions/subscribe', {
            package_id: selectedPackage.value.id,
            payment_method: selectedPaymentMethod.value,
        }, {
            onSuccess: () => {
                showPaymentModal.value = false;
                // Lấy payment data từ API
                fetchPaymentData();
            },
            onError: (errors) => {
                console.error('Subscription error:', errors);
                alert('Có lỗi xảy ra khi tạo thanh toán VNPay. Vui lòng thử lại.');
            }
        });
    } else {
        // Các phương thức thanh toán khác
        router.post('/admin/subscriptions/subscribe', {
            package_id: selectedPackage.value.id,
            payment_method: selectedPaymentMethod.value,
        }, {
            onSuccess: () => {
                showPaymentModal.value = false;
                router.reload();
            },
            onError: (errors) => {
                console.error('Subscription error:', errors);
                alert('Có lỗi xảy ra khi đăng ký gói dịch vụ. Vui lòng thử lại.');
            }
        });
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
        }
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
        }
    });
};

const cancelSubscription = () => {
    if (confirm('Bạn có chắc chắn muốn hủy gói dịch vụ?')) {
        router.post('/admin/subscriptions/cancel', {}, {
            onSuccess: () => {
                router.reload();
            },
            onError: (errors) => {
                console.error('Cancel error:', errors);
                alert('Có lỗi xảy ra khi hủy gói dịch vụ. Vui lòng thử lại.');
            }
        });
    }
};

const isCurrentPackage = (packageItem) => {
    return props.currentSubscription?.package_id === packageItem.id;
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
};

const getDaysRemaining = (expiresAt) => {
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
        'active': 'Hoạt động',
        'expired': 'Hết hạn',
        'cancelled': 'Đã hủy',
        'pending': 'Chờ thanh toán',
        'completed': 'Đã thanh toán',
        'failed': 'Thất bại',
        'refunded': 'Đã hoàn tiền',
    };
    return statusMap[status] || status;
};

const getStatusClass = (status) => {
    const classMap = {
        'active': 'bg-green-100 text-green-800',
        'expired': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'refunded': 'bg-blue-100 text-blue-800',
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentMethodText = (method) => {
    const methodMap = {
        'bank_transfer': 'Chuyển khoản',
        'credit_card': 'Thẻ tín dụng',
        'zalopay': 'ZaloPay',
        'vnpay': 'VNPay',
    };
    return methodMap[method] || method;
};
</script>
