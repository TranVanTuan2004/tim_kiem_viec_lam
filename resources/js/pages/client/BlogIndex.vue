<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    Clock,
    Eye,
    FileText,
    Filter,
    Search,
    User,
    ArrowRight
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    blogs: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: 'all',
        }),
    },
});

const searchQuery = ref(props.filters.search || '');
const showFilters = ref(false);

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(dateStr));
};

const handleSearch = () => {
    router.get('/blog', {
        search: searchQuery.value,
        category: props.filters.category,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const filterByCategory = (category: string) => {
    router.get('/blog', {
        search: searchQuery.value,
        category: category,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <ClientLayout title="Blog IT - Chia sẻ kiến thức & Kinh nghiệm">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 py-20 md:py-24">
            <div class="absolute inset-0 overflow-hidden">
                <div class="bg-grid-white/[0.05] absolute inset-0 bg-[size:20px_20px]"></div>
                <div class="absolute top-20 left-10 h-64 w-64 rounded-full bg-blue-400/20 blur-3xl"></div>
                <div class="absolute bottom-20 right-10 h-80 w-80 rounded-full bg-purple-400/20 blur-3xl"></div>
            </div>

            <div class="relative z-10 container mx-auto px-4 text-center text-white">
                <div class="mb-6 flex items-center justify-center gap-3">
                    <div class="rounded-full bg-white/20 p-3 backdrop-blur-sm">
                        <FileText class="h-10 w-10" />
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight md:text-6xl">
                        Blog IT & Chia sẻ
                    </h1>
                </div>
                <p class="mx-auto mb-8 max-w-2xl text-xl text-blue-100">
                    Cập nhật tin tức công nghệ, kinh nghiệm phỏng vấn và bí quyết phát triển sự nghiệp IT
                </p>

                <!-- Search Bar -->
                <div class="mx-auto flex max-w-2xl gap-2">
                    <div class="group relative flex-1">
                        <Search class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            placeholder="Tìm kiếm bài viết..."
                            class="h-12 border-0 bg-white/95 pl-10 text-base shadow-xl backdrop-blur-sm text-gray-900 placeholder:text-gray-500"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Button
                        size="lg"
                        class="h-12 bg-white px-8 font-semibold text-blue-600 hover:bg-blue-50"
                        @click="handleSearch"
                    >
                        Tìm kiếm
                    </Button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="py-12 bg-gray-50 min-h-screen">
            <div class="container mx-auto px-4">
                <!-- Categories -->
                <div class="mb-10 flex flex-wrap items-center justify-center gap-3">
                    <Button
                        variant="outline"
                        :class="['rounded-full border-2', props.filters.category === 'all' ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:text-white' : 'bg-white hover:border-blue-300 hover:text-blue-600']"
                        @click="filterByCategory('all')"
                    >
                        Tất cả
                    </Button>
                    <Button
                        v-for="cat in (categories as any[])"
                        :key="cat"
                        variant="outline"
                        :class="['rounded-full border-2 capitalize', props.filters.category === cat ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:text-white' : 'bg-white hover:border-blue-300 hover:text-blue-600']"
                        @click="filterByCategory(cat)"
                    >
                        {{ cat }}
                    </Button>
                </div>

                <!-- Blog Grid -->
                <div v-if="blogs.data.length > 0" class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(blog, index) in blogs.data"
                        :key="blog.id"
                        class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <!-- Image -->
                        <div class="relative h-48 overflow-hidden bg-gray-200">
                            <img
                                :src="blog.featured_image || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'"
                                :alt="blog.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                            />
                            <div class="absolute top-4 left-4">
                                <Badge class="bg-blue-600 hover:bg-blue-700 capitalize">
                                    {{ blog.category }}
                                </Badge>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-1 flex-col p-6">
                            <div class="mb-4 flex items-center gap-4 text-xs text-gray-500">
                                <div class="flex items-center gap-1">
                                    <Calendar class="h-3.5 w-3.5" />
                                    <span>{{ formatDate(blog.published_at) }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <Eye class="h-3.5 w-3.5" />
                                    <span>{{ blog.views_count }} lượt xem</span>
                                </div>
                            </div>

                            <h3 class="mb-3 text-xl font-bold text-gray-900 group-hover:text-blue-600 line-clamp-2">
                                <Link :href="`/blog/${blog.slug}`">
                                    {{ blog.title }}
                                </Link>
                            </h3>

                            <p class="mb-6 text-gray-600 line-clamp-3 flex-1">
                                {{ blog.excerpt }}
                            </p>

                            <div class="mt-auto flex items-center justify-between border-t pt-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 overflow-hidden rounded-full bg-gray-100">
                                        <img
                                            :src="blog.author?.avatar || `https://ui-avatars.com/api/?name=${blog.author?.name}&background=random`"
                                            :alt="blog.author?.name"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ blog.author?.name }}
                                    </span>
                                </div>
                                <Link :href="`/blog/${blog.slug}`" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                                    Đọc tiếp <ArrowRight class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="py-20 text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-blue-50">
                        <FileText class="h-12 w-12 text-blue-500" />
                    </div>
                    <h3 class="mb-2 text-2xl font-bold text-gray-900">Chưa có bài viết nào</h3>
                    <p class="text-gray-500">Hãy quay lại sau để xem các bài viết mới nhất nhé!</p>
                </div>

                <!-- Pagination -->
                <div v-if="blogs.links && blogs.last_page > 1" class="mt-12 flex justify-center gap-2">
                    <template v-for="(link, key) in blogs.links" :key="key">
                        <div
                            v-if="link.url === null"
                            class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-500"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            :class="[
                                'flex items-center justify-center rounded-lg border px-4 py-2 text-sm font-medium transition-colors',
                                link.active
                                    ? 'border-blue-600 bg-blue-600 text-white'
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
