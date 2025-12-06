<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Bell,
    BellOff,
    Briefcase,
    Calendar,
    Check,
    DollarSign,
    MapPin,
    Trash2,
    TrendingUp,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    data: {
        job_posting_id?: number;
        job_title?: string;
        company_name?: string;
        match_score?: number;
        location?: string;
        salary_range?: string;
    };
    is_read: boolean;
    created_at: string;
}

interface Props {
    notifications: {
        data: Notification[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links: any[];
    };
    unreadCount: number;
    filters: {
        type?: string;
        is_read?: string;
    };
}

const props = defineProps<Props>();

const deleteConfirm = ref<number | null>(null);

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        job_match: 'from-blue-500 to-indigo-600',
        application_update: 'from-purple-500 to-pink-600',
        interview_invite: 'from-green-500 to-emerald-600',
        application_accepted: 'from-emerald-500 to-teal-600',
        application_rejected: 'from-red-500 to-rose-600',
        job_alert: 'from-orange-500 to-amber-600',
        message_received: 'from-indigo-500 to-blue-600',
        system_notification: 'from-gray-500 to-slate-600',
    };
    return colors[type] || 'from-gray-500 to-slate-600';
};

const getTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        job_match: 'Công việc phù hợp',
        application_update: 'Cập nhật đơn ứng tuyển',
        interview_invite: 'Mời phỏng vấn',
        application_accepted: 'Đơn được chấp nhận',
        application_rejected: 'Đơn bị từ chối',
        job_alert: 'Cảnh báo việc làm',
        message_received: 'Tin nhắn mới',
        system_notification: 'Thông báo hệ thống',
    };
    return labels[type] || 'Thông báo';
};

const markAsRead = (id: number) => {
    router.post(
        `/candidate/notifications/${id}/read`,
        {},
        {
            preserveScroll: true,
        },
    );
};

const markAllAsRead = () => {
    router.post(
        '/candidate/notifications/read-all',
        {},
        {
            preserveScroll: true,
        },
    );
};

