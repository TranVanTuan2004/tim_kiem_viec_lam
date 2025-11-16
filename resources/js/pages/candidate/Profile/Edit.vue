<template>
    <AuthenticatedLayout>
        <Head title="Edit Profile" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Edit Your Profile
                        </h1>
                        <p class="mt-2 text-gray-600">
                            Update your professional information
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
                                <div v-if="profile.avatar_url || avatarPreview" class="flex-shrink-0">
                                    <img
                                        :src="avatarPreview || profile.avatar_url"
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
                                        >City</label
                                    >
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Province</label
                                    >
                                    <input
                                        v-model="form.province"
                                        type="text"
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
                                    >
                                        CV/Resume (PDF, DOC, DOCX - Max 5MB)
                                        <span
                                            v-if="profile.cv_url"
                                            class="font-normal text-gray-500"
                                            >- Current file uploaded</span
                                        >
                                    </label>
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
                        <div v-if="allSkills && allSkills.length > 0">
                            <h2
                                class="mb-4 text-lg font-semibold text-gray-900"
                            >
                                Skills
                            </h2>
                            <div class="space-y-3">
                                <div
                                    v-for="(skill, index) in form.skills"
                                    :key="index"
                                    class="flex items-start space-x-3 rounded-lg border border-gray-200 p-3"
                                >
                                    <div class="grid flex-1 grid-cols-3 gap-3">
                                        <select
                                            v-model="skill.id"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="">
                                                Select Skill
                                            </option>
                                            <option
                                                v-for="s in allSkills"
                                                :key="s.id"
                                                :value="s.id"
                                            >
                                                {{ s.name }}
                                            </option>
                                        </select>
                                        <select
                                            v-model="skill.level"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        >
                                            <option value="beginner">
                                                Beginner
                                            </option>
                                            <option value="intermediate">
                                                Intermediate
                                            </option>
                                            <option value="advanced">
                                                Advanced
                                            </option>
                                            <option value="expert">
                                                Expert
                                            </option>
                                        </select>
                                        <input
                                            v-model.number="
                                                skill.years_experience
                                            "
                                            type="number"
                                            min="0"
                                            placeholder="Years"
                                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
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
                                    + Add Skill
                                </button>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div
                            class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6"
                        >
                            <Link
                                :href="index.url()"
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
                                        ? 'Updating...'
                                        : 'Update Profile'
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { index, update } from '@/routes/candidate/profile';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    profile: any;
    allSkills?: Array<any>;
    user: any;
}

const props = defineProps<Props>();

const form = useForm({
    avatar: null as File | null,
    birth_date: props.profile.birth_date || '',
    gender: props.profile.gender || '',
    address: props.profile.address || '',
    city: props.profile.city || '',
    province: props.profile.province || '',
    cv_file: null as File | null,
    summary: props.profile.summary || '',
    current_position: props.profile.current_position || '',
    current_company: props.profile.current_company || '',
    expected_salary: props.profile.expected_salary || null,
    experience_level: props.profile.experience_level || '',
    is_available: props.profile.is_available ?? true,
    skills: props.profile.skills || [],
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
        id: '',
        level: 'intermediate',
        years_experience: 1,
    });
};

const removeSkill = (index: number) => {
    form.skills.splice(index, 1);
};

const submit = () => {
    form.put(update.url(), {
        preserveScroll: true,
    });
};
</script>
