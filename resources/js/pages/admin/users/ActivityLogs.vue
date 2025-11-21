<script setup>
defineProps({
    user: Object,
    logs: Array
});
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold">
      Lịch sử hoạt động: {{ user.name }} (ID: {{ user.id }})
    </h1>

    <ul class="mt-4 space-y-2">
      <li v-for="log in logs" :key="log.id" class="p-3 bg-gray-100 rounded">
        <div class="font-semibold">{{ log.description }}</div>
        <div class="text-sm text-gray-600">{{ new Date(log.created_at).toLocaleString() }}</div>
      </li>
    </ul>
  </div>
</template>
