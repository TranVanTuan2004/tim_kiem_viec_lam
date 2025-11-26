<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase
        title="Đăng ký tài khoản"
        description="Nhập thông tin của bạn bên dưới để tạo tài khoản"
    >
        <Head title="Đăng ký" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
            novalidate
        >
            <!-- Hiển thị lỗi tổng quát 'general' -->
            <div v-if="errors.general" class="mb-4 text-red-500 text-center text-sm">
                {{ errors.general }}
            </div>
            
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Họ và tên</Label>
                    <Input
                        id="name"
                        type="text"
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Họ và tên"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="Email"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Số điện thoại</Label>
                    <Input
                        id="phone"
                        type="tel"
                        :tabindex="3"
                        autocomplete="tel"
                        name="phone"
                        placeholder="Số điện thoại"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Mật khẩu</Label>
                    <Input
                        id="password"
                        type="password"
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Mật khẩu"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Nhập lại mật khẩu</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Nhập lại mật khẩu"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="6"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Đăng ký
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Bạn đã có tài khoản?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="7"
                    >Đăng nhập</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
