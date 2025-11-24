<template>
    <CandidateLayout>
        <Head title="Create Profile" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Create Your Profile
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Complete your profile to access all features
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <!-- Avatar Upload -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Profile Picture
                            </h2>
                            <div class="flex items-center space-x-6">
                                <div v-if="avatarPreview" class="flex-shrink-0">
                                    <img
                                        :src="avatarPreview"
                                        alt="Profile Avatar"
                                        class="h-24 w-24 rounded-full object-cover border-2 border-gray-300"
                                    />
                                </div>
                                <div v-else class="flex-shrink-0">
                                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Upload Avatar (JPG, PNG, GIF - Max 2MB)
                                    </label>
                                    <input
                                        @change="handleAvatarUpload"
                                        type="file"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <p
                                        v-if="form.errors.avatar"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.avatar }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Recommended size: 400x400 pixels
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Personal Information
                            </h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Birth Date</label
                                    >
                                    <input
                                        v-model="form.birth_date"
                                        type="date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="form.errors.birth_date"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.birth_date }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Phone Number <span class="text-red-500">*</span></label
                                    >
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Enter your phone number"
                                    />
                                    <p
                                        v-if="form.errors.phone"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Gender</label
                                    >
                                    <select
                                        v-model="form.gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >City <span class="text-red-500">*</span></label
                                    >
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Province <span class="text-red-500">*</span></label
                                    >
                                    <input
                                        v-model="form.province"
                                        type="text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Address</label
                                    >
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Professional Information
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Professional Summary</label
                                    >
                                    <textarea
                                        v-model="form.summary"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Brief description of your professional background and career goals"
                                    ></textarea>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Current Position</label
                                        >
                                        <input
                                            v-model="form.current_position"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Current Company</label
                                        >
                                        <input
                                            v-model="form.current_company"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Experience Level</label
                                        >
                                        <select
                                            v-model="form.experience_level"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">
                                                Select Experience Level
                                            </option>
                                            <option value="intern">
                                                Intern
                                            </option>
                                            <option value="fresher">
                                                Fresher
                                            </option>
                                            <option value="junior">
                                                Junior
                                            </option>
                                            <option value="middle">
                                                Middle Level
                                            </option>
                                            <option value="senior">
                                                Senior
                                            </option>
                                            <option value="lead">Lead</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Expected Salary (USD)</label
                                        >
                                        <input
                                            v-model.number="
                                                form.expected_salary
                                            "
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >CV/Resume (PDF, DOC, DOCX - Max
                                        5MB)</label
                                    >
                                    <input
                                        @change="handleFileUpload"
                                        type="file"
                                        accept=".pdf,.doc,.docx"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <p
                                        v-if="form.errors.cv_file"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.cv_file }}
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_available"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <label
                                        class="ml-2 block text-sm text-gray-700"
                                    >
                                        I am available for work opportunities
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Kỹ năng <span class="text-red-500">*</span>
                            </h2>
                            <p class="mb-3 text-sm text-gray-600">Vui lòng thêm ít nhất một kỹ năng</p>
                            <div class="space-y-3">
                                <div
                                    v-for="(skill, index) in form.skills"
                                    :key="index"
                                    class="flex items-start space-x-3 rounded-lg border border-gray-200 p-3"
                                >
                                    <input
                                        v-model="skill.name"
                                        type="text"
                                        placeholder="Nhập tên kỹ năng (ví dụ: JavaScript, Python, React...)"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <button
                                        type="button"
                                        @click="removeSkill(index)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="addSkill"
                                    class="w-full rounded-lg border-2 border-dashed border-gray-300 py-2 text-gray-600 hover:border-blue-500 hover:text-blue-600"
                                >
                                    + Thêm kỹ năng
                                </button>
                            </div>
                        </div>

                        <!-- Work Experience -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Kinh nghiệm làm việc
                            </h2>
                            <div class="space-y-3">
                                <div
                                    v-for="(exp, index) in form.work_experiences"
                                    :key="index"
                                    class="rounded-lg border border-gray-200 p-4 space-y-3"
                                >
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Công ty</label
                                            >
                                            <input
                                                v-model="exp.company_name"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Tên công ty"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Vị trí</label
                                            >
                                            <input
                                                v-model="exp.position"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Vị trí công việc"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Ngày bắt đầu</label
                                            >
                                            <input
                                                v-model="exp.start_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Ngày kết thúc</label
                                            >
                                            <input
                                                v-model="exp.end_date"
                                                type="date"
                                                :disabled="exp.is_current"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="exp.is_current"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label
                                            class="ml-2 block text-sm text-gray-700"
                                        >
                                            Đang làm việc tại đây
                                        </label>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Mô tả công việc</label
                                        >
                                        <textarea
                                            v-model="exp.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Mô tả về công việc và thành tích của bạn"
                                        ></textarea>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeWorkExperience(index)"
                                        class="text-sm text-red-600 hover:text-red-800"
                                    >
                                        Xóa kinh nghiệm này
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="addWorkExperience"
                                    class="w-full rounded-lg border-2 border-dashed border-gray-300 py-2 text-gray-600 hover:border-blue-500 hover:text-blue-600"
                                >
                                    + Thêm kinh nghiệm làm việc
                                </button>
                            </div>
                        </div>

                        <!-- Education -->
                        <div>
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Học vấn
                            </h2>
                            <div class="space-y-3">
                                <div
                                    v-for="(edu, index) in form.educations"
                                    :key="index"
                                    class="rounded-lg border border-gray-200 p-4 space-y-3"
                                >
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Trường học / Tổ chức</label
                                            >
                                            <input
                                                v-model="edu.institution"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Tên trường học"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Bằng cấp</label
                                            >
                                            <input
                                                v-model="edu.degree"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Ví dụ: Cử nhân, Thạc sĩ..."
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Chuyên ngành</label
                                            >
                                            <input
                                                v-model="edu.field_of_study"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Chuyên ngành học"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >GPA</label
                                            >
                                            <input
                                                v-model.number="edu.gpa"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="4"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="0.00 - 4.00"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Ngày bắt đầu</label
                                            >
                                            <input
                                                v-model="edu.start_date"
                                                type="date"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Ngày kết thúc</label
                                            >
                                            <input
                                                v-model="edu.end_date"
                                                type="date"
                                                :disabled="edu.is_current"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="edu.is_current"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                        <label
                                            class="ml-2 block text-sm text-gray-700"
                                        >
                                            Đang học
                                        </label>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Mô tả</label
                                        >
                                        <textarea
                                            v-model="edu.description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Mô tả về quá trình học tập, thành tích..."
                                        ></textarea>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeEducation(index)"
                                        class="text-sm text-red-600 hover:text-red-800"
                                    >
                                        Xóa học vấn này
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="addEducation"
                                    class="w-full rounded-lg border-2 border-dashed border-gray-300 py-2 text-gray-600 hover:border-blue-500 hover:text-blue-600"
                                >
                                    + Thêm học vấn
                                </button>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6"
                        >
                            <Link
                                :href="home.url()"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{
                                    form.processing
                                        ? 'Creating...'
                                        : 'Create Profile'
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
import { home } from '@/routes';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    allSkills?: Array<any>;
    user: any;
}

