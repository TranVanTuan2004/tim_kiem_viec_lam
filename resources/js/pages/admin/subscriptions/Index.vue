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
                    <!-- Badge hiển thị nếu là gói hiện tại -->
                    <div v-if="isCurrentPackage(packageItem)" class="mb-2 text-center">
                        <span class="inline-block bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            ✓ Gói Hiện Tại
                        </span>
                    </div>
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
                        <option value="vnpay">VNPay</option>
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
                        <option value="vnpay">VNPay</option>
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
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all animate-scaleIn">
            <!-- Header với gradient -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-t-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Nâng Cấp Gói Dịch Vụ</h3>
                            <p class="text-blue-100 text-sm mt-1">Chọn phương thức thanh toán</p>
                        </div>
                    </div>
                    <button 
                        @click="showPaymentModal = false"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <!-- Package Info Card -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 mb-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 rounded-lg p-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Gói dịch vụ</p>
                                <p class="text-lg font-bold text-gray-900">{{ selectedPackage?.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tổng thanh toán</span>
                            <span class="text-2xl font-bold text-blue-600">{{ formatPrice(selectedPackage?.price) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Phương thức thanh toán</label>
                    <div class="relative">
                        <select v-model="selectedPaymentMethod" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3.5 pr-10 text-gray-700 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300">
                            <option value="vnpay">VNPay - Thanh toán qua ví điện tử</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Bảo mật và an toàn</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <form 
                        id="vnpay-payment-form"
                        action="/admin/subscriptions/vnpay_payment" 
                        method="POST" 
                        class="flex-1"
                    >
                        <input type="hidden" name="_token" id="vnpay-csrf-token" value="">
                        <input type="hidden" name="package_id" id="vnpay-package-id" value="">
                        <input type="hidden" name="total_vnpay" id="vnpay-total" value="">
                        <input type="hidden" name="payment_method" value="vnpay">
                        <input type="hidden" name="redirect" value="1">
                        <button 
                            type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all shadow-lg hover:shadow-xl flex items-center justify-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Thanh Toán Ngay</span>
                        </button>
                    </form>
                    <button 
                        @click="showPaymentModal = false"
                        class="px-6 py-3.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors"
                    >
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- VNPay QR Code Modal -->
    <div v-if="showQRModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all animate-scaleIn overflow-hidden">
            <!-- Header với gradient xanh lá -->
            <div class="bg-gradient-to-r from-green-500 via-green-600 to-emerald-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Thanh Toán VNPay</h3>
                            <p class="text-green-100 text-sm mt-1">Quét mã QR để thanh toán</p>
                        </div>
                    </div>
                    <button 
                        @click="closeQRModal"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <!-- Order Info -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 mb-6 border border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">{{ paymentOrderInfo }}</p>
                        <p class="text-3xl font-bold text-green-600">{{ formatPrice(paymentAmount) }}</p>
                    </div>
                </div>
                
                <!-- QR Code -->
                <div class="text-center mb-6">
                    <div class="bg-white p-6 rounded-2xl border-4 border-green-500 shadow-lg inline-block">
                        <div class="bg-white p-2 rounded-lg">
                            <img :src="qrCodeUrl" alt="VNPay QR Code" class="w-64 h-64 mx-auto" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-center space-x-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Mã QR hợp lệ</span>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-200">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Hướng dẫn thanh toán:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Mở ứng dụng VNPay trên điện thoại</li>
                                <li>Quét mã QR code ở trên</li>
                                <li>Xác nhận thanh toán trong app</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a 
                        :href="vnpayPaymentUrl" 
                        target="_blank"
                        class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3.5 px-6 rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transform hover:scale-[1.02] transition-all shadow-lg hover:shadow-xl text-center flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>Mở Trang Thanh Toán VNPay</span>
                    </a>
                    <button
                        @click="closeQRModal"
                        class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-200 transition-colors"
                    >
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
        <!-- Toast Notification -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 translate-x-full"
        >
            <div
                v-if="toast.show"
                :class="toast.type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-green-50 border-green-200 text-green-800'"
                class="fixed top-4 right-4 z-50 max-w-md rounded-lg border shadow-lg p-4 flex items-start space-x-3"
            >
                <div v-if="toast.type === 'error'" class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div v-else class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium">{{ toast.message }}</p>
                </div>
                <button
                    @click="hideToast"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
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
const selectedPaymentMethod = ref('vnpay');
const qrCodeUrl = ref('');
const vnpayPaymentUrl = ref('');
const paymentAmount = ref(0);
const paymentOrderInfo = ref('');

// Toast notification
const toast = ref({
    show: false,
    message: '',
    type: 'error' // 'error' or 'success'
});

let toastTimeout = null;

const showToast = (message, type = 'error') => {
    toast.value = {
        show: true,
        message,
        type
    };
    
    // Auto hide after 5 seconds
    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }
    toastTimeout = setTimeout(() => {
        hideToast();
    }, 5000);
};

const hideToast = () => {
    toast.value.show = false;
    if (toastTimeout) {
        clearTimeout(toastTimeout);
        toastTimeout = null;
    }
};

// Check for flash messages on mount
const page = usePage();
onMounted(() => {
    // Debug: Log subscription data
    console.log('=== Subscription Debug ===');
    console.log('Current Subscription:', props.currentSubscription);
    console.log('Packages:', props.packages);
    if (props.currentSubscription) {
        console.log('Current Package ID:', props.currentSubscription.package_id);
        console.log('Current Package Object:', props.currentSubscription.package);
        console.log('Package ID from relationship:', props.currentSubscription.package?.id);
    }
    
    if (page.props.flash?.error) {
        showToast(page.props.flash.error, 'error');
    }
    if (page.props.flash?.success) {
        showToast(page.props.flash.success, 'success');
    }
});


const renewForm = ref({
    payment_method: 'vnpay',
});

const upgradeForm = ref({
    package_id: null,
    payment_method: 'vnpay',
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
        showToast('Bạn đang sử dụng gói này rồi!', 'error');
        return;
    }
    
    // Kiểm tra xem có phải downgrade không
    if (props.currentSubscription && packageItem.price < props.currentSubscription.package.price) {
        showToast('Không thể hạ cấp gói dịch vụ. Vui lòng liên hệ admin.', 'error');
        return;
    }
    
    // Hiển thị modal chọn phương thức thanh toán
    showPaymentModal.value = true;
    selectedPackage.value = packageItem;
    
    // Set giá trị vào form HTML thuần sau khi modal render
    nextTick(() => {
        // Lấy CSRF token từ meta tag hoặc cookie
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Nếu không có trong meta tag, thử lấy từ cookie
        if (!csrfToken) {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'XSRF-TOKEN') {
                    csrfToken = decodeURIComponent(value);
                    break;
                }
            }
        }
        
        const form = document.getElementById('vnpay-payment-form');
        if (form) {
            const csrfInput = document.getElementById('vnpay-csrf-token');
            const packageInput = document.getElementById('vnpay-package-id');
            const totalInput = document.getElementById('vnpay-total');
            
            if (csrfInput && csrfToken) csrfInput.value = csrfToken;
            if (packageInput) packageInput.value = packageItem.id;
            if (totalInput) totalInput.value = packageItem.price;
        }
    });
};

const confirmPayment = async () => {
    if (!selectedPackage.value || !selectedPaymentMethod.value) {
        alert('Vui lòng chọn phương thức thanh toán');
        return;
    }
    
    // Xử lý thanh toán VNPay - tạo payment và hiển thị QR code
    // Sử dụng fetch để nhận JSON response
    try {
        const response = await fetch('/admin/subscriptions/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                package_id: selectedPackage.value.id,
                payment_method: selectedPaymentMethod.value,
            }),
        });

        const data = await response.json();

        if (data.success) {
            showPaymentModal.value = false;
            displayQRCode(data);
        } else {
            showToast(data.message || 'Có lỗi xảy ra khi tạo thanh toán VNPay. Vui lòng thử lại.', 'error');
        }
    } catch (error) {
        console.error('Subscription error:', error);
        showToast('Có lỗi xảy ra khi tạo thanh toán VNPay. Vui lòng thử lại.', 'error');
    }
};

