<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Calendar,
    Clock,
    Eye,
    Facebook,
    Linkedin,
    Share2,
    Twitter,
    User,
    ArrowLeft
} from 'lucide-vue-next';

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
</script>

<template>
    <ClientLayout :title="blog.title">
        <!-- Breadcrumb & Back -->
        <div class="bg-gray-50 py-8">
            <div class="container mx-auto px-4">
                <Link href="/blog" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-4">
                    <ArrowLeft class="h-4 w-4" /> Quay lại danh sách blog
                </Link>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <Link href="/" class="hover:text-blue-600">Trang chủ</Link>
                    <span>/</span>
                    <Link href="/blog" class="hover:text-blue-600">Blog</Link>
                    <span>/</span>
                    <span class="text-gray-900 truncate max-w-[200px]">{{ blog.title }}</span>
                </div>
            </div>
        </div>

        <article class="py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <!-- Header -->
                        <div class="mb-8">
                            <Badge class="mb-4 bg-blue-100 text-blue-700 hover:bg-blue-200 capitalize">
                                {{ blog.category }}
                            </Badge>
                            <h1 class="mb-6 text-3xl font-extrabold leading-tight text-gray-900 md:text-4xl lg:text-5xl">
                                {{ blog.title }}
                            </h1>
                            
                            <div class="flex flex-wrap items-center gap-6 border-b border-gray-100 pb-8">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 overflow-hidden rounded-full bg-gray-100">
                                        <img
                                            :src="blog.author?.avatar || `https://ui-avatars.com/api/?name=${blog.author?.name}&background=random`"
                                            :alt="blog.author?.name"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ blog.author?.name }}</p>
                                        <p class="text-xs text-gray-500">Tác giả</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <Calendar class="h-4 w-4" />
                                    <span>{{ formatDate(blog.published_at) }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <Eye class="h-4 w-4" />
                                    <span>{{ blog.views_count }} lượt xem</span>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div v-if="blog.featured_image" class="mb-10 overflow-hidden rounded-2xl shadow-lg">
                            <img
                                :src="blog.featured_image"
                                :alt="blog.title"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <!-- Content -->
                        <div class="prose prose-lg prose-blue max-w-none text-gray-700">
                            <div v-html="blog.content"></div>
                        </div>

                        <!-- Tags -->
                        <div v-if="blog.tags && blog.tags.length" class="mt-10 flex flex-wrap gap-2">
                            <span class="text-sm font-medium text-gray-500">Tags:</span>
                            <Badge
                                v-for="tag in blog.tags"
                                :key="tag"
                                variant="secondary"
                                class="bg-gray-100 text-gray-700 hover:bg-gray-200"
                            >
                                #{{ tag }}
                            </Badge>
                        </div>

                        <!-- Share -->
                        <div class="mt-12 flex items-center justify-between rounded-xl bg-gray-50 p-6">
                            <span class="font-semibold text-gray-900">Chia sẻ bài viết này:</span>
                            <div class="flex gap-3">
                                <Button size="icon" variant="outline" class="rounded-full hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200">
                                    <Facebook class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="outline" class="rounded-full hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200">
                                    <Twitter class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="outline" class="rounded-full hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200">
                                    <Linkedin class="h-4 w-4" />
                                </Button>
                                <Button size="icon" variant="outline" class="rounded-full hover:bg-gray-100">
                                    <Share2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-4">
                        <div class="sticky top-24 space-y-8">
                            <!-- Related Posts -->
                            <Card v-if="relatedBlogs.length > 0" class="border-none shadow-lg">
                                <CardContent class="p-6">
                                    <h3 class="mb-6 text-lg font-bold text-gray-900">Bài viết liên quan</h3>
                                    <div class="space-y-6">
                                        <div v-for="related in (relatedBlogs as any[])" :key="related.id" class="group">
                                            <Link :href="`/blog/${related.slug}`" class="flex gap-4">
                                                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                                    <img
                                                        :src="related.featured_image || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'"
                                                        :alt="related.title"
                                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                    />
                                                </div>
                                                <div>
                                                    <h4 class="mb-1 font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                                        {{ related.title }}
                                                    </h4>
                                                    <span class="text-xs text-gray-500">{{ formatDate(related.published_at) }}</span>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Newsletter -->
                            <Card class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white border-none shadow-lg">
                                <CardContent class="p-6 text-center">
                                    <h3 class="mb-2 text-xl font-bold">Đăng ký nhận tin</h3>
                                    <p class="mb-6 text-blue-100 text-sm">Nhận những bài viết mới nhất về công nghệ và tuyển dụng hàng tuần.</p>
                                    <div class="space-y-3">
                                        <input
                                            type="email"
                                            placeholder="Email của bạn"
                                            class="w-full rounded-lg border-0 bg-white/10 px-4 py-3 text-white placeholder:text-blue-200 focus:ring-2 focus:ring-white"
                                        />
                                        <Button class="w-full bg-white text-blue-600 hover:bg-blue-50 font-semibold">
                                            Đăng ký ngay
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </ClientLayout>
</template>
