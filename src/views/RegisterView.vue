<template>
  <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-slate-950">
      <div class="absolute top-1/3 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/3 -left-32 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Register Card -->
    <div class="relative z-10 w-full max-w-md">
      <div class="glass-card p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/30">
            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
            </svg>
          </div>
          <h1 class="text-4xl font-extrabold gradient-text-bright">Crear Cuenta</h1>
          <p class="text-slate-400 mt-2 text-sm">Regístrate para gestionar tus contactos</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleRegister" class="space-y-5">
          <div>
            <label class="label-field">Usuario</label>
            <input
              v-model="form.nombre_de_usuario"
              type="text"
              placeholder="Mínimo 3 caracteres"
              class="input-field"
              required
            />
            <p v-if="errors.nombre_de_usuario" class="text-rose-400 text-xs mt-1">{{ errors.nombre_de_usuario }}</p>
          </div>

          <div>
            <label class="label-field">Contraseña</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Mínimo 6 caracteres"
              class="input-field"
              required
            />
            <p v-if="errors.password" class="text-rose-400 text-xs mt-1">{{ errors.password }}</p>
          </div>

          <div>
            <label class="label-field">Confirmar Contraseña</label>
            <input
              v-model="form.confirmar"
              type="password"
              placeholder="Repite tu contraseña"
              class="input-field"
              required
            />
            <p v-if="errors.confirmar" class="text-rose-400 text-xs mt-1">{{ errors.confirmar }}</p>
          </div>

          <!-- Error Message -->
          <p v-if="errorMsg" class="text-rose-400 text-sm text-center bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-2">
            {{ errorMsg }}
          </p>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary w-full flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>{{ loading ? 'Registrando...' : 'Crear Cuenta' }}</span>
          </button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-slate-400 text-sm mt-6">
          ¿Ya tienes cuenta?
          <router-link to="/login" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
            Inicia sesión
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import { useToast } from '../composables/useToast.js'

const router = useRouter()
const authStore = useAuthStore()
const { showSuccess, showError } = useToast()
const loading = ref(false)
const errorMsg = ref('')

const form = reactive({
  nombre_de_usuario: '',
  password: '',
  confirmar: ''
})

const errors = reactive({
  nombre_de_usuario: '',
  password: '',
  confirmar: ''
})

function validate() {
  let valid = true
  errors.nombre_de_usuario = ''
  errors.password = ''
  errors.confirmar = ''

  if (form.nombre_de_usuario.trim().length < 3) {
    errors.nombre_de_usuario = 'El usuario debe tener al menos 3 caracteres'
    valid = false
  }
  if (form.password.length < 6) {
    errors.password = 'La contraseña debe tener al menos 6 caracteres'
    valid = false
  }
  if (form.password !== form.confirmar) {
    errors.confirmar = 'Las contraseñas no coinciden'
    valid = false
  }
  return valid
}

async function handleRegister() {
  errorMsg.value = ''
  if (!validate()) return

  loading.value = true
  const result = await authStore.register({
    nombre_de_usuario: form.nombre_de_usuario.trim(),
    password: form.password
  })
  loading.value = false

  if (result.success) {
    showSuccess('¡Cuenta creada exitosamente! Inicia sesión.')
    router.push('/login')
  } else {
    errorMsg.value = result.message
    showError(result.message)
  }
}
</script>