<script setup lang="ts">
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    FileText,
    Upload,
    Trash2,
    CheckCircle,
    MoreVertical,
    Download,
    Plus,
    Calendar
} from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';

interface CV {
    id: number;
    name: string;
    file_path: string;
    file_url: string;
    is_default: boolean;
    created_at: string;
}

const props = defineProps<{
    cvs: CV[];
}>();

const isUploadOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    name: '',
    file: null as File | null,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file = target.files[0];
        if (!form.name) {
            form.name = target.files[0].name.split('.')[0];
        }
    }
};

const submitUpload = () => {
    form.post(route('candidate.cvs.store'), {
        onSuccess: () => {
            isUploadOpen.value = false;
            form.reset();
        },
    });
};

const deleteForm = useForm({});
const deleteCV = (id: number) => {
    if (confirm('Bạn có chắc chắn muốn xóa CV này không?')) {
        deleteForm.delete(route('candidate.cvs.destroy', id));
    }
};

const defaultForm = useForm({});
const setDefault = (id: number) => {
    defaultForm.post(route('candidate.cvs.default', id));
};
</script>

<template>
    <CandidateLayout>
        <Head title="Quản lý CV" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-xl">
                    <div class="relative px-8 py-10 sm:px-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-3xl font-bold text-white sm:text-4xl">
                                Quản lý CV
                            </h1>
                            <p class="mt-3 text-lg text-blue-100">
                                Tải lên và quản lý các phiên bản CV của bạn
                            </p>
                        </div>
                        <Dialog v-model:open="isUploadOpen">
                            <DialogTrigger as-child>
                                <Button class="bg-white text-blue-600 hover:bg-blue-50 border-0 shadow-lg font-semibold">
                                    <Plus class="w-4 h-4 mr-2" />
                                    Tải lên CV mới
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Tải lên CV mới</DialogTitle>
                                    <DialogDescription>
                                        Hỗ trợ định dạng PDF, DOC, DOCX. Tối đa 5MB.
                                    </DialogDescription>
                                </DialogHeader>
                                <form @submit.prevent="submitUpload" class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="name">Tên gợi nhớ</Label>
                                        <Input 
                                            id="name" 
                                            type="text" 
                                            v-model="form.name" 
                                            placeholder="Ví dụ: CV Fullstack Developer"
                                        />
                                        <p v-if="form.errors.name" class="text-sm text-red-600">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="file">File CV</Label>
                                        <div
                                            class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition-colors"
                                            @click="fileInput?.click()"
                                        >
                                            <div v-if="form.file" class="flex items-center justify-center gap-2 text-blue-600">
                                                <FileText class="w-6 h-6" />
                                                <span class="font-medium">{{ form.file.name }}</span>
                                            </div>
                                            <div v-else class="text-gray-500">
                                                <Upload class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                                <p class="text-sm font-medium">Nhấp để chọn file</p>
                                                <p class="text-xs mt-1">PDF, DOC, DOCX (Max 5MB)</p>
                                            </div>
                                        </div>
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            id="file"
                                            class="hidden"
                                            accept=".pdf,.doc,.docx"
                                            @change="handleFileChange"
                                        />
                                        <p v-if="form.errors.file" class="text-sm text-red-600">
                                            {{ form.errors.file }}
                                        </p>
                                    </div>
                                    <DialogFooter>
                                        <Button type="button" variant="outline" @click="isUploadOpen = false">
                                            Hủy
                                        </Button>
                                        <Button type="submit" :disabled="form.processing">
                                            {{ form.processing ? 'Đang tải lên...' : 'Tải lên' }}
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Card class="border-blue-200 bg-blue-50 transition-shadow hover:shadow-md">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Tổng số CV</p>
                                    <p class="text-3xl font-bold text-blue-700 mt-2">{{ cvs.length }}</p>
                                </div>
                                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <FileText class="h-6 w-6 text-blue-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="border-green-200 bg-green-50 transition-shadow hover:shadow-md">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600">CV Mặc định</p>
                                    <p class="text-lg font-bold text-green-700 mt-2 truncate max-w-[150px]">
                                        {{ cvs.find(c => c.is_default)?.name || 'Chưa có' }}
                                    </p>
                                </div>
                                <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center">
                                    <CheckCircle class="h-6 w-6 text-green-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="border-purple-200 bg-purple-50 transition-shadow hover:shadow-md">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600">Lần cập nhật cuối</p>
                                    <p class="text-lg font-bold text-purple-700 mt-2">
                                        {{ cvs.length > 0 ? cvs[0].created_at : 'N/A' }}
                                    </p>
                                </div>
                                <div class="h-12 w-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                    <Calendar class="h-6 w-6 text-purple-600" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Empty State -->
                <Card v-if="cvs.length === 0" class="text-center py-16 border-dashed border-2">
                    <CardContent>
                        <div class="mx-auto w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <FileText class="w-10 h-10 text-blue-500" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Chưa có CV nào</h3>
                        <p class="mt-2 text-gray-500 max-w-sm mx-auto">
                            Tải lên CV của bạn để sẵn sàng ứng tuyển vào các vị trí việc làm hấp dẫn.
                        </p>
                        <Button class="mt-8" @click="isUploadOpen = true" size="lg">
                            <Plus class="w-5 h-5 mr-2" />
                            Tải lên CV ngay
                        </Button>
                    </CardContent>
                </Card>

                <!-- CV List -->
                <div v-else>
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <FileText class="w-5 h-5 text-blue-600" />
                        Danh sách CV của bạn
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card v-for="cv in cvs" :key="cv.id" class="group hover:shadow-lg transition-all duration-300 border-gray-200 hover:border-blue-200">
                            <CardContent class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                                        <FileText class="w-6 h-6" />
                                    </div>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 text-gray-400 hover:text-gray-600">
                                                <MoreVertical class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-48">
                                            <DropdownMenuItem as-child>
                                                <a :href="cv.file_url" target="_blank" class="flex items-center cursor-pointer w-full">
                                                    <Download class="w-4 h-4 mr-2" />
                                                    Tải xuống
                                                </a>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="setDefault(cv.id)" v-if="!cv.is_default" class="cursor-pointer">
                                                <CheckCircle class="w-4 h-4 mr-2" />
                                                Đặt làm mặc định
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="deleteCV(cv.id)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                                <Trash2 class="w-4 h-4 mr-2" />
                                                Xóa
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>

                                <div>
                                    <h3 class="font-bold text-gray-900 truncate text-lg mb-1" :title="cv.name">
                                        {{ cv.name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                        <Calendar class="w-4 h-4" />
                                        {{ cv.created_at }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <Badge v-if="cv.is_default" class="bg-green-100 text-green-700 hover:bg-green-100 border-green-200 px-3 py-1">
                                        <CheckCircle class="w-3 h-3 mr-1" />
                                        Mặc định
                                    </Badge>
                                    <span v-else></span>
                                    
                                    <a 
                                        :href="cv.file_url" 
                                        target="_blank"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1"
                                    >
                                        Xem trước
                                    </a>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </CandidateLayout>
</template>
