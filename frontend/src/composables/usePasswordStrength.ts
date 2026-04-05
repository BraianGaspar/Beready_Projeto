import { ref } from 'vue'

export function usePasswordStrength() {
  const strengthClass = ref('')
  const strengthText = ref('Força da senha')
  const strengthWidth = ref('0%')

  const checkPasswordStrength = (password: string) => {
    // 🔥 CORREÇÃO: Garantir que password é string
    const pwd = String(password || '')

    if (!pwd) {
      strengthText.value = 'Força da senha'
      strengthClass.value = ''
      strengthWidth.value = '0%'
      return
    }

    let strength = 0

    if (pwd.length >= 8) strength++
    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++
    if (/\d/.test(pwd)) strength++
    if (/[^a-zA-Z\d]/.test(pwd)) strength++

    switch (strength) {
      case 0:
        strengthText.value = 'Força da senha: Muito Fraca'
        strengthClass.value = 'weak'
        strengthWidth.value = '25%'
        break
      case 1:
        strengthText.value = 'Força da senha: Fraca'
        strengthClass.value = 'weak'
        strengthWidth.value = '25%'
        break
      case 2:
        strengthText.value = 'Força da senha: Moderada'
        strengthClass.value = 'medium'
        strengthWidth.value = '50%'
        break
      case 3:
        strengthText.value = 'Força da senha: Forte'
        strengthClass.value = 'strong'
        strengthWidth.value = '75%'
        break
      case 4:
        strengthText.value = 'Força da senha: Muito Forte'
        strengthClass.value = 'very-strong'
        strengthWidth.value = '100%'
        break
    }
  }

  return { strengthClass, strengthText, strengthWidth, checkPasswordStrength }
}
