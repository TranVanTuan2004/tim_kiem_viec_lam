<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Calendar, Clock, MapPin, Video, Phone, Users } from 'lucide-vue-next';

interface Application {
    id: number;
    candidate: {
        id: number;
        name: string;
        email: string;
    };
    job_posting: {
        id: number;
        title: string;
    };
}

const props = defineProps<{
    application?: Application;
}>();

const form = useForm({
    application_id: props.application?.id || '',
    title: props.application ? `Phỏng vấn - ${props.application.job_posting.title}` : '',
    description: '',
    type: 'video' as 'phone' | 'video' | 'in_person',
    location: '',
    scheduled_at: '',
    duration: 60,
    timezone: 'Asia/Ho_Chi_Minh',
});

const interviewTypes = [
    { value: 'video', label: 'Video Call', icon: Video },
    { value: 'phone', label: 'Điện thoại', icon: Phone },
    { value: 'in_person', label: 'Trực tiếp', icon: Users },
];

const durations = [
    { value: 30, label: '30 phút' },
    { value: 45, label: '45 phút' },
    { value: 60, label: '1 giờ' },
    { value: 90, label: '1.5 giờ' },
    { value: 120, label: '2 giờ' },
];

const submit = () => {
    form.post('/employer/interviews', {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Tạo Lịch Phỏng Vấn">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Tạo Lịch Phỏng Vấn</h1>
                <p class="text-gray-600 mt-2">Lên lịch phỏng vấn với ứng viên</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Thông Tin Phỏng Vấn</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Candidate Info (if from application) -->
                                <div v-if="application" class="p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Ứng viên</p>
                                    <p class="font-semibold">{{ application.candidate.name }}</p>
                                    <p class="text-sm text-gray-600">{{ application.candidate.email }}</p>
                                    <p class="text-sm text-gray-600 mt-2">Vị trí: {{ application.job_posting.title }}</p>
                                </div>

                                <!-- Title -->
                                <div>
                                    <Label for="title">Tiêu đề <span class="text-red-500">*</span></Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        placeholder="VD: Phỏng vấn vòng 1 - Frontend Developer"
                                        :class="{ 'border-red-500': form.errors.title }"
                                    />
                                    <p v-if="form.errors.title" class="text-sm text-red-500 mt-1">
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <Label for="description">Mô tả</Label>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        placeholder="Nội dung phỏng vấn, yêu cầu chuẩn bị..."
                                        rows="4"
                                    />
                                </div>

                                <!-- Interview Type -->
                                <div>
                                    <Label>Hình thức <span class="text-red-500">*</span></Label>
                                    <div class="grid grid-cols-3 gap-3 mt-2">
                                        <button
                                            v-for="type in interviewTypes"
                                            :key="type.value"
                                            type="button"
                                            @click="form.type = type.value"
                                            :class="[
                                                'p-4 border-2 rounded-lg flex flex-col items-center gap-2 transition-all',
                                                form.type === type.value
                                                    ? 'border-blue-500 bg-blue-50'
                                                    : 'border-gray-200 hover:border-gray-300'
                                            ]"
                                        >
                                            <component :is="type.icon" class="h-6 w-6" />
                                            <span class="text-sm font-medium">{{ type.label }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Location/Link -->
                                <div>
                                    <Label for="location">
                                        <template v-if="form.type === 'in_person'">Địa điểm</template>
                                        <template v-else-if="form.type === 'video'">Link Meeting</template>
                                        <template v-else>Số điện thoại</template>
                                    </Label>
                                    <div class="relative">
                                        <MapPin v-if="form.type === 'in_person'" class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        <Video v-else-if="form.type === 'video'" class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        <Phone v-else class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        <Input
                                            id="location"
                                            v-model="form.location"
                                            :placeholder="
                                                form.type === 'in_person'
                                                    ? 'VD: Văn phòng công ty, Tầng 5'
                                                    : form.type === 'video'
                                                    ? 'VD: https://meet.google.com/xxx'
                                                    : 'VD: 0123456789'
                                            "
                                            class="pl-10"
                                        />
                                    </div>
                                </div>

                                <!-- Date & Time -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label for="scheduled_at">Ngày & Giờ <span class="text-red-500">*</span></Label>
                                        <div class="relative">
                                            <Calendar class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                            <Input
                                                id="scheduled_at"
                                                v-model="form.scheduled_at"
                                                type="datetime-local"
                                                class="pl-10"
                                                :class="{ 'border-red-500': form.errors.scheduled_at }"
                                            />
                                        </div>
                                        <p v-if="form.errors.scheduled_at" class="text-sm text-red-500 mt-1">
                                            {{ form.errors.scheduled_at }}
                                        </p>
                                    </div>

                                    <div>
                                        <Label for="duration">Thời lượng <span class="text-red-500">*</span></Label>
                                        <div class="relative">
                                            <Clock class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                            <select
                                                id="duration"
                                                v-model="form.duration"
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-10 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                            >
                                                <option v-for="d in durations" :key="d.value" :value="d.value">
                                                    {{ d.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-3 pt-4">
                                    <Button type="submit" :disabled="form.processing" class="flex-1">
                                        {{ form.processing ? 'Đang tạo...' : 'Tạo Lịch Phỏng Vấn' }}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="router.visit('/employer/interviews')"
                                    >
                                        Hủy
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar Tips -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Gợi Ý</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4 text-sm">
                            <div>
                                <p class="font-semibold mb-1">📅 Chọn thời gian phù hợp</p>
                                <p class="text-gray-600">Nên chọn giờ hành chính và tránh cuối tuần</p>
                            </div>
                            <div>
                                <p class="font-semibold mb-1">📧 Email tự động</p>
                                <p class="text-gray-600">Ứng viên sẽ nhận email mời phỏng vấn ngay sau khi bạn tạo</p>
                            </div>
                            <div>
                                <p class="font-semibold mb-1">⏰ Nhắc lịch</p>
                                <p class="text-gray-600">Hệ thống sẽ tự động gửi email nhắc trước 24 giờ</p>
                            </div>
                            <div>
                                <p class="font-semibold mb-1">✅ Xác nhận</p>
                                <p class="text-gray-600">Ứng viên có thể xác nhận hoặc đề xuất thời gian khác</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
