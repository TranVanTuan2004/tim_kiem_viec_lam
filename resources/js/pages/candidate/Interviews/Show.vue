<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
    Calendar, Clock, MapPin, Video, Phone, Building2, Briefcase, 
    ArrowLeft, CheckCircle, XCircle, AlertCircle 
} from 'lucide-vue-next';
import { ref, reactive } from 'vue';

interface Props {
    interview: {
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
        employer_notes: string | null;
        candidate_notes: string | null;
        company: {
            id: number;
            name: string;
            logo: string | null;
        };
        job_posting: {
            id: number;
            title: string;
        };
        created_at: string;
        confirmed_at: string | null;
    };
}

const props = defineProps<Props>();

const showConfirmModal = ref(false);
const showDeclineModal = ref(false);
const declineNotes = ref('');

const getStatusBadgeClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        declined: 'bg-red-100 text-red-800 border-red-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-gray-100 text-gray-800 border-gray-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getTypeIcon = (type: string) => {
    if (type === 'video') return Video;
    if (type === 'phone') return Phone;
    return Building2;
};

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    return {
        date: date.toLocaleDateString('vi-VN', { 
            weekday: 'long',
            day: '2-digit', 
            month: 'long', 
            year: 'numeric' 
        }),
        time: date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
    };
};

const confirmInterview = () => {
    router.post(`/candidate/interviews/${props.interview.id}/confirm`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showConfirmModal.value = false;
        },
    });
};

const declineInterview = () => {
    router.post(`/candidate/interviews/${props.interview.id}/decline`, {
        notes: declineNotes.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showDeclineModal.value = false;
            declineNotes.value = '';
        },
    });
};

// Reschedule Logic
const showRescheduleModal = ref(false);
const rescheduleForm = reactive({
    proposed_times: [''],
    notes: '',
});

const addTimeSlot = () => {
    if (rescheduleForm.proposed_times.length < 3) {
        rescheduleForm.proposed_times.push('');
    }
};

const removeTimeSlot = (index: number) => {
    rescheduleForm.proposed_times.splice(index, 1);
};

const submitReschedule = () => {
    // Filter out empty times
    const times = rescheduleForm.proposed_times.filter((t: string) => t);
    
    if (times.length === 0) {
        alert('Vui lòng chọn ít nhất một thời gian đề xuất');
        return;
    }

    router.post(`/candidate/interviews/${props.interview.id}/propose-reschedule`, {
        proposed_times: times,
        notes: rescheduleForm.notes,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showRescheduleModal.value = false;
            rescheduleForm.proposed_times = [''];
            rescheduleForm.notes = '';
        },
    });
};
</script>