const props = defineProps<Props>();

// Ensure allSkills is always an array
const allSkills = props.allSkills || [];

const form = useForm({
    avatar: null as File | null,
    birth_date: '',
    phone: props.user.phone || '',
    gender: '',
    address: '',
    city: '',
    province: '',
    cv_file: null as File | null,
    summary: '',
    current_position: '',
    current_company: '',
    expected_salary: null as number | null,
    experience_level: '',
    is_available: true,
    skills: [] as Array<{ 
        name: string;
    }>,
    work_experiences: [] as Array<{
        company_name: string;
        position: string;
        description: string;
        start_date: string;
        end_date: string;
        is_current: boolean;
    }>,
    educations: [] as Array<{
        institution: string;
        degree: string;
        field_of_study: string;
        start_date: string;
        end_date: string;
        gpa: number | null;
        description: string;
        is_current: boolean;
    }>,
});

const avatarPreview = ref<string | null>(null);

const handleAvatarUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.avatar = target.files[0];
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cv_file = target.files[0];
    }
};

const addSkill = () => {
    form.skills.push({
        name: '',
    });
};

const removeSkill = (index: number) => {
    form.skills.splice(index, 1);
};

const addWorkExperience = () => {
    form.work_experiences.push({
        company_name: '',
        position: '',
        description: '',
        start_date: '',
        end_date: '',
        is_current: false,
    });
};

const removeWorkExperience = (index: number) => {
    form.work_experiences.splice(index, 1);
};

const addEducation = () => {
    form.educations.push({
        institution: '',
        degree: '',
        field_of_study: '',
        start_date: '',
        end_date: '',
        gpa: null,
        description: '',
        is_current: false,
    });
};

const removeEducation = (index: number) => {
    form.educations.splice(index, 1);
};

const submit = () => {
    // Transform form data
    form.transform((data) => {
        const transformed = { ...data };
        
        // Transform skills - only keep skills with names
        if (transformed.skills) {
            transformed.skills = transformed.skills
                .filter((skill: any) => {
                    return skill.name && skill.name.trim() !== '';
                })
                .map((skill: any) => {
                    return {
                        name: skill.name.trim(),
                    };
                });
        }
        
        // For work_experiences, if is_current is true, set end_date to null
        if (transformed.work_experiences) {
            transformed.work_experiences = transformed.work_experiences.map((exp: any) => {
                const expData = { ...exp };
                if (expData.is_current) {
                    expData.end_date = null;
                }
                return expData;
            });
        }
        
        // For educations, if is_current is true, set end_date to null
        if (transformed.educations) {
            transformed.educations = transformed.educations.map((edu: any) => {
                const eduData = { ...edu };
                if (eduData.is_current) {
                    eduData.end_date = null;
                }
                delete eduData.is_current; // Remove is_current as it's not in the model
                return eduData;
            });
        }
        
        return transformed;
    }).post('/candidate/profile', {
        preserveScroll: true,
    });
};
</script>

