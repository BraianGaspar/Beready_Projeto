// src/shared/composables/usePhoneMask.ts
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

// Formata dígitos de telefone brasileiro: (99) 99999-9999 / (99) 9999-9999,
// incluindo formatação parcial enquanto o usuário digita
const maskDigits = (digits: string): string => {
  if (digits.length === 11) return digits.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
  if (digits.length === 10) return digits.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
  if (digits.length > 6) return digits.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
  if (digits.length > 2) return digits.replace(/(\d{2})(\d{0,5})/, '($1) $2')
  return digits
}

export const formatPhone = (phone?: string | null): string => {
  if (!phone) return ''
  const digits = phone.replace(/\D/g, '')
  return digits.length === 10 || digits.length === 11 ? maskDigits(digits) : phone
}

export function usePhoneMask() {
  const { t } = useI18n()
  const phoneError = ref('')

  const handlePhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement
    const digits = input.value.replace(/\D/g, '').slice(0, 11)

    input.value = maskDigits(digits)

    if (digits.length > 0 && digits.length < 11) {
      phoneError.value = t('register.phoneInvalid')
    } else {
      phoneError.value = ''
    }

    return input.value
  }

  const handlePhoneKeydown = (event: KeyboardEvent) => {
    const allowedKeys = [
      'Backspace',
      'Delete',
      'ArrowLeft',
      'ArrowRight',
      'Tab',
      'Escape',
      'Enter',
      'Home',
      'End',
    ]
    if (allowedKeys.includes(event.key)) return
    if (!/^[0-9]$/.test(event.key)) {
      event.preventDefault()
    }
  }

  return {
    phoneError,
    handlePhoneInput,
    handlePhoneKeydown,
    formatPhone,
  }
}
