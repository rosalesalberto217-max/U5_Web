import { ref } from 'vue'

const toasts = ref([])
let toastId = 0

export function useToast() {
  function addToast(message, type = 'info', duration = 4000) {
    const id = ++toastId
    toasts.value.push({ id, message, type, leaving: false })

    setTimeout(() => {
      dismissToast(id)
    }, duration)

    return id
  }

  function dismissToast(id) {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index !== -1) {
      toasts.value[index].leaving = true
      setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
      }, 300)
    }
  }

  function showSuccess(message) {
    return addToast(message, 'success')
  }

  function showError(message) {
    return addToast(message, 'error')
  }

  function showInfo(message) {
    return addToast(message, 'info')
  }

  return {
    toasts,
    showSuccess,
    showError,
    showInfo,
    dismissToast
  }
}
