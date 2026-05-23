<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8 space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-white">Mis Contactos</h1>
          <p class="text-slate-400 text-sm mt-1">
            {{ contactsStore.totalContacts }} contacto{{ contactsStore.totalContacts !== 1 ? 's' : '' }} en total
          </p>
        </div>
        <router-link to="/contacts/new" class="btn-primary flex items-center gap-2 self-start">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          Nuevo Contacto
        </router-link>
      </div>

      <!-- Search -->
      <div class="relative max-w-md">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
          <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </div>
        <input
          v-model="searchInput"
          @input="contactsStore.setSearchQuery(searchInput)"
          type="text"
          placeholder="Buscar contactos..."
          class="input-field pl-12"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="contactsStore.loading && contactsStore.contacts.length === 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="rounded-2xl overflow-hidden">
        <div class="h-40 skeleton"></div>
        <div class="p-4 bg-white/5 space-y-3">
          <div class="h-5 w-3/4 rounded skeleton"></div>
          <div class="h-4 w-1/2 rounded skeleton"></div>
          <div class="h-4 w-2/3 rounded skeleton"></div>
          <div class="flex gap-2 pt-2">
            <div class="h-8 flex-1 rounded-lg skeleton"></div>
            <div class="h-8 flex-1 rounded-lg skeleton"></div>
            <div class="h-8 flex-1 rounded-lg skeleton"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="contactsStore.filteredContacts.length === 0 && !contactsStore.loading" class="flex flex-col items-center justify-center py-20">
      <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500/10 to-violet-500/10 flex items-center justify-center mb-6 border border-white/5">
        <svg class="w-12 h-12 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">
        {{ searchInput ? 'Sin resultados' : 'No tienes contactos aún' }}
      </h3>
      <p class="text-slate-400 text-sm mb-6">
        {{ searchInput ? 'Intenta con otros términos de búsqueda' : '¡Agrega tu primer contacto para comenzar!' }}
      </p>
      <router-link v-if="!searchInput" to="/contacts/new" class="btn-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Agregar Contacto
      </router-link>
    </div>

    <!-- Contacts Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <ContactCard
        v-for="contact in contactsStore.filteredContacts"
        :key="contact.id"
        :contact="contact"
        @view="viewContact"
        @edit="editContact"
        @delete="confirmDelete"
      />
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Eliminar Contacto"
      :message="`¿Estás seguro de que deseas eliminar a ${deleteTarget?.nombre || 'este contacto'}? Esta acción no se puede deshacer.`"
      confirm-text="Sí, eliminar"
      @confirm="handleDelete"
      @cancel="showDeleteModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '../layouts/AppLayout.vue'
import ContactCard from '../components/ContactCard.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import { useContactsStore } from '../stores/contacts.js'
import { useToast } from '../composables/useToast.js'

const router = useRouter()
const contactsStore = useContactsStore()
const { showSuccess, showError } = useToast()

const searchInput = ref('')
const showDeleteModal = ref(false)
const deleteTarget = ref(null)

onMounted(async () => {
  await contactsStore.fetchContacts()
})

function viewContact(contact) {
  router.push(`/contacts/${contact.id}`)
}

function editContact(contact) {
  router.push(`/contacts/${contact.id}/edit`)
}

function confirmDelete(contact) {
  deleteTarget.value = contact
  showDeleteModal.value = true
}

async function handleDelete() {
  if (!deleteTarget.value) return
  const result = await contactsStore.deleteContact(deleteTarget.value.id)
  showDeleteModal.value = false

  if (result.success) {
    showSuccess('Contacto eliminado exitosamente')
  } else {
    showError(result.message)
  }
  deleteTarget.value = null
}
</script>
