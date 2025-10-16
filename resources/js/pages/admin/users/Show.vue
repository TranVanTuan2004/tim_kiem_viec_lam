<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Edit, Mail, Phone, Calendar, Shield } from 'lucide-vue-next';

interface Role {
    id: number;
    name: string;
    slug: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    bio: string | null;
    is_active: boolean;
    roles: Role[];
    created_at: string;
    updated_at: string;
}

interface Props {
    user: User;
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Users', href: '/admin/users' },
    { title: props.user.name, href: `/admin/users/${props.user.id}` },
];
</script>

<template>
    <Head :title="`User: ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/users">
                        <Button variant="outline" size="icon">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                        <Badge :variant="user.is_active ? 'default' : 'secondary'" class="mt-1">
                            {{ user.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                    </div>
                </div>
                <Link :href="`/admin/users/${user.id}/edit`">
                    <Button>
                        <Edit class="h-4 w-4 mr-2" />
                        Sửa
                    </Button>
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Thông tin cơ bản -->
                <Card>
                    <CardHeader>
                        <CardTitle>Thông tin cơ bản</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <Mail class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-muted-foreground">Email</p>
                                <p class="text-sm">{{ user.email }}</p>
                            </div>
                        </div>

                        <div v-if="user.phone" class="flex items-start space-x-3">
                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-muted-foreground">Số điện thoại</p>
                                <p class="text-sm">{{ user.phone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-muted-foreground">Ngày tạo</p>
                                <p class="text-sm">{{ new Date(user.created_at).toLocaleString('vi-VN') }}</p>
                            </div>
                        </div>

                        <div v-if="user.bio" class="pt-4 border-t">
                            <p class="text-sm font-medium text-muted-foreground mb-2">Bio</p>
                            <p class="text-sm">{{ user.bio }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Vai trò -->
                <Card>
                    <CardHeader>
                        <CardTitle>Vai trò</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div 
                                v-for="role in user.roles" 
                                :key="role.id"
                                class="flex items-center space-x-3 p-3 rounded-lg border bg-card"
                            >
                                <Shield class="h-5 w-5 text-primary" />
                                <div class="flex-1">
                                    <p class="font-medium">{{ role.name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ role.slug }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

