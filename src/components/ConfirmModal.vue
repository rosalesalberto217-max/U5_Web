<template>
  <teleport to="body">
    <transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[9998] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('cancel')"></div>

        <!-- Modal Content -->
        <div class="modal-content relative z-10 w-full max-w-md glass-card p-6 animate-fade-in">
          <!-- Icon -->
          <div class="flex items-center justify-center w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/20 mx-auto mb-4">
            <svg class="w-7 h-7 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
          </div>

          <!-- Title -->
          <h3 class="text-xl font-semibold text-white text-center mb-2">{{ title }}</h3>

          <!-- Message -->
          <p class="text-slate-400 text-center text-sm mb-6">{{ message }}</p>

          <!-- Actions -->
          <div class="flex gap-3">
            <button
              @click="$emit('cancel')"
              class="flex-1 btn-secondary text-center"
            >
              Cancelar
            </button>
            <button
              @click="$emit('confirm')"
              class="flex-1 btn-danger text-center"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: '¿Estás seguro?'
  },
  message: {
    type: String,
    default: 'Esta acción no se puede deshacer.'
  },
  confirmText: {
    type: String,
    default: 'Eliminar'
  }
})

defineEmits(['confirm', 'cancel'])
</script>
