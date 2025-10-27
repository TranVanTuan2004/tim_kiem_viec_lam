<script setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { vietnamLocations } from '@/lib/vietnamLocations';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';

// Form khởi tạo
const form = useForm({
    title: '',
    description: '',
    requirements: '',
    benefits: '',
    industry_id: '',
    employment_type: 'full_time',
    experience_level: 'fresher',
    city: '',
    province: '',
    location: '',
    min_salary: '',
    max_salary: '',
});

const districts = computed(() => {
    const province = vietnamLocations.find((p) => p.name === form.province);
    return province ? province.districts : [];
});

// Submit form
const submit = () => {
    console.log('Submitting form...', form.data());

    // Convert số nếu cần
    const payload = {
        ...form.data(),
        industry_id: Number(form.industry_id),
        min_salary: form.min_salary ? Number(form.min_salary) : null,
        max_salary: form.max_salary ? Number(form.max_salary) : null,
    };

    // Gửi thẳng URL POST
    form.post('/employer/posting', {
        data: payload,
        onSuccess: () => {
            successMessage.value = '✅ Tin tuyển dụng đã được tạo thành công!';
            form.reset(); // Reset form nếu muốn
        },
        onError: () => {
            successMessage.value = '';
        },
    });
};
</script>

<template>
    <Head title="Tạo mới bài đăng" />

    <AppLayout>
        <div class="mx-8 rounded-xl bg-white p-8 shadow">
            <div class="mb-6 flex items-center">
                <Link
                    href="/employer/posting"
                    class="flex items-center gap-2 text-gray-700 hover:text-gray-900"
                >
                    <ArrowLeft class="mr-1" text-current :size="30" /> Quay lại
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Tạo mới tin tuyển dụng</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title">Tiêu đề *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                placeholder="Lập trình viên Laravel"
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <Label for="description">Mô tả công việc *</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Mô tả chi tiết công việc..."
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <Label for="requirements">Yêu cầu ứng viên *</Label>
                            <Textarea
                                id="requirements"
                                v-model="form.requirements"
                                rows="3"
                                placeholder="Các kỹ năng, kinh nghiệm cần có..."
                            />
                            <p
                                v-if="form.errors.requirements"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.requirements }}
                            </p>
                        </div>

                        <div>
                            <Label for="benefits">Quyền lợi</Label>
                            <Textarea
                                id="benefits"
                                v-model="form.benefits"
                                rows="3"
                                placeholder="Những gì ứng viên sẽ nhận được..."
                            />
                            <p
                                v-if="form.errors.benefits"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.benefits }}
                            </p>
                        </div>

                        <div>
                            <Label for="industry_id">Ngành nghề *</Label>
                            <Input
                                id="industry_id"
                                v-model="form.industry_id"
                                placeholder="ID ngành nghề (tạm thời)"
                            />
                            <p
                                v-if="form.errors.industry_id"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.industry_id }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="employment_type"
                                    >Hình thức làm việc</Label
                                >
                                <select
                                    id="employment_type"
                                    v-model="form.employment_type"
                                    class="w-full rounded-md border p-2"
                                >
                                    <option value="full_time">Full-time</option>
                                    <option value="part_time">Part-time</option>
                                    <option value="internship">
                                        Internship
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.employment_type"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.employment_type }}
                                </p>
                            </div>

                            <div>
                                <Label for="experience_level"
                                    >Cấp bậc kinh nghiệm</Label
                                >
                                <select
                                    id="experience_level"
                                    v-model="form.experience_level"
                                    class="w-full rounded-md border p-2"
                                >
                                    <option value="fresher">Fresher</option>
                                    <option value="junior">Junior</option>
                                    <option value="senior">Senior</option>
                                </select>
                                <p
                                    v-if="form.errors.experience_level"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.experience_level }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Tỉnh -->
                            <div>
                                <label class="mb-1 block font-medium"
                                    >Tỉnh / Thành phố *</label
                                >
                                <select
                                    v-model="form.province"
                                    class="w-full rounded-lg border p-2 focus:ring focus:ring-indigo-200"
                                >
                                    <option value="">
                                        -- Chọn tỉnh / thành phố --
                                    </option>
                                    <option
                                        v-for="province in vietnamLocations"
                                        :key="province.name"
                                        :value="province.name"
                                    >
                                        {{ province.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.province"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.province }}
                                </p>
                            </div>

                            <!-- Quận / Huyện -->
                            <div>
                                <label class="mb-1 block font-medium"
                                    >Quận / Huyện *</label
                                >
                                <select
                                    v-model="form.city"
                                    class="w-full rounded-lg border p-2 focus:ring focus:ring-indigo-200"
                                    :disabled="!form.province"
                                >
                                    <option value="">
                                        -- Chọn quận / huyện --
                                    </option>
                                    <option
                                        v-for="district in districts"
                                        :key="district.name"
                                        :value="district.name"
                                    >
                                        {{ district.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.city"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.city }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <Label for="location">Địa điểm cụ thể</Label>
                            <Input
                                id="location"
                                v-model="form.location"
                                placeholder="53 Võ Văn Ngân, Phường Linh Chiểu"
                            />
                            <p
                                v-if="form.errors.location"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.location }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="min_salary">Lương tối thiểu</Label>
                                <Input
                                    id="min_salary"
                                    type="number"
                                    v-model="form.min_salary"
                                    placeholder="10.000.000"
                                />
                                <p
                                    v-if="form.errors.min_salary"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.min_salary }}
                                </p>
                            </div>
                            <div>
                                <Label for="max_salary">Lương tối đa</Label>
                                <Input
                                    id="max_salary"
                                    type="number"
                                    v-model="form.max_salary"
                                    placeholder="20.000.000"
                                />
                                <p
                                    v-if="form.errors.max_salary"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.max_salary }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <Button type="submit" :disabled="form.processing"
                                >Đăng tin</Button
                            >
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
