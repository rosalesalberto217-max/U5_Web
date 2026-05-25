import axios from 'axios'

let cachedConfig = null

export async function loadConfig() {
  if (cachedConfig) return cachedConfig
  try {
    const response = await fetch(import.meta.env.BASE_URL + 'config.json?t=' + Date.now())
    cachedConfig = await response.json()
    return cachedConfig
  } catch (error) {
    console.error('Error cargando configuración:', error)
    cachedConfig = { API_URL: 'https://alberto0o0.alwaysdata.net/api' }
    return cachedConfig
  }
}

export function getApiUrl() {
  return cachedConfig?.API_URL || 'https://alberto0o0.alwaysdata.net/api'
}

const api = axios.create({
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor: inject token + set baseURL dynamically
api.interceptors.request.use(async (config) => {
  if (!cachedConfig) {
    await loadConfig()
  }
  config.baseURL = cachedConfig.API_URL

  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, (error) => {
  return Promise.reject(error)
})

// Response interceptor: handle 401
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      if (!window.location.hash.includes('/login')) {
        window.location.href = import.meta.env.BASE_URL + '#/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
