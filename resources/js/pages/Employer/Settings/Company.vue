<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
import { 
    Globe, 
    MapPin, 
    Building, 
    Users, 
    Briefcase, 
    Camera, 
    Trash2,
    Building2,
    Map
} from 'lucide-vue-next';

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
    logo: string | null;
}

const props = defineProps<{
    company: Company;
}>();

const form = useForm({
    _method: 'patch',
    company_name: props.company.company_name,
    description: props.company.description,
    website: props.company.website ?? '',
    address: props.company.address ?? '',
    city: props.company.city ?? '',
    province: props.company.province ?? '',
    size: props.company.size ?? '',
    industry: props.company.industry ?? '',
    logo_file: null as File | null,
    remove_logo: false,
});

const logoPreviewUrl = ref<string | null>(null);

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.logo_file = file;
        logoPreviewUrl.value = URL.createObjectURL(file);
        form.remove_logo = false;
    } else {
        form.logo_file = null;
        logoPreviewUrl.value = null;
    }
};

const handleRemoveLogo = () => {
    form.logo_file = null;
    logoPreviewUrl.value = null;
    form.remove_logo = true;
    
    const logoInput = document.getElementById('logo_file') as HTMLInputElement;
    if (logoInput) {
        logoInput.value = '';
    }
};

const submit = () => {
    form.post('/employer/settings/company', {
        onSuccess: (page) => {
            if (logoPreviewUrl.value) {
                URL.revokeObjectURL(logoPreviewUrl.value);
                logoPreviewUrl.value = null;
            }
            form.logo_file = null;
            form.remove_logo = false;
        },
        onError: (errors) => {
            if (Object.keys(errors).length > 0) {
                console.error('Lỗi cập nhật!', errors);
            }
        },
    });
};

onMounted(() => {
    // Cleanup handled by browser/Inertia refresh
});
</script>

