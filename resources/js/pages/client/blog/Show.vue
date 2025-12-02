<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Calendar,
    Eye,
    Share2,
    Star,
    User,
    ArrowLeft,
    Clock,
    BookOpen,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    blog: {
        type: Object,
        required: true,
    },
    relatedBlogs: {
        type: Array,
        default: () => [],
    },
});

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

const shareBlog = () => {
    if (navigator.share) {
        navigator.share({
            title: props.blog.title,
            text: props.blog.excerpt,
            url: window.location.href,
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Đã sao chép link bài viết!');
    }
};

const readingTime = computed(() => {
    const wordsPerMinute = 200;
    const text = props.blog.content || '';
    const words = text.replace(/<[^>]*>/g, '').split(/\s+/).length;
    const minutes = Math.ceil(words / wordsPerMinute);
    return minutes;
});
</script>

<template>
    <ClientLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Back Button -->
            <div class="mb-8">
                <Button variant="ghost" as-child class="group hover:bg-primary/10 transition-all duration-300">
                    <Link href="/blog" class="flex items-center gap-2">
                        <ArrowLeft class="h-4 w-4 group-hover:-translate-x-1 transition-transform" />
                        <span class="font-medium">Quay lại danh sách</span>
                    </Link>
                </Button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content -->
                <article class="lg:col-span-8">
                    <!-- Header -->
                    <div class="mb-10">
                        <div class="flex items-center gap-3 mb-6">
                            <Badge
                                v-if="blog.is_featured"
                                variant="default"
                                class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-white border-0 shadow-lg px-4 py-1.5 text-sm font-semibold"
                            >
                                <Star class="h-4 w-4 mr-1.5 fill-current animate-pulse" />
                                Nổi bật
                            </Badge>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                            {{ blog.title }}
                        </h1>
                        
                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 mb-8 p-5 bg-muted/30 rounded-lg border border-border/50">
                            <div class="flex items-center gap-2">
                                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <User class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">{{ blog.author?.name }}</div>
                                    <div class="text-xs text-muted-foreground">Tác giả</div>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-border"></div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Calendar class="h-4 w-4" />
                                <span>{{ formatDate(blog.published_at) }}</span>
                            </div>
                            <div class="h-8 w-px bg-border"></div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Clock class="h-4 w-4" />
                                <span>{{ readingTime }} phút đọc</span>
                            </div>
                            <div class="h-8 w-px bg-border"></div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Eye class="h-4 w-4" />
                                <span>{{ blog.views || 0 }} lượt xem</span>
                            </div>
                            <div class="ml-auto">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="shareBlog"
                                >
                                    <Share2 class="h-4 w-4 mr-2" />
                                    Chia sẻ
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div
                        v-if="blog.featured_image"
                        class="mb-8 rounded-lg overflow-hidden border border-border/50 relative group"
                    >
                        <img
                            :src="getImageUrl(blog.featured_image)"
                            :alt="blog.title"
                            class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                        />
                    </div>

                    <!-- Content -->
                    <div
                        class="prose prose-lg prose-slate dark:prose-invert max-w-none mb-12"
                        v-html="blog.content"
                    />

                    <!-- Author Card -->
                    <Card class="mt-8 border border-border/50">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg font-semibold flex items-center gap-2">
                                <User class="h-5 w-5 text-primary" />
                                Về tác giả
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-start gap-4">
                                <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <User class="h-8 w-8 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold mb-1">
                                        {{ blog.author?.name }}
                                    </h3>
                                    <p class="text-sm text-muted-foreground mb-2">
                                        {{ blog.author?.email }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Tác giả của bài viết này trên Blog IT
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </article>

                <!-- Sidebar -->
                <aside class="lg:col-span-4 space-y-6">
                    <!-- Table of Contents Placeholder -->
                    <Card class="sticky top-24 border border-border/50">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg font-semibold flex items-center gap-2">
                                <BookOpen class="h-5 w-5 text-primary" />
                                Nội dung bài viết
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 pt-2">
                            <div class="flex items-center gap-2 p-2 rounded hover:bg-muted transition-colors cursor-pointer text-sm">
                                <div class="h-1.5 w-1.5 rounded-full bg-primary"></div>
                                <span class="text-muted-foreground">Giới thiệu</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded hover:bg-muted transition-colors cursor-pointer text-sm">
                                <div class="h-1.5 w-1.5 rounded-full bg-primary"></div>
                                <span class="text-muted-foreground">Nội dung chính</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded hover:bg-muted transition-colors cursor-pointer text-sm">
                                <div class="h-1.5 w-1.5 rounded-full bg-primary"></div>
                                <span class="text-muted-foreground">Kết luận</span>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Related Blogs -->
                    <Card v-if="relatedBlogs.length > 0" class="border border-border/50">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg font-semibold flex items-center gap-2">
                                <Star class="h-5 w-5 text-primary" />
                                Bài viết liên quan
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4 pt-2">
                            <Link
                                v-for="relatedBlog in relatedBlogs"
                                :key="relatedBlog.id"
                                :href="`/blog/${relatedBlog.slug}`"
                                class="group block p-4 rounded-lg border border-border/50 hover:border-primary/30 hover:shadow-md transition-all"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        v-if="relatedBlog.featured_image"
                                        class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-muted"
                                    >
                                        <img
                                            :src="getImageUrl(relatedBlog.featured_image)"
                                            :alt="relatedBlog.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm line-clamp-2 group-hover:text-primary transition-colors mb-1.5 leading-snug">
                                            {{ relatedBlog.title }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                            <Calendar class="h-3 w-3" />
                                            <span>{{ formatDate(relatedBlog.published_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </CardContent>
                    </Card>

                    <!-- Share Card -->
                    <Card class="border border-border/50">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg font-semibold">Chia sẻ bài viết</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Button
                                variant="outline"
                                class="w-full"
                                @click="shareBlog"
                            >
                                <Share2 class="h-4 w-4 mr-2" />
                                Chia sẻ ngay
                            </Button>
                        </CardContent>
                    </Card>
                </aside>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
:deep(.prose) {
    color: hsl(var(--foreground));
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4) {
    color: hsl(var(--foreground));
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

:deep(.prose h1) {
    font-size: 2.25em;
    border-bottom: 2px solid hsl(var(--border));
    padding-bottom: 0.5rem;
}

:deep(.prose h2) {
    font-size: 1.875em;
}

:deep(.prose h3) {
    font-size: 1.5em;
}

:deep(.prose p) {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    font-size: 1.0625rem;
}

:deep(.prose img) {
    border-radius: 0.75rem;
    margin: 2rem 0;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

:deep(.prose a) {
    color: hsl(var(--primary));
    text-decoration: underline;
    font-weight: 500;
    transition: color 0.2s;
}

:deep(.prose a:hover) {
    color: hsl(var(--primary) / 0.8);
}

:deep(.prose code) {
    background-color: hsl(var(--muted));
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.9em;
    font-weight: 500;
    color: hsl(var(--primary));
}

:deep(.prose pre) {
    background-color: hsl(var(--muted));
    padding: 1.5rem;
    border-radius: 0.75rem;
    overflow-x: auto;
    border: 1px solid hsl(var(--border));
    margin: 1.5rem 0;
}

:deep(.prose pre code) {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

:deep(.prose blockquote) {
    border-left: 4px solid hsl(var(--primary));
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: hsl(var(--muted-foreground));
    background-color: hsl(var(--muted) / 0.5);
    padding: 1rem 1.5rem;
    border-radius: 0 0.5rem 0.5rem 0;
}

:deep(.prose ul),
:deep(.prose ol) {
    margin: 1.5rem 0;
    padding-left: 2rem;
}

:deep(.prose li) {
    margin: 0.5rem 0;
    line-height: 1.8;
}

:deep(.prose strong) {
    font-weight: 700;
    color: hsl(var(--foreground));
}

:deep(.prose hr) {
    border-color: hsl(var(--border));
    margin: 2rem 0;
}
</style>

