<template>
    <CandidateLayout>
        <Head title="Thêm học vấn" />

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link
                        :href="index.url()"
                        class="text-blue-600 hover:text-blue-700 flex items-center gap-2"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Quay lại
                    </Link>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Thêm học vấn
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Thêm thông tin về học vấn của bạn
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Trường học / Tổ chức
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.institution"
                                    type="text"
                                    placeholder="Ví dụ: Đại học Bách Khoa"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="form.errors.institution"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.institution }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Bằng cấp
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.degree"
                                    type="text"
                                    placeholder="Ví dụ: Cử nhân, Thạc sĩ..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="form.errors.degree"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.degree }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Chuyên ngành
                                </label>
                                <input
                                    v-model="form.field_of_study"
                                    type="text"
                                    placeholder="Ví dụ: Công nghệ thông tin"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="form.errors.field_of_study"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.field_of_study }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Ngày bắt đầu
                                    <span class="text-red-600">*</span>
                                </label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="form.errors.start_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.start_date }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Ngày kết thúc
                                </label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    :disabled="form.is_current"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                />
                                <p
                                    v-if="form.errors.end_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.end_date }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    GPA (0-4.0)
                                </label>
                                <input
                                    v-model.number="form.gpa"
                                    type="number"
                                    min="0"
                                    max="4"
                                    step="0.01"
                                    placeholder="Ví dụ: 3.8"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="form.errors.gpa"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.gpa }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_current"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">
                                        Tôi đang học tại đây
                                    </span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Mô tả
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="5"
                                    placeholder="Mô tả về quá trình học tập, thành tích..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6"
                        >
                            <Link
                                :href="index.url()"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Hủy
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? 'Đang thêm...'
                                        : 'Thêm học vấn'
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </CandidateLayout>
</template>

<script setup lang="ts">
import CandidateLayout from '@/layouts/CandidateLayout.vue';
import {
    create as createRoute,
    index,
    store,
} from '@/routes/candidate/educations';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    institution: '',
    degree: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    gpa: null as number | null,
    description: '',
    is_current: false,
});

const submit = () => {
    form.transform((data) => {
        const transformed = { ...data };
        // If is_current is true, set end_date to null
        if (transformed.is_current) {
            transformed.end_date = null;
        }
        return transformed;
    }).post(store.url(), {
        preserveScroll: true,
    });
};
</script>

