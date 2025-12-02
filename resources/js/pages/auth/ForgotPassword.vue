<script setup lang="ts">
import PasswordResetLinkController from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayoutWithHeader from '@/layouts/auth/AuthLayoutWithHeader.vue';
import { login } from '@/routes';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayoutWithHeader title="Quên mật khẩu">
        <Head title="Quên mật khẩu" />

        <div class="container mx-auto px-4 py-12">
            <div class="max-w-md mx-auto">
                <!-- Logo -->
                <Link href="/" class="flex items-center space-x-2 mb-8 justify-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-red-600 shadow-md">
                        <span class="text-xl font-bold text-white">IT</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">Job Portal</span>
                </Link>

                <!-- Title -->
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Quên mật khẩu</h1>
                    <p class="text-gray-600">Nhập email của bạn để nhận liên kết đặt lại mật khẩu</p>
                </div>

                <!-- Success Status -->
                <div v-if="status" class="mb-6 text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
                    {{ status }}
                </div>

                <!-- Forgot Password Form -->
                <div class="space-y-6">
                    <Form
                        v-bind="PasswordResetLinkController.store.form()"
                        v-slot="{ errors, processing }"
                        novalidate
                    >
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="email">Địa chỉ email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    autocomplete="off"
                                    autofocus
                                    placeholder="your.email@example.com"
                                    class="h-11"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <Button
                                type="submit"
                                class="w-full h-11 bg-red-600 hover:bg-red-700"
                                :disabled="processing"
                                data-test="email-password-reset-link-button"
                            >
                                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                                Gửi liên kết đặt lại mật khẩu
                            </Button>
                        </div>
                    </Form>

                    <div class="text-center text-sm text-gray-600">
                        <span>Hoặc, quay lại </span>
                        <TextLink :href="login()" class="text-red-600 hover:text-red-700 font-medium">
                            Đăng nhập
                        </TextLink>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayoutWithHeader>
</template>
