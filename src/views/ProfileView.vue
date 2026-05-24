<template>
  <AppLayout>
    <div class="max-w-2xl">
      <h1 class="text-3xl font-bold gradient-text mb-8">Mi Perfil</h1>

      <div class="glass-card p-6 md:p-8">
        <!-- Avatar Section -->
        <div class="flex flex-col items-center mb-8">
          <div
            class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-500/30 cursor-pointer group relative"
            @click="$refs.fotoInput.click()"
          >
            <img
              v-if="avatarPreview"
              :src="avatarPreview"
              class="w-full h-full object-cover group-hover:opacity-75 transition-opacity"
              alt="Avatar"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center group-hover:opacity-75 transition-opacity">
              <span class="text-4xl font-bold text-white">{{ userInitial }}</span>
            </div>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
              <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
              </svg>
            </div>
          </div>
          <input ref="fotoInput" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
          <p class="text-slate-500 text-xs mt-3">Haz clic para cambiar tu foto</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSave" class="space-y-5">
          <div>
            <label class="label-field">Nombre de Usuario</label>
            <input v-model="form.nombre_de_usuario" type="text" class="input-field" placeholder="Tu nombre de usuario" />
          </div>

          <div>
            <label class="label-field">Nueva Contraseña</label>
            <input v-model="form.password" type="password" class="input-field" placeholder="Dejar en blanco para no cambiar" />
            <p class="text-slate-500 text-xs mt-1">Mínimo 6 caracteres si deseas cambiarla</p>
          </div>

          <!-- Error -->
          <p v-if="errorMsg" class="text-rose-400 text-sm bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-2">
            {{ errorMsg }}
          </p>

          <button type="submit" :disabled="saving" class="btn-primary w-full flex items-center justify-center gap-2">
            <svg v-if="saving" class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </form>

        <!-- Info -->
        <div v-if="authStore.user?.fecha_registro" class="mt-6 pt-6 border-t border-white/5 text-center">
          <p class="text-slate-500 text-xs">Miembro desde {{ formatDate(authStore.user.fecha_registro) }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import AppLayout from '../layouts/AppLayout.vue'
import { useAuthStore } from '../stores/auth.js'
import { useToast } from '../composables/useToast.js'

const authStore = useAuthStore()
const { showSuccess, showError } = useToast()

const saving = ref(false)
const errorMsg = ref('')
const photoFile = ref(null)
const avatarPreview = ref(null)

const form = reactive({
  nombre_de_usuario: '',
  password: ''
})

const userInitial = computed(() => {
  const name = authStore.user?.nombre_de_usuario || '?'
  return name.charAt(0).toUpperCase()
})

onMounted(() => {
  form.nombre_de_usuario = authStore.user?.nombre_de_usuario || ''
  if (authStore.userPhoto) {
    avatarPreview.value = authStore.userPhoto
  }
})

function handleFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    photoFile.value = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function handleSave() {
  errorMsg.value = ''

  if (form.password && form.password.length < 6) {
    errorMsg.value = 'La contraseña debe tener al menos 6 caracteres'
    return
  }

  saving.value = true
  const formData = new FormData()

  if (form.nombre_de_usuario.trim()) {
    formData.append('nombre_de_usuario', form.nombre_de_usuario.trim())
  }
  if (form.password) {
    formData.append('password', form.password)
  }
  if (photoFile.value) {
    formData.append('foto', photoFile.value)
  }

  const result = await authStore.updateProfile(formData)
  saving.value = false

  if (result.success) {
    showSuccess('Perfil actualizado exitosamente')
    form.password = ''
  } else {
    errorMsg.value = result.message
    showError(result.message)
  }
}
</script>