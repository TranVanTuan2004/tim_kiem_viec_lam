<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import { vietnamLocations } from '@/lib/vietnamLocations';
import AlertError from '@/components/AlertError.vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import Icon from '@/components/Icon.vue';
import { ChevronLeft } from 'lucide-vue-next';

// Lấy props job từ Inertia
const { job, industries } = defineProps<{ job: any; industries: any[] }>();

// Form khởi tạo với dữ liệu hiện tại
const form = useForm({
    title: job.title,
    description: job.description,
    requirements: job.requirements,
    benefits: job.benefits,
    industry_id: job.industry_id,
    employment_type: job.employment_type,
    experience_level: job.experience_level,
    city: job.city,
    province: job.province,
    location: job.location,
    min_salary: job.min_salary,
    max_salary: job.max_salary,
});

// districts phụ thuộc vào province
const districts = computed(() => {
    const province = vietnamLocations.find(p => p.name === form.province);
    return province ? province.districts : [];
});

// Submit form
const submit = () => {
    form.transform((data) => ({
        ...data,
        industry_id: Number(data.industry_id),
        min_salary: data.min_salary ? Number(data.min_salary) : null,
        max_salary: data.max_salary ? Number(data.max_salary) : null,
    })).put(`/employer/posting/${job.id}`, {
        onSuccess: () => console.log('✅ Cập nhật thành công'),
    });
};
</script>

<template>
    <Head title="Chỉnh sửa tin tuyển dụng" />

    <AppLayout>
        <div class="mx-8 rounded-xl bg-white p-8 shadow">
            <div class="mb-6 flex items-center">
                <Link href="/employer/posting" class="flex items-center gap-2 text-gray-700 hover:text-gray-900">
                    <ArrowLeft class="mr-1" text-current :size="30" /> Quay lại
  </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Chỉnh sửa tin tuyển dụng</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Tiêu đề -->
                        <div>
                            <Label for="title">Tiêu đề *</Label>
                            <Input id="title" v-model="form.title" placeholder="Lập trình viên Laravel" />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <Label for="description">Mô tả công việc *</Label>
                            <Textarea id="description" v-model="form.description" :rows="4" />
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
                        </div>

                        <!-- Yêu cầu -->
                        <div>
                            <Label for="requirements">Yêu cầu ứng viên *</Label>
                            <Textarea id="requirements" v-model="form.requirements" :rows="3" />
                            <p v-if="form.errors.requirements" class="text-red-500 text-sm mt-1">{{ form.errors.requirements }}</p>
                        </div>

                        <!-- Quyền lợi -->
                        <div>
                            <Label for="benefits">Quyền lợi</Label>
                            <Textarea id="benefits" v-model="form.benefits" :rows="3" />
                            <p v-if="form.errors.benefits" class="text-red-500 text-sm mt-1">{{ form.errors.benefits }}</p>
                        </div>

                        <!-- Ngành nghề -->
                        <div>
                            <Label for="industry_id">Ngành nghề *</Label>
                            <select
                                id="industry_id"
                                v-model="form.industry_id"
                                class="w-full rounded-md border p-2"
                            >
                                <option value="">-- Chọn ngành nghề --</option>
                                <option
                                    v-for="industry in industries"
                                    :key="industry.id"
                                    :value="industry.id"
                                >
                                    {{ industry.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.industry_id" class="text-red-500 text-sm mt-1">{{ form.errors.industry_id }}</p>
                        </div>

                        <!-- Hình thức & cấp bậc -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="employment_type">Hình thức làm việc</Label>
                                <select id="employment_type" v-model="form.employment_type" class="w-full rounded-md border p-2">
                                    <option value="full_time">Full-time</option>
                                    <option value="part_time">Part-time</option>
                                    <option value="internship">Internship</option>
                                </select>
                                <p v-if="form.errors.employment_type" class="text-red-500 text-sm mt-1">{{ form.errors.employment_type }}</p>
                            </div>
                            <div>
                                <Label for="experience_level">Cấp bậc kinh nghiệm</Label>
                                <select id="experience_level" v-model="form.experience_level" class="w-full rounded-md border p-2">
                                    <option value="fresher">Fresher</option>
                                    <option value="junior">Junior</option>
                                    <option value="senior">Senior</option>
                                </select>
                                <p v-if="form.errors.experience_level" class="text-red-500 text-sm mt-1">{{ form.errors.experience_level }}</p>
                            </div>
                        </div>

                        <!-- Tỉnh & Quận/Huyện -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label>Tỉnh / Thành phố *</Label>
                                <select v-model="form.province" class="w-full rounded-lg border p-2">
                                    <option value="">-- Chọn tỉnh / thành phố --</option>
                                    <option v-for="province in vietnamLocations" :key="province.name" :value="province.name">
                                        {{ province.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.province" class="text-red-500 text-sm mt-1">{{ form.errors.province }}</p>
                            </div>
                            <div>
                                <Label>Quận / Huyện *</Label>
                                <select v-model="form.city" class="w-full rounded-lg border p-2" :disabled="!form.province">
                                    <option value="">-- Chọn quận / huyện --</option>
                                    <option v-for="district in districts" :key="district.name" :value="district.name">
                                        {{ district.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.city" class="text-red-500 text-sm mt-1">{{ form.errors.city }}</p>
                            </div>
                        </div>

                        <!-- Địa điểm cụ thể -->
                        <div>
                            <Label for="location">Địa điểm cụ thể</Label>
                            <Input id="location" v-model="form.location" />
                            <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</p>
                        </div>

                        <!-- Lương -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="min_salary">Lương tối thiểu</Label>
                                <Input id="min_salary" type="number" v-model="form.min_salary" step="100" />
                                <p v-if="form.errors.min_salary" class="text-red-500 text-sm mt-1">{{ form.errors.min_salary }}</p>
                            </div>
                            <div>
                                <Label for="max_salary">Lương tối đa</Label>
                                <Input id="max_salary" type="number" v-model="form.max_salary" step="100"/>
                                <p v-if="form.errors.max_salary" class="text-red-500 text-sm mt-1">{{ form.errors.max_salary }}</p>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end pt-4">
                            <Button type="submit" :disabled="form.processing">Cập nhật</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

