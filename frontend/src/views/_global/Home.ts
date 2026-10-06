import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export function useHome() {
  const router = useRouter()
  const authStore = useAuthStore()

  onMounted(() => {
    if (authStore.isAuthenticated) {
      router.push('/dashboard')
    }
  })

  return {}
}
