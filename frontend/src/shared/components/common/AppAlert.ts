import { ref, onMounted } from 'vue'

export function useAppAlert() {
  const visible = ref(true)

  const close = () => {
    visible.value = false
  }

  return {
    visible,
    close
  }
}