const deleteNotification = (id: number) => {
    if (deleteConfirm.value === id) {
        router.delete(`/candidate/notifications/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirm.value = null;
            },
        });
    } else {
        deleteConfirm.value = id;
        setTimeout(() => {
            deleteConfirm.value = null;
        }, 3000);
    }
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return `${diffMins} phút trước`;
    if (diffHours < 24) return `${diffHours} giờ trước`;
    if (diffDays < 7) return `${diffDays} ngày trước`;

    return date.toLocaleDateString('vi-VN');
};
</script>

<template>
    <CandidateLayout>
        <Head title="Thông báo" />

        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl"
                >
                    <div class="relative px-8 py-10 sm:px-12">
                        <h1 class="text-3xl font-bold text-white sm:text-4xl">
                            Thông báo của bạn
                        </h1>
                        <p class="mt-3 text-lg text-blue-100">
                            {{
                                unreadCount > 0
                                    ? `Bạn có ${unreadCount} thông báo chưa đọc`
                                    : 'Bạn đã đọc hết thông báo'
                            }}
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div
                    class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6"
                >
                    <Card class="transition-shadow hover:shadow-md">
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ notifications.total }}
                            </div>
                            <div class="text-sm text-gray-600">Tổng cộng</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-blue-200 bg-blue-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-blue-700">
                                {{ unreadCount }}
                            </div>
                            <div class="text-sm text-blue-700">Chưa đọc</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-green-200 bg-green-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-green-700">
                                {{ notifications.total - unreadCount }}
                            </div>
                            <div class="text-sm text-green-700">Đã đọc</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-purple-200 bg-purple-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent
                            class="flex items-center justify-center p-4"
                        >
                            <Button
                                v-if="unreadCount > 0"
                                @click="markAllAsRead"
                                variant="ghost"
                                size="sm"
                                class="text-purple-700 hover:bg-purple-100 hover:text-purple-800"
                            >
                                <Check class="mr-2 h-4 w-4" />
                                Đánh dấu tất cả
                            </Button>
                            <div v-else class="text-sm text-purple-700">
                                Hoàn thành
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Notifications List -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg"
                            >Danh sách thông báo</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="p-0">
                        <!-- Empty State -->
                        <div
                            v-if="notifications.data.length === 0"
                            class="p-12 text-center"
                        >
                            <BellOff class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-4 text-sm font-medium text-gray-900">
                                Không có thông báo nào
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Bạn sẽ nhận được thông báo khi có công việc phù
                                hợp
                            </p>
                            <div class="mt-6">
                                <Button as-child>
                                    <Link href="/jobs">Tìm việc ngay</Link>
                                </Button>
                            </div>
                        </div>

                        <!-- Notifications Table -->
                        <div v-else class="divide-y divide-gray-200">
                            <div
                                v-for="notification in notifications.data"
                                :key="notification.id"
                                :class="[
                                    'p-6 transition-colors hover:bg-gray-50',
                                    !notification.is_read && 'bg-blue-50/30',
                                ]"
                            >
                                <div class="flex gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div
                                            :class="[
                                                'flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br',
                                                getTypeColor(notification.type),
                                            ]"
                                        >
                                            <Bell class="h-6 w-6 text-white" />
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="mb-2 flex items-start justify-between gap-4"
                                        >
                                            <div class="flex-1">
                                                <div
                                                    class="mb-1 flex items-center gap-2"
                                                >
                                                    <Badge
                                                        :variant="
                                                            notification.type ===
                                                            'job_match'
                                                                ? 'default'
                                                                : 'secondary'
                                                        "
                                                    >
                                                        {{
                                                            getTypeLabel(
                                                                notification.type,
                                                            )
                                                        }}
                                                    </Badge>
                                                    <span
                                                        v-if="
                                                            !notification.is_read
                                                        "
                                                        class="relative flex h-2 w-2"
                                                    >
                                                        <span
                                                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"
                                                        ></span>
                                                        <span
                                                            class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"
                                                        ></span>
                                                    </span>
                                                </div>
                                                <h3
                                                    class="mb-1 text-lg font-semibold text-gray-900"
                                                >
                                                    {{ notification.title }}
                                                </h3>
                                                <p
                                                    class="text-sm text-gray-600"
                                                >
                                                    {{ notification.message }}
                                                </p>
                                            </div>
                                            <div
                                                class="flex items-center text-xs text-gray-500"
                                            >
                                                <Calendar
                                                    class="mr-1 h-3.5 w-3.5"
                                                />
                                                {{
                                                    formatDate(
                                                        notification.created_at,
                                                    )
                                                }}
                                            </div>
                                        </div>

                                        <!-- Job Details (for job_match type) -->
                                        <div
                                            v-if="
                                                notification.type ===
                                                    'job_match' &&
                                                notification.data
                                            "
                                            class="mt-4 rounded-lg border border-gray-100 bg-gray-50 p-4"
                                        >
                                            <div
                                                class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2"
                                            >
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                >
                                                    <Briefcase
                                                        class="h-4 w-4 text-blue-600"
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Công ty
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900"
                                                        >
                                                            {{
                                                                notification
                                                                    .data
                                                                    .company_name
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                >
                                                    <MapPin
                                                        class="h-4 w-4 text-green-600"
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Địa điểm
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900"
                                                        >
                                                            {{
                                                                notification
                                                                    .data
                                                                    .location
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                >
                                                    <DollarSign
                                                        class="h-4 w-4 text-emerald-600"
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Mức lương
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900"
                                                        >
                                                            {{
                                                                notification
                                                                    .data
                                                                    .salary_range
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                >
                                                    <TrendingUp
                                                        class="h-4 w-4 text-blue-600"
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Độ phù hợp
                                                        </p>
                                                        <p
                                                            class="font-bold text-blue-600"
                                                        >
                                                            {{
                                                                notification
                                                                    .data
                                                                    .match_score
                                                            }}%
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <Link
                                                v-if="
                                                    notification.data
                                                        .job_posting_id
                                                "
                                                :href="`/jobs/${notification.data.job_posting_id}`"
                                                class="inline-flex items-center justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                                            >
                                                Xem chi tiết công việc
                                            </Link>
                                        </div>

                                        <!-- Actions -->
                                        <div
                                            class="mt-4 flex items-center gap-2 border-t border-gray-100 pt-4"
                                        >
                                            <Button
                                                v-if="!notification.is_read"
                                                @click="
                                                    markAsRead(notification.id)
                                                "
                                                variant="outline"
                                                size="sm"
                                            >
                                                <Check
                                                    class="mr-1 h-3.5 w-3.5"
                                                />
                                                Đánh dấu đã đọc
                                            </Button>
                                            <Button
                                                @click="
                                                    deleteNotification(
                                                        notification.id,
                                                    )
                                                "
                                                :variant="
                                                    deleteConfirm ===
                                                    notification.id
                                                        ? 'destructive'
                                                        : 'outline'
                                                "
                                                size="sm"
                                            >
                                                <Trash2
                                                    class="mr-1 h-3.5 w-3.5"
                                                />
                                                {{
                                                    deleteConfirm ===
                                                    notification.id
                                                        ? 'Xác nhận xóa'
                                                        : 'Xóa'
                                                }}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>

                    <!-- Pagination -->
                    <div
                        v-if="notifications.data.length > 0"
                        class="border-t border-gray-200 p-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Hiển thị {{ notifications.from }} đến
                                {{ notifications.to }} trong tổng số
                                {{ notifications.total }} thông báo
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in notifications.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        'rounded-md border border-gray-300 px-3 py-2 text-sm',
                                    ]"
                                    v-html="link.label"
                                >
                                </Link>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </CandidateLayout>
</template>