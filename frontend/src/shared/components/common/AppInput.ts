import { ref } from 'vue'

export function useAppInput() {
  const showPassword = ref(false)

  const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
  }

  const getInputType = (type: string): string => {
    if (type === 'password' && showPassword.value) {
      return 'text'
    }
    return type
  }

  return {
    showPassword,
    togglePasswordVisibility,
    getInputType
  }
}