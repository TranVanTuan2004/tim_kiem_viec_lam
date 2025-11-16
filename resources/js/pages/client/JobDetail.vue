<script setup lang="ts">
import axios from 'axios';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ClientLayout from '@/layouts/ClientLayout.vue';
import jobs from '@/routes/jobs';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Award,
    Bookmark,
    Briefcase,
    Calendar,
    Clock,
    DollarSign,
    Eye,
    MapPin,
    Share2,
    Users,
    Heart,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    job: {
        type: Object as () => any,
        required: true,
    },
});

const page = usePage();
const auth = computed(() => page.props.auth);

// State
// const isSaved = ref(false);
const isFavorite = ref(false);
const isSharing = ref(false);

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

// Methods
// const toggleSaveJob = () => {
//     if (!auth.value.user) {
//         router.visit('/login');
//         return;
//     }

//     // TODO: Implement save job API
//     isSaved.value = !isSaved.value;

//     if (isSaved.value) {
//         // Show success message
//         console.log('Job saved!');
//     } else {
//         console.log('Job unsaved!');
//     }
// };

const toggleFavoriteJob = async () => {
    if (!auth.value.user) {
        router.visit('/login');
        return;
    }

    const previousState = isFavorite.value;
    isFavorite.value = !isFavorite.value;

    try {
        const response = await axios.post(`/candidate/favorites/toggle/${jobData.value.id}`);
        isFavorite.value = response.data.is_favorited;
        alert(response.data.message);
    } catch (error: unknown) {
        isFavorite.value = previousState;

        let msg = 'Thao tác thất bại, vui lòng thử lại.';
        if (axios.isAxiosError(error) && error.response) {
            msg = error.response.data?.message || msg;
        }

        alert(msg);
    }
};


const shareJob = () => {
    if (navigator.share) {
        navigator
            .share({
                title: jobData.value.title,
                text: `Check out this job: ${jobData.value.title} at ${companyName.value}`,
                url: window.location.href,
            })
            .then(() => {
                console.log('Shared successfully');
            })
            .catch((error) => {
                console.log('Error sharing:', error);
            });
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
};
</script>

<template>
    <ClientLayout :title="jobData.title || 'Chi tiết việc làm'">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
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

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <!-- <Button
                                variant="outline"
                                size="icon"
                                @click="toggleSaveJob"
                                :class="{
                                    'bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-950 dark:text-red-400':
                                        isSaved,
                                }"
                            >
                                <Bookmark
                                    :class="{ 'fill-current': isSaved }"
                                    class="h-5 w-5"
                                />
                            </Button> -->

                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 flex-shrink-0 text-muted-foreground transition-colors hover:text-red-600"
                                @click="toggleFavoriteJob"
                                :class="{ 'text-red-600': isFavorite }"
                            >
                                <Heart
                                    class="h-5 w-5"
                                    :class="{ 'fill-current text-red-600': isFavorite }"
                                />
                            </Button>

                            <Button
                                variant="outline"
                                size="icon"
                                @click="shareJob"
                            >
                                <Share2 class="h-5 w-5" />
                            </Button>
                        </div>
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
                        <!-- Company Card -->
                        <Card v-if="jobData.company">
                            <CardHeader>
                                <CardTitle>About Company</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Link
                                    :href="`/companies/${jobData.company.slug}`"
                                    class="group block"
                                >
                                    <div class="space-y-3">
                                        <h3
                                            class="text-lg font-semibold transition-colors group-hover:text-red-600"
                                        >
                                            {{ companyName }}
                                        </h3>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="w-full"
                                        >
                                            View Company Profile →
                                        </Button>
                                    </div>
                                </Link>
                            </CardContent>
                        </Card>

                        <!-- Job Overview Card -->
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

                        <!-- Job Statistics (if available) -->
                        <Card
                            v-if="jobData.views || jobData.applications_count"
                        >
                            <CardHeader>
                                <CardTitle>Job Statistics</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-3 text-sm">
                                <div
                                    v-if="jobData.views"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <Eye
                                            class="h-4 w-4 text-muted-foreground"
                                        />
                                        <span class="text-muted-foreground"
                                            >Views</span
                                        >
                                    </div>
                                    <span class="font-semibold">{{
                                        jobData.views
                                    }}</span>
                                </div>
                                <div
                                    v-if="jobData.applications_count"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <Users
                                            class="h-4 w-4 text-muted-foreground"
                                        />
                                        <span class="text-muted-foreground"
                                            >Applicants</span
                                        >
                                    </div>
                                    <span class="font-semibold">{{
                                        jobData.applications_count
                                    }}</span>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Apply Button -->
                        <Button
                            size="lg"
                            class="w-full bg-red-600 hover:bg-red-700"
                            @click="router.visit(jobs.apply(jobData.slug).url)"
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
