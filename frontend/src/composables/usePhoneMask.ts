import { ref } from 'vue'

export function usePhoneMask() {
  const phone = ref('')
  const phoneError = ref('')

  const applyPhoneMask = (value: string): string => {
    // Remove tudo que não é dígito
    let digits = value.replace(/\D/g, '')

    // Limita a 11 dígitos (DDD + número)
    if (digits.length > 11) {
      digits = digits.slice(0, 11)
    }

    if (digits.length === 0) {
      return ''
    }
    if (digits.length <= 2) {
      return `(${digits}`
    }
    if (digits.length <= 7) {
      return `(${digits.slice(0, 2)})${digits.slice(2)}`
    }

    return `(${digits.slice(0, 2)})${digits.slice(2, 7)}-${digits.slice(7)}`
  }

  const validatePhone = (value: string): string | null => {
    const digits = value.replace(/\D/g, '')

    if (!value) {
      return null
    }
    if (digits.length === 0) {
      return 'Telefone inválido'
    }
    if (digits.length > 0 && digits.length < 11) {
      return 'Telefone deve ter 11 dígitos'
    }
    if (digits.length === 11) {
      return null
    }
    return null
  }

  const formatPhone = (value: string) => {
    const masked = applyPhoneMask(value)
    phone.value = masked
    phoneError.value = validatePhone(masked) || ''
    return masked
  }

  const handlePhoneInput = (event: Event): string => {
    const target = event.target as HTMLInputElement
    const rawValue = target.value
    const masked = applyPhoneMask(rawValue)
    target.value = masked
    phone.value = masked
    phoneError.value = validatePhone(masked) || ''
    return masked
  }

  const handlePhoneKeydown = (event: KeyboardEvent) => {
    const allowedKeys = [
      'Backspace',
      'Delete',
      'ArrowLeft',
      'ArrowRight',
      'ArrowUp',
      'ArrowDown',
      'Tab',
      'Home',
      'End',
    ]

    if (allowedKeys.includes(event.key)) {
      return
    }

    if (!/^[0-9]$/.test(event.key)) {
      event.preventDefault()
    }
  }

  return {
    phone,
    phoneError,
    applyPhoneMask,
    validatePhone,
    formatPhone,
    handlePhoneInput,
    handlePhoneKeydown,
  }
}
