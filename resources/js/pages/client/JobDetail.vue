<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Award,
    Briefcase,
    Calendar,
    Clock,
    DollarSign,
    MapPin,
    Users,
} from 'lucide-vue-next';
import { computed, defineProps } from 'vue';

const props = defineProps({
    job: {
        type: Object as () => any,
        required: true,
    },
});

// Computed safe accessors with fallbacks
const jobData = computed(() => props.job || {});

const getSalaryText = computed(() => {
    const j = jobData.value;
    const min = j.min_salary;
    const max = j.max_salary;
    const type = j.salary_type || '';
    if (min && max) return `${min} - ${max} ${type}`;
    if (min) return `From ${min} ${type}`;
    if (max) return `Up to ${max} ${type}`;
    return j.salary_text || 'Thỏa thuận';
});

const employmentTypeText = computed(() => {
    const t = jobData.value.employment_type || '';
    return t ? String(t).replace(/_/g, ' ') : 'N/A';
});

const experienceLevelText = computed(
    () => jobData.value.experience_level || 'N/A',
);

const applicationDeadlineText = computed(() => {
    const d = jobData.value.application_deadline;
    try {
        return d ? new Date(d).toLocaleDateString() : 'Không có';
    } catch (e) {
        return 'Không có';
    }
});

const publishedAtText = computed(() => {
    const d = jobData.value.published_at;
    try {
        return d ? new Date(d).toLocaleDateString() : 'N/A';
    } catch (e) {
        return 'N/A';
    }
});

const companyName = computed(
    () =>
        jobData.value.company?.name ||
        jobData.value.company?.company_name ||
        'Công ty',
);
const skillsList = computed(() =>
    Array.isArray(jobData.value.skills)
        ? jobData.value.skills
        : jobData.value.skills || [],
);
const quantityText = computed(() => jobData.value.quantity ?? '1');
const locationText = computed(
    () => jobData.value.location || jobData.value.city || 'Nơi làm việc',
);
</script>

<template>
    <ClientLayout :title="jobData.title || 'Chi tiết việc làm'">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 text-4xl font-bold tracking-tight">
                        {{ jobData.title || 'Untitled' }}
                    </h1>
                    <div
                        class="flex items-center space-x-4 text-muted-foreground"
                    >
                        <Link
                            :href="`/companies/${jobData.company?.slug || 'techcorp-vietnam'}`"
                            class="cursor-pointer transition-colors hover:text-primary"
                        >
                            {{ companyName }}
                        </Link>
                        <span class="flex items-center"
                            ><MapPin class="mr-1 h-4 w-4" />
                            {{ locationText }}</span
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <Card>
                            <CardContent class="pt-6">
                                <div
                                    class="prose dark:prose-invert max-w-none space-y-6"
                                >
                                    <h2 class="text-2xl font-semibold">
                                        Mô tả công việc
                                    </h2>
                                    <p
                                        v-html="
                                            jobData.description ||
                                            jobData.description_html ||
                                            ''
                                        "
                                    ></p>

                                    <h2 class="text-2xl font-semibold">
                                        Yêu cầu
                                    </h2>
                                    <p v-html="jobData.requirements || ''"></p>

                                    <h2 class="text-2xl font-semibold">
                                        Quyền lợi
                                    </h2>
                                    <p v-html="jobData.benefits || ''"></p>
                                </div>

                                <!-- Skills -->
                                <div class="mt-8">
                                    <h3 class="mb-4 text-xl font-semibold">
                                        Required Skills
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            v-for="skill in skillsList"
                                            :key="skill.id ?? skill"
                                            variant="secondary"
                                        >
                                            {{ skill.name ?? skill }}
                                        </Badge>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Job Overview</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4 text-sm">
                                <div class="flex items-center">
                                    <DollarSign
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">Lương</div>
                                        <div class="text-muted-foreground">
                                            {{ getSalaryText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <MapPin
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">
                                            Location
                                        </div>
                                        <div class="text-muted-foreground">
                                            {{ locationText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <Briefcase
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">
                                            Employment Type
                                        </div>
                                        <div
                                            class="text-muted-foreground capitalize"
                                        >
                                            {{ employmentTypeText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <Award
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">
                                            Experience Level
                                        </div>
                                        <div
                                            class="text-muted-foreground capitalize"
                                        >
                                            {{ experienceLevelText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <Users
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">
                                            Quantity
                                        </div>
                                        <div class="text-muted-foreground">
                                            {{ quantityText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <Calendar
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">
                                            Application Deadline
                                        </div>
                                        <div class="text-muted-foreground">
                                            {{ applicationDeadlineText }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <Clock
                                        class="mr-3 h-5 w-5 text-muted-foreground"
                                    />
                                    <div>
                                        <div class="font-semibold">Posted</div>
                                        <div class="text-muted-foreground">
                                            {{ publishedAtText }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Button
                            size="lg"
                            class="w-full bg-red-600 hover:bg-red-700"
                            @click="router.visit(`/jobs/${jobData.slug}/apply`)"
                            >Apply Now</Button
                        >
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.prose h2 {
    margin-bottom: 1rem;
}
.prose p {
    margin-bottom: 1.5rem;
}
</style>
