<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';
import { vietnamLocations } from '@/lib/vietnamLocations';

// Toast chỉ dùng cho success
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

// Nhận props từ server
const props = defineProps<{ job: any; industries: any[] }>();

// Form khởi tạo với dữ liệu hiện tại
const form = useForm({
    title: props.job.title,
    description: props.job.description,
    requirements: props.job.requirements,
    benefits: props.job.benefits,
    industry_id: props.job.industry_id,
    employment_type: props.job.employment_type,
    experience_level: props.job.experience_level,
    city: props.job.city,
    province: props.job.province,
    location: props.job.location,
    min_salary: props.job.min_salary,
    max_salary: props.job.max_salary,
});

// districts phụ thuộc vào province
const districts = computed(() => {
    const province = vietnamLocations.find(p => p.name === form.province);
    return province ? province.districts : [];
});

// Submit form
const submit = () => {
    form
        .transform((data) => ({
            ...data,
            industry_id: Number(data.industry_id),
            min_salary: data.min_salary ? Number(data.min_salary) : null,
            max_salary: data.max_salary ? Number(data.max_salary) : null,
        }))
        .put(`/employer/posting/${props.job.id}`, {
            onSuccess: () => {
                toast.success('🎉 Cập nhật tin tuyển dụng thành công!');
            },
            // Không cần onError, validate đã hiện dưới input
        });
};
</script>

<template>
    <Head title="Chỉnh sửa tin tuyển dụng" />

```
<AppLayout>
    <div class="m-8 rounded-xl bg-indigo-50 p-8 shadow-lg border border-indigo-200">
        <!-- Back -->
        <div class="mb-6 flex items-center">
            <Link
                href="/employer/posting"
                class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-medium"
            >
                <ArrowLeft class="mr-1" :size="26" /> Quay lại
            </Link>
        </div>

        <Card class="shadow-xl border border-indigo-300 rounded-xl bg-white">
            <CardHeader>
                <CardTitle class="text-2xl font-bold text-indigo-700">
                    Chỉnh sửa tin tuyển dụng
                </CardTitle>
            </CardHeader>

            <CardContent class="space-y-6">
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Tiêu đề -->
                    <div>
                        <Label for="title">Tiêu đề <span class="text-red-500">*</span></Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Lập trình viên Laravel"
                            class="border-indigo-300 bg-indigo-50"
                        />
                        <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <!-- Mô tả -->
                    <div>
                        <Label for="description">Mô tả công việc <span class="text-red-500">*</span></Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="Mô tả chi tiết công việc..."
                            class="border-indigo-300 bg-indigo-50"
                        />
                        <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Yêu cầu -->
                    <div>
                        <Label for="requirements">Yêu cầu ứng viên <span class="text-red-500">*</span></Label>
                        <Textarea
                            id="requirements"
                            v-model="form.requirements"
                            rows="3"
                            placeholder="Các kỹ năng, kinh nghiệm cần có..."
                            class="border-indigo-300 bg-indigo-50"
                        />
                        <p v-if="form.errors.requirements" class="text-sm text-red-600">{{ form.errors.requirements }}</p>
                    </div>

                    <!-- Quyền lợi -->
                    <div>
                        <Label for="benefits">Quyền lợi</Label>
                        <Textarea
                            id="benefits"
                            v-model="form.benefits"
                            rows="3"
                            placeholder="Những gì ứng viên sẽ nhận được..."
                            class="border-indigo-50 bg-indigo-50"
                        />
                        <p v-if="form.errors.benefits" class="text-sm text-red-600">{{ form.errors.benefits }}</p>
                    </div>

                    <!-- Ngành nghề -->
                    <div>
                        <Label for="industry_id">Ngành nghề <span class="text-red-500">*</span></Label>
                        <select
                            id="industry_id"
                            v-model="form.industry_id"
                            class="w-full rounded-md border border-indigo-300 bg-indigo-50 p-2"
                        >
                            <option value="">-- Chọn ngành nghề --</option>
                            <option v-for="industry in props.industries" :key="industry.id" :value="industry.id">
                                {{ industry.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.industry_id" class="text-sm text-red-600">{{ form.errors.industry_id }}</p>
                    </div>

                    <!-- Hình thức + cấp bậc -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label>Hình thức làm việc</Label>
                            <select
                                v-model="form.employment_type"
                                class="w-full rounded-md border border-indigo-300 bg-indigo-50 p-2"
                            >
                                <option value="full_time">Full-time</option>
                                <option value="part_time">Part-time</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div>
                            <Label>Cấp bậc kinh nghiệm</Label>
                            <select
                                v-model="form.experience_level"
                                class="w-full rounded-md border border-indigo-300 bg-indigo-50 p-2"
                            >
                                <option value="intern">Intern</option>
                                <option value="fresher">Fresher</option>
                                <option value="junior">Junior</option>
                                <option value="middle">Middle</option>
                                <option value="senior">Senior</option>
                                <option value="lead">Lead</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tỉnh & Huyện -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label>Tỉnh / Thành phố *</Label>
                            <select
                                v-model="form.province"
                                class="w-full rounded-md border border-indigo-300 bg-indigo-50 p-2"
                            >
                                <option value="">-- Chọn tỉnh / thành phố --</option>
                                <option
                                    v-for="province in vietnamLocations"
                                    :key="province.name"
                                    :value="province.name"
                                >
                                    {{ province.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.province" class="text-sm text-red-600">{{ form.errors.province }}</p>
                        </div>

                        <div>
                            <Label>Quận / Huyện *</Label>
                            <select
                                v-model="form.city"
                                :disabled="!form.province"
                                class="w-full rounded-md border border-indigo-300 bg-indigo-50 p-2 disabled:bg-gray-100"
                            >
                                <option value="">-- Chọn quận / huyện --</option>
                                <option
                                    v-for="district in districts"
                                    :key="district.name"
                                    :value="district.name"
                                >
                                    {{ district.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.city" class="text-sm text-red-600">{{ form.errors.city }}</p>
                        </div>
                    </div>

                    <!-- Địa điểm -->
                    <div>
                        <Label>Địa điểm cụ thể</Label>
                        <Input
                            v-model="form.location"
                            placeholder="53 Võ Văn Ngân, Thủ Đức"
                            class="border-indigo-300 bg-indigo-50"
                        />
                        <p v-if="form.errors.location" class="text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>

                    <!-- Lương -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <Label>Lương tối thiểu</Label>
                            <Input
                                type="number"
                                v-model="form.min_salary"
                                placeholder="10.000.000"
                                class="border-indigo-300 bg-indigo-50"
                            />
                        </div>
                        <div>
                            <Label>Lương tối đa</Label>
                            <Input
                                type="number"
                                v-model="form.max_salary"
                                placeholder="20.000.000"
                                class="border-indigo-300 bg-indigo-50"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button
                            type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white"
                            :disabled="form.processing"
                        >
                            Cập nhật
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</AppLayout>
```

</template>
