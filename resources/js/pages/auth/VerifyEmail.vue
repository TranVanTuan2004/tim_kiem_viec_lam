<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { LoaderCircle, CheckCircle2, XCircle, Mail } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps<{
    status?: string;
    email?: string | null;
    error?: string | null;
    success?: string | null;
}>();

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Form state
const emailInput = ref(props.email || '');
const isLoading = ref(false);
const message = ref<{ type: 'success' | 'error'; text: string } | null>(null);

// Hiển thị error/success từ session nếu có
if (props.error) {
    message.value = { type: 'error', text: props.error };
} else if (props.success) {
    message.value = { type: 'success', text: props.success };
}

// Resend verification email
const resendVerification = async () => {
    if (!emailInput.value) {
        message.value = {
            type: 'error',
            text: 'Vui lòng nhập email'
        };
        return;
    }

    isLoading.value = true;
    message.value = null;

    try {
        const response = await axios.post('/email/resend', {
            email: emailInput.value
        });

        message.value = {
            type: 'success',
            text: response.data.message || 'Email xác thực đã được gửi lại'
        };
    } catch (error: any) {
        const errorMessage = error.response?.data?.message || 'Gửi email xác thực thất bại, vui lòng thử lại sau';
        message.value = {
            type: 'error',
            text: errorMessage
        };
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <AuthLayout
        title="Xác minh email"
        description="Vui lòng xác minh email của bạn bằng cách nhấn vào đường link mà chúng tôi vừa gửi đến email của bạn."
    >
        <Head title="Xác minh email" />

        <!-- Success message when verification link is sent -->
        <Alert v-if="status === 'verification-link-sent'" class="mb-4 border-green-200 bg-green-50">
            <CheckCircle2 class="h-4 w-4 text-green-600" />
            <AlertDescription class="text-green-800">
                Một liên kết xác minh mới đã được gửi đến địa chỉ email mà bạn đã cung cấp khi đăng ký.
            </AlertDescription>
        </Alert>

        <!-- Response message from resend action -->
        <Alert v-if="message" :class="message.type === 'success' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'" class="mb-4">
            <CheckCircle2 v-if="message.type === 'success'" class="h-4 w-4 text-green-600" />
            <XCircle v-else class="h-4 w-4 text-red-600" />
            <AlertDescription :class="message.type === 'success' ? 'text-green-800' : 'text-red-800'">
                {{ message.text }}
            </AlertDescription>
        </Alert>

        <div class="space-y-6">
            <!-- Email input field -->
            <div class="space-y-2">
                <Label for="email">Email</Label>
                <div class="relative">
                    <Mail class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="email"
                        v-model="emailInput"
                        type="email"
                        placeholder="Nhập email của bạn"
                        :disabled="isLoading || (isAuthenticated && !!props.email)"
                        class="pl-10"
                        @keyup.enter="resendVerification"
                    />
                </div>
                <p class="text-xs text-muted-foreground">
                    {{ isAuthenticated && props.email 
                        ? 'Email đã được điền tự động từ tài khoản của bạn' 
                        : 'Nhập email bạn đã sử dụng khi đăng ký' 
                    }}
                </p>
            </div>

            <!-- Resend button -->
            <Button 
                @click="resendVerification" 
                :disabled="isLoading || !emailInput"
                variant="secondary"
                class="w-full"
            >
                <LoaderCircle v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                <Mail v-else class="mr-2 h-4 w-4" />
                Gửi lại email xác thực
            </Button>

            <!-- Action links -->
            <div class="flex flex-col gap-2 text-center text-sm">
                <TextLink
                    v-if="isAuthenticated"
                    :href="logout()"
                    as="button"
                    class="text-muted-foreground hover:text-foreground"
                >
                    Đăng xuất
                </TextLink>
                <TextLink
                    v-else
                    href="/login"
                    class="text-muted-foreground hover:text-foreground"
                >
                    Quay lại đăng nhập
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
