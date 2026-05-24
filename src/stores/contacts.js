import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../config/api.js'

export const useContactsStore = defineStore('contacts', () => {
  // State
  const contacts = ref([])
  const currentContact = ref(null)
  const loading = ref(false)
  const searchQuery = ref('')

  // Getters
  const filteredContacts = computed(() => {
    if (!searchQuery.value.trim()) return contacts.value
    const q = searchQuery.value.toLowerCase().trim()
    return contacts.value.filter(c => {
      const nombre = (c.nombre || '').toLowerCase()
      const apellido = (c.apellido || '').toLowerCase()
      const telefono = (c.telefono || '').toLowerCase()
      const email = (c.email || '').toLowerCase()
      return nombre.includes(q) || apellido.includes(q) || telefono.includes(q) || email.includes(q)
    })
  })

  const totalContacts = computed(() => contacts.value.length)

  // Actions
  async function fetchContacts(buscar = '') {
    loading.value = true
    try {
      const params = buscar ? `?buscar=${encodeURIComponent(buscar)}` : ''
      const response = await api.get(`/contactos/index.php${params}`)
      if (response.data.success) {
        contacts.value = response.data.data || []
      }
      return { success: true }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al cargar contactos'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function fetchContact(id) {
    loading.value = true
    try {
      const response = await api.get(`/contactos/detalle.php?id=${id}`)
      if (response.data.success) {
        currentContact.value = response.data.data
      }
      return { success: true, data: currentContact.value }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al cargar contacto'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function createContact(formData) {
    loading.value = true
    try {
      const response = await api.post('/contactos/crear.php', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (response.data.success) {
        await fetchContacts()
        return { success: true, message: response.data.message || 'Contacto creado exitosamente', data: response.data.data }
      }
      return { success: false, message: response.data.message || 'Error al crear contacto' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al crear contacto'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function updateContact(formData) {
    loading.value = true
    try {
      const response = await api.post('/contactos/actualizar.php', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (response.data.success) {
        await fetchContacts()
        return { success: true, message: response.data.message || 'Contacto actualizado exitosamente' }
      }
      return { success: false, message: response.data.message || 'Error al actualizar contacto' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al actualizar contacto'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function deleteContact(id) {
    loading.value = true
    try {
      const response = await api.post('/contactos/eliminar.php', { id })
      if (response.data.success) {
        contacts.value = contacts.value.filter(c => c.id !== id)
        return { success: true, message: response.data.message || 'Contacto eliminado' }
      }
      return { success: false, message: response.data.message || 'Error al eliminar contacto' }
    } catch (error) {
      const msg = error.response?.data?.message || 'Error al eliminar contacto'
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  function setSearchQuery(query) {
    searchQuery.value = query
  }

  return {
    contacts,
    currentContact,
    loading,
    searchQuery,
    filteredContacts,
    totalContacts,
    fetchContacts,
    fetchContact,
    createContact,
    updateContact,
    deleteContact,
    setSearchQuery
  }
})
