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
    // Controller của bạn đã được cấu hình để nhận logo_file (POST/multipart) và xử lý PATCH
    form.post(route('employer.company.update'), {
        onSuccess: (page) => {
            // ĐÃ SỬA: Thay thế logic toast bằng alert/console log
            const successMessage = page.props.flash.success as string ?? 'Thông tin công ty đã được cập nhật.';
            console.log(successMessage); // Log ra console
            
            // Nếu bạn muốn hiển thị thông báo, bạn nên dùng một modal tùy chỉnh
            // hoặc dùng alert() nếu bạn chấp nhận hạn chế của nó (NHƯNG KHÔNG NÊN DÙNG ALERT TRONG IFRAME)
            // Thay vào đó, chúng ta sẽ reload page để thấy dữ liệu mới (thường là cách Inertia xử lý)
            
            // Xóa URL tạm thời sau khi upload xong
            if (logoPreviewUrl.value) {
                URL.revokeObjectURL(logoPreviewUrl.value);
                logoPreviewUrl.value = null;
            }
            
            // Đặt lại trạng thái logo
            form.logo_file = null;
            form.remove_logo = false;
        },
        onError: (errors) => {
            // ĐÃ SỬA: Thay thế logic toast bằng console log
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
        <div class="space-y-6 p-6">
            <h1 class="text-3xl font-bold">Cài đặt Công ty</h1>
            <p class="text-gray-500">
                Cập nhật thông tin cơ bản và logo công ty của bạn.
            </p>

            <Card>
                <CardHeader>
                    <CardTitle>Thông tin Công ty</CardTitle>
                    <CardDescription>
                        Các thông tin này sẽ hiển thị công khai trên trang tuyển dụng.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Logo Section -->
                        <div class="space-y-2">
                            <Label for="logo_file">Logo Công ty</Label>
                            <div class="flex items-center space-x-4">
                                <!-- Hiển thị Logo hiện tại hoặc Preview mới -->
                                <img
                                    :src="
                                        logoPreviewUrl || (
                                            props.company.logo && !form.remove_logo
                                                ? `/storage/${props.company.logo}`
                                                : 'https://placehold.co/100x100/A0A0A0/FFFFFF?text=No+Logo'
                                        )
                                    "
                                    alt="Company Logo"
                                    class="h-24 w-24 rounded-full object-cover shadow-lg border border-gray-200"
                                />

                                <div class="flex flex-col space-y-2 flex-grow">
                                    <Input
                                        id="logo_file"
                                        type="file"
                                        @change="handleLogoChange"
                                        accept="image/*"
                                        :class="{
                                            'border-red-500':
                                                form.errors.logo_file,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.logo_file"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.logo_file }}
                                    </p>
                                    
                                    <!-- Tùy chọn xóa logo -->
                                    <div v-if="props.company.logo || logoPreviewUrl">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            class="text-sm text-red-500 hover:text-red-700"
                                            @click="handleRemoveLogo"
                                            :disabled="form.remove_logo"
                                        >
                                            {{ form.remove_logo ? 'Đã chọn xóa' : 'Xóa logo hiện tại' }}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 pt-1" v-if="props.company.logo">
                                <Checkbox
                                    id="remove_logo_checkbox"
                                    :checked="form.remove_logo"
                                    @update:checked="form.remove_logo = $event; form.logo_file = null; logoPreviewUrl = null;"
                                />
                                <Label for="remove_logo_checkbox">Xóa logo hiện tại (Nếu không upload file mới)</Label>
                            </div>
                        </div>

                        <!-- Company Name -->
                        <div class="space-y-2">
                            <Label for="company_name" required
                                >Tên Công ty</Label
                            >
                            <Input
                                id="company_name"
                                type="text"
                                v-model="form.company_name"
                                :class="{
                                    'border-red-500': form.errors.company_name,
                                }"
                            />
                            <p
                                v-if="form.errors.company_name"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.company_name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Mô tả Công ty</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Website -->
                        <div class="space-y-2">
                            <Label for="website">Website</Label>
                            <Input
                                id="website"
                                type="url"
                                v-model="form.website"
                                :class="{
                                    'border-red-500': form.errors.website,
                                }"
                            />
                            <p
                                v-if="form.errors.website"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.website }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="space-y-2">
                            <Label for="address">Địa chỉ chi tiết</Label>
                            <Input
                                id="address"
                                type="text"
                                v-model="form.address"
                                :class="{
                                    'border-red-500': form.errors.address,
                                }"
                            />
                            <p
                                v-if="form.errors.address"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.address }}
                            </p>
                        </div>
                        
                        <!-- City, Province, Size, Industry (Grid Layout) -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <!-- City -->
                            <div class="space-y-2">
                                <Label for="city">Thành phố</Label>
                                <Input
                                    id="city"
                                    type="text"
                                    v-model="form.city"
                                    :class="{
                                        'border-red-500': form.errors.city,
                                    }"
                                />
                                <p v-if="form.errors.city" class="text-sm text-red-600">
                                    {{ form.errors.city }}
                                </p>
                            </div>
                            
                            <!-- Province -->
                            <div class="space-y-2">
                                <Label for="province">Tỉnh/Khu vực</Label>
                                <Input
                                    id="province"
                                    type="text"
                                    v-model="form.province"
                                    :class="{
                                        'border-red-500': form.errors.province,
                                    }"
                                />
                                <p v-if="form.errors.province" class="text-sm text-red-600">
                                    {{ form.errors.province }}
                                </p>
                            </div>

                            <!-- Size -->
                            <div class="space-y-2">
                                <Label for="size">Quy mô (Số nhân viên)</Label>
                                <Input
                                    id="size"
                                    type="text"
                                    v-model="form.size"
                                    placeholder="Ví dụ: 100-500"
                                    :class="{
                                        'border-red-500': form.errors.size,
                                    }"
                                />
                                <p v-if="form.errors.size" class="text-sm text-red-600">
                                    {{ form.errors.size }}
                                </p>
                            </div>

                            <!-- Industry -->
                            <div class="space-y-2">
                                <Label for="industry">Lĩnh vực</Label>
                                <Input
                                    id="industry"
                                    type="text"
                                    v-model="form.industry"
                                    placeholder="Ví dụ: Công nghệ thông tin"
                                    :class="{
                                        'border-red-500': form.errors.industry,
                                    }"
                                />
                                <p v-if="form.errors.industry" class="text-sm text-red-600">
                                    {{ form.errors.industry }}
                                </p>
                            </div>
                        </div>


                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="mt-4"
                            >
                                {{ form.processing ? 'Đang cập nhật...' : 'Lưu Thay Đổi' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
