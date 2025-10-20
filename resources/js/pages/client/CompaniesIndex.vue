<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { router } from '@inertiajs/vue3';
import { Building2, MapPin, Search, Star, Users } from 'lucide-vue-next';
import { computed, defineProps } from 'vue';

const props = defineProps({
    companies: {
        type: Object as () => any,
        required: true,
    },
});

const companiesData = computed(() => props.companies?.data || []);
const pagination = computed(() => ({
    current_page: props.companies?.current_page || 1,
    last_page: props.companies?.last_page || 1,
    per_page: props.companies?.per_page || 12,
    total: props.companies?.total || 0,
}));

const formatDate = (dateString: string) => {
    try {
        return new Date(dateString).toLocaleDateString('vi-VN');
    } catch (e) {
        return 'N/A';
    }
};

const getCompanySizeText = (size: string) => {
    if (!size) return 'Không xác định';

    const sizeMap: Record<string, string> = {
        '1-10': '1-10 nhân viên',
        '11-50': '11-50 nhân viên',
        '51-200': '51-200 nhân viên',
        '200+': '200+ nhân viên',
    };

    return sizeMap[size] || size;
};

const getLocationText = (company: any) => {
    const city = company.city;
    const province = company.province;

    if (city && province) {
        return `${city}, ${province}`;
    }
    return city || province || 'Không xác định';
};
</script>

<template>
    <ClientLayout title="Danh sách công ty">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-4 text-4xl font-bold">Danh sách công ty</h1>
                    <p class="text-lg text-muted-foreground">
                        Khám phá các công ty uy tín đang tuyển dụng
                    </p>
                </div>

                <!-- Search and Filters -->
                <div class="mb-8">
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="flex-1">
                            <div class="relative">
                                <Search
                                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    placeholder="Tìm kiếm công ty..."
                                    class="pl-10"
                                />
                            </div>
                        </div>
                        <Button variant="outline"> Lọc theo ngành </Button>
                        <Button variant="outline"> Lọc theo địa điểm </Button>
                    </div>
                </div>

                <!-- Companies Grid -->
                <div
                    class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="company in companiesData"
                        :key="company.id"
                        class="group cursor-pointer overflow-hidden transition-all hover:shadow-lg"
                        @click="router.visit(`/companies/${company.slug}`)"
                    >
                        <div
                            class="h-24 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950 dark:to-indigo-950"
                        ></div>
                        <CardContent class="relative pt-0">
                            <div
                                class="absolute -top-12 left-1/2 -translate-x-1/2"
                            >
                                <div
                                    class="flex h-24 w-24 items-center justify-center rounded-xl border-4 border-background bg-white shadow-lg dark:bg-card"
                                >
                                    <img
                                        v-if="company.logo"
                                        :src="company.logo"
                                        :alt="company.name"
                                        class="h-16 w-16 rounded-lg object-cover"
                                    />
                                    <Building2
                                        v-else
                                        class="h-8 w-8 text-muted-foreground"
                                    />
                                </div>
                            </div>

                            <div class="space-y-3 pt-14 text-center">
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <h3
                                        class="text-lg font-semibold transition-colors group-hover:text-blue-600"
                                    >
                                        {{ company.name }}
                                    </h3>
                                    <div
                                        v-if="company.is_verified"
                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500"
                                    >
                                        <span class="text-xs text-white"
                                            >✓</span
                                        >
                                    </div>
                                </div>

                                <div
                                    v-if="company.rating > 0"
                                    class="flex items-center justify-center space-x-1 text-sm"
                                >
                                    <Star
                                        class="h-4 w-4 fill-yellow-400 text-yellow-400"
                                    />
                                    <span class="font-medium">{{
                                        company.rating.toFixed(1)
                                    }}</span>
                                    <span class="text-muted-foreground"
                                        >({{ company.total_reviews }} đánh
                                        giá)</span
                                    >
                                </div>

                                <p
                                    class="line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{ company.description || 'Chưa có mô tả' }}
                                </p>

                                <div
                                    class="flex items-center justify-center gap-3 text-xs text-muted-foreground"
                                >
                                    <div class="flex items-center space-x-1">
                                        <MapPin class="h-3 w-3" />
                                        <span>{{
                                            getLocationText(company)
                                        }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <Users class="h-3 w-3" />
                                        <span>{{
                                            getCompanySizeText(company.size)
                                        }}</span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <Badge variant="secondary">
                                        {{ company.industry || 'N/A' }}
                                    </Badge>
                                    <Badge
                                        v-if="company.job_postings_count > 0"
                                        variant="default"
                                    >
                                        {{ company.job_postings_count }} việc
                                        làm
                                    </Badge>
                                </div>

                                <Button variant="outline" class="w-full">
                                    Xem chi tiết
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Pagination -->
                <div
                    v-if="pagination.last_page > 1"
                    class="flex justify-center"
                >
                    <div class="flex items-center space-x-2">
                        <Button
                            variant="outline"
                            :disabled="pagination.current_page === 1"
                            @click="
                                router.visit(
                                    `/companies?page=${pagination.current_page - 1}`,
                                )
                            "
                        >
                            Trước
                        </Button>

                        <div class="flex items-center space-x-1">
                            <Button
                                v-for="page in Math.min(
                                    5,
                                    pagination.last_page,
                                )"
                                :key="page"
                                :variant="
                                    page === pagination.current_page
                                        ? 'default'
                                        : 'outline'
                                "
                                size="sm"
                                @click="router.visit(`/companies?page=${page}`)"
                            >
                                {{ page }}
                            </Button>
                        </div>

                        <Button
                            variant="outline"
                            :disabled="
                                pagination.current_page === pagination.last_page
                            "
                            @click="
                                router.visit(
                                    `/companies?page=${pagination.current_page + 1}`,
                                )
                            "
                        >
                            Sau
                        </Button>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="companiesData.length === 0"
                    class="py-12 text-center"
                >
                    <Building2
                        class="mx-auto mb-4 h-16 w-16 text-muted-foreground"
                    />
                    <h3 class="mb-2 text-lg font-semibold">
                        Không tìm thấy công ty nào
                    </h3>
                    <p class="text-muted-foreground">
                        Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm
                    </p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
