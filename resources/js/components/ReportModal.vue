<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { AlertCircle } from 'lucide-vue-next';

interface Props {
    open: boolean;
    reportableType: 'App\\Models\\JobPosting' | 'App\\Models\\Company';
    reportableId: number;
    reportableTitle: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const form = ref({
    type: 'other',
    reason: '',
});

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});

const types = [
    { value: 'spam', label: 'Spam' },
    { value: 'fraud', label: 'Lừa đảo' },
    { value: 'inappropriate', label: 'Nội dung không phù hợp' },
    { value: 'fake', label: 'Giả mạo' },
    { value: 'other', label: 'Khác' },
];

const reportTypeLabel = computed(() => {
    return props.reportableType === 'App\\Models\\JobPosting'
        ? 'tin tuyển dụng'
        : 'công ty';
});

const closeModal = () => {
    emit('update:open', false);
    // Reset form
    form.value = {
        type: 'other',
        reason: '',
    };
    errors.value = {};
};

const submitReport = () => {
    // Clear previous errors
    errors.value = {};

    // Validate
    if (!form.value.reason || form.value.reason.trim().length < 10) {
        errors.value.reason = 'Vui lòng mô tả chi tiết lý do báo cáo (tối thiểu 10 ký tự)';
        return;
    }

    if (form.value.reason.length > 1000) {
        errors.value.reason = 'Mô tả không được vượt quá 1000 ký tự';
        return;
    }

    isSubmitting.value = true;

    router.post(
        '/candidate/reports',
        {
            reportable_type: props.reportableType,
            reportable_id: props.reportableId,
            type: form.value.type,
            reason: form.value.reason,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (err) => {
                errors.value = err;
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        }
    );
};
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="sm:max-w-[525px]">
            <DialogHeader>
                <DialogTitle>Báo cáo vi phạm</DialogTitle>
                <DialogDescription>
                    Bạn đang báo cáo {{ reportTypeLabel }}:
                    <span class="font-semibold text-gray-900">{{ reportableTitle }}</span>
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <!-- Type Select -->
                <div class="grid gap-2">
                    <Label for="type">Loại vi phạm</Label>
                    <select
                        id="type"
                        v-model="form.type"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        <option
                            v-for="type in types"
                            :key="type.value"
                            :value="type.value"
                        >
                            {{ type.label }}
                        </option>
                    </select>
                </div>

                <!-- Reason Textarea -->
                <div class="grid gap-2">
                    <Label for="reason">
                        Mô tả chi tiết
                        <span class="text-red-500">*</span>
                    </Label>
                    <Textarea
                        id="reason"
                        v-model="form.reason"
                        placeholder="Vui lòng mô tả chi tiết vấn đề bạn gặp phải..."
                        rows="5"
                        :class="{ 'border-red-500': errors.reason }"
                    />
                    <p class="text-sm text-gray-500">
                        {{ form.reason.length }}/1000 ký tự
                    </p>
                    <p v-if="errors.reason" class="text-sm text-red-500 flex items-center gap-1">
                        <AlertCircle class="h-4 w-4" />
                        {{ errors.reason }}
                    </p>
                </div>

                <!-- Info Alert -->
                <div class="rounded-md bg-blue-50 p-3">
                    <p class="text-sm text-blue-800">
                        <strong>Lưu ý:</strong> Báo cáo của bạn sẽ được quản trị viên xem xét.
                        Vui lòng cung cấp thông tin chính xác để chúng tôi có thể xử lý kịp thời.
                    </p>
                </div>
            </div>

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    @click="closeModal"
                    :disabled="isSubmitting"
                >
                    Hủy
                </Button>
                <Button
                    type="button"
                    @click="submitReport"
                    :disabled="isSubmitting"
                >
                    {{ isSubmitting ? 'Đang gửi...' : 'Gửi báo cáo' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
