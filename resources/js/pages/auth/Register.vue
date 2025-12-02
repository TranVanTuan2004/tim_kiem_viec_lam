<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayoutWithHeader from '@/layouts/auth/AuthLayoutWithHeader.vue';
import { login } from '@/routes';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthLayoutWithHeader title="Đăng ký">
        <Head title="Đăng ký" />

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Đăng ký tài khoản</h1>
                    <p class="text-gray-600">Tạo tài khoản để bắt đầu tìm việc</p>
                </div>

                <!-- Register Form -->
                <Form
                    v-bind="RegisteredUserController.store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                    novalidate
                >
                    <div v-if="errors.general" class="mb-4 text-red-500 text-center text-sm">
                        {{ errors.general }}
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Họ và tên</Label>
                            <Input
                                id="name"
                                type="text"
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                name="name"
                                placeholder="Nguyễn Văn A"
                                class="h-11"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                :tabindex="2"
                                autocomplete="email"
                                name="email"
                                placeholder="your.email@example.com"
                                class="h-11"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="space-y-2">
                            <Label for="phone">Số điện thoại</Label>
                            <Input
                                id="phone"
                                type="tel"
                                :tabindex="3"
                                autocomplete="tel"
                                name="phone"
                                placeholder="0912345678"
                                class="h-11"
                            />
                            <InputError :message="errors.phone" />
                        </div>

                        <div class="space-y-2">
                            <Label for="password">Mật khẩu</Label>
                            <Input
                                id="password"
                                type="password"
                                :tabindex="4"
                                autocomplete="new-password"
                                name="password"
                                placeholder="••••••••"
                                class="h-11"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="space-y-2">
                            <Label for="password_confirmation">Nhập lại mật khẩu</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                :tabindex="5"
                                autocomplete="new-password"
                                name="password_confirmation"
                                placeholder="••••••••"
                                class="h-11"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <Button
                            type="submit"
                            class="w-full h-11 bg-red-600 hover:bg-red-700"
                            tabindex="6"
                            :disabled="processing"
                            data-test="register-user-button"
                        >
                            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                            Đăng ký
                        </Button>
                    </div>

                    <div class="text-center text-sm text-gray-600">
                        Bạn đã có tài khoản?
                        <TextLink
                            :href="login()"
                            class="text-red-600 hover:text-red-700 font-medium"
                            :tabindex="7"
                        >
                            Đăng nhập
                        </TextLink>
                    </div>
                </Form>
            </div>
        </div>
    </AuthLayoutWithHeader>
</template>