<template>
    <CandidateLayout>
        <Head :title="`Lịch Phỏng Vấn - ${interview.job_posting.title}`" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Back Button -->
                <Link href="/candidate/interviews" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6">
                    <ArrowLeft class="h-4 w-4" />
                    <span>Quay lại danh sách</span>
                </Link>

                <!-- Header -->
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div v-if="interview.company.logo" class="h-16 w-16 rounded-lg overflow-hidden">
                                    <img :src="interview.company.logo" :alt="interview.company.name" class="h-full w-full object-cover" />
                                </div>
                                <div v-else class="h-16 w-16 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                    <Briefcase class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ interview.job_posting.title }}</h1>
                                    <p class="text-gray-600">{{ interview.company.name }}</p>
                                </div>
                            </div>
                            <Badge :class="getStatusBadgeClass(interview.status)" class="text-sm px-3 py-1">
                                {{ interview.status_label }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Interview Details -->
                <Card class="mb-6">
                    <CardContent class="p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Thông Tin Phỏng Vấn</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <Calendar class="h-5 w-5 text-gray-400 mt-0.5" />
                                <div>
                                    <div class="font-medium text-gray-900">{{ formatDateTime(interview.scheduled_at).date }}</div>
                                    <div class="text-sm text-gray-600">{{ formatDateTime(interview.scheduled_at).time }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <Clock class="h-5 w-5 text-gray-400" />
                                <span class="text-gray-900">{{ interview.duration }} phút</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <component :is="getTypeIcon(interview.type)" class="h-5 w-5 text-gray-400" />
                                <span class="text-gray-900">{{ interview.type_label }}</span>
                            </div>

                            <div v-if="interview.location" class="flex items-start gap-3">
                                <MapPin class="h-5 w-5 text-gray-400 mt-0.5" />
                                <span class="text-gray-900">{{ interview.location }}</span>
                            </div>

                            <div v-if="interview.description" class="pt-4 border-t">
                                <h3 class="font-medium text-gray-900 mb-2">Mô tả</h3>
                                <p class="text-gray-600 whitespace-pre-line">{{ interview.description }}</p>
                            </div>

                            <div v-if="interview.employer_notes" class="pt-4 border-t">
                                <h3 class="font-medium text-gray-900 mb-2">Ghi chú từ nhà tuyển dụng</h3>
                                <p class="text-gray-600 whitespace-pre-line">{{ interview.employer_notes }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <Card v-if="interview.status === 'pending'" class="border-yellow-200 bg-yellow-50">
                    <CardContent class="p-6">
                        <div class="flex items-start gap-3 mb-4">
                            <AlertCircle class="h-5 w-5 text-yellow-600 mt-0.5" />
                            <div>
                                <h3 class="font-medium text-yellow-900">Vui lòng xác nhận tham gia</h3>
                                <p class="text-sm text-yellow-700 mt-1">Bạn cần xác nhận hoặc từ chối lời mời phỏng vấn này</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Button @click="showConfirmModal = true" class="flex-1 bg-green-600 hover:bg-green-700">
                                <CheckCircle class="h-4 w-4 mr-2" />
                                Xác Nhận Tham Gia
                            </Button>
                            <Button @click="showRescheduleModal = true" variant="outline" class="flex-1 border-blue-300 text-blue-700 hover:bg-blue-50">
                                <Clock class="h-4 w-4 mr-2" />
                                Đề Xuất Lịch Khác
                            </Button>
                            <Button @click="showDeclineModal = true" variant="outline" class="flex-1 border-red-300 text-red-700 hover:bg-red-50">
                                <XCircle class="h-4 w-4 mr-2" />
                                Từ Chối
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card v-else-if="interview.status === 'confirmed'" class="border-green-200 bg-green-50">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-3">
                            <CheckCircle class="h-5 w-5 text-green-600" />
                            <div>
                                <h3 class="font-medium text-green-900">Đã xác nhận tham gia</h3>
                                <p class="text-sm text-green-700 mt-1">Bạn đã xác nhận tham gia buổi phỏng vấn này</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <Card v-else-if="interview.status === 'rescheduled'" class="border-purple-200 bg-purple-50">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-3">
                            <Clock class="h-5 w-5 text-purple-600" />
                            <div>
                                <h3 class="font-medium text-purple-900">Đã đề xuất lịch khác</h3>
                                <p class="text-sm text-purple-700 mt-1">Bạn đã gửi đề xuất thay đổi lịch phỏng vấn. Vui lòng chờ phản hồi từ nhà tuyển dụng.</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Confirm Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showConfirmModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Xác Nhận Tham Gia</h3>
                <p class="text-gray-600 mb-6">Bạn có chắc chắn muốn xác nhận tham gia buổi phỏng vấn này?</p>
                <div class="flex gap-3">
                    <Button @click="showConfirmModal = false" variant="outline" class="flex-1">Hủy</Button>
                    <Button @click="confirmInterview" class="flex-1 bg-green-600 hover:bg-green-700">Xác Nhận</Button>
                </div>
            </div>
        </div>

        <!-- Decline Modal -->
        <div v-if="showDeclineModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showDeclineModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Từ Chối Lời Mời</h3>
                <p class="text-gray-600 mb-4">Bạn có thể để lại lý do từ chối (không bắt buộc):</p>
                <textarea
                    v-model="declineNotes"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6"
                    rows="4"
                    placeholder="Lý do từ chối..."
                ></textarea>
                <div class="flex gap-3">
                    <Button @click="showDeclineModal = false" variant="outline" class="flex-1">Hủy</Button>
                    <Button @click="declineInterview" class="flex-1 bg-red-600 hover:bg-red-700">Từ Chối</Button>
                </div>
            </div>
        </div>

        <!-- Reschedule Modal -->
        <div v-if="showRescheduleModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showRescheduleModal = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Đề Xuất Lịch Khác</h3>
                <p class="text-gray-600 mb-4">Vui lòng chọn các khoảng thời gian bạn có thể tham gia:</p>
                
                <div class="space-y-3 mb-4">
                    <div v-for="(time, index) in rescheduleForm.proposed_times" :key="index" class="flex gap-2">
                        <input 
                            type="datetime-local" 
                            v-model="rescheduleForm.proposed_times[index]"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                        <Button 
                            v-if="rescheduleForm.proposed_times.length > 1"
                            @click="removeTimeSlot(index)" 
                            variant="ghost" 
                            size="icon"
                            class="text-red-500 hover:text-red-700 hover:bg-red-50"
                        >
                            <XCircle class="h-5 w-5" />
                        </Button>
                    </div>
                    <Button 
                        v-if="rescheduleForm.proposed_times.length < 3"
                        @click="addTimeSlot" 
                        variant="outline" 
                        class="w-full border-dashed border-gray-300 text-gray-600 hover:border-blue-500 hover:text-blue-600"
                    >
                        <Clock class="h-4 w-4 mr-2" />
                        Thêm thời gian
                    </Button>
                </div>

                <p class="text-gray-600 mb-2">Ghi chú thêm:</p>
                <textarea
                    v-model="rescheduleForm.notes"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6"
                    rows="3"
                    placeholder="Ghi chú cho nhà tuyển dụng..."
                ></textarea>

                <div class="flex gap-3">
                    <Button @click="showRescheduleModal = false" variant="outline" class="flex-1">Hủy</Button>
                    <Button @click="submitReschedule" class="flex-1 bg-blue-600 hover:bg-blue-700">Gửi Đề Xuất</Button>
                </div>
            </div>
        </div>
    </CandidateLayout>
</template>
