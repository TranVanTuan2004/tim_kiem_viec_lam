<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { Calendar, Search, Download, Trash2, Activity, User, Clock, FileText, TrendingUp, Database } from 'lucide-vue-next';

interface ActivityLog {
    id: number;
    log_name: string;
    description: string;
    subject_type: string;
    event: string;
    properties: any;
    created_at: string;
    causer: {
        id: number;
        name: string;
        email: string;
        avatar?: string;
    } | null;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    logs: {
        data: ActivityLog[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    statistics: {
        total: number;
        created: number;
        updated: number;
        deleted: number;
        by_type: Record<string, number>;
        by_day: Array<{ date: string; count: number }>;
    };
    recentActivities: ActivityLog[];
    filters: {
        user_id?: number;
        type?: string;
        event?: string;
        search?: string;
        date_from?: string;
        date_to?: string;
    };
}

const props = defineProps<Props>();

// Debug: Log để kiểm tra data
console.log('Activity Logs Data:', props.logs);
console.log('First Log:', props.logs.data[0]);
if (props.logs.data[0]) {
    console.log('Causer:', props.logs.data[0].causer);
}

const search = ref(props.filters.search || '');
const userFilter = ref(props.filters.user_id?.toString() || '');
const typeFilter = ref(props.filters.type || '');
const eventFilter = ref(props.filters.event || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Activity Logs', href: '/admin/activity-logs' },
];

function applyFilters() {
    router.get('/admin/activity-logs', {
        search: search.value || undefined,
        user_id: userFilter.value || undefined,
        type: typeFilter.value || undefined,
        event: eventFilter.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, { preserveState: false });
}

function clearFilters() {
    search.value = '';
    userFilter.value = '';
    typeFilter.value = '';
    eventFilter.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
}

function getEventColor(event: string) {
    return {
        created: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-800',
        updated: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-800',
        deleted: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200 dark:border-red-800',
    }[event] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 border border-gray-200 dark:border-gray-800';
}

function formatDate(dateString: string) {
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getUserInitials(name: string) {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function formatValue(value: any): string {
    if (value === null || value === undefined) return 'N/A';
    if (typeof value === 'boolean') return value ? 'Có' : 'Không';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
}

function refreshData() {
    router.reload({ only: ['logs', 'statistics', 'recentActivities'] });
}

function confirmCleanLogs() {
    if (confirm('Bạn có chắc muốn xóa logs cũ hơn 90 ngày? Hành động này không thể hoàn tác.')) {
        router.post('/admin/activity-logs/clean', { days: 90 }, {
            onSuccess: () => {
                alert('Đã xóa logs cũ thành công!');
                refreshData();
            }
        });
    }
}
</script>

<template>
    <Head title="Activity Logs - System Monitoring" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 m-[20px]">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <Activity class="h-6 w-6 text-primary" />
                        </div>
                        Lịch Sử Hoạt Động
                    </h1>
                    <p class="text-muted-foreground mt-2 ml-[52px]">
                        Theo dõi và quản lý tất cả hoạt động trong hệ thống
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" @click="() => router.get('/admin/activity-logs/export')">
                        <Download class="mr-2 h-4 w-4" />
                        Export
                    </Button>
                    <Button variant="default" size="sm" @click="refreshData">
                        <TrendingUp class="mr-2 h-4 w-4" />
                        Refresh
                    </Button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="border-l-4 border-l-blue-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Tổng Hoạt Động
                        </CardTitle>
                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                            <Activity class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.total.toLocaleString() }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            <TrendingUp class="inline h-3 w-3 mr-1" />
                            7 ngày qua
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-green-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Đã Tạo
                        </CardTitle>
                        <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-950 flex items-center justify-center">
                            <Database class="h-4 w-4 text-green-600 dark:text-green-400" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ statistics.created.toLocaleString() }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Records mới
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-blue-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Đã Cập Nhật
                        </CardTitle>
                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                            <FileText class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ statistics.updated.toLocaleString() }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Thay đổi dữ liệu
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-red-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Đã Xóa
                        </CardTitle>
                        <div class="h-8 w-8 rounded-full bg-red-100 dark:bg-red-950 flex items-center justify-center">
                            <Trash2 class="h-4 w-4 text-red-600 dark:text-red-400" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ statistics.deleted.toLocaleString() }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Records xóa
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <Card class="shadow-sm">
                <CardHeader class="pb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <Search class="h-5 w-5" />
                                Bộ Lọc Tìm Kiếm
                            </CardTitle>
                            <CardDescription class="mt-1">Tìm kiếm và lọc hoạt động theo tiêu chí</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3 mb-4">
                        <div class="relative">
                            <Input
                                v-model="search"
                                placeholder="Tìm kiếm..."
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <select 
                            v-model="typeFilter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">Tất cả loại</option>
                            <option value="default">Default</option>
                            <option value="user">User</option>
                            <option value="job_posting">Job Posting</option>
                        </select>

