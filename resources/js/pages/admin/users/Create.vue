<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft } from 'lucide-vue-next';

interface Role {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    bio: '',
    is_active: true,
    roles: [] as number[],
});

const submit = () => {
    form.post('/admin/users');
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Users', href: '/admin/users' },
    { title: 'Thêm mới', href: '/admin/users/create' },
];
</script>

<template>
    <Head title="Thêm User mới" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Link href="/admin/users">
                    <Button variant="outline" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h1 class="text-2xl font-bold">Thêm User mới</h1>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Thông tin cơ bản -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Thông tin cơ bản</CardTitle>
                            <CardDescription>Nhập thông tin user</CardDescription>
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
                                <Label for="bio">Bio</Label>
                                <Textarea id="bio" v-model="form.bio" rows="3" />
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
                                <CardTitle>Mật khẩu</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="password">Mật khẩu *</Label>
                                    <Input id="password" v-model="form.password" type="password" required />
                                    <span v-if="form.errors.password" class="text-sm text-destructive">
                                        {{ form.errors.password }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation">Xác nhận mật khẩu *</Label>
                                    <Input 
                                        id="password_confirmation" 
                                        v-model="form.password_confirmation" 
                                        type="password" 
                                        required 
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Vai trò</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-3">
                                    <div 
                                        v-for="role in roles" 
                                        :key="role.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox 
                                            :id="`role-${role.id}`"
                                            :value="role.id"
                                            v-model:checked="form.roles"
                                        />
                                        <Label :for="`role-${role.id}`" class="cursor-pointer">
                                            {{ role.name }}
                                        </Label>
                                    </div>
                                </div>
                                <span v-if="form.errors.roles" class="text-sm text-destructive">
                                    {{ form.errors.roles }}
                                </span>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Trạng thái</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex items-center space-x-2">
                                    <Checkbox 
                                        id="is_active"
                                        v-model:checked="form.is_active"
                                    />
                                    <Label for="is_active" class="cursor-pointer">
                                        Active
                                    </Label>
                                </div>
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
                        {{ form.processing ? 'Đang lưu...' : 'Lưu User' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

