<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    Edit,
    ExternalLink,
    Eye,
    Github,
    Lock,
    MoreVertical,
    Plus,
    Star,
    Trash2,
    Unlock,
} from 'lucide-vue-next';

interface Portfolio {
    id: number;
    title: string;
    description: string;
    project_url: string | null;
    github_url: string | null;
    demo_url: string | null;
    main_image: string | null;
    images: string[];
    technologies: string[];
    duration: string;
    is_ongoing: boolean;
    is_featured: boolean;
    is_public: boolean;
    display_order: number;
    created_at: string;
}

interface Props {
    portfolios: {
        data: Portfolio[];
        links: any[];
        meta: any;
    };
}

const props = defineProps<Props>();

const deletePortfolio = (id: number) => {
    if (confirm('Bạn có chắc muốn xóa dự án này?')) {
        router.delete(`/candidate/portfolios/${id}`, {
            onSuccess: () => {
                alert('Dự án đã được xóa thành công!');
            },
        });
    }
};

const toggleFeatured = (id: number) => {
    router.post(
        `/candidate/portfolios/${id}/toggle-featured`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const togglePublic = (id: number) => {
    router.post(
        `/candidate/portfolios/${id}/toggle-public`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Portfolio', href: '/candidate/portfolios' },
];
</script>

<template>
    <Head title="Quản lý Portfolio" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Portfolio & Dự án
                    </h1>
                    <p class="mt-2 text-muted-foreground">
                        Quản lý các dự án và portfolio của bạn
                    </p>
                </div>
                <Link href="/candidate/portfolios/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Thêm dự án mới
                    </Button>
                </Link>
            </div>

            <!-- Portfolio Grid -->
            <div
                v-if="portfolios.data.length > 0"
                class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="portfolio in portfolios.data"
                    :key="portfolio.id"
                    class="overflow-hidden"
                >
                    <!-- Image -->
                    <div
                        class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200"
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
                            <span class="text-4xl">🎨</span>
                        </div>

                        <!-- Badges -->
                        <div class="absolute top-2 left-2 flex gap-2">
                            <Badge
                                v-if="portfolio.is_featured"
                                variant="default"
                                class="bg-yellow-500"
                            >
                                <Star class="mr-1 h-3 w-3" />
                                Featured
                            </Badge>
                            <Badge
                                v-if="!portfolio.is_public"
                                variant="secondary"
                            >
                                <Lock class="mr-1 h-3 w-3" />
                                Private
                            </Badge>
                        </div>

                        <!-- Actions Dropdown -->
                        <div class="absolute top-2 right-2">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="secondary"
                                        size="icon"
                                        class="h-8 w-8"
                                    >
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem
                                        @click="
                                            router.visit(
                                                `/candidate/portfolios/${portfolio.id}`,
                                            )
                                        "
                                    >
                                        <Eye class="mr-2 h-4 w-4" />
                                        Xem
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="
                                            router.visit(
                                                `/candidate/portfolios/${portfolio.id}/edit`,
                                            )
                                        "
                                    >
                                        <Edit class="mr-2 h-4 w-4" />
                                        Sửa
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="toggleFeatured(portfolio.id)"
                                    >
                                        <Star class="mr-2 h-4 w-4" />
                                        {{
                                            portfolio.is_featured
                                                ? 'Bỏ featured'
                                                : 'Đặt featured'
                                        }}
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="togglePublic(portfolio.id)"
                                    >
                                        <component
                                            :is="
                                                portfolio.is_public
                                                    ? Lock
                                                    : Unlock
                                            "
                                            class="mr-2 h-4 w-4"
                                        />
                                        {{
                                            portfolio.is_public ? 'Ẩn' : 'Hiện'
                                        }}
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        @click="deletePortfolio(portfolio.id)"
                                        class="text-red-600 focus:text-red-600"
                                    >
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Xóa
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>

                    <!-- Content -->
                    <CardHeader>
                        <CardTitle class="line-clamp-1">{{
                            portfolio.title
                        }}</CardTitle>
                        <CardDescription class="line-clamp-2">
                            {{ portfolio.description || 'Chưa có mô tả' }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-3">
                        <!-- Duration -->
                        <div
                            class="flex items-center text-sm text-muted-foreground"
                        >
                            <Calendar class="mr-2 h-4 w-4" />
                            {{ portfolio.duration }}
                        </div>

                        <!-- Technologies -->
                        <div
                            v-if="
                                portfolio.technologies &&
                                portfolio.technologies.length > 0
                            "
                            class="flex flex-wrap gap-1"
                        >
                            <Badge
                                v-for="tech in portfolio.technologies.slice(
                                    0,
                                    3,
                                )"
                                :key="tech"
                                variant="outline"
                                class="text-xs"
                            >
                                {{ tech }}
                            </Badge>
                            <Badge
                                v-if="portfolio.technologies.length > 3"
                                variant="outline"
                                class="text-xs"
                            >
                                +{{ portfolio.technologies.length - 3 }}
                            </Badge>
                        </div>

                        <!-- Links -->
                        <div class="flex gap-2">
                            <a
                                v-if="portfolio.github_url"
                                :href="portfolio.github_url"
                                target="_blank"
                                class="flex items-center text-sm text-blue-600 hover:underline"
                            >
                                <Github class="mr-1 h-3 w-3" />
                                GitHub
                            </a>
                            <a
                                v-if="portfolio.demo_url"
                                :href="portfolio.demo_url"
                                target="_blank"
                                class="flex items-center text-sm text-blue-600 hover:underline"
                            >
                                <ExternalLink class="mr-1 h-3 w-3" />
                                Demo
                            </a>
                        </div>
                    </CardContent>

                    <CardFooter>
                        <Button
                            variant="outline"
                            class="w-full"
                            @click="
                                router.visit(
                                    `/candidate/portfolios/${portfolio.id}/edit`,
                                )
                            "
                        >
                            <Edit class="mr-2 h-4 w-4" />
                            Chỉnh sửa
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty State -->
            <Card v-else class="py-16">
                <CardContent
                    class="flex flex-col items-center justify-center text-center"
                >
                    <div class="mb-4 rounded-full bg-gray-100 p-6">
                        <span class="text-6xl">📁</span>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">
                        Chưa có dự án nào
                    </h3>
                    <p class="mb-6 max-w-md text-muted-foreground">
                        Bắt đầu xây dựng portfolio của bạn bằng cách thêm các dự
                        án bạn đã thực hiện
                    </p>
                    <Link href="/candidate/portfolios/create">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Thêm dự án đầu tiên
                        </Button>
                    </Link>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div
                v-if="portfolios.meta && portfolios.meta.last_page > 1"
                class="flex justify-center gap-2"
            >
                <Link
                    v-for="link in portfolios.links"
                    :key="link.label"
                    :href="link.url"
                    :class="[
                        'rounded border px-4 py-2',
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-background hover:bg-accent',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
