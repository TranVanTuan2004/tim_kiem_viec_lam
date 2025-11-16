<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Checkbox } from '@/components/ui/checkbox';
import { 
    Search, 
    CheckCircle, 
    XCircle, 
    Trash2, 
    Star,
    MessageSquare,
    Building2,
    User,
    Clock,
    Filter,
    TrendingUp
} from 'lucide-vue-next';

interface Review {
    id: number;
    rating: number;
    title: string;
    review: string;
    status: string;
    created_at: string;
    company: {
        id: number;
        company_name: string;
        logo: string;
    };
    candidate?: {
        user?: {
            name: string;
            email: string;
        };
    } | null;
}

interface Props {
    reviews: {
        data: Review[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    statistics: {
        total: number;
        pending: number;
        approved: number;
        rejected: number;
        average_rating: number;
    };
    filters: {
        status?: string;
        rating?: number;
        search?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const ratingFilter = ref(props.filters.rating?.toString() || '');
const selectedReviews = ref<number[]>([]);

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Quản lý đánh giá', href: '/admin/company-reviews' },
];

function applyFilters() {
    router.get('/admin/company-reviews', {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
        rating: ratingFilter.value || undefined,
    }, { preserveState: false });
}

function clearFilters() {
    search.value = '';
    statusFilter.value = '';
    ratingFilter.value = '';
    applyFilters();
}

function approveReview(reviewId: number) {
    router.post(`/admin/company-reviews/${reviewId}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            selectedReviews.value = selectedReviews.value.filter(id => id !== reviewId);
        }
    });
}

function rejectReview(reviewId: number) {
    if (!confirm('Bạn có chắc muốn từ chối đánh giá này?')) return;
    
    router.post(`/admin/company-reviews/${reviewId}/reject`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            selectedReviews.value = selectedReviews.value.filter(id => id !== reviewId);
        }
    });
}

function deleteReview(reviewId: number) {
    if (!confirm('Bạn có chắc muốn xóa đánh giá này? Hành động này không thể hoàn tác.')) return;
    
    router.delete(`/admin/company-reviews/${reviewId}`, {
        preserveScroll: true,
        onSuccess: () => {
            selectedReviews.value = selectedReviews.value.filter(id => id !== reviewId);
        }
    });
}

function bulkApprove() {
    if (selectedReviews.value.length === 0) return;
    
    router.post('/admin/company-reviews/bulk-approve', {
        ids: selectedReviews.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedReviews.value = [];
        }
    });
}

function bulkReject() {
    if (selectedReviews.value.length === 0) return;
    if (!confirm(`Bạn có chắc muốn từ chối ${selectedReviews.value.length} đánh giá đã chọn?`)) return;
    
    router.post('/admin/company-reviews/bulk-reject', {
        ids: selectedReviews.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedReviews.value = [];
        }
    });
}

function toggleSelectAll() {
    if (selectedReviews.value.length === props.reviews.data.length) {
        selectedReviews.value = [];
    } else {
        selectedReviews.value = props.reviews.data.map(r => r.id);
    }
}

function getStatusBadge(status: string) {
    const badges = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border-yellow-200',
        approved: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border-green-200',
        rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border-red-200',
    };
    return badges[status as keyof typeof badges] || '';
}

function getStatusText(status: string) {
    const texts = {
        pending: 'Chờ duyệt',
        approved: 'Đã duyệt',
        rejected: 'Đã từ chối',
    };
    return texts[status as keyof typeof texts] || status;
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

const allSelected = computed(() => {
    return props.reviews.data.length > 0 && selectedReviews.value.length === props.reviews.data.length;
});
</script>

<template>
    <Head title="Quản lý đánh giá công ty" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 m-[20px]">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <MessageSquare class="h-6 w-6 text-primary" />
                        </div>
                        Quản lý đánh giá công ty
                    </h1>
                    <p class="text-muted-foreground mt-2 ml-[52px]">
                        Duyệt và quản lý đánh giá từ ứng viên về các công ty
                    </p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <Card class="border-l-4 border-l-blue-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Tổng đánh giá</CardTitle>
                        <MessageSquare class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ statistics.total }}</div>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-yellow-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Chờ duyệt</CardTitle>
                        <Clock class="h-4 w-4 text-yellow-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ statistics.pending }}</div>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-green-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Đã duyệt</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ statistics.approved }}</div>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-red-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Đã từ chối</CardTitle>
                        <XCircle class="h-4 w-4 text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ statistics.rejected }}</div>
                    </CardContent>
                </Card>

                <Card class="border-l-4 border-l-purple-500">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Đánh giá TB</CardTitle>
                        <Star class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-purple-600">{{ statistics.average_rating }}</div>
                        <p class="text-xs text-muted-foreground">/ 5 sao</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Filter class="h-5 w-5" />
                        Bộ lọc
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-4 mb-4">
                        <Input
                            v-model="search"
                            placeholder="Tìm kiếm..."
                            @keyup.enter="applyFilters"
                        />

                        <select 
                            v-model="statusFilter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Đã từ chối</option>
                        </select>

                        <select 
                            v-model="ratingFilter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Tất cả đánh giá</option>
                            <option value="5">5 sao</option>
                            <option value="4">4 sao</option>
                            <option value="3">3 sao</option>
                            <option value="2">2 sao</option>
                            <option value="1">1 sao</option>
                        </select>

                        <div class="flex gap-2">
                            <Button @click="applyFilters" class="flex-1">
                                <Search class="mr-2 h-4 w-4" />
                                Tìm
                            </Button>
                            <Button variant="outline" @click="clearFilters">
                                Xóa
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Bulk Actions -->
            <Card v-if="selectedReviews.length > 0" class="bg-blue-50 dark:bg-blue-950/30 border-blue-200">
                <CardContent class="py-4">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">
                            Đã chọn {{ selectedReviews.length }} đánh giá
                        </span>
                        <div class="flex gap-2">
                            <Button size="sm" @click="bulkApprove">
                                <CheckCircle class="mr-2 h-4 w-4" />
                                Duyệt tất cả
                            </Button>
                            <Button variant="destructive" size="sm" @click="bulkReject">
                                <XCircle class="mr-2 h-4 w-4" />
                                Từ chối tất cả
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Reviews List -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Danh sách đánh giá</CardTitle>
                        <div class="flex items-center gap-2">
                            <Checkbox 
                                :checked="allSelected"
                                @update:checked="toggleSelectAll"
                            />
                            <span class="text-sm text-muted-foreground">Chọn tất cả</span>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="review in reviews.data"
                            :key="review.id"
                            class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Checkbox -->
                                <Checkbox 
                                    :checked="selectedReviews.includes(review.id)"
                                    @update:checked="(checked) => {
                                        if (checked) selectedReviews.push(review.id);
                                        else selectedReviews = selectedReviews.filter(id => id !== review.id);
                                    }"
                                    class="mt-1"
                                />

                                <!-- Content -->
                                <div class="flex-1 space-y-3">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="flex items-center gap-1">
                                                    <Building2 class="h-4 w-4 text-muted-foreground" />
                                                    <span class="font-semibold">
                                                        {{ review.company.company_name }}
                                                    </span>
                                                </div>
                                                <Separator orientation="vertical" class="h-4" />
                                                <div class="flex items-center gap-1">
                                                    <User class="h-4 w-4 text-muted-foreground" />
                                                    <span class="text-sm">{{ review.candidate?.user?.name || 'Ẩn danh' }}</span>
                                                </div>
                                            </div>

                                            <!-- Rating & Status -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-0.5">
                                                    <Star
                                                        v-for="i in 5"
                                                        :key="i"
                                                        class="h-4 w-4"
                                                        :class="
                                                            i <= review.rating
                                                                ? 'fill-yellow-500 text-yellow-500'
                                                                : 'text-gray-300'
                                                        "
                                                    />
                                                </div>
                                                <Badge :class="getStatusBadge(review.status)">
                                                    {{ getStatusText(review.status) }}
                                                </Badge>
                                                <span class="text-sm text-muted-foreground">
                                                    {{ formatDate(review.created_at) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <Button
                                                v-if="review.status === 'pending'"
                                                size="sm"
                                                variant="default"
                                                @click="approveReview(review.id)"
                                            >
                                                <CheckCircle class="h-4 w-4 mr-1" />
                                                Duyệt
                                            </Button>
                                            <Button
                                                v-if="review.status === 'pending' || review.status === 'approved'"
                                                size="sm"
                                                variant="outline"
                                                @click="rejectReview(review.id)"
                                            >
                                                <XCircle class="h-4 w-4 mr-1" />
                                                Từ chối
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="destructive"
                                                @click="deleteReview(review.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Title & Content -->
                                    <div>
                                        <h4 class="font-bold text-lg mb-1">{{ review.title }}</h4>
                                        <p class="text-muted-foreground leading-relaxed">{{ review.review }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="reviews.data.length === 0" class="text-center py-12 text-muted-foreground">
                            <MessageSquare class="h-16 w-16 mx-auto mb-4 text-muted-foreground/30" />
                            <p>Không tìm thấy đánh giá nào</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="reviews.last_page > 1"
                        class="flex items-center justify-center gap-2 pt-6 mt-6 border-t"
                    >
                        <Button
                            :disabled="reviews.current_page === 1"
                            variant="outline"
                            size="sm"
                            @click="router.get(`/admin/company-reviews?page=${reviews.current_page - 1}`)"
                        >
                            Trước
                        </Button>
                        <span class="text-sm text-muted-foreground">
                            Trang {{ reviews.current_page }} / {{ reviews.last_page }}
                        </span>
                        <Button
                            :disabled="reviews.current_page === reviews.last_page"
                            variant="outline"
                            size="sm"
                            @click="router.get(`/admin/company-reviews?page=${reviews.current_page + 1}`)"
                        >
                            Sau
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

