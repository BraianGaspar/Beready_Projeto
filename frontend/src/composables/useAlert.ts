import { ref, readonly } from 'vue'

interface AlertMessage {
  id: number
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  duration: number
}

const alerts = ref<AlertMessage[]>([])
let nextId = 0

export function useAlert() {
  const removeAlert = (id: number) => {
    const index = alerts.value.findIndex((a) => a.id === id)
    if (index !== -1) {
      alerts.value.splice(index, 1)
    }
  }

  const clearAllAlerts = () => {
    alerts.value = []
  }

  const showAlert = (options: {
    message: string
    type?: 'success' | 'error' | 'warning' | 'info'
    duration?: number
  }) => {
    const id = nextId++
    const duration = options.duration || 1500

    const alert: AlertMessage = {
      id,
      message: options.message,
      type: options.type || 'info',
      duration,
    }

    alerts.value.push(alert)

    setTimeout(() => {
      removeAlert(id)
    }, duration + 100)

    return id
  }

  const success = (message: string, duration?: number) => {
    return showAlert({ message, type: 'success', duration })
  }

  const error = (message: string, duration?: number) => {
    return showAlert({ message, type: 'error', duration })
  }

  const warning = (message: string, duration?: number) => {
    return showAlert({ message, type: 'warning', duration })
  }

  const info = (message: string, duration?: number) => {
    return showAlert({ message, type: 'info', duration })
  }

  return {
    alerts: readonly(alerts),
    showAlert,
    removeAlert,
    clearAllAlerts,
    success,
    error,
    warning,
    info,
  }
}