<template>
    <Head title="Hồ sơ công ty" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 p-6">
            <!-- Header -->
            <div class="max-w-6xl mx-auto mb-8">
                <div class="flex items-center gap-4 mb-2">
                    <div class="p-3 bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
                        <Building2 class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Hồ sơ công ty</h1>
                        <p class="text-gray-600">Quản lý thông tin thương hiệu và hình ảnh công ty của bạn</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- LEFT: Logo & Quick Info -->
                <div class="lg:col-span-4 space-y-6">
                    <Card class="border-0 shadow-lg ring-1 ring-black/5 overflow-hidden">
                        <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-600 relative">
                            <div class="absolute inset-0 bg-grid-white/10 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))]"></div>
                        </div>
                        <CardContent class="relative pt-0 text-center px-6 pb-8">
                            <div class="relative -mt-16 mb-6 inline-block">
                                <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl bg-white overflow-hidden relative group">
                                    <img
                                        :src="logoPreviewUrl || (
                                            props.company.logo && !form.remove_logo
                                                ? `/storage/${props.company.logo}`
                                                : 'https://placehold.co/200x200?text=Logo'
                                        )"
                                        alt="Company Logo"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    />
                                    
                                    <!-- Overlay Upload -->
                                    <label
                                        for="logo_file"
                                        class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer backdrop-blur-sm"
                                    >
                                        <Camera class="w-8 h-8 text-white mb-1" />
                                        <span class="text-xs font-medium text-white">Thay đổi logo</span>
                                    </label>
                                </div>
                                
                                <!-- Remove Button -->
                                <button
                                    v-if="props.company.logo || logoPreviewUrl"
                                    @click="handleRemoveLogo"
                                    :disabled="form.remove_logo"
                                    class="absolute -bottom-2 -right-2 p-2 bg-white rounded-full shadow-md border border-gray-100 text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Xóa logo"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <input
                                id="logo_file"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleLogoChange"
                            />

                            <h2 class="text-xl font-bold text-gray-900 mb-1">{{ form.company_name || 'Tên công ty' }}</h2>
                            <p class="text-sm text-gray-500 flex items-center justify-center gap-1.5 mb-4">
                                <Briefcase class="w-3.5 h-3.5" />
                                {{ form.industry || 'Chưa cập nhật ngành nghề' }}
                            </p>

                            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Quy mô</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ form.size || '--' }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Khu vực</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ form.city || '--' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Tips Card -->
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 bg-blue-200 rounded-full text-xs">💡</span>
                            Mẹo hồ sơ
                        </h3>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 w-1 h-1 bg-blue-400 rounded-full"></span>
                                Logo rõ nét giúp tăng 30% lượt xem tin tuyển dụng.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 w-1 h-1 bg-blue-400 rounded-full"></span>
                                Mô tả chi tiết về văn hóa công ty thu hút ứng viên phù hợp hơn.
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- RIGHT: Edit Form -->
                <div class="lg:col-span-8">
                    <Card class="border-0 shadow-lg ring-1 ring-black/5">
                        <CardHeader class="border-b border-gray-100 pb-6">
                            <CardTitle class="text-xl">Thông tin chi tiết</CardTitle>
                            <CardDescription>Cập nhật thông tin đầy đủ để tăng độ uy tín cho doanh nghiệp</CardDescription>
                        </CardHeader>
                        <CardContent class="p-6">
                            <form @submit.prevent="submit" class="space-y-8">
                                <!-- Basic Info Section -->
                                <div class="space-y-6">
                                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                        <Building class="w-4 h-4 text-blue-600" />
                                        Thông tin cơ bản
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 gap-6">
                                        <div class="space-y-2">
                                            <Label for="company_name">Tên Công ty <span class="text-red-500">*</span></Label>
                                            <div class="relative">
                                                <Building2 class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="company_name"
                                                    v-model="form.company_name"
                                                    class="pl-10"
                                                    placeholder="Nhập tên công ty đầy đủ"
                                                />
                                            </div>
                                            <p v-if="form.errors.company_name" class="text-sm text-red-600">{{ form.errors.company_name }}</p>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="description">Giới thiệu / Mô tả</Label>
                                            <Textarea
                                                id="description"
                                                v-model="form.description"
                                                :rows="5"
                                                class="resize-none"
                                                placeholder="Giới thiệu về lịch sử, sứ mệnh, tầm nhìn và văn hóa công ty..."
                                            />
                                            <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 pt-8 space-y-6">
                                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                        <MapPin class="w-4 h-4 text-blue-600" />
                                        Liên hệ & Địa điểm
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <Label for="website">Website</Label>
                                            <div class="relative">
                                                <Globe class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="website"
                                                    v-model="form.website"
                                                    class="pl-10"
                                                    placeholder="https://example.com"
                                                />
                                            </div>
                                            <p v-if="form.errors.website" class="text-sm text-red-600">{{ form.errors.website }}</p>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="size">Quy mô nhân sự</Label>
                                            <div class="relative">
                                                <Users class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="size"
                                                    v-model="form.size"
                                                    class="pl-10"
                                                    placeholder="VD: 50-100 nhân viên"
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-2 md:col-span-2">
                                            <Label for="address">Địa chỉ trụ sở</Label>
                                            <div class="relative">
                                                <MapPin class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="address"
                                                    v-model="form.address"
                                                    class="pl-10"
                                                    placeholder="Số nhà, tên đường, phường/xã..."
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="city">Thành phố</Label>
                                            <div class="relative">
                                                <Building class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="city"
                                                    v-model="form.city"
                                                    class="pl-10"
                                                    placeholder="VD: TP. Hồ Chí Minh"
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="province">Tỉnh / Khu vực</Label>
                                            <div class="relative">
                                                <Map class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="province"
                                                    v-model="form.province"
                                                    class="pl-10"
                                                    placeholder="VD: Quận 1"
                                                />
                                            </div>
                                        </div>

                                        <div class="space-y-2 md:col-span-2">
                                            <Label for="industry">Lĩnh vực hoạt động</Label>
                                            <div class="relative">
                                                <Briefcase class="absolute left-3 top-3 w-4 h-4 text-gray-400" />
                                                <Input
                                                    id="industry"
                                                    v-model="form.industry"
                                                    class="pl-10"
                                                    placeholder="VD: Công nghệ phần mềm, Bất động sản..."
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="form.reset()"
                                        :disabled="form.processing"
                                    >
                                        Hủy bỏ
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md hover:shadow-lg transition-all px-8"
                                    >
                                        {{ form.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


