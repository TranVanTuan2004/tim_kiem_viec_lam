<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Search,
    Plus,
    Edit,
    Trash2,
    Star,
    Eye,
    Calendar,
    User,
} from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Blog {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    featured_image: string | null;
    status: 'draft' | 'published';
    published_at: string | null;
    views: number;
    is_featured: boolean;
    author: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    blogs: {
        data: Blog[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: any[];
    };
    filters: {
        q?: string;
        status?: string;
        is_featured?: string;
        author_id?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.q || '');
const status = ref(props.filters.status || '');
const is_featured = ref(props.filters.is_featured || '');
const isDeleteDialogOpen = ref(false);
const blogToDelete = ref<Blog | null>(null);

const page = usePage();
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    setTimeout(() => {
        toastMessage.value = '';
    }, 5000);
};

// Watch for flash messages from backend
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });

onMounted(() => {
    const flash = (page.props as any).flash;
    if (flash?.success) {
        showToast(flash.success, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    }
});

function applyFilters() {
    router.get(
        '/admin/blogs',
        {
            q: search.value || undefined,
            status: status.value || undefined,
            is_featured: is_featured.value || undefined,
        },
        { preserveState: false }
    );
}

function clearFilters() {
    search.value = '';
    status.value = '';
    is_featured.value = '';
    applyFilters();
}

function getImageUrl(image: string | null) {
    if (!image) return '/placeholder-blog.jpg';
    if (image.startsWith('http')) return image;
    // If already starts with /storage/, return as is
    if (image.startsWith('/storage/')) return image;
    // Otherwise, add /storage/ prefix
    return `/storage/${image}`;
}

function openDeleteDialog(blog: Blog) {
    blogToDelete.value = blog;
    isDeleteDialogOpen.value = true;
}

function confirmDelete() {
    if (!blogToDelete.value) return;

    router.delete(`/admin/blogs/${blogToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
            blogToDelete.value = null;
        },
    });
}

function toggleFeatured(blog: Blog) {
    router.post(`/admin/blogs/${blog.id}/toggle-featured`, {}, {
        preserveScroll: true,
    });
}

function formatDate(dateStr: string | null) {
    if (!dateStr) return 'Chưa xuất bản';
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(new Date(dateStr));
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Quản lý Blog', href: '/admin/blogs' },
];
</script>

<template>
    <Head title="Quản lý Blog" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 m-[20px]">
            <!-- Toast Notification -->
            <div
                v-if="toastMessage"
                :class="[
                    'fixed top-4 right-4 z-50 rounded-lg p-4 shadow-lg border flex items-center gap-3 animate-in slide-in-from-top-5',
                    toastType === 'success'
                        ? 'bg-green-50 border-green-200'
                        : 'bg-red-50 border-red-200',
                ]"
            >
                <svg
                    v-if="toastType === 'success'"
                    class="h-5 w-5 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <svg
                    v-else
                    class="h-5 w-5 text-red-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p
                    :class="[
                        'text-sm font-medium',
                        toastType === 'success'
                            ? 'text-green-800'
                            : 'text-red-800',
                    ]"
                >
                    {{ toastMessage }}
                </p>
                <button
                    @click="toastMessage = ''"
                    class="ml-4"
                >
                    <svg
                        class="h-4 w-4 text-gray-400 hover:text-gray-600"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>

            <!-- Page Header -->
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2"
            >
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight flex items-center gap-3"
                    >
                        <div
                            class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center"
                        >
                            <Edit class="h-6 w-6 text-primary" />
                        </div>
                        Quản lý Blog
                    </h1>
                    <p class="text-muted-foreground mt-2 ml-[52px]">
                        Quản lý tất cả các bài viết blog
                    </p>
                </div>
                <Link href="/admin/blogs/create">
                    <Button>
                        <Plus class="h-4 w-4 mr-2" />
                        Tạo bài viết mới
                    </Button>
                </Link>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle>Bộ lọc</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground"
                            />
                            <Input
                                v-model="search"
                                placeholder="Tìm kiếm..."
                                class="pl-10"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <select
                            v-model="status"
                            @change="applyFilters"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="">Tất cả</option>
                            <option value="draft">Bản nháp</option>
                            <option value="published">Đã xuất bản</option>
                        </select>
                        <select
                            v-model="is_featured"
                            @change="applyFilters"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="">Tất cả</option>
                            <option value="1">Nổi bật</option>
                            <option value="0">Không nổi bật</option>
                        </select>
                        <div class="flex gap-2">
                            <Button @click="applyFilters" class="flex-1">
                                Áp dụng
                            </Button>
                            <Button
                                variant="outline"
                                @click="clearFilters"
                                class="flex-1"
                            >
                                Xóa
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Blogs Table -->
            <Card>
                <CardHeader>
                    <CardTitle>
                        Danh sách bài viết ({{ blogs.total }})
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="blogs.data.length > 0" class="space-y-4">
                        <div
                            v-for="blog in blogs.data"
                            :key="blog.id"
                            class="flex items-center gap-4 p-4 border rounded-lg hover:bg-accent/50 transition-colors"
                        >
                            <div
                                v-if="blog.featured_image"
                                class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0"
                            >
                                <img
                                    :src="getImageUrl(blog.featured_image)"
                                    :alt="blog.title"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-semibold truncate">
                                        {{ blog.title }}
                                    </h3>
                                    <Badge
                                        v-if="blog.is_featured"
                                        variant="default"
                                        class="bg-yellow-500"
                                    >
                                        <Star class="h-3 w-3 mr-1" />
                                        Nổi bật
                                    </Badge>
                                    <Badge
                                        :variant="
                                            blog.status === 'published'
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{
                                            blog.status === 'published'
                                                ? (blog.published_at && new Date(blog.published_at) > new Date()
                                                    ? 'Đã lên lịch'
                                                    : 'Đã xuất bản')
                                                : 'Bản nháp'
                                        }}
                                    </Badge>
                                </div>
                                <p
                                    v-if="blog.excerpt"
                                    class="text-sm text-muted-foreground line-clamp-2 mb-2"
                                >
                                    {{ blog.excerpt }}
                                </p>
                                <div
                                    class="flex items-center gap-4 text-xs text-muted-foreground"
                                >
                                    <div class="flex items-center gap-1">
                                        <User class="h-3 w-3" />
                                        <span>{{ blog.author.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        <span>{{ formatDate(blog.published_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Eye class="h-3 w-3" />
                                        <span>{{ blog.views }} lượt xem</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="toggleFeatured(blog)"
                                    :title="
                                        blog.is_featured
                                            ? 'Bỏ nổi bật'
                                            : 'Đánh dấu nổi bật'
                                    "
                                >
                                    <Star
                                        :class="[
                                            'h-4 w-4',
                                            blog.is_featured
                                                ? 'fill-yellow-500 text-yellow-500'
                                                : '',
                                        ]"
                                    />
                                </Button>
                                <Link :href="`/admin/blogs/${blog.id}/edit`">
                                    <Button variant="ghost" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="openDeleteDialog(blog)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="blogs.links && blogs.links.length > 3"
                            class="flex justify-center gap-2 mt-6"
                        >
                            <template
                                v-for="(link, index) in blogs.links"
                                :key="index"
                            >
                                <Button
                                    v-if="link.url"
                                    :variant="link.active ? 'default' : 'outline'"
                                    :disabled="!link.url"
                                    @click="
                                        link.url &&
                                            router.visit(link.url, {
                                                preserveState: true,
                                            })
                                    "
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-4 py-2 text-muted-foreground"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <p class="text-muted-foreground">
                            Không tìm thấy bài viết nào
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Delete Dialog -->
            <Dialog v-model:open="isDeleteDialogOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Xác nhận xóa</DialogTitle>
                        <DialogDescription>
                            Bạn có chắc chắn muốn xóa bài viết "{{
                                blogToDelete?.title
                            }}" không? Hành động này không thể hoàn tác.
                        </DialogDescription>
                    </DialogHeader>
                    <div class="flex justify-end gap-2">
                        <Button
                            variant="outline"
                            @click="isDeleteDialogOpen = false"
                        >
                            Hủy
                        </Button>
                        <Button variant="destructive" @click="confirmDelete">
                            Xóa
                        </Button>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

