<template>
  <div class="flex flex-col items-center justify-center py-12">
    <div class="relative">
      <div class="w-12 h-12 rounded-full border-2 border-transparent border-t-indigo-500 border-r-violet-500 animate-spin"></div>
      <div class="absolute inset-0 w-12 h-12 rounded-full border-2 border-transparent border-b-indigo-500/30 border-l-violet-500/30 animate-spin-slow"></div>
    </div>
    <p v-if="text" class="mt-4 text-sm text-slate-400 animate-pulse-soft">{{ text }}</p>
  </div>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    default: ''
  }
})
</script>
