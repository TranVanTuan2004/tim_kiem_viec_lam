<template>
  <div class="flex flex-wrap gap-2">
    <template v-for="(link, i) in links" :key="i">
      <span
        v-if="!link.url"
        v-html="link.label"
        class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed"
      />
      <Link
        v-else
        :href="link.url"
        v-html="link.label"
        class="px-3 py-1 border rounded hover:bg-gray-100"
        :class="{ 'bg-blue-600 text-white': link.active }"
      />
    </template>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ links: Array })
</script>
