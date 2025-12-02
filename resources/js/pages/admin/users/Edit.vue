```typescript
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Role } from '@/types'; // Added User and Role types from '@/types'
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft } from 'lucide-vue-next';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'; // Added RadioGroup and RadioGroupItem imports

// Removed local interface definitions for Role and User as they are now imported from '@/types'
// interface Role {
//     id: number;
//     name: string;
//     slug: string;
// }

// interface User {
//     id: number;
//     name: string;
//     email: string;
//     phone: string | null;
//     bio: string | null;
//     is_active: boolean;
//     roles: Role[];
// }

interface Props {
    user: User;
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    phone: props.user.phone || '',
    bio: props.user.bio || '',
    updated_at: props.user.updated_at, // Test Case 2: Optimistic locking
    role_id: props.user.roles.length > 0 ? String(props.user.roles[0].id) : null as string | null,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        roles: data.role_id ? [parseInt(data.role_id)] : [],
    })).put(`/admin/users/${props.user.id}`);
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Users', href: '/admin/users' },
    { title: 'Sửa', href: `/admin/users/${props.user.id}/edit` },
];
</script>

<template>
    <Head :title="`Sửa thông tin người dùng: ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Link href="/admin/users">
                    <Button variant="outline" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Sửa thông tin người dùng: {{ user.name }}</h1>
            </div>

            <form @submit.prevent="submit" novalidate>
                <!-- Test Case 2: Display concurrency update error -->
                <div v-if="form.errors.concurrent_update" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-medium">
                        {{ form.errors.concurrent_update }}
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Thông tin cơ bản -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Thông tin cơ bản</CardTitle>
                            <CardDescription>Cập nhật thông tin người dùng</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Họ tên *</Label>
                                <Input id="name" v-model="form.name" required />
                                <span v-if="form.errors.name" class="text-sm text-destructive">
                                    {{ form.errors.name }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email *</Label>
                                <Input id="email" v-model="form.email" type="email" required />
                                <span v-if="form.errors.email" class="text-sm text-destructive">
                                    {{ form.errors.email }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="phone">Số điện thoại</Label>
                                <Input id="phone" v-model="form.phone" />
                                <span v-if="form.errors.phone" class="text-sm text-destructive">
                                    {{ form.errors.phone }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="bio">Tiểu sử</Label>
                                <Textarea id="bio" v-model="form.bio" :rows="3" />
                                <span v-if="form.errors.bio" class="text-sm text-destructive">
                                    {{ form.errors.bio }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Mật khẩu & Phân quyền -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Đổi mật khẩu</CardTitle>
                                <CardDescription>Để trống nếu không muốn đổi</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="password">Mật khẩu mới</Label>
                                    <Input id="password" v-model="form.password" type="password" />
                                    <span v-if="form.errors.password" class="text-sm text-destructive">
                                        {{ form.errors.password }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation">Xác nhận mật khẩu</Label>
                                    <Input 
                                        id="password_confirmation" 
                                        v-model="form.password_confirmation" 
                                        type="password" 
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Vai trò</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <RadioGroup v-model="form.role_id" class="space-y-3">
                                    <div
                                        v-for="role in roles"
                                        :key="role.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <RadioGroupItem
                                            :id="`role-${role.id}`"
                                            :value="String(role.id)"
                                        />
                                        <Label
                                            :for="`role-${role.id}`"
                                            class="cursor-pointer"
                                        >
                                            {{ role.name }}
                                        </Label>
                                    </div>
                                </RadioGroup>
                                <span
                                    v-if="form.errors.roles"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.roles }}
                                </span>
                            </CardContent>
                        </Card>

                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-4 mt-6">
                    <Link href="/admin/users">
                        <Button type="button" variant="outline">Hủy</Button>
                    </Link>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Đang lưu...' : 'Cập nhật' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

