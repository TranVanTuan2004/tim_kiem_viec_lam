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
    Eye,
    Search,
    Star,
    User,
    BookOpen,
    TrendingUp,
    Sparkles,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    blogs: {
        type: Object,
        required: true,
    },
    featuredBlogs: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            q: '',
            featured: false,
        }),
    },
});

const searchQuery = ref(props.filters.q || '');

const handleSearch = () => {
    const params: any = {};
    if (searchQuery.value) {
        params.q = searchQuery.value;
    }
    router.get('/blog', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(dateStr));
};

const getImageUrl = (image: string | null) => {
    if (!image) return '/placeholder-blog.jpg';
    if (image.startsWith('http')) return image;
    // If already starts with /storage/, return as is
    if (image.startsWith('/storage/')) return image;
    // Otherwise, add /storage/ prefix
    return `/storage/${image}`;
};

const hasBlogs = computed(() => props.blogs.data && props.blogs.data.length > 0);
</script>

<template>
    <ClientLayout>
        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-primary/20 via-primary/10 to-background py-20 md:py-24 overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-grid-pattern opacity-[0.03]"></div>
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-primary/20 to-primary/10 backdrop-blur-sm border border-primary/20 text-primary mb-8 shadow-lg hover:shadow-xl transition-all duration-300">
                        <Sparkles class="h-4 w-4 animate-pulse" />
                        <span class="text-sm font-semibold">Blog IT - Nơi chia sẻ kiến thức</span>
                    </div>
                    <h1 class="text-6xl md:text-7xl lg:text-8xl font-extrabold mb-6 bg-gradient-to-r from-primary via-primary/80 to-primary/60 bg-clip-text text-transparent leading-tight">
                        Blog IT
                    </h1>
                    <p class="text-xl md:text-2xl text-muted-foreground mb-10 max-w-2xl mx-auto leading-relaxed">
                        Khám phá những bài viết mới nhất về công nghệ, việc làm và xu hướng IT
                    </p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto">
                        <div class="flex gap-3 bg-background/95 backdrop-blur-sm rounded-2xl shadow-2xl p-3 border-2 border-primary/10 hover:border-primary/30 transition-all duration-300">
                            <div class="relative flex-1">
                                <Search
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-muted-foreground"
                                />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Tìm kiếm bài viết..."
                                    class="pl-12 h-14 border-0 focus-visible:ring-0 text-base bg-transparent"
                                    @keyup.enter="handleSearch"
                                />
                            </div>
                            <Button @click="handleSearch" size="lg" class="h-14 px-8 text-base font-semibold shadow-lg hover:shadow-xl transition-all">
                                <Search class="h-5 w-5 mr-2" />
                                Tìm kiếm
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <!-- Empty State - Full Width -->
            <div v-if="!hasBlogs" class="flex items-center justify-center min-h-[60vh]">
                <div class="text-center max-w-md mx-auto w-full">
                    <div class="relative mb-8 flex justify-center">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>
                        </div>
                        <div class="relative bg-gradient-to-br from-primary/20 to-primary/10 rounded-full w-24 h-24 flex items-center justify-center">
                            <BookOpen class="h-12 w-12 text-primary" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Chưa có bài viết nào</h3>
                    <p class="text-muted-foreground mb-6">
                        Hiện tại chưa có bài viết nào được xuất bản. Hãy quay lại sau nhé!
                    </p>
                    <Button as-child variant="outline">
                        <Link href="/">Về trang chủ</Link>
                    </Button>
                </div>
            </div>

            <!-- Blog List with Sidebar -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-8">
                    <!-- Blog List -->
                    <div class="space-y-8">
                        <Link
                            v-for="blog in blogs.data"
                            :key="blog.id"
                            :href="`/blog/${blog.slug}`"
                            class="group block"
                        >
                            <Card class="overflow-hidden border border-border/50 bg-background transition-all duration-300 hover:border-primary/30 hover:shadow-lg">
                                <div class="flex flex-col md:flex-row">
                                    <!-- Image -->
                                    <div class="md:w-80 h-64 md:h-80 relative overflow-hidden bg-muted flex-shrink-0">
                                        <img
                                            :src="getImageUrl(blog.featured_image)"
                                            :alt="blog.title"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <Badge
                                            v-if="blog.is_featured"
                                            class="absolute top-4 left-4 bg-primary text-primary-foreground border-0 shadow-md z-10"
                                        >
                                            <Star class="h-3 w-3 mr-1 fill-current" />
                                            Nổi bật
                                        </Badge>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 p-6 md:p-8 flex flex-col">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                                <User class="h-3.5 w-3.5" />
                                                <span>{{ blog.author?.name }}</span>
                                            </div>
                                            <span class="text-muted-foreground">•</span>
                                            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                                <Calendar class="h-3.5 w-3.5" />
                                                <span>{{ formatDate(blog.published_at) }}</span>
                                            </div>
                                        </div>
                                        <h2 class="text-2xl md:text-3xl font-bold mb-3 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            {{ blog.title }}
                                        </h2>
                                        <p class="text-muted-foreground mb-4 line-clamp-2 leading-relaxed text-sm flex-1">
                                            {{ blog.excerpt || 'Không có mô tả' }}
                                        </p>
                                        <div class="flex items-center justify-between pt-4 border-t border-border/50 mt-auto">
                                            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                                <Eye class="h-3.5 w-3.5" />
                                                <span>{{ blog.views || 0 }} lượt xem</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-primary text-sm font-medium group-hover:gap-2 transition-all">
                                                <span>Đọc thêm</span>
                                                <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="blogs.links && blogs.links.length > 3"
                        class="mt-8 flex justify-center gap-2"
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

                <!-- Sidebar -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Featured Blogs -->
                    <Card v-if="featuredBlogs.length > 0" class="border border-border/50">
                        <CardHeader class="pb-3">
                            <div class="flex items-center gap-2">
                                <TrendingUp class="h-5 w-5 text-primary" />
                                <CardTitle class="text-lg font-semibold">Bài viết nổi bật</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4 pt-2">
                            <Link
                                v-for="blog in featuredBlogs"
                                :key="(blog as any).id"
                                :href="`/blog/${(blog as any).slug}`"
                                class="group block p-4 rounded-lg border border-border/50 hover:border-primary/30 hover:shadow-md transition-all"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        v-if="(blog as any).featured_image"
                                        class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-muted"
                                    >
                                        <img
                                            :src="getImageUrl((blog as any).featured_image)"
                                            :alt="(blog as any).title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1 mb-1.5">
                                            <Star class="h-3 w-3 text-yellow-500 fill-yellow-500" />
                                            <span class="text-xs font-medium text-yellow-600">Nổi bật</span>
                                        </div>
                                        <h4 class="font-semibold text-sm line-clamp-2 group-hover:text-primary transition-colors mb-1.5 leading-snug">
                                            {{ (blog as any).title }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                            <Calendar class="h-3 w-3" />
                                            <span>{{ formatDate((blog as any).published_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.bg-grid-pattern {
    background-image: 
        linear-gradient(to right, currentColor 1px, transparent 1px),
        linear-gradient(to bottom, currentColor 1px, transparent 1px);
    background-size: 20px 20px;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-1000 {
    animation-delay: 1s;
}
</style>

