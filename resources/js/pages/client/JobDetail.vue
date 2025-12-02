<script setup lang="ts">
import axios from 'axios';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import ClientLayout from '@/layouts/ClientLayout.vue';
import jobs from '@/routes/jobs';
import { getCompanyLogoUrl } from '@/utils/storage';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Award,
    Briefcase,
    Calendar,
    Clock,
    DollarSign,
    Eye,
    Flag,
    MapPin,
    Share2,
    Users,
    Heart,
    Building2,
    CheckCircle2,
    CheckCircle,
} from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';
import ReportModal from '@/components/ReportModal.vue';
import { useClientToast } from '@/composables/useClientToast';

const props = defineProps({
    job: {
        type: Object as () => any,
        required: true,
    },
});

const page = usePage();
const auth = computed(() => page.props.auth);

// State
const isFavorite = ref(false);
const isSharing = ref(false);
const showReportModal = ref(false);

const { showToast } = useClientToast();

// Computed safe accessors with fallbacks
const jobData = computed(() => props.job || {});

const getSalaryText = computed(() => {
    const j = jobData.value;
    const min = j.min_salary;
    const max = j.max_salary;
    const type = j.salary_type || '';
    if (min && max) return `${min} - ${max} ${type}`;
    if (min) return `Từ ${min} ${type}`;
    if (max) return `Đến ${max} ${type}`;
    return j.salary_text || 'Thỏa thuận';
});

const employmentTypeText = computed(() => {
    const t = jobData.value.employment_type || '';
    return t ? String(t).replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase()) : 'N/A';
});

const experienceLevelText = computed(
    () => jobData.value.experience_level || 'N/A',
);

const applicationDeadlineText = computed(() => {
    const d = jobData.value.application_deadline;
    try {
        return d ? new Date(d).toLocaleDateString('vi-VN') : 'Không có';
    } catch (e) {
        return 'Không có';
    }
});

