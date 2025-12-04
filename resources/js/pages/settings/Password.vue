<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

// Dữ liệu và Refs
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: edit().url,
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />

        <SettingsLayout>
            <div class="mx-auto max-w-3xl"> 
                
                <div class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-8 md:p-10">

                    <div class="pb-6 mb-6 border-b border-gray-100 dark:border-gray-800">
                        <HeadingSmall
                            title="Update password"
                            description="Ensure your account is using a long, random password to stay secure."
                        />
                    </div>

                    <Form
                        v-bind="PasswordController.update.form()"
                        :options="{
                            preserveScroll: true,
                        }"
                        reset-on-success
                        :reset-on-error="[
                            'password',
                            'password_confirmation',
                            'current_password',
                        ]"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="current_password">Current password</Label>
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                name="current_password"
                                type="password"
                                class="block w-full max-w-md"
                                autocomplete="current-password"
                                placeholder="Current password"
                                :aria-invalid="!!errors.current_password"
                            />
                            <InputError :message="errors.current_password" />
                        </div>
                        
                        <div class="grid gap-2">
                            <Label for="password">New password</Label>
                            <Input
                                id="password"
                                ref="passwordInput"
                                name="password"
                                type="password"
                                class="block w-full max-w-md"
                                autocomplete="new-password"
                                placeholder="New password"
                                :aria-invalid="!!errors.password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm password</Label>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="block w-full max-w-md"
                                autocomplete="new-password"
                                placeholder="Confirm password"
                                :aria-invalid="!!errors.password_confirmation"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <div class="flex items-center gap-4 pt-4">
                            <Button
                                :disabled="processing"
                                data-test="update-password-button"
                                size="lg"
                                class="min-w-28 transition-colors duration-200"
                            >
                                <span v-if="processing">Updating...</span>
                                <span v-else>Save password</span>
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out duration-300"
                                enter-from-class="opacity-0 translate-x-1"
                                leave-active-class="transition ease-in-out duration-300"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-green-600 dark:text-green-400 font-semibold"
                                >
                                    Saved successfully.
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </div>
                </div>
        </SettingsLayout>
    </AppLayout>
</template>