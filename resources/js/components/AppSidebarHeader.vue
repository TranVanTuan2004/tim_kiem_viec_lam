<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';
import { Bell, ChevronDown, User, Mail, Briefcase, Calendar, MapPin, ExternalLink } from 'lucide-vue-next';
import { computed, ref } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth);
const notifications = computed(() => page.props.notifications as { unread_count: number; recent: any[] } | undefined);
const isAdmin = computed(() => auth.value?.user?.roles?.includes('Admin') ?? false);

const expandedNotification = ref<number | null>(null);

const toggleNotification = (notification: any, event: Event) => {
    event.preventDefault();
    event.stopPropagation();
    
    // Toggle expand/collapse
    if (expandedNotification.value === notification.id) {
        expandedNotification.value = null;
    } else {
        expandedNotification.value = notification.id;
        
        // Đánh dấu đã đọc nếu chưa đọc
        if (!notification.is_read) {
            router.post(`/admin/notifications/${notification.id}/read`, {}, {
                preserveScroll: true,
                preserveState: true,
                only: ['notifications'],
            });
        }
    }
};

const handleViewDetails = (notification: any) => {
    router.visit('/admin/notifications');
};

const getNotificationDetails = (notification: any) => {
    if (!notification.data) return null;
    
    // Data có thể đã được parse từ backend hoặc là string
    const data = typeof notification.data === 'string' 
        ? (() => {
            try {
                return JSON.parse(notification.data);
            } catch {
                return null;
            }
        })()
        : notification.data;
    
    if (!data) return null;
    
    const type = data.type || notification.type;
    
    switch (type) {
        case 'new_user':
            return {
                type: 'new_user',
                title: 'Thông tin người dùng mới',
                fields: [
                    { label: 'Tên', value: data.user_name, icon: User },
                    { label: 'Email', value: data.user_email, icon: Mail },
                    { label: 'Vai trò', value: getRoleLabel(data.role), icon: Briefcase },
                    { label: 'Thời gian đăng ký', value: formatDateTime(data.created_at), icon: Calendar },
                ],
                actionLink: data.user_id ? `/admin/users/${data.user_id}` : null,
            };
        case 'new_job_posting':
        case 'job_posting_pending':
            return {
                type: 'job_posting',
                title: 'Thông tin đơn tuyển dụng',
                fields: [
                    { label: 'Tiêu đề', value: data.job_title, icon: Briefcase },
                    { label: 'Công ty', value: data.company_name, icon: Briefcase },
                    { label: 'Trạng thái', value: getStatusLabel(data.status), icon: Briefcase },
                    { label: data.location ? 'Địa điểm' : null, value: data.location, icon: data.location ? MapPin : null },
                    { label: 'Thời gian', value: formatDateTime(data.created_at || data.updated_at), icon: Calendar },
                ],
                actionLink: data.job_posting_id ? `/admin/job-postings/${data.job_posting_id}` : null,
            };
        case 'new_cv':
        case 'updated_cv':
            return {
                type: 'cv',
                title: 'Thông tin CV',
                fields: [
                    { label: 'Ứng viên', value: data.user_name, icon: User },
                    { label: 'Email', value: data.user_email, icon: Mail },
                    { label: 'File CV', value: data.cv_file, icon: Briefcase },
                    { label: 'Thời gian', value: formatDateTime(data.created_at || data.updated_at), icon: Calendar },
                ],
                actionLink: data.user_id ? `/admin/users/${data.user_id}` : null,
            };
        default:
            return null;
    }
};

const getRoleLabel = (role: string) => {
    const labels: Record<string, string> = {
        'Candidate': 'Ứng viên',
        'Employer': 'Nhà tuyển dụng',
        'Admin': 'Quản trị viên',
    };
    return labels[role] || role;
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'pending': 'Chờ duyệt',
        'approved': 'Đã duyệt',
        'rejected': 'Từ chối',
        'active': 'Đang hoạt động',
        'inactive': 'Không hoạt động',
    };
    return labels[status] || status;
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Notification Bell Icon (Admin only) -->
        <div v-if="isAdmin" class="flex items-center">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative h-9 w-9"
                    >
                        <Bell class="h-5 w-5" />
                        <Badge
                            v-if="notifications?.unread_count && notifications.unread_count > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full p-0 text-xs"
                            variant="destructive"
                        >
                            {{ notifications.unread_count > 99 ? '99+' : notifications.unread_count }}
                        </Badge>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-80 p-0">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="font-semibold">Thông báo</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div
                            v-if="!notifications?.recent || notifications.recent.length === 0"
                            class="p-4 text-center text-sm text-muted-foreground"
                        >
                            Không có thông báo nào
                        </div>
                        <template v-else>
                            <Collapsible
                                v-for="notification in notifications.recent"
                                :key="notification.id"
                                :open="expandedNotification === notification.id"
                                class="border-b border-border"
                            >
                                <CollapsibleTrigger as-child>
                                    <button
                                        @click="(e) => toggleNotification(notification, e)"
                                        class="w-full text-left p-3 hover:bg-accent transition-colors cursor-pointer"
                                        :class="{ 'bg-accent/50': !notification.is_read }"
                                    >
                                        <div class="flex items-start gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <p
                                                        class="text-sm font-medium"
                                                        :class="{ 'font-semibold': !notification.is_read }"
                                                    >
                                                        {{ notification.title }}
                                                    </p>
                                                    <ChevronDown
                                                        class="h-4 w-4 text-muted-foreground transition-transform"
                                                        :class="{ 'rotate-180': expandedNotification === notification.id }"
                                                    />
                                                </div>
                                                <p class="text-xs text-muted-foreground line-clamp-2 mt-1">
                                                    {{ notification.message }}
                                                </p>
                                                <p class="text-xs text-muted-foreground mt-1">
                                                    {{ notification.created_at }}
                                                </p>
                                            </div>
                                            <div
                                                v-if="!notification.is_read"
                                                class="h-2 w-2 rounded-full bg-primary mt-1"
                                            ></div>
                                        </div>
                                    </button>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <div class="px-3 pb-3 space-y-3">
                                        <div
                                            v-if="getNotificationDetails(notification)"
                                            class="bg-muted/50 rounded-lg p-3 space-y-2"
                                        >
                                            <h4 class="text-xs font-semibold text-muted-foreground mb-2">
                                                {{ getNotificationDetails(notification)?.title }}
                                            </h4>
                                            <div class="space-y-2">
                                                <div
                                                    v-for="field in getNotificationDetails(notification)?.fields"
                                                    :key="field.label"
                                                    v-show="field.label && field.value"
                                                    class="flex items-start gap-2 text-xs"
                                                >
                                                    <component
                                                        :is="field.icon"
                                                        class="h-3.5 w-3.5 text-muted-foreground mt-0.5 shrink-0"
                                                    />
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-muted-foreground">{{ field.label }}: </span>
                                                        <span class="font-medium">{{ field.value }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="getNotificationDetails(notification)?.actionLink"
                                                class="pt-2 border-t border-border"
                                            >
                                                <Link
                                                    :href="getNotificationDetails(notification)?.actionLink"
                                                    class="inline-flex items-center gap-1 text-xs text-primary hover:underline"
                                                >
                                                    Xem chi tiết
                                                    <ExternalLink class="h-3 w-3" />
                                                </Link>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                class="h-7 text-xs"
                                                @click="handleViewDetails(notification)"
                                            >
                                                Xem tất cả thông báo
                                            </Button>
                                        </div>
                                    </div>
                                </CollapsibleContent>
                            </Collapsible>
                        </template>
                    </div>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
