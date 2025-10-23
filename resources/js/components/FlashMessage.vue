<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle, Info, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const page = usePage();
const show = ref(false);
const timeoutId = ref<number | null>(null);

const flashMessage = computed(() => {
    const flash = page.props.flash as Record<string, string> | undefined;
    if (!flash) return null;

    if (flash.success) {
        return { type: 'success', message: flash.success };
    } else if (flash.error) {
        return { type: 'error', message: flash.error };
    } else if (flash.info) {
        return { type: 'info', message: flash.info };
    }
    return null;
});

const icon = computed(() => {
    switch (flashMessage.value?.type) {
        case 'success':
            return CheckCircle;
        case 'error':
            return AlertCircle;
        case 'info':
            return Info;
        default:
            return Info;
    }
});

const variant = computed(() => {
    switch (flashMessage.value?.type) {
        case 'error':
            return 'destructive';
        default:
            return 'default';
    }
});

const alertClass = computed(() => {
    switch (flashMessage.value?.type) {
        case 'success':
            return 'border-green-500 bg-green-50 text-green-900 dark:border-green-700 dark:bg-green-950 dark:text-green-100';
        case 'info':
            return 'border-blue-500 bg-blue-50 text-blue-900 dark:border-blue-700 dark:bg-blue-950 dark:text-blue-100';
        default:
            return '';
    }
});

const iconClass = computed(() => {
    switch (flashMessage.value?.type) {
        case 'success':
            return 'text-green-600 dark:text-green-400';
        case 'info':
            return 'text-blue-600 dark:text-blue-400';
        default:
            return '';
    }
});

const showMessage = () => {
    show.value = true;

    // Auto hide after 5 seconds
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
    timeoutId.value = window.setTimeout(() => {
        show.value = false;
    }, 5000);
};

const hideMessage = () => {
    show.value = false;
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
};

watch(flashMessage, (newValue) => {
    if (newValue) {
        showMessage();
    }
});

onMounted(() => {
    if (flashMessage.value) {
        showMessage();
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div
            v-if="show && flashMessage"
            class="fixed top-4 right-4 z-50 max-w-md"
        >
            <Alert :variant="variant" :class="alertClass">
                <component :is="icon" class="h-5 w-5" :class="iconClass" />
                <AlertDescription
                    class="flex items-start justify-between gap-2"
                >
                    <span class="flex-1">{{ flashMessage.message }}</span>
                    <button
                        @click="hideMessage"
                        class="flex-shrink-0 opacity-70 transition-opacity hover:opacity-100"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </AlertDescription>
            </Alert>
        </div>
    </Transition>
</template>
