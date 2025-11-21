<template>
    <ClientLayout>
        <Head title="Đơn ứng tuyển của tôi" />

        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl"
                >
                    <!-- <div class="inset-0 bg-black/10"></div> -->
                    <div class="relative px-8 py-10 sm:px-12">
                        <h1 class="text-3xl font-bold text-white sm:text-4xl">
                            Đơn ứng tuyển của tôi
                        </h1>
                        <p class="mt-3 text-lg text-blue-100">
                            Theo dõi trạng thái các đơn ứng tuyển của bạn
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
                                {{ stats.total }}
                            </div>
                            <div class="text-sm text-gray-600">Tổng cộng</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-yellow-200 bg-yellow-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-yellow-700">
                                {{ stats.pending }}
                            </div>
                            <div class="text-sm text-yellow-700">Đang chờ</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-blue-200 bg-blue-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-blue-700">
                                {{ stats.reviewing }}
                            </div>
                            <div class="text-sm text-blue-700">Đang xem xét</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-green-200 bg-green-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-green-700">
                                {{ stats.interview }}
                            </div>
                            <div class="text-sm text-green-700">Phỏng vấn</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-purple-200 bg-purple-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-purple-700">
                                {{ stats.accepted }}
                            </div>
                            <div class="text-sm text-purple-700">Chấp nhận</div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-red-200 bg-red-50 transition-shadow hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <div class="text-2xl font-bold text-red-700">
                                {{ stats.rejected }}
                            </div>
                            <div class="text-sm text-red-700">Từ chối</div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Filters -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle class="text-lg">Bộ lọc</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Trạng thái
                                </label>
                                <select
                                    v-model="localFilters.status"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="all">
                                        Tất cả trạng thái
                                    </option>
                                    <option value="pending">Đang chờ</option>
                                    <option value="reviewing">Đang xem xét</option>
                                    <option value="interview">Phỏng vấn</option>
                                    <option value="rejected">Từ chối</option>
                                    <option value="accepted">Chấp nhận</option>
                                    <option value="withdrawn">Đã rút</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Sắp xếp theo
                                </label>
                                <select
                                    v-model="localFilters.sort_by"
                                    @change="applyFilters"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="created_at">
                                        Ngày ứng tuyển
                                    </option>
                                    <option value="updated_at">
                                        Cập nhật gần nhất
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Tìm kiếm
                                </label>
                                <div class="relative">
                                    <Search
                                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                                    />
                                    <input
                                        v-model="localFilters.search"
                                        @input="debounceSearch"
                                        type="text"
                                        placeholder="Tìm việc làm hoặc công ty..."
                                        class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Applications List -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg"
                            >Danh sách đơn ứng tuyển</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="p-0">
                        <div v-if="applications.data.length === 0" class="p-12 text-center">
                            <FileText class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-4 text-sm font-medium text-gray-900">
                                Không tìm thấy đơn ứng tuyển
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Bắt đầu ứng tuyển để xem chúng ở đây!
                            </p>
                            <div class="mt-6">
                                <Button as-child>
                                    <Link href="/jobs"> Tìm việc làm </Link>
                                </Button>
                            </div>
                        </div>

                        <div v-else class="responsive-table-wrapper">
                            <table class="w-full text-sm text-left mobile-card-view">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3">Công ty</th>
                                        <th class="px-6 py-3">Vị trí</th>
                                        <th class="px-6 py-3">Trạng thái</th>
                                        <th class="px-6 py-3">Ngày ứng tuyển</th>
                                        <th class="px-6 py-3 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="application in applications.data"
                                        :key="application.id"
                                        class="bg-white border-b hover:bg-gray-50"
                                    >
                                        <td class="px-6 py-4" data-label="Công ty">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="application.job_posting.company.logo"
                                                    :src="application.job_posting.company.logo"
                                                    :alt="application.job_posting.company.name"
                                                    class="h-10 w-10 rounded-lg border border-gray-200 object-cover"
                                                />
                                                <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100">
                                                    <Building2 class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <div class="font-medium text-gray-900">
                                                    {{ application.job_posting.company.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Vị trí">
                                            <Link
                                                :href="`/jobs/${application.job_posting.id}`"
                                                class="font-medium text-blue-600 hover:underline"
                                            >
                                                {{ application.job_posting.title }}
                                            </Link>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ application.job_posting.location }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4" data-label="Trạng thái">
                                            <Badge :variant="getStatusVariant(application.status)">
                                                {{ getStatusLabel(application.status) }}
                                            </Badge>
                                        </td>
                                        <td class="px-6 py-4" data-label="Ngày ứng tuyển">
                                            <div class="flex items-center text-gray-500">
                                                <Calendar class="mr-1 h-4 w-4" />
                                                {{ formatDate(application.applied_at) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right" data-label="Hành động">
                                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                                <Link
                                                    :href="`/candidate/applications/${application.id}`"
                                                    class="inline-flex items-center justify-center rounded-md bg-gray-900 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none"
                                                >
                                                    Xem
                                                </Link>
                                                <Button
                                                    v-if="['pending', 'reviewing'].includes(application.status)"
                                                    @click="withdrawApplication(application.id)"
                                                    variant="destructive"
                                                    size="sm"
                                                >
                                                    Rút đơn
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>

                    <!-- Pagination -->
                    <div
                        v-if="applications.data.length > 0"
                        class="border-t border-gray-200 p-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Hiển thị {{ applications.from }} đến
                                {{ applications.to }} trong tổng số
                                {{ applications.total }} đơn ứng tuyển
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in applications.links"
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
    </ClientLayout>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Calendar, FileText, Search } from 'lucide-vue-next';
import { reactive } from 'vue';

interface Props {
    applications: any;
    stats: any;
    filters: any;
}

const props = defineProps<Props>();

const localFilters = reactive({ ...props.filters });
let searchTimeout: number | null = null;

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        pending: 'Chờ xem xét',
        reviewing: 'Đang xem xét',
        interview: 'Phỏng vấn',
        rejected: 'Từ chối',
        accepted: 'Chấp nhận',
        withdrawn: 'Đã rút',
    };
    return labels[status] || status;
};

const getStatusVariant = (
    status: string,
): 'default' | 'secondary' | 'destructive' | 'outline' => {
    const variants: Record<
        string,
        'default' | 'secondary' | 'destructive' | 'outline'
    > = {
        pending: 'secondary',
        reviewing: 'outline',
        interview: 'default',
        rejected: 'destructive',
        accepted: 'default',
        withdrawn: 'secondary',
    };
    return variants[status] || 'secondary';
};

const applyFilters = () => {
    router.get('/candidate/applications', localFilters, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debounceSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const withdrawApplication = (id: number) => {
    if (confirm('Bạn có chắc muốn rút đơn ứng tuyển này?')) {
        router.post(
            `/candidate/applications/${id}/withdraw`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Handle success
                },
            },
        );
    }
};
</script>
