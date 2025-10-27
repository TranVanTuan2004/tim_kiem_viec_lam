<template>
    <div class="relative">
        <MapPin class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-gray-400 z-10" />
        <Input
            v-model="searchQuery"
            :placeholder="placeholder"
            class="h-14 pl-10 pr-10 text-base cursor-pointer border-gray-200 hover:border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200"
            readonly
            @click="toggleDropdown"
            @keyup.enter="handleSearch"
        />
        <ChevronDown 
            class="absolute top-1/2 right-3 h-5 w-5 -translate-y-1/2 text-gray-400 cursor-pointer transition-transform duration-200 hover:text-gray-600"
            :class="{ 'rotate-180': isOpen }"
            @click="toggleDropdown"
        />
        
        <!-- Dropdown -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-[-10px]"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-[-10px]"
        >
            <div 
                v-if="isOpen" 
                class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-80 overflow-hidden backdrop-blur-sm"
            >
                <!-- Search Header -->
                <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                    <div class="relative">
                        <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input
                            v-model="searchFilter"
                            placeholder="Tìm tỉnh thành..."
                            class="w-full pl-9 pr-3 py-2 text-sm border-gray-200 focus:border-blue-400 focus:ring-1 focus:ring-blue-100"
                            @click.stop
                        />
                    </div>
                </div>
                
                <!-- Provinces List -->
                <div class="max-h-64 overflow-y-auto custom-scrollbar">
                    <!-- Loading State -->
                    <div v-if="loading" class="flex items-center justify-center py-8">
                        <div class="flex items-center space-x-2 text-gray-500">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                            <span class="text-sm">Đang tải danh sách tỉnh thành...</span>
                        </div>
                    </div>
                    
                    <!-- Error State -->
                    <div v-else-if="error" class="flex flex-col items-center justify-center py-8 text-red-500">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-3">
                            <AlertCircle class="h-6 w-6 text-red-400" />
                        </div>
                        <p class="text-sm font-medium">{{ error }}</p>
                        <button 
                            @click="toggleDropdown"
                            class="mt-2 px-3 py-1 bg-red-100 text-red-600 rounded text-xs hover:bg-red-200 transition-colors"
                        >
                            Thử lại
                        </button>
                    </div>
                    
                    <!-- Provinces List -->
                    <div v-else>
                        <div
                            v-for="(province, index) in filteredProvinces"
                            :key="province.code"
                            class="px-4 py-3 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 cursor-pointer text-sm transition-all duration-150 border-b border-gray-50 last:border-b-0 group"
                            :class="{ 'bg-blue-50': index % 2 === 0 }"
                            @click="selectProvince(province)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-400 group-hover:bg-blue-500 transition-colors"></div>
                                    <span class="font-medium text-gray-800 group-hover:text-blue-700 transition-colors">
                                        {{ province.name }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs text-gray-400">
                                    <span class="px-2 py-1 bg-gray-100 rounded-full group-hover:bg-blue-100 transition-colors">
                                        {{ province.division_type }}
                                    </span>
                                    <span class="px-2 py-1 bg-gray-100 rounded-full group-hover:bg-blue-100 transition-colors">
                                        {{ province.code }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State -->
                        <div v-if="filteredProvinces.length === 0" class="flex flex-col items-center justify-center py-8 text-gray-500">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <Search class="h-6 w-6 text-gray-400" />
                            </div>
                            <p class="text-sm font-medium">Không tìm thấy tỉnh thành nào</p>
                            <p class="text-xs text-gray-400 mt-1">Thử tìm kiếm với từ khóa khác</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 text-center">
                    {{ filteredProvinces.length }} tỉnh thành được tìm thấy
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { MapPin, ChevronDown, Search, AlertCircle } from 'lucide-vue-next';
import { useProvinces } from '@/composables/useProvinces';

// Interface
interface Province {
    name: string;
    code: number;
    division_type: string;
    codename: string;
    phone_code: number;
    wards: any[];
}

// Props
interface Props {
    modelValue?: string;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    placeholder: 'Địa điểm: Hà Nội, TP.HCM...'
});

// Emits
const emit = defineEmits<{
    'update:modelValue': [value: string];
    'search': [location: string];
}>();

// Sử dụng composable
const { loadProvinces, searchProvinces, loading, error } = useProvinces();

// State
const searchQuery = ref(props.modelValue);
const isOpen = ref(false);
const searchFilter = ref('');

// Computed
const filteredProvinces = computed(() => {
    return searchProvinces(searchFilter.value);
});

// Methods
const toggleDropdown = async () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        await loadProvinces();
    }
};

const selectProvince = (province: Province) => {
    searchQuery.value = province.name;
    emit('update:modelValue', province.name);
    isOpen.value = false;
    searchFilter.value = '';
};

const handleSearch = () => {
    emit('search', searchQuery.value);
};

// Close dropdown when clicking outside
const handleClickOutside = (event: Event) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.relative')) {
        isOpen.value = false;
    }
};

// Lifecycle
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue;
});
</script>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>