// Hiển thị QR code từ payment URL
const displayQRCode = async (paymentData) => {
    try {
        paymentAmount.value = paymentData.amount || selectedPackage.value.price;
        paymentOrderInfo.value = paymentData.order_info || `Thanh toán gói ${selectedPackage.value.name}`;
        vnpayPaymentUrl.value = paymentData.payment_url;
        
        // Tạo QR code từ payment URL sử dụng API QR code
        // Sử dụng qrcode.js hoặc API online
        const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(paymentData.payment_url)}`;
        qrCodeUrl.value = qrApiUrl;
        
        showQRModal.value = true;
    } catch (error) {
        console.error('Error displaying QR code:', error);
        showToast('Có lỗi khi tạo QR code. Vui lòng thử lại.', 'error');
    }
};

const closeQRModal = () => {
    showQRModal.value = false;
    // Reload để cập nhật trạng thái payment
    router.reload();
};


const renewSubscription = () => {
    router.post('/admin/subscriptions/renew', renewForm.value, {
        onSuccess: () => {
            showRenewModal.value = false;
            showToast('Gia hạn gói dịch vụ thành công!', 'success');
            router.reload();
        },
        onError: (errors) => {
            console.error('Renew error:', errors);
            const errorMessage = errors?.message || errors?.error || 'Có lỗi xảy ra khi gia hạn gói dịch vụ. Vui lòng thử lại.';
            showToast(errorMessage, 'error');
        }
    });
};

const upgradeSubscription = () => {
    if (!upgradeForm.value.package_id) {
        showToast('Vui lòng chọn gói dịch vụ để nâng cấp', 'error');
        return;
    }
    
    router.post('/admin/subscriptions/upgrade', upgradeForm.value, {
        onSuccess: () => {
            showUpgradeModal.value = false;
            showToast('Nâng cấp gói dịch vụ thành công!', 'success');
            router.reload();
        },
        onError: (errors) => {
            console.error('Upgrade error:', errors);
            const errorMessage = errors?.message || errors?.error || 'Có lỗi xảy ra khi nâng cấp gói dịch vụ. Vui lòng thử lại.';
            showToast(errorMessage, 'error');
        }
    });
};

const cancelSubscription = () => {
    if (confirm('Bạn có chắc chắn muốn hủy gói dịch vụ?')) {
        router.post('/admin/subscriptions/cancel', {}, {
            onSuccess: () => {
                showToast('Hủy gói dịch vụ thành công!', 'success');
                router.reload();
            },
            onError: (errors) => {
                console.error('Cancel error:', errors);
                const errorMessage = errors?.message || errors?.error || 'Có lỗi xảy ra khi hủy gói dịch vụ. Vui lòng thử lại.';
                showToast(errorMessage, 'error');
            }
        });
    }
};

const isCurrentPackage = (packageItem) => {
    if (!props.currentSubscription) {
        return false;
    }
    
    // So sánh package_id để xác định gói hiện tại
    // Kiểm tra cả package_id trực tiếp và package.id từ relationship
    const currentPackageId = props.currentSubscription.package_id || props.currentSubscription.package?.id;
    
    if (!currentPackageId) {
        console.log('No package ID found for subscription');
        return false;
    }
    
    const isMatch = currentPackageId === packageItem.id;
    
    // Debug log
    if (isMatch) {
        console.log('Package matched:', {
            currentPackageId,
            packageItemId: packageItem.id,
            packageName: packageItem.name
        });
    }
    
    return isMatch;
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
        'vnpay': 'VNPay',
        'free': 'Miễn phí',
    };
    return methodMap[method] || method;
};
</script>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}

.animate-scaleIn {
    animation: scaleIn 0.3s ease-out;
}
</style>
