<script setup lang="ts">
import { useClientToast } from '@/composables/useClientToast';
import { CheckCircle, XCircle, Info, X } from 'lucide-vue-next';

const { toasts, removeToast } = useClientToast();

const getIcon = (type: string) => {
    switch (type) {
        case 'success':
            return CheckCircle;
        case 'error':
            return XCircle;
        default:
            return Info;
    }
};

const getColorClasses = (type: string) => {
    switch (type) {
        case 'success':
            return 'bg-green-50 border-green-200 text-green-800';
        case 'error':
            return 'bg-red-50 border-red-200 text-red-800';
        default:
            return 'bg-blue-50 border-blue-200 text-blue-800';
    }
};

const getIconColor = (type: string) => {
    switch (type) {
        case 'success':
            return 'text-green-600';
        case 'error':
            return 'text-red-600';
        default:
            return 'text-blue-600';
    }
};
</script>

<template>
    <div class="fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none">
        <TransitionGroup
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 translate-x-full"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="[
                    'pointer-events-auto w-96 max-w-[calc(100vw-2rem)] rounded-lg border-2 p-4 shadow-2xl backdrop-blur-sm',
                    getColorClasses(toast.type)
                ]"
            >
                <div class="flex items-start gap-3">
                    <component
                        :is="getIcon(toast.type)"
                        :class="['h-6 w-6 flex-shrink-0', getIconColor(toast.type)]"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm">{{ toast.title }}</p>
                        <p class="text-sm mt-1 opacity-90">{{ toast.message }}</p>
                    </div>
                    <button
                        @click="removeToast(toast.id)"
                        class="flex-shrink-0 opacity-50 hover:opacity-100 transition-opacity"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>
