<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Calendar,
    Edit,
    ExternalLink,
    Github,
    Globe,
    Lock,
    Star,
} from 'lucide-vue-next';

interface Props {
    portfolio: {
        id: number;
        title: string;
        description: string;
        project_url: string | null;
        github_url: string | null;
        demo_url: string | null;
        images: string[];
        main_image: string | null;
        technologies: string[];
        duration: string;
        start_date: string | null;
        end_date: string | null;
        is_ongoing: boolean;
        is_featured: boolean;
        is_public: boolean;
        created_at: string;
        updated_at: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/candidate/dashboard' },
    { title: 'Portfolio', href: '/candidate/portfolios' },
    {
        title: props.portfolio.title,
        href: `/candidate/portfolios/${props.portfolio.id}`,
    },
];
</script>

<template>
    <Head :title="portfolio.title" />

    <CandidateLayout>
        <div class="mx-auto flex max-w-5xl flex-col gap-6 p-4">
            <!-- Header with Actions -->
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="mb-2 flex items-center gap-3">
                        <h1 class="text-3xl font-bold tracking-tight">
                            {{ portfolio.title }}
                        </h1>
                        <Badge
                            v-if="portfolio.is_featured"
                            variant="default"
                            class="bg-yellow-500"
                        >
                            <Star class="mr-1 h-3 w-3" />
                            Featured
                        </Badge>
                        <Badge v-if="!portfolio.is_public" variant="secondary">
                            <Lock class="mr-1 h-3 w-3" />
                            Private
                        </Badge>
                    </div>
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <Calendar class="h-4 w-4" />
                        <span>{{ portfolio.duration }}</span>
                    </div>
                </div>
                <Link :href="`/candidate/portfolios/${portfolio.id}/edit`">
                    <Button>
                        <Edit class="mr-2 h-4 w-4" />
                        Chỉnh sửa
                    </Button>
                </Link>
            </div>

            <!-- Main Image -->
            <Card v-if="portfolio.main_image || portfolio.images.length > 0">
                <CardContent class="p-0">
                    <div
                        class="relative aspect-video overflow-hidden rounded-lg bg-gradient-to-br from-gray-100 to-gray-200"
                    >
                        <img
                            v-if="portfolio.main_image"
                            :src="portfolio.main_image"
                            :alt="portfolio.title"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full items-center justify-center text-gray-400"
                        >
                            <span class="text-6xl">🎨</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Links -->
            <Card
                v-if="
                    portfolio.project_url ||
                    portfolio.github_url ||
                    portfolio.demo_url
                "
            >
                <CardHeader>
                    <CardTitle>Liên kết</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-3">
                    <a
                        v-if="portfolio.project_url"
                        :href="portfolio.project_url"
                        target="_blank"
                        class="inline-flex items-center"
                    >
                        <Button variant="outline">
                            <Globe class="mr-2 h-4 w-4" />
                            Website
                            <ExternalLink class="ml-2 h-3 w-3" />
                        </Button>
                    </a>
                    <a
                        v-if="portfolio.github_url"
                        :href="portfolio.github_url"
                        target="_blank"
                        class="inline-flex items-center"
                    >
                        <Button variant="outline">
                            <Github class="mr-2 h-4 w-4" />
                            GitHub
                            <ExternalLink class="ml-2 h-3 w-3" />
                        </Button>
                    </a>
                    <a
                        v-if="portfolio.demo_url"
                        :href="portfolio.demo_url"
                        target="_blank"
                        class="inline-flex items-center"
                    >
                        <Button variant="outline">
                            <ExternalLink class="mr-2 h-4 w-4" />
                            Live Demo
                            <ExternalLink class="ml-2 h-3 w-3" />
                        </Button>
                    </a>
                </CardContent>
            </Card>

            <!-- Description -->
            <Card v-if="portfolio.description">
                <CardHeader>
                    <CardTitle>Mô tả dự án</CardTitle>
                </CardHeader>
                <CardContent>
                    <p
                        class="leading-relaxed whitespace-pre-wrap text-muted-foreground"
                    >
                        {{ portfolio.description }}
                    </p>
                </CardContent>
            </Card>

            <!-- Technologies -->
            <Card
                v-if="
                    portfolio.technologies && portfolio.technologies.length > 0
                "
            >
                <CardHeader>
                    <CardTitle>Công nghệ sử dụng</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="tech in portfolio.technologies"
                            :key="tech"
                            variant="secondary"
                            class="px-3 py-1 text-sm"
                        >
                            {{ tech }}
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Gallery -->
            <Card v-if="portfolio.images && portfolio.images.length > 1">
                <CardHeader>
                    <CardTitle>Thư viện ảnh</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                        <div
                            v-for="(image, index) in portfolio.images"
                            :key="index"
                            class="relative aspect-video overflow-hidden rounded-lg border"
                        >
                            <img
                                :src="image"
                                :alt="`${portfolio.title} - Image ${index + 1}`"
                                class="h-full w-full object-cover transition-transform hover:scale-105"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Timeline -->
            <Card>
                <CardHeader>
                    <CardTitle>Thời gian</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground"
                            >Thời gian thực hiện:</span
                        >
                        <span class="font-medium">{{
                            portfolio.duration
                        }}</span>
                    </div>
                    <div
                        v-if="portfolio.start_date"
                        class="flex items-center justify-between"
                    >
                        <span class="text-muted-foreground">Ngày bắt đầu:</span>
                        <span class="font-medium">{{
                            new Date(portfolio.start_date).toLocaleDateString(
                                'vi-VN',
                            )
                        }}</span>
                    </div>
                    <div
                        v-if="portfolio.end_date && !portfolio.is_ongoing"
                        class="flex items-center justify-between"
                    >
                        <span class="text-muted-foreground"
                            >Ngày kết thúc:</span
                        >
                        <span class="font-medium">{{
                            new Date(portfolio.end_date).toLocaleDateString(
                                'vi-VN',
                            )
                        }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Trạng thái:</span>
                        <Badge
                            :variant="
                                portfolio.is_ongoing ? 'default' : 'secondary'
                            "
                        >
                            {{
                                portfolio.is_ongoing
                                    ? 'Đang thực hiện'
                                    : 'Đã hoàn thành'
                            }}
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Metadata -->
            <Card>
                <CardHeader>
                    <CardTitle>Thông tin khác</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground"
                            >Trạng thái hiển thị:</span
                        >
                        <Badge
                            :variant="
                                portfolio.is_public ? 'default' : 'secondary'
                            "
                        >
                            {{ portfolio.is_public ? 'Công khai' : 'Riêng tư' }}
                        </Badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground"
                            >Dự án nổi bật:</span
                        >
                        <Badge
                            :variant="
                                portfolio.is_featured ? 'default' : 'outline'
                            "
                        >
                            {{ portfolio.is_featured ? 'Có' : 'Không' }}
                        </Badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Ngày tạo:</span>
                        <span>{{
                            new Date(portfolio.created_at).toLocaleDateString(
                                'vi-VN',
                            )
                        }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground"
                            >Cập nhật lần cuối:</span
                        >
                        <span>{{
                            new Date(portfolio.updated_at).toLocaleDateString(
                                'vi-VN',
                            )
                        }}</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Back Button -->
            <div class="flex justify-center">
                <Link href="/candidate/portfolios">
                    <Button variant="outline"> Quay lại danh sách </Button>
                </Link>
            </div>
        </div>
    </CandidateLayout>
</template>

