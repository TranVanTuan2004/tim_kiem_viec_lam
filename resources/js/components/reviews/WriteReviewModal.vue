<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Star } from 'lucide-vue-next';

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    companyId: {
        type: Number,
        required: true,
    },
    companySlug: {
        type: String,
        required: true,
    },
    existingReview: {
        type: Object as () => any,
        default: null,
    },
});

const emit = defineEmits(['update:open', 'success']);

const hoveredRating = ref(0);

const form = useForm({
    rating: props.existingReview?.rating || 0,
    title: props.existingReview?.title || '',
    review: props.existingReview?.review || '',
});

const isEditing = computed(() => !!props.existingReview);

const setRating = (rating: number) => {
    form.rating = rating;
};

const handleMouseEnter = (rating: number) => {
    hoveredRating.value = rating;
};

const handleMouseLeave = () => {
    hoveredRating.value = 0;
};

const displayRating = computed(() => hoveredRating.value || form.rating);

const submitReview = () => {
    if (isEditing.value) {
        form.put(`/companies/${props.companySlug}/reviews/${props.existingReview.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
                form.reset();
            },
        });
    } else {
        form.post(`/companies/${props.companySlug}/reviews`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
                form.reset();
            },
        });
    }
};

const closeModal = () => {
    emit('update:open', false);
    if (!isEditing.value) {
        form.reset();
    }
};

const getRatingText = (rating: number) => {
    const texts = ['', 'Rất tệ', 'Tệ', 'Trung bình', 'Tốt', 'Xuất sắc'];
    return texts[rating] || '';
};
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>
                    {{ isEditing ? 'Chỉnh sửa đánh giá' : 'Viết đánh giá công ty' }}
                </DialogTitle>
                <DialogDescription>
                    {{ isEditing ? 'Cập nhật đánh giá của bạn về công ty này.' : 'Chia sẻ trải nghiệm làm việc của bạn tại công ty này.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitReview" class="space-y-6">
                <!-- Rating -->
                <div class="space-y-2">
                    <Label class="text-base font-semibold">
                        Đánh giá của bạn <span class="text-red-500">*</span>
                    </Label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1">
                            <button
                                v-for="i in 5"
                                :key="i"
                                type="button"
                                @click="setRating(i)"
                                @mouseenter="handleMouseEnter(i)"
                                @mouseleave="handleMouseLeave"
                                class="transition-transform hover:scale-110"
                            >
                                <Star
                                    class="h-8 w-8"
                                    :class="
                                        i <= displayRating
                                            ? 'fill-yellow-500 text-yellow-500'
                                            : 'text-gray-300'
                                    "
                                />
                            </button>
                        </div>
                        <span v-if="form.rating > 0" class="text-sm font-medium text-muted-foreground">
                            {{ getRatingText(form.rating) }}
                        </span>
                    </div>
                    <p v-if="form.errors.rating" class="text-sm text-red-500">
                        {{ form.errors.rating }}
                    </p>
                </div>

                <!-- Title -->
                <div class="space-y-2">
                    <Label for="title" class="text-base font-semibold">
                        Tiêu đề <span class="text-red-500">*</span>
                    </Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        type="text"
                        placeholder="VD: Môi trường làm việc tuyệt vời"
                        :class="{ 'border-red-500': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="text-sm text-red-500">
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Review Content -->
                <div class="space-y-2">
                    <Label for="review" class="text-base font-semibold">
                        Nội dung đánh giá <span class="text-red-500">*</span>
                    </Label>
                    <Textarea
                        id="review"
                        v-model="form.review"
                        placeholder="Chia sẻ trải nghiệm của bạn về công ty: văn hóa làm việc, đồng nghiệp, cơ hội phát triển..."
                        rows="6"
                        :class="{ 'border-red-500': form.errors.review }"
                    />
                    <div class="flex justify-between">
                        <p v-if="form.errors.review" class="text-sm text-red-500">
                            {{ form.errors.review }}
                        </p>
                        <p class="text-sm text-muted-foreground ml-auto">
                            {{ form.review.length }}/2000
                        </p>
                    </div>
                </div>

                <!-- Notice -->
                <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 p-4">
                    <p class="text-sm text-blue-900 dark:text-blue-200">
                        💡 Đánh giá của bạn sẽ được xem xét trước khi hiển thị công khai. Vui lòng đảm bảo nội dung trung thực và không vi phạm quy định.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <Button
                        type="button"
                        variant="outline"
                        @click="closeModal"
                        :disabled="form.processing"
                    >
                        Hủy
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing || !form.rating || !form.title || !form.review"
                    >
                        {{ form.processing ? 'Đang gửi...' : isEditing ? 'Cập nhật' : 'Gửi đánh giá' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>


