<script setup lang="ts">
import NewPasswordController from '@/actions/App/Http/Controllers/Auth/NewPasswordController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayoutWithHeader from '@/layouts/auth/AuthLayoutWithHeader.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayoutWithHeader title="Đặt lại mật khẩu">
        <Head title="Đặt lại mật khẩu" />

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt lại mật khẩu</h1>
                    <p class="text-gray-600">Vui lòng nhập mật khẩu mới của bạn</p>
                </div>

                <!-- Reset Password Form -->
                <Form
                    v-bind="NewPasswordController.store.form()"
                    :transform="(data) => ({ ...data, token, email })"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                >
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                autocomplete="email"
                                v-model="inputEmail"
                                readonly
                                class="h-11 bg-gray-50"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="space-y-2">
                            <Label for="password">Mật khẩu mới</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                autofocus
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
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="h-11"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <Button
                            type="submit"
                            class="w-full h-11 bg-red-600 hover:bg-red-700"
                            :disabled="processing"
                            data-test="reset-password-button"
                        >
                            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin mr-2" />
                            Đặt lại mật khẩu
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthLayoutWithHeader>
</template>
