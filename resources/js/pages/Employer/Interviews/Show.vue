<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Calendar, Clock, MapPin, Video, Phone, Users, ArrowLeft, CheckCircle, XCircle, Edit, RefreshCw } from 'lucide-vue-next';

interface Interview {
    id: number;
    title: string;
    description: string;
    type: string;
    type_label: string;
    location: string;
    scheduled_at: string;
    duration: number;
    timezone: string;
    status: string;
    status_label: string;
    employer_notes: string;
    candidate_notes: string;
    proposed_times: string[];
    candidate: {
        id: number;
        name: string;
        email: string;
        phone: string;
    };
    job_posting: {
        id: number;
        title: string;
    };
    created_at: string;
    confirmed_at: string;
}

const props = defineProps<{
    interview: Interview;
}>();

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    declined: 'bg-red-100 text-red-800',
    rescheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-gray-100 text-gray-600',
};

const showCompleteDialog = ref(false);
const showCancelDialog = ref(false);
const showRescheduleDialog = ref(false);

const rescheduleForm = useForm({
    scheduled_at: '',
    duration: props.interview.duration,
});

const complete = () => {
    router.post(route('employer.interviews.complete', props.interview.id), {}, {
        onSuccess: () => {
            showCompleteDialog.value = false;
        }
    });
};

const cancel = () => {
    router.delete(route('employer.interviews.destroy', props.interview.id), {
        onSuccess: () => {
            showCancelDialog.value = false;
        }
    });
};

const reschedule = () => {
    rescheduleForm.post(route('employer.interviews.reschedule', props.interview.id), {
        onSuccess: () => {
            showRescheduleDialog.value = false;
        }
    });
};

const acceptReschedule = (time: string) => {
    if (confirm(`Bạn có chắc muốn chấp nhận thời gian ${new Date(time).toLocaleString('vi-VN')}?`)) {
        router.post(route('employer.interviews.reschedule.accept', props.interview.id), {
            scheduled_at: time,
        }, {
            preserveScroll: true,
        });
    }
};

const declineReschedule = () => {
    if (confirm('Bạn có chắc muốn từ chối tất cả các thời gian đề xuất?')) {
        router.post(route('employer.interviews.reschedule.decline', props.interview.id), {}, {
            preserveScroll: true,
        });
    }
};

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return {
        full: date.toLocaleString('vi-VN'),
        date: date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' }),
        time: date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
    };
};
</script>

