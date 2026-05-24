<template>
  <div
    class="glass-card-hover group cursor-pointer overflow-hidden"
    @click="$emit('view', contact)"
  >
    <!-- Photo / Initials -->
    <div class="relative h-40 bg-gradient-to-br from-indigo-500/20 to-violet-500/20 flex items-center justify-center overflow-hidden">
      <img
        v-if="contact.foto"
        :src="photoUrl"
        :alt="fullName"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <div v-else class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
        <span class="text-2xl font-bold text-white">{{ initials }}</span>
      </div>

      <!-- Overlay on hover -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>

    <!-- Info -->
    <div class="p-4">
      <h3 class="text-lg font-semibold text-white truncate">{{ fullName }}</h3>

      <div v-if="contact.telefono" class="flex items-center gap-2 mt-2 text-slate-400 text-sm">
        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
        </svg>
        <span class="truncate">{{ contact.telefono }}</span>
      </div>

      <div v-if="contact.email" class="flex items-center gap-2 mt-1.5 text-slate-400 text-sm">
        <svg class="w-4 h-4 text-violet-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
        </svg>
        <span class="truncate">{{ contact.email }}</span>
      </div>
    </div>

    <!-- Actions -->
    <div class="px-4 pb-4 flex gap-2">
      <button
        @click.stop="$emit('view', contact)"
        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white/5 hover:bg-indigo-500/20 text-slate-400 hover:text-indigo-300 text-xs font-medium transition-all duration-200 border border-transparent hover:border-indigo-500/20"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Ver
      </button>

      <button
        @click.stop="$emit('edit', contact)"
        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white/5 hover:bg-violet-500/20 text-slate-400 hover:text-violet-300 text-xs font-medium transition-all duration-200 border border-transparent hover:border-violet-500/20"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Editar
      </button>

      <button
        @click.stop="$emit('delete', contact)"
        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-white/5 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs font-medium transition-all duration-200 border border-transparent hover:border-rose-500/20"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
        Borrar
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { getApiUrl } from '../config/api.js'

const props = defineProps({
  contact: {
    type: Object,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete'])

const fullName = computed(() => {
  const nombre = props.contact.nombre || ''
  const apellido = props.contact.apellido || ''
  return `${nombre} ${apellido}`.trim() || 'Sin nombre'
})

const initials = computed(() => {
  const nombre = props.contact.nombre || ''
  const apellido = props.contact.apellido || ''
  const first = nombre.charAt(0).toUpperCase()
  const second = apellido.charAt(0).toUpperCase()
  return `${first}${second}` || '?'
})

const photoUrl = computed(() => {
  if (!props.contact.foto) return ''
  const base = getApiUrl()
  return `${base}/${props.contact.foto}`
})
</script>
