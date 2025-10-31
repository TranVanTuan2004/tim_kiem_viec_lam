<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';


interface Company {
    id: number;
    company_name: string;
    description: string;
    website: string | null;
    address: string | null;
    city: string | null;
    province: string | null;
    size: string | null;
    industry: string | null;
    logo: string | null; // Đường dẫn đến logo
}

const props = defineProps<{
    company: Company;
    // THÊM: Nếu bạn dùng Flash Messages, bạn cần định nghĩa nó trong props.
    // flash: {
    //     success: string | null;
    //     error: string | null;
    // };
}>();

// ĐÃ XÓA: const { toast } = useToast();

// Dùng Inertia Form để quản lý trạng thái form và file upload
// Quan trọng: Phải dùng useForm() để xử lý file.
const form = useForm({
    _method: 'patch', // Laravel sẽ nhận là PATCH request
    company_name: props.company.company_name,
    description: props.company.description,
    website: props.company.website ?? '',
    address: props.company.address ?? '',
    city: props.company.city ?? '',
    province: props.company.province ?? '',
    size: props.company.size ?? '',
    industry: props.company.industry ?? '',

    // Logo fields
    logo_file: null as File | null, // Trường này sẽ chứa file
    remove_logo: false, // Dùng để gửi tín hiệu xóa logo
});

// Trạng thái hiển thị preview logo mới
const logoPreviewUrl = ref<string | null>(null);

// Xử lý khi chọn file logo mới
const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.logo_file = file;
        // Tạo URL tạm thời để hiển thị preview
        logoPreviewUrl.value = URL.createObjectURL(file);
        form.remove_logo = false; // Bỏ chọn xóa nếu có file mới
    } else {
        form.logo_file = null;
        logoPreviewUrl.value = null;
    }
};

// Xử lý khi nhấn nút xóa logo
const handleRemoveLogo = () => {
    form.logo_file = null;
    logoPreviewUrl.value = null;
    form.remove_logo = true;
    
    // Reset input file để có thể chọn lại file cũ
    const logoInput = document.getElementById('logo_file') as HTMLInputElement;
    if (logoInput) {
        logoInput.value = '';
    }
};

// Gửi form
const submit = () => {
    // Gửi form PATCH tới đúng URL update
    form.post('/employer/settings/company', {
        onSuccess: (page) => {
            const successMessage = page.props.flash?.success ?? 'Thông tin công ty đã được cập nhật.';
            console.log(successMessage);

            // Xóa preview URL để tránh leak bộ nhớ
            if (logoPreviewUrl.value) {
                URL.revokeObjectURL(logoPreviewUrl.value);
                logoPreviewUrl.value = null;
            }

            form.logo_file = null;
            form.remove_logo = false;
        },
        onError: (errors) => {
            if (Object.keys(errors).length > 0) {
                console.error('Lỗi cập nhật! Vui lòng kiểm tra lại các trường thông tin.', errors);
            }
        },
    });
};

// Đảm bảo cleanup URL tạm thời khi component unmount
onMounted(() => {
    // Không cần cleanup ở đây vì nó sẽ được refresh hoàn toàn
});
</script>

<template>
    <Head title="Cài đặt công ty" />

    <AppLayout>
        <div class="bg-gray-50 min-h-screen p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Cài đặt Công ty</h1>
                <p class="text-gray-500 mt-1">Cập nhật thông tin cơ bản và logo công ty của bạn để ứng viên có ấn tượng tốt nhất.</p>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT: Company Profile Card -->
                <Card class="lg:col-span-1 sticky top-20 self-start">
                    <CardHeader class="text-center">
                        <div class="relative w-32 h-32 mx-auto">
                            <img
                                :src="logoPreviewUrl || (
                                    props.company.logo && !form.remove_logo
                                        ? `/storage/${props.company.logo}`
                                        : 'https://placehold.co/200x200?text=No+Logo'
                                )"
                                alt="Company Logo"
                                class="w-full h-full rounded-full object-cover border shadow-sm"
                            />
                            <div
                                class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 hover:opacity-100 transition"
                            >
                                <label
                                    for="logo_file"
                                    class="bg-white text-sm px-3 py-1 rounded cursor-pointer font-medium hover:bg-gray-100"
                                >
                                    Thay logo
                                </label>
                            </div>
                        </div>

                        <input
                            id="logo_file"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleLogoChange"
                        />

                        <div v-if="props.company.logo || logoPreviewUrl" class="mt-3">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-sm text-red-500 hover:text-red-700"
                                @click="handleRemoveLogo"
                                :disabled="form.remove_logo"
                            >
                                {{ form.remove_logo ? 'Đã chọn xóa logo' : 'Xóa logo hiện tại' }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="text-center">
                        <h2 class="text-lg font-semibold text-gray-800">{{ form.company_name }}</h2>
                        <p class="text-gray-500 text-sm">
                            {{ form.industry || 'Chưa cập nhật ngành nghề' }}
                        </p>
                        <p class="text-gray-400 text-sm">
                            {{ form.city ? form.city + ', ' + form.province : 'Chưa có địa chỉ' }}
                        </p>
                    </CardContent>
                </Card>

                <!-- RIGHT: Form Card -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Thông tin chi tiết</CardTitle>
                        <CardDescription>Điền thông tin chính xác giúp hồ sơ công ty chuyên nghiệp và thu hút hơn.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">

                            <!-- Tên công ty -->
                            <div class="space-y-1">
                                <Label for="company_name">Tên Công ty <span class="text-red-500">*</span></Label>
                                <Input
                                    id="company_name"
                                    type="text"
                                    v-model="form.company_name"
                                    placeholder="VD: Công ty TNHH ABC"
                                />
                                <p v-if="form.errors.company_name" class="text-sm text-red-600">{{ form.errors.company_name }}</p>
                            </div>

                            <!-- Mô tả -->
                            <div class="space-y-1">
                                <Label for="description">Giới thiệu / Mô tả</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Giới thiệu về công ty, sản phẩm, văn hóa làm việc..."
                                />
                                <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Website -->
                            <div class="space-y-1">
                                <Label for="website">Website</Label>
                                <Input
                                    id="website"
                                    type="url"
                                    v-model="form.website"
                                    placeholder="https://example.com"
                                />
                                <p v-if="form.errors.website" class="text-sm text-red-600">{{ form.errors.website }}</p>
                            </div>

                            <!-- Address -->
                            <div class="space-y-1">
                                <Label for="address">Địa chỉ</Label>
                                <Input
                                    id="address"
                                    type="text"
                                    v-model="form.address"
                                    placeholder="Số nhà, đường, quận/huyện..."
                                />
                                <p v-if="form.errors.address" class="text-sm text-red-600">{{ form.errors.address }}</p>
                            </div>

                            <!-- Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label for="city">Thành phố</Label>
                                    <Input id="city" v-model="form.city" placeholder="TP.HCM, Hà Nội..." />
                                </div>

                                <div>
                                    <Label for="province">Tỉnh / Khu vực</Label>
                                    <Input id="province" v-model="form.province" placeholder="VD: Bình Dương" />
                                </div>

                                <div>
                                    <Label for="size">Quy mô công ty</Label>
                                    <Input id="size" v-model="form.size" placeholder="VD: 50 - 200 nhân viên" />
                                </div>

                                <div>
                                    <Label for="industry">Lĩnh vực hoạt động</Label>
                                    <Input id="industry" v-model="form.industry" placeholder="VD: Công nghệ thông tin" />
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end pt-6 border-t">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2 text-white bg-blue-600 hover:bg-blue-700"
                                >
                                    {{ form.processing ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>


