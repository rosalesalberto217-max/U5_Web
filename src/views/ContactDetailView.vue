<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8">
      <button @click="router.push('/dashboard')" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al Dashboard
      </button>
    </div>

    <!-- Loading -->
    <LoadingSpinner v-if="contactsStore.loading" text="Cargando contacto..." />

    <!-- Contact Detail -->
    <div v-else-if="contact" class="max-w-3xl">
      <div class="glass-card overflow-hidden">
        <!-- Top Section with Photo -->
        <div class="relative h-48 bg-gradient-to-br from-indigo-500/20 to-violet-500/20 flex items-center justify-center">
          <img
            v-if="contact.foto"
            :src="photoUrl"
            :alt="fullName"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-28 h-28 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center shadow-xl shadow-indigo-500/30">
            <span class="text-4xl font-bold text-white">{{ initials }}</span>
          </div>
        </div>

        <!-- Info Section -->
        <div class="p-6 md:p-8">
          <h2 class="text-3xl font-bold text-white mb-1">{{ fullName }}</h2>
          <p v-if="contact.fecha_creacion" class="text-slate-500 text-sm mb-6">
            Agregado el {{ formatDate(contact.fecha_creacion) }}
          </p>

          <!-- Detail Items -->
          <div class="space-y-4">
            <div v-if="contact.telefono" class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
              <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Teléfono</p>
                <p class="text-white font-medium mt-0.5">{{ contact.telefono }}</p>
              </div>
            </div>

            <div v-if="contact.email" class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
              <div class="w-10 h-10 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Email</p>
                <p class="text-white font-medium mt-0.5">{{ contact.email }}</p>
              </div>
            </div>

            <div v-if="contact.direccion" class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
              <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Dirección</p>
                <p class="text-white font-medium mt-0.5">{{ contact.direccion }}</p>
              </div>
            </div>

            <div v-if="contact.notas" class="flex items-start gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
              <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Notas</p>
                <p class="text-white font-medium mt-0.5 whitespace-pre-wrap">{{ contact.notas }}</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 mt-8 pt-6 border-t border-white/5">
            <router-link :to="`/contacts/${contact.id}/edit`" class="btn-primary flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
              </svg>
              Editar
            </router-link>
            <button @click="showDeleteModal = true" class="btn-danger flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
              </svg>
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="text-center py-20">
      <p class="text-slate-400 text-lg">Contacto no encontrado</p>
      <router-link to="/dashboard" class="btn-primary mt-4 inline-block">Volver al Dashboard</router-link>
    </div>

    <!-- Confirm Delete -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Eliminar Contacto"
      :message="`¿Deseas eliminar a ${fullName}?`"
      confirm-text="Sí, eliminar"
      @confirm="handleDelete"
      @cancel="showDeleteModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import { useContactsStore } from '../stores/contacts.js'
import { useToast } from '../composables/useToast.js'
import { getApiUrl } from '../config/api.js'

const route = useRoute()
const router = useRouter()
const contactsStore = useContactsStore()
const { showSuccess, showError } = useToast()
const showDeleteModal = ref(false)

const contact = computed(() => contactsStore.currentContact)

const fullName = computed(() => {
  if (!contact.value) return ''
  return `${contact.value.nombre || ''} ${contact.value.apellido || ''}`.trim()
})

const initials = computed(() => {
  if (!contact.value) return '?'
  const f = (contact.value.nombre || '').charAt(0).toUpperCase()
  const l = (contact.value.apellido || '').charAt(0).toUpperCase()
  return `${f}${l}` || '?'
})

const photoUrl = computed(() => {
  if (!contact.value?.foto) return ''
  return `${getApiUrl()}/${contact.value.foto}`
})

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(async () => {
  await contactsStore.fetchContact(route.params.id)
})

async function handleDelete() {
  const result = await contactsStore.deleteContact(contact.value.id)
  showDeleteModal.value = false
  if (result.success) {
    showSuccess('Contacto eliminado')
    router.push('/dashboard')
  } else {
    showError(result.message)
  }
}
</script>