<template>
    <AuthenticatedLayout :title="interview.title">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <Button variant="ghost" @click="router.visit(route('employer.interviews.index'))" class="mb-4">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Quay lại
                </Button>
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">{{ interview.title }}</h1>
                        <Badge :class="statusColors[interview.status]" class="mt-2">
                            {{ interview.status_label }}
                        </Badge>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-if="['pending', 'confirmed'].includes(interview.status)"
                            @click="showRescheduleDialog = true"
                            variant="outline"
                        >
                            <RefreshCw class="h-4 w-4 mr-2" />
                            Đổi Lịch
                        </Button>
                        <Button
                            v-if="interview.status === 'confirmed'"
                            @click="showCompleteDialog = true"
                            variant="outline"
                        >
                            <CheckCircle class="h-4 w-4 mr-2" />
                            Hoàn Thành
                        </Button>
                        <Button
                            v-if="['pending', 'confirmed'].includes(interview.status)"
                            @click="showCancelDialog = true"
                            variant="destructive"
                        >
                            <XCircle class="h-4 w-4 mr-2" />
                            Hủy Lịch
                        </Button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Interview Details -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Thông Tin Phỏng Vấn</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Mô tả</p>
                                <p class="mt-1">{{ interview.description || 'Không có mô tả' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Hình thức</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <Video v-if="interview.type === 'video'" class="h-5 w-5" />
                                        <Phone v-else-if="interview.type === 'phone'" class="h-5 w-5" />
                                        <Users v-else class="h-5 w-5" />
                                        <span>{{ interview.type_label }}</span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Thời lượng</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <Clock class="h-5 w-5" />
                                        <span>{{ interview.duration }} phút</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Thời gian</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <Calendar class="h-5 w-5" />
                                    <span>{{ new Date(interview.scheduled_at).toLocaleString('vi-VN') }}</span>
                                </div>
                            </div>

                            <div v-if="interview.location">
                                <p class="text-sm text-gray-600">
                                    <template v-if="interview.type === 'in_person'">Địa điểm</template>
                                    <template v-else-if="interview.type === 'video'">Link Meeting</template>
                                    <template v-else>Số điện thoại</template>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <MapPin class="h-5 w-5" />
                                    <span>{{ interview.location }}</span>
                                </div>
                            </div>

                            <div v-if="interview.employer_notes">
                                <p class="text-sm text-gray-600">Ghi chú của bạn</p>
                                <p class="mt-1 p-3 bg-gray-50 rounded">{{ interview.employer_notes }}</p>
                            </div>

                            <div v-if="interview.candidate_notes">
                                <p class="text-sm text-gray-600">Ghi chú từ ứng viên</p>
                                <p class="mt-1 p-3 bg-blue-50 rounded">{{ interview.candidate_notes }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Proposed Times (if rescheduled) -->
                    <Card v-if="interview.proposed_times && interview.proposed_times.length > 0">
                        <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle>Thời Gian Đề Xuất Từ Ứng Viên</CardTitle>
                            <Button variant="destructive" size="sm" @click="declineReschedule">
                                <XCircle class="h-4 w-4 mr-2" />
                                Từ Chối Tất Cả
                            </Button>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <div
                                    v-for="(time, index) in interview.proposed_times"
                                    :key="index"
                                    class="p-3 border rounded flex items-center justify-between"
                                >
                                    <span>{{ new Date(time).toLocaleString('vi-VN') }}</span>
                                    <Button size="sm" @click="acceptReschedule(time)">Chấp Nhận</Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Candidate Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Ứng Viên</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div>
                                <p class="font-semibold">{{ interview.candidate.name }}</p>
                                <p class="text-sm text-gray-600">{{ interview.candidate.email }}</p>
                                <p v-if="interview.candidate.phone" class="text-sm text-gray-600">
                                    {{ interview.candidate.phone }}
                                </p>
                            </div>
                            <div class="pt-3 border-t">
                                <p class="text-sm text-gray-600">Vị trí ứng tuyển</p>
                                <p class="font-medium">{{ interview.job_posting.title }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Timeline -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Timeline</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600">Tạo lúc</p>
                                <p>{{ new Date(interview.created_at).toLocaleString('vi-VN') }}</p>
                            </div>
                            <div v-if="interview.confirmed_at">
                                <p class="text-gray-600">Xác nhận lúc</p>
                                <p>{{ new Date(interview.confirmed_at).toLocaleString('vi-VN') }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Complete Confirmation Dialog -->
        <Dialog v-model:open="showCompleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Xác nhận hoàn thành</DialogTitle>
                    <DialogDescription>
                        Bạn có chắc muốn đánh dấu buổi phỏng vấn này đã hoàn thành?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCompleteDialog = false">Hủy</Button>
                    <Button @click="complete">Xác nhận</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Cancel Confirmation Dialog -->
        <Dialog v-model:open="showCancelDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Xác nhận hủy lịch</DialogTitle>
                    <DialogDescription>
                        Bạn có chắc muốn hủy lịch phỏng vấn này? Hành động này không thể hoàn tác.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCancelDialog = false">Không</Button>
                    <Button variant="destructive" @click="cancel">Hủy Lịch</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Reschedule Dialog -->
        <Dialog v-model:open="showRescheduleDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Đổi lịch phỏng vấn</DialogTitle>
                    <DialogDescription>
                        Chọn thời gian mới cho buổi phỏng vấn
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="reschedule" class="space-y-4">
                    <div>
                        <Label for="reschedule_date">Ngày & Giờ mới</Label>
                        <Input
                            id="reschedule_date"
                            v-model="rescheduleForm.scheduled_at"
                            type="datetime-local"
                            required
                        />
                    </div>
                    <div>
                        <Label for="reschedule_duration">Thời lượng (phút)</Label>
                        <Input
                            id="reschedule_duration"
                            v-model.number="rescheduleForm.duration"
                            type="number"
                            min="15"
                            max="480"
                            required
                        />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showRescheduleDialog = false">Hủy</Button>
                        <Button type="submit" :disabled="rescheduleForm.processing">
                            {{ rescheduleForm.processing ? 'Đang xử lý...' : 'Đổi Lịch' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
