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
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    Edit,
    ExternalLink,
    Eye,
    Folder,
    Github,
    Lock,
    MoreVertical,
    Plus,
    Sparkles,
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
                // Handle success
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
</script>

<template>
    <CandidateLayout>
        <Head title="Quản lý Portfolio" />

        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 shadow-xl"
                >
                    <!-- <div class="absolute inset-0 bg-black/10"></div> -->
                    <div class="relative px-8 py-10 sm:px-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1
                                    class="text-3xl font-bold text-white sm:text-4xl"
                                >
                                    Portfolio & Dự án
                                </h1>
                                <p class="mt-3 text-lg text-indigo-100">
                                    Quản lý các dự án và portfolio của bạn
                                </p>
                            </div>
                            <Button
                                as-child
                                class="bg-white text-indigo-700 shadow-lg hover:bg-indigo-50"
                            >
                                <Link href="/candidate/portfolios/create">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Thêm dự án mới
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Grid -->
                <div
                    v-if="portfolios.data.length > 0"
                    class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="portfolio in portfolios.data"
                        :key="portfolio.id"
                        class="group overflow-hidden transition-all hover:border-indigo-300 hover:shadow-xl"
                    >
                        <!-- Image -->
                        <div
                            class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200"
                        >
                            <img
                                v-if="portfolio.main_image"
                                :src="portfolio.main_image"
                                :alt="portfolio.title"
                                class="h-full w-full object-cover transition-transform group-hover:scale-110"
                            />
                            <div
                                v-else
                                class="flex h-full items-center justify-center text-gray-400"
                            >
                                <Folder class="h-16 w-16" />
                            </div>

                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex gap-2">
                                <Badge
                                    v-if="portfolio.is_featured"
                                    variant="default"
                                    class="bg-yellow-500 hover:bg-yellow-600"
                                >
                                    <Star class="mr-1 h-3 w-3" />
                                    Nổi bật
                                </Badge>
                                <Badge
                                    v-if="!portfolio.is_public"
                                    variant="secondary"
                                >
                                    <Lock class="mr-1 h-3 w-3" />
                                    Riêng tư
                                </Badge>
                            </div>

                            <!-- Actions Dropdown -->
                            <div class="absolute top-3 right-3">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button
                                            variant="secondary"
                                            size="icon"
                                            class="h-9 w-9 bg-white/90 backdrop-blur-sm hover:bg-white"
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
                                            @click="
                                                toggleFeatured(portfolio.id)
                                            "
                                        >
                                            <Star class="mr-2 h-4 w-4" />
                                            {{
                                                portfolio.is_featured
                                                    ? 'Bỏ nổi bật'
                                                    : 'Đặt nổi bật'
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
                                                portfolio.is_public
                                                    ? 'Ẩn công khai'
                                                    : 'Hiện công khai'
                                            }}
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="
                                                deletePortfolio(portfolio.id)
                                            "
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

                        <CardContent class="space-y-4">
                            <!-- Duration -->
                            <div
                                class="flex items-center text-sm text-muted-foreground"
                            >
                                <Calendar class="mr-2 h-4 w-4" />
                                {{ portfolio.duration }}
                                <Badge
                                    v-if="portfolio.is_ongoing"
                                    variant="secondary"
                                    class="ml-2"
                                >
                                    Đang thực hiện
                                </Badge>
                            </div>

                            <!-- Technologies -->
                            <div
                                v-if="
                                    portfolio.technologies &&
                                    portfolio.technologies.length > 0
                                "
                                class="flex flex-wrap gap-2"
                            >
                                <Badge
                                    v-for="tech in portfolio.technologies.slice(
                                        0,
                                        4,
                                    )"
                                    :key="tech"
                                    variant="outline"
                                    class="text-xs"
                                >
                                    {{ tech }}
                                </Badge>
                                <Badge
                                    v-if="portfolio.technologies.length > 4"
                                    variant="outline"
                                    class="text-xs"
                                >
                                    +{{ portfolio.technologies.length - 4 }}
                                </Badge>
                            </div>

                            <!-- Links -->
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    v-if="portfolio.github_url"
                                    as-child
                                    variant="outline"
                                    size="sm"
                                    class="text-xs"
                                >
                                    <a
                                        :href="portfolio.github_url"
                                        target="_blank"
                                    >
                                        <Github class="mr-1 h-3 w-3" />
                                        GitHub
                                    </a>
                                </Button>
                                <Button
                                    v-if="portfolio.demo_url"
                                    as-child
                                    variant="outline"
                                    size="sm"
                                    class="text-xs"
                                >
                                    <a
                                        :href="portfolio.demo_url"
                                        target="_blank"
                                    >
                                        <ExternalLink class="mr-1 h-3 w-3" />
                                        Demo
                                    </a>
                                </Button>
                                <Button
                                    v-if="portfolio.project_url"
                                    as-child
                                    variant="outline"
                                    size="sm"
                                    class="text-xs"
                                >
                                    <a
                                        :href="portfolio.project_url"
                                        target="_blank"
                                    >
                                        <ExternalLink class="mr-1 h-3 w-3" />
                                        Dự án
                                    </a>
                                </Button>
                            </div>
                        </CardContent>

                        <CardFooter class="flex gap-2">
                            <Button as-child variant="outline" class="flex-1">
                                <Link
                                    :href="`/candidate/portfolios/${portfolio.id}`"
                                >
                                    <Eye class="mr-2 h-4 w-4" />
                                    Xem
                                </Link>
                            </Button>
                            <Button as-child variant="default" class="flex-1">
                                <Link
                                    :href="`/candidate/portfolios/${portfolio.id}/edit`"
                                >
                                    <Edit class="mr-2 h-4 w-4" />
                                    Sửa
                                </Link>
                            </Button>
                        </CardFooter>
                    </Card>
                </div>

                <!-- Empty State -->
                <Card v-else>
                    <CardContent class="py-16 text-center">
                        <div
                            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100"
                        >
                            <Sparkles class="h-10 w-10 text-indigo-600" />
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-gray-900">
                            Chưa có dự án nào
                        </h3>
                        <p class="mb-6 max-w-md text-gray-500">
                            Bắt đầu xây dựng portfolio của bạn bằng cách thêm
                            các dự án bạn đã thực hiện
                        </p>
                        <Button as-child>
                            <Link href="/candidate/portfolios/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Thêm dự án đầu tiên
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <!-- Pagination -->
                <div
                    v-if="
                        portfolios.meta &&
                        portfolios.meta.last_page > 1 &&
                        portfolios.data.length > 0
                    "
                    class="mt-6"
                >
                    <Card>
                        <CardContent class="p-6">
                            <div class="flex items-center justify-center gap-2">
                                <Link
                                    v-for="link in portfolios.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url
                                            ? 'cursor-not-allowed opacity-50'
                                            : '',
                                        'rounded-md border border-gray-300 px-4 py-2 text-sm transition-colors',
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </CandidateLayout>
</template>

