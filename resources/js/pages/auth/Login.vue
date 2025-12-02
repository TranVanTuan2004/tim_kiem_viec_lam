<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayoutWithHeader from '@/layouts/auth/AuthLayoutWithHeader.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Search, Building2, FileText, TrendingUp } from 'lucide-vue-next';
import type { PageProps } from '@inertiajs/core';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const page = usePage<PageProps & {
    flash?: {
        success?: string;
        error?: string;
    };
}>();
</script>

<template>
    <AuthLayoutWithHeader title="Đăng nhập">
        <Head title="Đăng nhập" />

        <div class="container mx-auto px-4 py-12 lg:py-16">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center max-w-7xl mx-auto">
                <!-- Left Column: Login Form -->
                <div class="w-full max-w-md mx-auto lg:mx-0">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center space-x-2 mb-8">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-red-600 shadow-md">
                            <span class="text-xl font-bold text-white">IT</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">Job Portal</span>
                    </Link>

                    <!-- Title -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Đăng nhập</h1>
                        <p class="text-gray-600">Chào mừng bạn quay trở lại</p>
                    </div>

                    <!-- Flash Messages -->
                    <div v-if="page.props.flash?.error" class="mb-4 text-center text-sm font-medium text-red-600 bg-red-50 p-3 rounded-md border border-red-200">
                        {{ page.props.flash.error }}
                    </div>

                    <div v-if="page.props.flash?.success" class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
                        {{ page.props.flash.success }}
                    </div>

                    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                        {{ status }}
                    </div>

                    <!-- Login Form -->
                    <Form
                        v-bind="AuthenticatedSessionController.store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div v-if="errors.general" class="mb-4 text-red-600 text-sm text-center">
                            {{ errors.general }}
                        </div>
                        
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="text"
                                    name="email"
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="email"
                                    placeholder="your.email@example.com"
                                    class="h-11"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label for="password">Mật khẩu</Label>
                                    <TextLink
                                        v-if="canResetPassword"
                                        :href="request()"
                                        class="text-sm text-red-600 hover:text-red-700"
                                        :tabindex="5"
                                    >
                                        Quên mật khẩu?
                                    </TextLink>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="h-11"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <div class="flex items-center">
                                <Label for="remember" class="flex items-center space-x-2 cursor-pointer">
                                    <Checkbox id="remember" name="remember" :tabindex="3" />
                                    <span class="text-sm">Ghi nhớ tài khoản</span>
                                </Label>
                            </div>

                            <Button
                                type="submit"
                                class="w-full h-11 bg-red-600 hover:bg-red-700"
                                :tabindex="4"
                                :disabled="processing"
                                data-test="login-button"
                            >
                                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                                Đăng nhập
                            </Button>
                        </div>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <span class="w-full border-t"></span>
                            </div>
                            <div class="relative flex justify-center text-xs uppercase">
                                <span class="bg-gray-50 px-2 text-gray-500">Hoặc</span>
                            </div>
                        </div>

                        <!-- Social Login -->
                        <Button
                            as="a"
                            href="/auth/google"
                            class="bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 w-full h-11"
                            variant="outline"
                        >
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Đăng nhập với Google
                        </Button>

                        <!-- Register Link -->
                        <div class="text-center text-sm text-gray-600">
                            Bạn chưa có tài khoản?
                            <TextLink :href="register()" class="text-red-600 hover:text-red-700 font-medium" :tabindex="5">
                                Đăng ký ngay
                            </TextLink>
                        </div>
                    </Form>
                </div>

                <!-- Right Column: Promotional Content -->
                <div class="hidden lg:block">
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                                Khám phá hơn 40,000+<br/>
                                cơ hội việc làm IT
                            </h2>
                            <p class="text-lg text-gray-600">
                                Kết nối với các công ty công nghệ hàng đầu và tìm kiếm công việc phù hợp với bạn
                            </p>
                        </div>

                        <div class="space-y-6">
                            <!-- Feature 1 -->
                            <div class="flex items-start gap-4 group">
                                <div class="flex-shrink-0 w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                    <Search class="w-7 h-7 text-red-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-900 mb-1">Tìm kiếm việc làm dễ dàng</h3>
                                    <p class="text-gray-600">
                                        Tiếp cận hàng ngàn tin tuyển dụng từ các công ty hàng đầu với công cụ tìm kiếm thông minh
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="flex items-start gap-4 group">
                                <div class="flex-shrink-0 w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <Building2 class="w-7 h-7 text-blue-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-900 mb-1">Kết nối với nhà tuyển dụng</h3>
                                    <p class="text-gray-600">
                                        Gặp gỡ trực tiếp với các công ty công nghệ hàng đầu và nhận được phản hồi nhanh chóng
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="flex items-start gap-4 group">
                                <div class="flex-shrink-0 w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                    <FileText class="w-7 h-7 text-green-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-900 mb-1">Tạo CV chuyên nghiệp</h3>
                                    <p class="text-gray-600">
                                        Sử dụng công cụ tạo CV miễn phí với các template chuyên nghiệp và hiện đại
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 4 -->
                            <div class="flex items-start gap-4 group">
                                <div class="flex-shrink-0 w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                    <TrendingUp class="w-7 h-7 text-purple-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-900 mb-1">Phát triển sự nghiệp</h3>
                                    <p class="text-gray-600">
                                        Truy cập các khóa học, bài viết và tài nguyên giúp bạn phát triển kỹ năng
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-6 pt-6 border-t">
                            <div>
                                <div class="text-3xl font-bold text-red-600">40K+</div>
                                <div class="text-sm text-gray-600 mt-1">Việc làm</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-blue-600">5K+</div>
                                <div class="text-sm text-gray-600 mt-1">Công ty</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-green-600">100K+</div>
                                <div class="text-sm text-gray-600 mt-1">Ứng viên</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayoutWithHeader>
</template>
