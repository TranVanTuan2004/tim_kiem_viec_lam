<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface Props {
  open: boolean;
  candidateName?: string;
  applicationId: number;
  submitUrl: string;
}

const closeModal = () => {
  localOpen.value = false;
  form.value = { type: 'other', reason: '', errors: {}, isSubmitting: false };
  successMessage.value = '';
  generalError.value = '';
};

const successMessage = ref('');
const generalError = ref('');
const errors = ref<Record<string, string>>({});

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'report-success', message: string): void; // thêm dòng này
}>();


// ✅ Local state để bind với Dialog
const localOpen = ref(props.open);

// Sync khi prop thay đổi từ cha
watch(() => props.open, val => localOpen.value = val);

// Emit khi localOpen thay đổi
watch(localOpen, val => emit('update:open', val));

const form = ref({
  type: 'other',
  reason: '',
  errors: {} as Record<string, string>,
  isSubmitting: false
});

const types = [
  { value: 'spam', label: 'Spam' },
  { value: 'fraud', label: 'Lừa đảo' },
  { value: 'inappropriate', label: 'Nội dung không phù hợp' },
  { value: 'fake', label: 'Giả mạo' },
  { value: 'other', label: 'Khác' },
];

const submitReport = () => {
  form.value.errors = {};
  if (!form.value.reason || form.value.reason.trim().length < 10) {
    form.value.errors.reason = 'Vui lòng mô tả chi tiết lý do báo cáo (tối thiểu 10 ký tự)';
    return;
  }

  if (form.value.reason.length > 1000) {
    errors.value.reason = 'Mô tả không được vượt quá 1000 ký tự';
    return;
  }

  form.value.isSubmitting = true;

  router.post(props.submitUrl, {
    candidate_id: props.applicationId,
    type: form.value.type,
    reason: form.value.reason,
  }, {
    preserveScroll: true,
    onSuccess: () => {
        emit('report-success', 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
        setTimeout(() => {
          emit('report-success', '');
        }, 3000);
        closeModal();
    },
    onError: (err) => {
        if (err && typeof err === "object" && "errors" in err && typeof err.errors === "object") {
            const errorsObj = err.errors as Record<string, string>; // ép kiểu tạm
            const filteredErrors: Record<string, string> = {};

            for (const key in errorsObj) {
                filteredErrors[key] = errorsObj[key];
            }

            form.value.errors = filteredErrors;
        } else {
            form.value.errors = { general: "Đã xảy ra lỗi. Vui lòng thử lại." };
        }
    },
    onFinish: () => form.value.isSubmitting = false,
  });
};
</script>

<template>
  <!-- bind với localOpen thay vì prop -->
  <Dialog v-model:open="localOpen">
    <DialogContent class="max-w-lg p-6">
      <DialogHeader>
        <DialogTitle>Báo cáo vi phạm ứng viên</DialogTitle>
        <DialogDescription>
          Báo cáo ứng viên: <strong>{{ candidateName }}</strong>
        </DialogDescription>
      </DialogHeader>

      <div v-if="successMessage" class="rounded-md bg-green-50 p-3 mb-4">
            <p class="text-sm text-green-800">{{ successMessage }}</p>
        </div>

        <div v-if="generalError" class="rounded-md bg-red-50 p-3 mb-4">
            <p class="text-sm text-red-800">{{ generalError }}</p>
        </div>

      <div class="mt-4 space-y-4">
        <div>
          <Label>Loại báo cáo</Label>
          <select v-model="form.type" class="w-full mt-1 p-2 border rounded-lg">
            <option v-for="type in types" :key="type.value" :value="type.value">{{ type.label }}</option>
          </select>
        </div>

        <div>
          <Label>Nội dung báo cáo</Label>
          <Textarea v-model="form.reason" :rows="6" placeholder="Mô tả chi tiết lý do báo cáo (tối thiểu 10 ký tự)" class="w-full mt-1 border rounded-lg" />
          <p v-if="form.errors.reason" class="text-red-600 text-sm mt-1">{{ form.errors.reason }}</p>
        </div>
      </div>
        <div class="rounded-md bg-blue-50 p-3">
            <p class="text-sm text-blue-800">
                <strong>Lưu ý:</strong> Báo cáo của bạn sẽ được quản trị viên xem xét.
                Vui lòng cung cấp thông tin chính xác để chúng tôi có thể xử lý kịp thời.
            </p>
        </div>
      <DialogFooter class="mt-6 flex justify-end gap-3">
        <Button variant="outline" @click="closeModal" :disabled="form.isSubmitting">Hủy</Button>
        <Button @click="submitReport" :disabled="form.isSubmitting">{{ form.isSubmitting ? 'Đang gửi...' : 'Gửi báo cáo' }}</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>