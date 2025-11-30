<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    FileText,
    ArrowLeft,
    Save,
    Upload,
    X,
    Image as ImageIcon,
} from 'lucide-vue-next';

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: '',
    featured_image_file: null as File | null,
    status: 'draft' as 'draft' | 'published',
    published_at: '',
    is_featured: false,
    meta_title: '',
    meta_description: '',
});

const imagePreview = ref<string | null>(null);

function handleImageChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        form.featured_image_file = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    form.featured_image_file = null;
    form.featured_image = '';
    imagePreview.value = null;
}

function submit() {
    form.post('/admin/blogs', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboard' },
    { title: 'Quản lý Blog', href: '/admin/blogs' },
    { title: 'Tạo bài viết mới', href: '/admin/blogs/create' },
];
</script>

<template>
    <Head title="Tạo bài viết mới" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 m-[20px]">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight flex items-center gap-3"
                    >
                        <div
                            class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center"
                        >
                            <FileText class="h-6 w-6 text-primary" />
                        </div>
                        Tạo bài viết mới
                    </h1>
                    <p class="text-muted-foreground mt-2 ml-[52px]">
                        Thêm bài viết blog mới
                    </p>
                </div>
                <Button
                    variant="outline"
                    @click="router.visit('/admin/blogs')"
                >
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Quay lại
                </Button>
            </div>

            <form @submit.prevent="submit">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Main Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Nội dung bài viết</CardTitle>
                                <CardDescription>
                                    Nhập thông tin chi tiết cho bài viết
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="title">
                                        Tiêu đề <span class="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        placeholder="Nhập tiêu đề bài viết"
                                        :class="{
                                            'border-red-500': form.errors.title,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.title"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.title }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="slug">Slug (URL)</Label>
                                    <Input
                                        id="slug"
                                        v-model="form.slug"
                                        placeholder="Tự động tạo từ tiêu đề nếu để trống"
                                        :class="{
                                            'border-red-500': form.errors.slug,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.slug"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.slug }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        URL thân thiện cho bài viết (ví dụ:
                                        bai-viet-moi)
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="excerpt">Tóm tắt</Label>
                                    <Textarea
                                        id="excerpt"
                                        v-model="form.excerpt"
                                        :rows="3"
                                        placeholder="Nhập tóm tắt ngắn gọn về bài viết..."
                                        :class="{
                                            'border-red-500':
                                                form.errors.excerpt,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.excerpt"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.excerpt }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="content">
                                        Nội dung <span class="text-red-500">*</span>
                                    </Label>
                                    <Textarea
                                        id="content"
                                        v-model="form.content"
                                        :rows="15"
                                        placeholder="Nhập nội dung bài viết..."
                                        :class="{
                                            'border-red-500':
                                                form.errors.content,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.content"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.content }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Hỗ trợ HTML và markdown
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="featured_image_file">
                                        Ảnh đại diện
                                    </Label>
                                    
                                    <!-- Image Preview -->
                                    <div
                                        v-if="imagePreview || form.featured_image"
                                        class="mb-4 relative"
                                    >
                                        <img
                                            :src="imagePreview || form.featured_image"
                                            alt="Preview"
                                            class="w-full h-64 object-cover rounded-lg border"
                                        />
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute top-2 right-2"
                                            @click="removeImage"
                                        >
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>

                                    <!-- File Upload -->
                                    <div
                                        v-if="!imagePreview && !form.featured_image"
                                        class="border-2 border-dashed rounded-lg p-6 text-center hover:border-primary transition-colors"
                                    >
                                        <label
                                            for="featured_image_file"
                                            class="cursor-pointer flex flex-col items-center gap-2"
                                        >
                                            <div class="p-3 rounded-full bg-primary/10">
                                                <Upload class="h-6 w-6 text-primary" />
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-primary">
                                                    Click để tải ảnh lên
                                                </span>
                                                <p class="text-xs text-muted-foreground mt-1">
                                                    PNG, JPG, GIF, WEBP (tối đa 5MB)
                                                </p>
                                            </div>
                                        </label>
                                        <input
                                            id="featured_image_file"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleImageChange"
                                        />
                                    </div>

                                    <!-- URL Input (Alternative) -->
                                    <div
                                        v-if="!imagePreview && !form.featured_image"
                                        class="mt-4"
                                    >
                                        <Label for="featured_image" class="text-sm text-muted-foreground">
                                            Hoặc nhập URL ảnh
                                        </Label>
                                        <Input
                                            id="featured_image"
                                            v-model="form.featured_image"
                                            placeholder="https://example.com/image.jpg"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.featured_image,
                                            }"
                                        />
                                    </div>

                                    <p
                                        v-if="form.errors.featured_image || form.errors.featured_image_file"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.featured_image || form.errors.featured_image_file }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- SEO Settings -->
                        <Card>
                            <CardHeader>
                                <CardTitle>SEO Settings</CardTitle>
                                <CardDescription>
                                    Tối ưu hóa cho công cụ tìm kiếm
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="meta_title">Meta Title</Label>
                                    <Input
                                        id="meta_title"
                                        v-model="form.meta_title"
                                        placeholder="Tiêu đề SEO (tối đa 60 ký tự)"
                                        :class="{
                                            'border-red-500':
                                                form.errors.meta_title,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.meta_title"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.meta_title }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="meta_description"
                                        >Meta Description</Label
                                    >
                                    <Textarea
                                        id="meta_description"
                                        v-model="form.meta_description"
                                        :rows="3"
                                        placeholder="Mô tả SEO (tối đa 160 ký tự)"
                                        :class="{
                                            'border-red-500':
                                                form.errors.meta_description,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.meta_description"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.meta_description }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Xuất bản</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="status">
                                        Trạng thái <span class="text-red-500">*</span>
                                    </Label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        :class="[
                                            'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
                                            {
                                                'border-red-500':
                                                    form.errors.status,
                                            },
                                        ]"
                                    >
                                        <option value="draft">Bản nháp</option>
                                        <option value="published">Đã xuất bản</option>
                                    </select>
                                    <p
                                        v-if="form.errors.status"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <div
                                    v-if="form.status === 'published'"
                                    class="space-y-2"
                                >
                                    <Label for="published_at"
                                        >Ngày xuất bản</Label
                                    >
                                    <Input
                                        id="published_at"
                                        v-model="form.published_at"
                                        type="datetime-local"
                                        :class="{
                                            'border-red-500':
                                                form.errors.published_at,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.published_at"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.published_at }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Để trống để xuất bản ngay. Nếu chọn thời gian trong tương lai, blog sẽ tự động hiển thị khi đến thời gian đó.
                                    </p>
                                    <div
                                        v-if="form.published_at && new Date(form.published_at) > new Date()"
                                        class="text-xs text-yellow-600 bg-yellow-50 border border-yellow-200 rounded p-2"
                                    >
                                        ⚠️ Blog sẽ được xuất bản vào thời gian đã chọn, không hiển thị ngay.
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input
                                        id="is_featured"
                                        v-model="form.is_featured"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                    <Label for="is_featured" class="cursor-pointer"
                                        >Đánh dấu nổi bật</Label
                                    >
                                </div>
                            </CardContent>
                        </Card>

                        <div class="flex gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="h-4 w-4 mr-2" />
                                {{ form.processing ? 'Đang lưu...' : 'Lưu bài viết' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

