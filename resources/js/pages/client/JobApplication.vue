<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import ClientLayout from '@/layouts/ClientLayout.vue';
import jobs from '@/routes/jobs';
import { useForm } from '@inertiajs/vue3';
import {
    Briefcase,
    Building2,
    CheckCircle,
    FileText,
    MapPin,
    Upload,
} from 'lucide-vue-next';
import { computed, defineProps, ref, onMounted } from 'vue';
import { getCompanyLogoUrl } from '@/utils/storage';

const props = defineProps({
    job: {
        type: Object as () => any,
        required: true,
    },
    candidateProfile: {
        type: Object as () => any,
        required: true,
    },
});

const form = useForm({
    cover_letter: '',
    cv_file: null as File | null,
    cv_id: null as number | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const fileName = ref<string>('');

const hasStoredCVs = computed(() => {
    return props.candidateProfile?.cvs && props.candidateProfile.cvs.length > 0;
});

// Initialize default CV selection
onMounted(() => {
    if (hasStoredCVs.value) {
        const defaultCv = props.candidateProfile.cvs.find((cv: any) => cv.is_default);
        if (defaultCv) {
            form.cv_id = defaultCv.id;
        } else {
            form.cv_id = props.candidateProfile.cvs[0].id;
        }
    }
});

const handleCvSelection = (value: string) => {
    if (value === 'new') {
        form.cv_id = null;
    } else {
        form.cv_id = parseInt(value);
        form.cv_file = null;
        fileName.value = '';
    }
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.cv_file = file;
        fileName.value = file.name;
        // Ensure 'new' is selected implicitly by setting cv_id to null
        form.cv_id = null;
    }
};

const removeFile = () => {
    form.cv_file = null;
    fileName.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    form.post(jobs.apply.store(props.job.slug).url, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <ClientLayout :title="`Ứng tuyển - ${job.title}`">
        <div class="min-h-screen bg-muted/40">
            <div class="container mx-auto px-4 py-12">
                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white"
                            >
                                <CheckCircle class="h-4 w-4" />
                            </div>
                            <span class="font-medium text-red-600"
                                >Chọn việc làm</span
                            >
                        </div>
                        <div class="h-0.5 w-12 bg-red-600"></div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white"
                            >
                                2
                            </div>
                            <span class="font-medium text-red-600"
                                >Nộp đơn ứng tuyển</span
                            >
                        </div>
                        <div class="h-0.5 w-12 bg-muted"></div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-muted text-muted-foreground"
                            >
                                3
                            </div>
                            <span class="text-muted-foreground">Hoàn tất</span>
                        </div>
                    </div>
                </div>

                <div class="mx-auto max-w-4xl">
                    <!-- Job Info Card -->
                    <Card class="mb-6">
                        <CardContent class="p-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-muted"
                                >
                                    <img
                                        :src="getCompanyLogoUrl(job.company?.logo, job.company?.name)"
                                        :alt="job.company?.name"
                                        class="h-full w-full object-contain p-2"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h2 class="mb-2 text-2xl font-bold">
                                        {{ job.title }}
                                    </h2>
                                    <div
                                        class="flex flex-wrap gap-4 text-sm text-muted-foreground"
                                    >
                                        <div class="flex items-center gap-1">
                                            <Building2 class="h-4 w-4" />
                                            <span>{{ job.company?.name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <MapPin class="h-4 w-4" />
                                            <span>{{ job.location }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Briefcase class="h-4 w-4" />
                                            <span class="capitalize">{{
                                                job.employment_type?.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Application Form -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <FileText class="h-5 w-5" />
                                Thông tin ứng tuyển
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Cover Letter -->
                                <div class="space-y-2">
                                    <Label for="cover_letter">
                                        Thư xin việc
                                        <span class="text-muted-foreground"
                                            >(Không bắt buộc)</span
                                        >
                                    </Label>
                                    <Textarea
                                        id="cover_letter"
                                        v-model="form.cover_letter"
                                        placeholder="Giới thiệu về bản thân và lý do bạn phù hợp với vị trí này..."
                                        :rows="8"
                                        class="resize-none"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Viết một thư ngắn giới thiệu về bản thân
                                        và kinh nghiệm của bạn (tùy chọn)
                                    </p>
                                    <div
                                        v-if="form.errors.cover_letter"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.cover_letter }}
                                    </div>
                                </div>

                                <!-- CV Selection -->
                                <div class="space-y-4">
                                    <Label>CV / Resume <span class="text-red-600">*</span></Label>

                                    <!-- Stored CVs List -->
                                    <RadioGroup
                                        v-if="hasStoredCVs"
                                        :model-value="form.cv_id ? form.cv_id.toString() : 'new'"
                                        @update:model-value="handleCvSelection"
                                        class="space-y-3"
                                    >
                                        <div 
                                            v-for="cv in candidateProfile.cvs" 
                                            :key="cv.id" 
                                            class="flex items-center space-x-2 border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors" 
                                            :class="{'border-blue-500 bg-blue-50 ring-1 ring-blue-500': form.cv_id === cv.id}"
                                            @click="handleCvSelection(cv.id.toString())"
                                        >
                                            <RadioGroupItem :value="cv.id.toString()" :id="`cv-${cv.id}`" />
                                            <div class="flex-1 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <FileText class="w-4 h-4 text-blue-600" />
                                                    <span class="font-medium">{{ cv.name }}</span>
                                                    <span v-if="cv.is_default" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Mặc định</span>
                                                </div>
                                                <!-- We use a simple link or button to preview if needed, preventing event propagation -->
                                            </div>
                                        </div>

                                        <div 
                                            class="flex items-center space-x-2 border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors" 
                                            :class="{'border-blue-500 bg-blue-50 ring-1 ring-blue-500': !form.cv_id}"
                                            @click="handleCvSelection('new')"
                                        >
                                            <RadioGroupItem value="new" id="cv-new" />
                                            <Label for="cv-new" class="flex-1 cursor-pointer font-medium">
                                                Tải lên CV mới
                                            </Label>
                                        </div>
                                    </RadioGroup>

                                    <!-- File Upload Area (Show if 'new' is selected or no stored CVs) -->
                                    <div v-if="!form.cv_id" class="space-y-2 mt-2 animate-in fade-in slide-in-from-top-2">
                                        <div
                                            v-if="!form.cv_file"
                                            class="flex items-center justify-center rounded-lg border-2 border-dashed border-muted-foreground/25 p-8 transition-colors hover:border-muted-foreground/50"
                                            @click="fileInput?.click()"
                                        >
                                            <div
                                                class="cursor-pointer text-center"
                                            >
                                                <Upload
                                                    class="mx-auto mb-2 h-8 w-8 text-muted-foreground"
                                                />
                                                <p
                                                    class="mb-1 text-sm font-medium"
                                                >
                                                    Nhấp để tải lên CV
                                                </p>
                                                <p
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    PDF, DOC, DOCX (tối đa 5MB)
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Selected File Display -->
                                        <div
                                            v-if="form.cv_file"
                                            class="flex items-center justify-between rounded-lg border bg-muted p-4"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <FileText
                                                    class="h-5 w-5 text-muted-foreground"
                                                />
                                                <span class="text-sm">{{
                                                    fileName
                                                }}</span>
                                            </div>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                @click="removeFile"
                                            >
                                                Xóa
                                            </Button>
                                        </div>

                                        <input
                                            ref="fileInput"
                                            type="file"
                                            accept=".pdf,.doc,.docx"
                                            class="hidden"
                                            @change="handleFileChange"
                                        />
                                    </div>

                                    <div
                                        v-if="form.errors.cv_file"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.cv_file }}
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div
                                    class="flex items-center justify-between gap-4 border-t pt-6"
                                >
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="
                                            $inertia.visit(`/jobs/${job.slug}`)
                                        "
                                        :disabled="form.processing"
                                    >
                                        Quay lại
                                    </Button>
                                    <Button
                                        type="submit"
                                        class="bg-red-600 hover:bg-red-700"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing"
                                            >Đang gửi...</span
                                        >
                                        <span v-else>Nộp đơn ứng tuyển</span>
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Tips Card -->
                    <Card class="mt-6">
                        <CardHeader>
                            <CardTitle class="text-lg"
                                >💡 Mẹo để tăng cơ hội được chọn</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-2 text-sm text-muted-foreground">
                                <li>
                                    ✓ Đảm bảo CV của bạn cập nhật và phù hợp với
                                    vị trí ứng tuyển
                                </li>
                                <li>
                                    ✓ Viết thư xin việc ngắn gọn, rõ ràng và thể
                                    hiện sự nhiệt tình
                                </li>
                                <li>
                                    ✓ Nhấn mạnh những kỹ năng và kinh nghiệm
                                    liên quan đến công việc
                                </li>
                                <li>
                                    ✓ Kiểm tra kỹ chính tả và ngữ pháp trước khi
                                    gửi
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
