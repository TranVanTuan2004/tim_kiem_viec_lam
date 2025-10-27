<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    title: '',
    description: '',
    project_url: '',
    github_url: '',
    demo_url: '',
    images: [] as File[],
    technologies: [] as string[],
    start_date: '',
    end_date: '',
    is_ongoing: false,
    is_featured: false,
    is_public: true,
});

const newTechnology = ref('');
const imagePreviewUrls = ref<string[]>([]);

const addTechnology = () => {
    if (newTechnology.value.trim()) {
        form.technologies.push(newTechnology.value.trim());
        newTechnology.value = '';
    }
};

const removeTechnology = (index: number) => {
    form.technologies.splice(index, 1);
};

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files) {
        const filesArray = Array.from(files);

        // Add new files
        filesArray.forEach((file) => {
            form.images.push(file);

            // Create preview URL
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreviewUrls.value.push(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeImage = (index: number) => {
    form.images.splice(index, 1);
    imagePreviewUrls.value.splice(index, 1);
};

const submit = () => {
    form.post('/candidate/portfolios', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreviewUrls.value = [];
        },
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Portfolio', href: '/candidate/portfolios' },
    { title: 'Tạo mới', href: '/candidate/portfolios/create' },
];
</script>

<template>
    <Head title="Tạo Portfolio mới" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-4xl flex-col gap-6 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Tạo dự án mới</h1>
                <p class="mt-2 text-muted-foreground">
                    Thêm dự án vào portfolio của bạn
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Thông tin cơ bản</CardTitle>
                        <CardDescription
                            >Thông tin chính về dự án của bạn</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Title -->
                        <div class="space-y-2">
                            <Label for="title">
                                Tên dự án <span class="text-red-500">*</span>
                            </Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                placeholder="Ví dụ: Website thương mại điện tử"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Mô tả</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Mô tả chi tiết về dự án..."
                                rows="5"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- URLs -->
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="project_url">Website URL</Label>
                                <Input
                                    id="project_url"
                                    v-model="form.project_url"
                                    type="url"
                                    placeholder="https://example.com"
                                    :class="{
                                        'border-red-500':
                                            form.errors.project_url,
                                    }"
                                />
                                <p
                                    v-if="form.errors.project_url"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.project_url }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="github_url">GitHub URL</Label>
                                <Input
                                    id="github_url"
                                    v-model="form.github_url"
                                    type="url"
                                    placeholder="https://github.com/..."
                                    :class="{
                                        'border-red-500':
                                            form.errors.github_url,
                                    }"
                                />
                                <p
                                    v-if="form.errors.github_url"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.github_url }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="demo_url">Demo URL</Label>
                                <Input
                                    id="demo_url"
                                    v-model="form.demo_url"
                                    type="url"
                                    placeholder="https://demo.example.com"
                                    :class="{
                                        'border-red-500': form.errors.demo_url,
                                    }"
                                />
                                <p
                                    v-if="form.errors.demo_url"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.demo_url }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Images -->
                <Card>
                    <CardHeader>
                        <CardTitle>Hình ảnh dự án</CardTitle>
                        <CardDescription
                            >Tải lên hình ảnh showcase dự án (tối đa 10
                            ảnh)</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Upload Button -->
                        <div>
                            <Label
                                for="images"
                                class="flex h-32 cursor-pointer items-center justify-center rounded-lg border-2 border-dashed hover:bg-accent"
                            >
                                <div class="text-center">
                                    <Upload
                                        class="mx-auto h-8 w-8 text-muted-foreground"
                                    />
                                    <p
                                        class="mt-2 text-sm text-muted-foreground"
                                    >
                                        Click để tải ảnh lên
                                    </p>
                                </div>
                            </Label>
                            <Input
                                id="images"
                                type="file"
                                multiple
                                accept="image/*"
                                class="hidden"
                                @change="handleImageUpload"
                            />
                            <p
                                v-if="form.errors.images"
                                class="mt-2 text-sm text-red-500"
                            >
                                {{ form.errors.images }}
                            </p>
                        </div>

                        <!-- Image Previews -->
                        <div
                            v-if="imagePreviewUrls.length > 0"
                            class="grid grid-cols-2 gap-4 md:grid-cols-4"
                        >
                            <div
                                v-for="(url, index) in imagePreviewUrls"
                                :key="index"
                                class="relative aspect-square overflow-hidden rounded-lg border"
                            >
                                <img
                                    :src="url"
                                    alt="Preview"
                                    class="h-full w-full object-cover"
                                />
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="icon"
                                    class="absolute top-1 right-1 h-6 w-6"
                                    @click="removeImage(index)"
                                >
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Technologies -->
                <Card>
                    <CardHeader>
                        <CardTitle>Công nghệ sử dụng</CardTitle>
                        <CardDescription
                            >Thêm các công nghệ và kỹ năng sử dụng trong dự
                            án</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex gap-2">
                            <Input
                                v-model="newTechnology"
                                placeholder="Ví dụ: React, Node.js, MongoDB..."
                                @keyup.enter.prevent="addTechnology"
                            />
                            <Button type="button" @click="addTechnology">
                                <Plus class="h-4 w-4" />
                            </Button>
                        </div>

                        <div
                            v-if="form.technologies.length > 0"
                            class="flex flex-wrap gap-2"
                        >
                            <Badge
                                v-for="(tech, index) in form.technologies"
                                :key="index"
                                variant="secondary"
                                class="gap-1"
                            >
                                {{ tech }}
                                <button
                                    type="button"
                                    @click="removeTechnology(index)"
                                    class="hover:text-destructive"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </Badge>
                        </div>
                        <p
                            v-if="form.errors.technologies"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.technologies }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Timeline -->
                <Card>
                    <CardHeader>
                        <CardTitle>Thời gian thực hiện</CardTitle>
                        <CardDescription
                            >Thời gian bắt đầu và kết thúc dự
                            án</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="start_date">Ngày bắt đầu</Label>
                                <Input
                                    id="start_date"
                                    v-model="form.start_date"
                                    type="date"
                                    :class="{
                                        'border-red-500':
                                            form.errors.start_date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.start_date"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.start_date }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="end_date">Ngày kết thúc</Label>
                                <Input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    :disabled="form.is_ongoing"
                                    :class="{
                                        'border-red-500': form.errors.end_date,
                                    }"
                                />
                                <p
                                    v-if="form.errors.end_date"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.end_date }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_ongoing"
                                v-model:checked="form.is_ongoing"
                            />
                            <Label for="is_ongoing" class="cursor-pointer">
                                Dự án đang thực hiện
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <!-- Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle>Cài đặt</CardTitle>
                        <CardDescription
                            >Tùy chọn hiển thị và nổi bật</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_featured"
                                v-model:checked="form.is_featured"
                            />
                            <Label for="is_featured" class="cursor-pointer">
                                Đánh dấu là dự án nổi bật
                            </Label>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_public"
                                v-model:checked="form.is_public"
                            />
                            <Label for="is_public" class="cursor-pointer">
                                Hiển thị công khai
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit('/candidate/portfolios')"
                    >
                        Hủy
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Đang lưu...' : 'Tạo dự án' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
