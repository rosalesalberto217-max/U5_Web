<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8">
      <button @click="router.back()" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver
      </button>
      <h1 class="text-3xl font-bold gradient-text">{{ isEditing ? 'Editar Contacto' : 'Nuevo Contacto' }}</h1>
    </div>

    <!-- Loading -->
    <LoadingSpinner v-if="loadingData" text="Cargando contacto..." />

    <!-- Form -->
    <div v-else class="glass-card p-6 md:p-8 max-w-3xl">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Photo Upload -->
        <div class="flex flex-col items-center mb-6">
          <div
            class="w-32 h-32 rounded-2xl border-2 border-dashed border-white/20 hover:border-indigo-500/50 flex items-center justify-center overflow-hidden cursor-pointer transition-all duration-300 bg-white/5 group"
            @click="$refs.fotoInput.click()"
          >
            <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" alt="Preview" />
            <div v-else class="text-center p-4">
              <svg class="w-8 h-8 mx-auto text-slate-500 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
              </svg>
              <p class="text-xs text-slate-500 mt-1">Subir foto</p>
            </div>
          </div>
          <input ref="fotoInput" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
          <button v-if="photoPreview" type="button" @click="removePhoto" class="text-xs text-rose-400 hover:text-rose-300 mt-2 transition-colors">
            Quitar foto
          </button>
        </div>

        <!-- Fields Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="label-field">Nombre *</label>
            <input v-model="form.nombre" type="text" placeholder="Nombre del contacto" class="input-field" required />
            <p v-if="errors.nombre" class="text-rose-400 text-xs mt-1">{{ errors.nombre }}</p>
          </div>
          <div>
            <label class="label-field">Apellido</label>
            <input v-model="form.apellido" type="text" placeholder="Apellido (opcional)" class="input-field" />
          </div>
          <div>
            <label class="label-field">Teléfono *</label>
            <input v-model="form.telefono" type="text" placeholder="Número de teléfono" class="input-field" required />
            <p v-if="errors.telefono" class="text-rose-400 text-xs mt-1">{{ errors.telefono }}</p>
          </div>
          <div>
            <label class="label-field">Email</label>
            <input v-model="form.email" type="email" placeholder="correo@ejemplo.com" class="input-field" />
          </div>
        </div>

        <div>
          <label class="label-field">Dirección</label>
          <input v-model="form.direccion" type="text" placeholder="Dirección (opcional)" class="input-field" />
        </div>

        <div>
          <label class="label-field">Notas</label>
          <textarea v-model="form.notas" rows="3" placeholder="Notas adicionales..." class="input-field resize-none"></textarea>
        </div>

        <!-- Error -->
        <p v-if="errorMsg" class="text-rose-400 text-sm bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-2">
          {{ errorMsg }}
        </p>

        <!-- Actions -->
        <div class="flex gap-3 pt-2">
          <button type="button" @click="router.back()" class="btn-secondary">
            Cancelar
          </button>
          <button type="submit" :disabled="saving" class="btn-primary flex items-center gap-2">
            <svg v-if="saving" class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ saving ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Guardar Contacto') }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '../layouts/AppLayout.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useContactsStore } from '../stores/contacts.js'
import { useToast } from '../composables/useToast.js'
import { getApiUrl } from '../config/api.js'

const route = useRoute()
const router = useRouter()
const contactsStore = useContactsStore()
const { showSuccess, showError } = useToast()

const isEditing = computed(() => !!route.params.id)
const loadingData = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const photoPreview = ref(null)
const photoFile = ref(null)

const form = reactive({
  nombre: '',
  apellido: '',
  telefono: '',
  email: '',
  direccion: '',
  notas: ''
})

const errors = reactive({
  nombre: '',
  telefono: ''
})

onMounted(async () => {
  if (isEditing.value) {
    loadingData.value = true
    const result = await contactsStore.fetchContact(route.params.id)
    if (result.success && contactsStore.currentContact) {
      const c = contactsStore.currentContact
      form.nombre = c.nombre || ''
      form.apellido = c.apellido || ''
      form.telefono = c.telefono || ''
      form.email = c.email || ''
      form.direccion = c.direccion || ''
      form.notas = c.notas || ''
      if (c.foto) {
        photoPreview.value = `${getApiUrl()}/${c.foto}`
      }
    }
    loadingData.value = false
  }
})

function handleFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

function removePhoto() {
  photoFile.value = null
  photoPreview.value = null
}

function validate() {
  let valid = true
  errors.nombre = ''
  errors.telefono = ''

  if (!form.nombre.trim()) {
    errors.nombre = 'El nombre es obligatorio'
    valid = false
  }
  if (!form.telefono.trim()) {
    errors.telefono = 'El teléfono es obligatorio'
    valid = false
  }
  return valid
}

async function handleSubmit() {
  errorMsg.value = ''
  if (!validate()) return

  saving.value = true
  const formData = new FormData()

  if (isEditing.value) {
    formData.append('id', route.params.id)
  }

  formData.append('nombre', form.nombre.trim())
  formData.append('apellido', form.apellido.trim())
  formData.append('telefono', form.telefono.trim())
  formData.append('email', form.email.trim())
  formData.append('direccion', form.direccion.trim())
  formData.append('notas', form.notas.trim())

  if (photoFile.value) {
    formData.append('foto', photoFile.value)
  }

  let result
  if (isEditing.value) {
    result = await contactsStore.updateContact(formData)
  } else {
    result = await contactsStore.createContact(formData)
  }

  saving.value = false

  if (result.success) {
    showSuccess(result.message)
    router.push('/dashboard')
  } else {
    errorMsg.value = result.message
    showError(result.message)
  }
}
</script>