const publishedAtText = computed(() => {
    const d = jobData.value.published_at;
    try {
        return d ? new Date(d).toLocaleDateString('vi-VN') : 'N/A';
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
        
        showToast(
            'success',
            response.data.is_favorited ? 'Đã lưu' : 'Đã xóa',
            response.data.message
        );
    } catch (error: unknown) {
        isFavorite.value = previousState;

        let msg = 'Thao tác thất bại, vui lòng thử lại.';
        if (axios.isAxiosError(error) && error.response) {
            msg = error.response.data?.message || msg;
        }

        showToast('error', 'Lỗi', msg);
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
        showToast('success', 'Đã sao chép', 'Link đã được sao chép vào clipboard!');
    }
};
</script>

<template>
    <ClientLayout :title="jobData.title || 'Chi tiết việc làm'">
        <div class="bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <div class="flex items-center gap-4 text-sm">
                        <Link
                            href="/"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            Trang chủ
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <Link
                            href="/jobs"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            Việc làm
                        </Link>
                        <span class="text-muted-foreground">/</span>
                        <span class="font-medium text-foreground">
                            {{ jobData.title }}
                        </span>
                    </div>
                </div>

                <!-- Job Header -->
                <div class="mb-8">
                    <div class="flex items-start gap-6">
                        <!-- Company Logo -->
                        <div class="group flex-shrink-0">
                            <div
                                class="relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 shadow-lg ring-2 ring-red-100 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:ring-red-300"
                            >
                                <img
                                    :src="getCompanyLogoUrl(jobData.company?.logo, companyName)"
                                    :alt="companyName"
                                    class="relative z-10 h-full w-full object-contain p-2"
                                />
                                <!-- Glow Effect -->
                                <div
                                    class="absolute inset-0 rounded-xl bg-gradient-to-br from-red-400/30 to-orange-400/30 opacity-0 blur-xl transition-opacity duration-500 group-hover:opacity-100"
                                ></div>
                            </div>
                        </div>

                        <!-- Job Info -->
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                <h1 class="text-3xl font-bold">
                                    {{ jobData.title || 'Untitled' }}
                                </h1>
                                <CheckCircle
                                    v-if="jobData.is_featured"
                                    class="h-6 w-6 text-green-600"
                                />
                            </div>

                            <div
                                class="mb-4 flex items-center gap-4 text-muted-foreground"
                            >
                                <Link
                                    :href="`/companies/${jobData.company?.slug || 'techcorp-vietnam'}`"
                                    class="flex items-center gap-1 transition-colors hover:text-primary"
                                >
                                    <Building2 class="h-4 w-4" />
                                    {{ companyName }}
                                </Link>
                                <span class="flex items-center gap-1">
                                    <MapPin class="h-4 w-4" />
                                    {{ locationText }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <DollarSign class="h-4 w-4" />
                                    {{ getSalaryText }}
                                </span>
                            </div>

                            <!-- Stats -->
                            <div
                                v-if="jobData.views || jobData.applications_count"
                                class="flex items-center gap-4 text-sm text-muted-foreground"
                            >
                                <span v-if="jobData.views" class="flex items-center gap-1">
                                    <Eye class="h-4 w-4" />
                                    {{ jobData.views }} lượt xem
                                </span>
                                <span v-if="jobData.applications_count" class="flex items-center gap-1">
                                    <Users class="h-4 w-4" />
                                    {{ jobData.applications_count }} ứng viên
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2">
                            <Button
                                variant="destructive"
                                size="lg"
                                class="flex-1"
                                @click="router.visit(jobs.apply(jobData.slug).url)"
                            >
                                Ứng tuyển ngay
                            </Button>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="icon"
                                    @click="toggleFavoriteJob"
                                    :class="{ 'bg-red-50 text-red-600 hover:bg-red-100': isFavorite }"
                                >
                                    <Heart
                                        :class="{ 'fill-current': isFavorite }"
                                        class="h-5 w-5"
                                    />
                                </Button>

                                <Button
                                    variant="outline"
                                    size="icon"
                                    @click="shareJob"
                                >
                                    <Share2 class="h-5 w-5" />
                                </Button>
                                
                                <Button
                                    v-if="auth.user"
                                    variant="outline"
                                    size="icon"
                                    @click="showReportModal = true"
                                    title="Báo cáo vi phạm"
                                >
                                    <Flag class="h-5 w-5" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Main Content -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Job Description Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Briefcase class="h-5 w-5 text-primary" />
                                    Mô tả công việc
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div
                                    class="prose dark:prose-invert max-w-none"
                                    v-html="jobData.description || jobData.description_html || 'Không có mô tả'"
                                ></div>
                            </CardContent>
                        </Card>

                        <!-- Requirements Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <CheckCircle2 class="h-5 w-5 text-primary" />
                                    Yêu cầu công việc
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div
                                    class="prose dark:prose-invert max-w-none"
                                    v-html="jobData.requirements || 'Không có yêu cầu cụ thể'"
                                ></div>
                            </CardContent>
                        </Card>

                        <!-- Benefits Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Award class="h-5 w-5 text-primary" />
                                    Quyền lợi
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div
                                    class="prose dark:prose-invert max-w-none"
                                    v-html="jobData.benefits || 'Không có thông tin quyền lợi'"
                                ></div>
                            </CardContent>
                        </Card>

                        <!-- Skills Card -->
                        <Card v-if="skillsList.length > 0">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Award class="h-5 w-5 text-primary" />
                                    Kỹ năng yêu cầu
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="skill in skillsList"
                                        :key="skill.id ?? skill"
                                        variant="secondary"
                                        class="text-sm"
                                    >
                                        {{ skill.name ?? skill }}
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Company Card -->
                        <Card v-if="jobData.company">
                            <CardHeader>
                                <CardTitle>Về công ty</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Link
                                    :href="`/companies/${jobData.company.slug}`"
                                    class="group block"
                                >
                                    <div class="space-y-4">
                                        <h3
                                            class="text-lg font-semibold transition-colors group-hover:text-primary"
                                        >
                                            {{ companyName }}
                                        </h3>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="w-full"
                                        >
                                            Xem trang công ty
                                        </Button>
                                    </div>
                                </Link>
                            </CardContent>
                        </Card>

                        <!-- Job Overview Card -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Thông tin chung</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <DollarSign
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Mức lương</div>
                                        <div class="font-medium">
                                            {{ getSalaryText }}
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <MapPin
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Địa điểm</div>
                                        <div class="font-medium">
                                            {{ locationText }}
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <Briefcase
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Loại hình</div>
                                        <div class="font-medium capitalize">
                                            {{ employmentTypeText }}
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <Award
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Kinh nghiệm</div>
                                        <div class="font-medium capitalize">
                                            {{ experienceLevelText }}
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <Users
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Số lượng</div>
                                        <div class="font-medium">
                                            {{ quantityText }} người
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <Calendar
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Hạn nộp hồ sơ</div>
                                        <div class="font-medium">
                                            {{ applicationDeadlineText }}
                                        </div>
                                    </div>
                                </div>

                                <Separator />
                                
                                <div class="flex items-center gap-2">
                                    <Clock
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                    <div class="flex-1">
                                        <div class="text-sm text-muted-foreground">Ngày đăng</div>
                                        <div class="font-medium">
                                            {{ publishedAtText }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Apply Button (Sticky) -->
                        <div class="sticky top-4">
                            <Button
                                size="lg"
                                class="w-full bg-red-600 hover:bg-red-700"
                                @click="router.visit(jobs.apply(jobData.slug).url)"
                            >
                                Ứng tuyển ngay
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Modal -->
        <ReportModal
            v-if="auth.user"
            :open="showReportModal"
            @update:open="showReportModal = $event"
            reportable-type="App\Models\JobPosting"
            :reportable-id="jobData.id"
            :reportable-title="jobData.title"
        />
    </ClientLayout>
</template>

<style scoped>
/* Prose Styling */
.prose h2 {
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: hsl(var(--foreground));
}

.prose h3 {
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: hsl(var(--foreground));
}

.prose p {
    margin-bottom: 1rem;
    line-height: 1.75;
}

.prose ul,
.prose ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose strong {
    font-weight: 600;
    color: hsl(var(--foreground));
}
</style>
