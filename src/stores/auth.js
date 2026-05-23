import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api, { getApiUrl } from '../config/api.js'

export const useAuthStore = defineStore('auth', () => {
  // State
  const token = ref(localStorage.getItem('auth_token') || null)
  const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
  const loading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value)

  const userPhoto = computed(() => {
    if (!user.value?.foto) return null
    const base = getApiUrl()
    return `${base}/${user.value.foto}`
  })

  // Actions
  async function login(credentials) {
    loading.value = true
    try {
      const response = await api.post('/auth/login.php', credentials)
      const data = response.data

      if (data.success && data.token) {
        token.value = data.token
        user.value = data.usuario || null
        localStorage.setItem('auth_token', data.token)
        if (data.usuario) {
          localStorage.setItem('auth_user', JSON.stringify(data.usuario))
        }
        return { success: true, message: 'Inicio de sesión exitoso' }
      }
      return { success: false, message: data.message || 'Error al iniciar sesión' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error de conexión al servidor'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function register(userData) {
    loading.value = true
    try {
      const response = await api.post('/auth/registrar.php', userData)
      if (response.data.success) {
        return { success: true, message: response.data.message || 'Registro exitoso' }
      }
      return { success: false, message: response.data.message || 'Error al registrarse' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al registrarse'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function fetchProfile() {
    loading.value = true
    try {
      const response = await api.get('/auth/perfil.php')
      if (response.data.success && response.data.data) {
        user.value = response.data.data
        localStorage.setItem('auth_user', JSON.stringify(user.value))
      }
      return { success: true, data: user.value }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al obtener perfil'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function updateProfile(formData) {
    loading.value = true
    try {
      const response = await api.post('/auth/editar.php', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (response.data.success && response.data.data) {
        user.value = response.data.data
        localStorage.setItem('auth_user', JSON.stringify(user.value))
      }
      return { success: true, message: response.data.message || 'Perfil actualizado' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al actualizar perfil'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/auth/logout.php')
    } catch (e) {
      // Ignore errors on logout
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
  }

  return {
    token,
    user,
    loading,
    isAuthenticated,
    userPhoto,
    login,
    register,
    fetchProfile,
    updateProfile,
    logout
  }
})