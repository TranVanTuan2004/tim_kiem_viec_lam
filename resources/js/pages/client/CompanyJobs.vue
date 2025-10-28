<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Briefcase,
    Building2,
    Calendar,
    DollarSign,
    MapPin,
} from 'lucide-vue-next';
import { computed, defineProps } from 'vue';

const props = defineProps({
    company: {
        type: Object as () => any,
        required: true,
    },
    jobs: {
        type: Object as () => any,
        required: true,
    },
});

const companyData = computed(() => props.company || {});
const jobsData = computed(() => props.jobs?.data || []);
const jobsMeta = computed(() => props.jobs?.meta || {});

const getSalaryText = (job: any) => {
    const min = job.min_salary;
    const max = job.max_salary;
    const type = job.salary_type || '';

    if (min && max) return `${min} - ${max} ${type}`;
    if (min) return `Từ ${min} ${type}`;
    if (max) return `Đến ${max} ${type}`;
    return 'Thỏa thuận';
};

const getEmploymentTypeText = (type: string) => {
    if (!type) return 'N/A';
    return type.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const getExperienceLevelText = (level: string) => {
    if (!level) return 'N/A';
    return level.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString('vi-VN');
    } catch (e) {
        return 'N/A';
    }
};

const loadMore = () => {
    if (jobsData.value.length < jobsMeta.value.total) {
        router.visit(`/companies/${companyData.value.slug}/jobs`, {
            data: { page: (jobsMeta.value.current_page || 1) + 1 },
            preserveState: true,
            preserveScroll: true,
            only: ['jobs'],
        });
    }
};
</script>

<template>
    <ClientLayout :title="`Việc làm tại ${companyData.name}`">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Header -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center gap-4">
                        <Link
                            href="/companies"
                            class="text-muted-foreground hover:text-primary"
                        >
                            Danh sách công ty
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <Link
                            :href="`/companies/${companyData.slug}`"
                            class="text-muted-foreground hover:text-primary"
                        >
                            {{ companyData.name }}
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <span class="font-medium">Việc làm</span>
                    </div>

                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0">
                            <div
                                class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg border bg-white shadow-sm"
                            >
                                <img
                                    v-if="companyData.logo"
                                    :src="companyData.logo"
                                    :alt="companyData.name"
                                    class="h-full w-full object-cover"
                                />
                                <Building2
                                    v-else
                                    class="h-8 w-8 text-muted-foreground"
                                />
                            </div>
                        </div>

                        <div class="flex-1">
                            <h1 class="mb-2 text-3xl font-bold">
                                Việc làm tại {{ companyData.name }}
                            </h1>
                            <p class="mb-4 text-muted-foreground">
                                {{ jobsMeta.total || 0 }} việc làm đang tuyển
                            </p>
                            <div
                                v-if="companyData.description"
                                class="text-sm text-muted-foreground"
                                v-html="companyData.description"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Jobs List -->
                <div v-if="jobsData.length > 0" class="space-y-6">
                    <div
                        v-for="job in jobsData"
                        :key="job.id"
                        class="rounded-lg border bg-white shadow-sm transition-shadow hover:shadow-md"
                    >
                        <Card class="border-0 shadow-none">
                            <CardContent class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-1">
                                                <Link
                                                    :href="`/jobs/${job.slug}`"
                                                    class="group"
                                                >
                                                    <h3
                                                        class="mb-2 text-xl font-semibold transition-colors group-hover:text-primary"
                                                    >
                                                        {{ job.title }}
                                                    </h3>
                                                </Link>

                                                <div
                                                    class="mb-3 flex items-center gap-4 text-sm text-muted-foreground"
                                                >
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <MapPin
                                                            class="h-4 w-4"
                                                        />
                                                        {{
                                                            job.location ||
                                                            job.city ||
                                                            'Nơi làm việc'
                                                        }}
                                                    </span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Briefcase
                                                            class="h-4 w-4"
                                                        />
                                                        {{
                                                            getEmploymentTypeText(
                                                                job.employment_type,
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <DollarSign
                                                            class="h-4 w-4"
                                                        />
                                                        {{ getSalaryText(job) }}
                                                    </span>
                                                </div>

                                                <div
                                                    class="mb-4 flex items-center gap-4 text-sm text-muted-foreground"
                                                >
                                                    <span>{{
                                                        getExperienceLevelText(
                                                            job.experience_level,
                                                        )
                                                    }}</span>
                                                    <span
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Calendar
                                                            class="h-4 w-4"
                                                        />
                                                        {{
                                                            formatDate(
                                                                job.published_at,
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        v-if="job.industry"
                                                        class="flex items-center gap-1"
                                                    >
                                                        <Building2
                                                            class="h-4 w-4"
                                                        />
                                                        {{ job.industry.name }}
                                                    </span>
                                                </div>

                                                <!-- Skills -->
                                                <div
                                                    v-if="
                                                        job.skills &&
                                                        job.skills.length > 0
                                                    "
                                                    class="flex flex-wrap gap-2"
                                                >
                                                    <Badge
                                                        v-for="skill in job.skills"
                                                        :key="skill.id"
                                                        variant="secondary"
                                                    >
                                                        {{ skill.name }}
                                                    </Badge>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <Link
                                                    :href="`/jobs/${job.slug}`"
                                                >
                                                    <Button variant="default">
                                                        Xem chi tiết
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Load More Button -->
                    <div
                        v-if="jobsData.length < jobsMeta.total"
                        class="pt-6 text-center"
                    >
                        <Button
                            variant="outline"
                            @click="loadMore"
                            :disabled="false"
                        >
                            Xem thêm việc làm
                        </Button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="py-12 text-center">
                    <Briefcase
                        class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    />
                    <h3 class="mb-2 text-lg font-semibold">
                        Chưa có việc làm nào
                    </h3>
                    <p class="text-muted-foreground">
                        {{ companyData.name }} chưa đăng tuyển việc làm nào.
                    </p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
