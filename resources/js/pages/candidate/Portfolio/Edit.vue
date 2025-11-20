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
import ClientLayout from '@/layouts/ClientLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    portfolio: {
        id: number;
        title: string;
        description: string;
        project_url: string | null;
        github_url: string | null;
        demo_url: string | null;
        images: string[];
        technologies: string[];
        start_date: string | null;
        end_date: string | null;
        is_ongoing: boolean;
        is_featured: boolean;
        is_public: boolean;
    };
}

const props = defineProps<Props>();

const form = useForm({
    title: props.portfolio.title,
    description: props.portfolio.description || '',
    project_url: props.portfolio.project_url || '',
    github_url: props.portfolio.github_url || '',
    demo_url: props.portfolio.demo_url || '',
    images: props.portfolio.images || [],
    new_images: [] as File[],
    technologies: props.portfolio.technologies || [],
    start_date: props.portfolio.start_date || '',
    end_date: props.portfolio.end_date || '',
    is_ongoing: props.portfolio.is_ongoing,
    is_featured: props.portfolio.is_featured,
    is_public: props.portfolio.is_public,
    _method: 'PUT',
});

const newTechnology = ref('');
const newImagePreviewUrls = ref<string[]>([]);

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

        filesArray.forEach((file) => {
            form.new_images.push(file);

            const reader = new FileReader();
            reader.onload = (e) => {
                newImagePreviewUrls.value.push(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeExistingImage = (index: number) => {
    form.images.splice(index, 1);
};

const removeNewImage = (index: number) => {
    form.new_images.splice(index, 1);
    newImagePreviewUrls.value.splice(index, 1);
};

const submit = () => {
    form.post(`/candidate/portfolios/${props.portfolio.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            newImagePreviewUrls.value = [];
        },
    });
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/candidate/dashboard' },
    { title: 'Portfolio', href: '/candidate/portfolios' },
    {
        title: 'Chỉnh sửa',
        href: `/candidate/portfolios/${props.portfolio.id}/edit`,
    },
];
</script>

<template>
    <Head :title="`Chỉnh sửa: ${portfolio.title}`" />

    <ClientLayout>
        <div class="mx-auto flex max-w-4xl flex-col gap-6 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Chỉnh sửa dự án
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Cập nhật thông tin dự án
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
                            >Quản lý hình ảnh showcase dự án</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Existing Images -->
                        <div v-if="form.images.length > 0">
                            <Label class="mb-2 block">Hình ảnh hiện tại</Label>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div
                                    v-for="(url, index) in form.images"
                                    :key="index"
                                    class="relative aspect-square overflow-hidden rounded-lg border"
                                >
                                    <img
                                        :src="url"
                                        alt="Existing"
                                        class="h-full w-full object-cover"
                                    />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="icon"
                                        class="absolute top-1 right-1 h-6 w-6"
                                        @click="removeExistingImage(index)"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload New Images -->
                        <div>
                            <Label class="mb-2 block">Thêm hình ảnh mới</Label>
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
                        </div>

                        <!-- New Image Previews -->
                        <div v-if="newImagePreviewUrls.length > 0">
                            <Label class="mb-2 block">Ảnh mới sẽ thêm</Label>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div
                                    v-for="(url, index) in newImagePreviewUrls"
                                    :key="index"
                                    class="relative aspect-square overflow-hidden rounded-lg border border-green-500"
                                >
                                    <img
                                        :src="url"
                                        alt="New Preview"
                                        class="h-full w-full object-cover"
                                    />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="icon"
                                        class="absolute top-1 right-1 h-6 w-6"
                                        @click="removeNewImage(index)"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                    <Badge
                                        class="absolute bottom-1 left-1 bg-green-500"
                                    >
                                        Mới
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="form.errors.images"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.images }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Technologies -->
                <Card>
                    <CardHeader>
                        <CardTitle>Công nghệ sử dụng</CardTitle>
                        <CardDescription
                            >Các công nghệ và kỹ năng sử dụng trong dự
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
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="end_date">Ngày kết thúc</Label>
                                <Input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    :disabled="form.is_ongoing"
                                />
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
                        {{
                            form.processing
                                ? 'Đang cập nhật...'
                                : 'Cập nhật dự án'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </ClientLayout>
</template>
