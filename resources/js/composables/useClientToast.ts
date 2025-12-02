import { ref } from 'vue';

export interface ToastMessage {
    id: number;
    type: 'success' | 'error' | 'info';
    title: string;
    message: string;
}

const toasts = ref<ToastMessage[]>([]);
let nextId = 1;

export function useClientToast() {
    const showToast = (type: 'success' | 'error' | 'info', title: string, message: string) => {
        const id = nextId++;
        toasts.value.push({ id, type, title, message });

        // Auto remove after 4 seconds
        setTimeout(() => {
            removeToast(id);
        }, 4000);
    };

    const removeToast = (id: number) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    return {
        toasts,
        showToast,
        removeToast,
    };
}