                        <select 
                            v-model="eventFilter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="">Tất cả sự kiện</option>
                            <option value="created">Đã Tạo</option>
                            <option value="updated">Đã Cập Nhật</option>
                            <option value="deleted">Đã Xóa</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button @click="applyFilters" class="flex-1 md:flex-none">
                            <Search class="mr-2 h-4 w-4" />
                            Áp Dụng Bộ Lọc
                        </Button>
                        <Button variant="outline" @click="clearFilters" class="flex-1 md:flex-none">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Xóa Bộ Lọc
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Activity Logs List -->
            <Card class="shadow-sm">
                <CardHeader class="pb-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <FileText class="h-5 w-5" />
                                Danh Sách Hoạt Động
                            </CardTitle>
                            <CardDescription class="mt-1">
                                Tổng {{ logs.total.toLocaleString() }} hoạt động, đang hiển thị {{ logs.data.length }} records
                            </CardDescription>
                        </div>
                        <div class="flex gap-2">
                            <Button variant="destructive" size="sm" @click="confirmCleanLogs">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Dọn Dẹp Logs Cũ
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="pt-2">
                    <div class="space-y-4">
                        <div
                            v-for="log in logs.data"
                            :key="log.id"
                            class="group relative rounded-lg border bg-card p-4 hover:bg-accent/10 hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex items-start gap-4">
                                <!-- User Avatar & Info -->
                                <div class="flex flex-col items-center gap-2 min-w-[110px]">
                                    <Avatar v-if="log.causer" class="h-12 w-12 border-2 border-primary/30 shadow-sm">
                                        <AvatarImage
                                            :src="log.causer.avatar || ''"
                                            :alt="log.causer.name"
                                        />
                                        <AvatarFallback class="bg-gradient-to-br from-primary/20 to-primary/10 text-primary font-bold text-base">
                                            {{ getUserInitials(log.causer.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div v-else class="h-12 w-12 rounded-full bg-gradient-to-br from-muted to-muted/50 flex items-center justify-center border-2 border-muted-foreground/20 shadow-sm">
                                        <Activity class="h-6 w-6 text-muted-foreground" />
                                    </div>
                                    
                                    <div class="text-center">
                                        <p v-if="log.causer" class="text-sm font-bold text-foreground leading-tight mb-0.5">
                                            {{ log.causer.name }}
                                        </p>
                                        <p v-else class="text-sm font-bold text-muted-foreground">
                                            System
                                        </p>
                                        <p v-if="log.causer" class="text-[10px] text-muted-foreground truncate max-w-[110px]">
                                            {{ log.causer.email }}
                                        </p>
                                    </div>
                                </div>

                                <Separator orientation="vertical" class="h-auto" />

                                <!-- Activity Details -->
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-sm font-semibold">
                                                    {{ log.description }}
                                                </h4>
                                                <Badge :class="getEventColor(log.event)" class="shrink-0">
                                                    {{ log.event }}
                                                </Badge>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 text-xs text-muted-foreground">
                                                <FileText class="h-3 w-3" />
                                                <span>{{ log.subject_type || 'N/A' }}</span>
                                                <span>•</span>
                                                <Clock class="h-3 w-3" />
                                                <span>{{ formatDate(log.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <!-- Show properties details -->
                                    <div v-if="log.properties && Object.keys(log.properties).length > 0" class="mt-3">
                                        <!-- Show attributes (new values) -->
                                        <div v-if="log.properties.attributes" class="bg-gradient-to-br from-muted/40 to-muted/20 rounded-lg p-3 border border-muted shadow-sm">
                                            <p class="text-xs font-bold text-foreground mb-2 flex items-center gap-2">
                                                <div class="h-6 w-6 rounded bg-primary/10 flex items-center justify-center">
                                                    <Database class="h-3.5 w-3.5 text-primary" />
                                                </div>
                                                Chi tiết thay đổi:
                                            </p>
                                            <div class="space-y-1.5 ml-6">
                                                <div v-for="(value, key) in log.properties.attributes" :key="key" class="flex items-start gap-2 text-xs">
                                                    <span class="font-bold text-foreground min-w-[100px] capitalize bg-muted/50 px-2 py-0.5 rounded text-xs">{{ key }}:</span>
                                                    <div class="flex-1">
                                                        <span v-if="log.properties.old && log.properties.old[key] !== undefined" class="flex items-center gap-2 flex-wrap">
                                                            <span class="text-muted-foreground line-through decoration-red-500 decoration-2 bg-red-50 dark:bg-red-950/30 px-2 py-0.5 rounded">{{ formatValue(log.properties.old[key]) }}</span>
                                                            <span class="text-lg text-muted-foreground">→</span>
                                                            <span class="text-green-600 dark:text-green-400 font-bold bg-green-50 dark:bg-green-950/30 px-2 py-0.5 rounded">{{ formatValue(value) }}</span>
                                                        </span>
                                                        <span v-else class="text-green-600 dark:text-green-400 font-semibold bg-green-50 dark:bg-green-950/30 px-2 py-0.5 rounded">{{ formatValue(value) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Show other properties (for manual logs like login/logout) -->
                                        <div v-else class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/30 dark:to-blue-950/10 rounded-lg p-3 border border-blue-200 dark:border-blue-900 shadow-sm">
                                            <p class="text-xs font-bold text-foreground mb-2 flex items-center gap-2">
                                                <div class="h-6 w-6 rounded bg-blue-500/10 flex items-center justify-center">
                                                    <FileText class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" />
                                                </div>
                                                Thông tin bổ sung:
                                            </p>
                                            <div class="space-y-1.5 ml-6">
                                                <div v-for="(value, key) in log.properties" :key="key" class="flex items-start gap-2 text-xs">
                                                    <span class="font-bold text-foreground min-w-[100px] capitalize bg-blue-100 dark:bg-blue-950/50 px-2 py-0.5 rounded text-xs">{{ key }}:</span>
                                                    <span class="text-muted-foreground flex-1 break-all bg-background/50 px-2 py-0.5 rounded text-xs">{{ formatValue(value) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="logs.last_page > 1"
                            class="flex items-center justify-center gap-2 pt-4"
                        >
                            <Button
                                :disabled="logs.current_page === 1"
                                variant="outline"
                                size="sm"
                                @click="router.get('/admin/activity-logs?page=' + (logs.current_page - 1))"
                            >
                                Trước
                            </Button>
                            <span class="text-sm text-muted-foreground">
                                Trang {{ logs.current_page }} / {{ logs.last_page }}
                            </span>
                            <Button
                                :disabled="logs.current_page === logs.last_page"
                                variant="outline"
                                size="sm"
                                @click="router.get('/admin/activity-logs?page=' + (logs.current_page + 1))"
                            >
                                Sau
                            </Button>
                        </div>

                        <div v-if="logs.data.length === 0" class="text-center py-12 text-muted-foreground">
                            Không có hoạt động nào được tìm thấy
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
