<template>
  <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-slate-950">
      <div class="absolute top-1/4 -left-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">
      <div class="glass-card p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/30">
            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
          </div>
          <h1 class="text-4xl font-extrabold gradient-text-bright">ContactOS</h1>
          <p class="text-slate-400 mt-2 text-sm">Inicia sesión en tu cuenta</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="label-field">Usuario</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
              </div>
              <input
                v-model="form.nombre_de_usuario"
                type="text"
                placeholder="Tu nombre de usuario"
                class="input-field pl-12"
                required
              />
            </div>
          </div>

          <div>
            <label class="label-field">Contraseña</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
              </div>
              <input
                v-model="form.password"
                type="password"
                placeholder="Tu contraseña"
                class="input-field pl-12"
                required
              />
            </div>
          </div>

          <!-- Error Message -->
          <p v-if="errorMsg" class="text-rose-400 text-sm text-center bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-2">
            {{ errorMsg }}
          </p>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="authStore.loading"
            class="btn-primary w-full flex items-center justify-center gap-2"
          >
            <svg v-if="authStore.loading" class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>{{ authStore.loading ? 'Ingresando...' : 'Ingresar' }}</span>
          </button>
        </form>

        <!-- Register Link -->
        <p class="text-center text-slate-400 text-sm mt-6">
          ¿No tienes cuenta?
          <router-link to="/register" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
            Regístrate aquí
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
const errorMsg = ref('')

const form = reactive({
  nombre_de_usuario: '',
  password: ''
})

async function handleLogin() {
  errorMsg.value = ''
  if (!form.nombre_de_usuario.trim() || !form.password) {
    errorMsg.value = 'Todos los campos son obligatorios'
    return
  }

  const result = await authStore.login({
    nombre_de_usuario: form.nombre_de_usuario.trim(),
    password: form.password
  })

  if (result.success) {
    showSuccess('¡Bienvenido!')
    router.push('/dashboard')
  } else {
    errorMsg.value = result.message
    showError(result.message)
  }
}
</script>