<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePermissions } from '@/composables/usePermissions';

// Import Admin Dashboard component
import AdminDashboard from '@/pages/Dashboard.vue';

// Import Employer Dashboard component  
import EmployerDashboard from '@/pages/Employer/Dashboard.vue';

const page = usePage();
const { hasRole } = usePermissions();

// Read props from Inertia page props
const props = defineProps<{
    isAdmin?: boolean;
    // Admin props
    stats?: any;
    roleStats?: any;
    jobStats?: any;
    applicationStats?: any;
    revenue?: any;
    topJobs?: any;
    topCompanies?: any;
    newUsers?: any;
    newJobs?: any;
    last30Days?: any;
    recentActivities?: any;
    // Employer props
    company?: any;
    recentJobs?: any[];
    recentApplications?: any[];
}>();

const isAdminDashboard = computed(() => {
    return props.isAdmin ?? hasRole('Admin');
});
</script>

<template>
    <Head :title="isAdminDashboard ? 'Admin Dashboard' : 'Employer Dashboard'" />
    
    <!-- Admin Dashboard - uses page.props directly -->
    <AdminDashboard v-if="isAdminDashboard" />
    
    <!-- Employer Dashboard - receives props from controller -->
    <EmployerDashboard
        v-else
        :stats="props.stats"
        :recent-jobs="props.recentJobs"
        :recent-applications="props.recentApplications"
        :company="props.company"
    />
</template>

