import { ref, watch } from 'vue'

export function usePhoneMask() {
  const phone = ref('')
  const phoneError = ref('')

  const applyPhoneMask = (value: string): string => {
    // Remove tudo que não é dígito
    let digits = value.replace(/\D/g, '')

    // Limita a 11 dígitos (99 99999-9999)
    if (digits.length > 11) {
      digits = digits.slice(0, 11)
    }

    // Aplica a máscara
    if (digits.length <= 2) {
      return digits.replace(/^(\d{0,2})/, '($1')
    } else if (digits.length <= 7) {
      return digits.replace(/^(\d{2})(\d{0,5})/, '($1) $2')
    } else {
      return digits.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
    }
  }

  const validatePhone = (value: string): string | null => {
    const digits = value.replace(/\D/g, '')

    if (value && digits.length === 0) {
      return 'Telefone inválido'
    }
    if (value && digits.length > 0 && digits.length < 10) {
      return 'Telefone deve ter pelo menos 10 dígitos'
    }
    if (value && digits.length === 10) {
      // Telefone fixo (99) 9999-9999
      return null
    }
    if (value && digits.length === 11) {
      // Celular (99) 99999-9999
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

  const handlePhoneInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    const rawValue = target.value
    const masked = applyPhoneMask(rawValue)
    target.value = masked
    phone.value = masked
    phoneError.value = validatePhone(masked) || ''
  }

  return {
    phone,
    phoneError,
    applyPhoneMask,
    validatePhone,
    formatPhone,
    handlePhoneInput,
  }
}
