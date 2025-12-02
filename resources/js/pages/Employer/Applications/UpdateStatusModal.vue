<template>
  <Dialog :open="open" @update:open="$emit('update:open', $event)">
    <DialogContent class="sm:max-w-[500px]">
      <DialogHeader>
        <DialogTitle>Cập nhật trạng thái</DialogTitle>
        <DialogDescription>
          Cập nhật trạng thái cho ứng viên {{ application?.candidate?.user?.full_name }}
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-4 py-4">
        <!-- Status Select -->
        <div class="space-y-2">
          <Label for="status">Trạng thái</Label>
          <select
            id="status"
            v-model="form.status"
            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
          >
            <option value="" disabled>Chọn trạng thái</option>
            <option value="pending">Chờ xem xét</option>
            <option value="reviewing">Đang xem xét</option>
            <option value="interview">Phỏng vấn</option>
            <option value="accepted">Chấp nhận</option>
            <option value="rejected">Từ chối</option>
          </select>
          <p v-if="form.errors.status" class="text-sm text-red-500">
            {{ form.errors.status }}
          </p>
        </div>

        <!-- Interview Date (Only for Interview status) -->
        <div v-if="form.status === 'interview'" class="space-y-2">
          <Label for="interview_date">Ngày phỏng vấn</Label>
          <input
            id="interview_date"
            type="datetime-local"
            v-model="form.interview_date"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
          />
          <p v-if="form.errors.interview_date" class="text-sm text-red-500">
            {{ form.errors.interview_date }}
          </p>
        </div>

        <!-- Notes -->
        <div class="space-y-2">
          <Label for="notes">Ghi chú (tùy chọn)</Label>
          <Textarea
            id="notes"
            v-model="form.notes"
            placeholder="Nhập ghi chú về lần cập nhật này..."
            rows="3"
          />
          <p v-if="form.errors.notes" class="text-sm text-red-500">
            {{ form.errors.notes }}
          </p>
        </div>

        <DialogFooter>
          <Button
            type="button"
            variant="outline"
            @click="$emit('update:open', false)"
          >
            Hủy
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Đang lưu...' : 'Lưu thay đổi' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps({
  open: Boolean,
  application: Object,
  newStatus: String,
})

const emit = defineEmits(['update:open', 'success'])

const form = useForm({
  status: '',
  notes: '',
  interview_date: '',
})

// Watch for changes to initialize form
watch(
  () => [props.open, props.application, props.newStatus],
  ([isOpen, app, status]) => {
    if (isOpen && app) {
      form.status = status || app.status
      form.notes = app.notes || ''
      form.interview_date = app.interview_date || ''
      form.clearErrors()
    }
  },
  { immediate: true }
)

const submit = () => {
  if (!props.application) return

  form.patch(`/employer/applications/${props.application.id}/status`, {
    onSuccess: () => {
      emit('update:open', false)
      emit('success')
      form.reset()
    },
  })
}
</script>
