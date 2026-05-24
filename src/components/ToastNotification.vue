<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border backdrop-blur-xl',
          toast.leaving ? 'toast-leave' : 'toast-enter',
          typeClasses[toast.type]
        ]"
      >
        <!-- Icon -->
        <div class="flex-shrink-0 mt-0.5">
          <!-- Success -->
          <svg v-if="toast.type === 'success'" class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <!-- Error -->
          <svg v-else-if="toast.type === 'error'" class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <!-- Info -->
          <svg v-else class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <!-- Message -->
        <p class="text-sm font-medium text-white flex-1">{{ toast.message }}</p>

        <!-- Close -->
        <button
          @click="dismissToast(toast.id)"
          class="flex-shrink-0 text-white/40 hover:text-white/80 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast.js'

const { toasts, dismissToast } = useToast()

const typeClasses = {
  success: 'bg-emerald-500/10 border-emerald-500/20',
  error: 'bg-rose-500/10 border-rose-500/20',
  info: 'bg-blue-500/10 border-blue-500/20'
}
</script>
