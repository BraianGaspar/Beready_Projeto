// frontend/src/shared/composables/usePlan.ts
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useAuth } from './useAuth'

export function usePlan() {
  const authStore = useAuthStore()
  const { user } = useAuth()
  
  // Usa o user do useAuth que tem a tipagem correta
  const currentPlan = computed(() => {
    return user.value?.assinatura?.plano
  })
  
  const isFree = computed(() => currentPlan.value?.nome === 'Gratuito')
  const isTrial = computed(() => currentPlan.value?.nome === 'Trial')
  const isPremium = computed(() => currentPlan.value?.nome === 'Premium')
  
  // Verifica se o usuário é admin
  const isAdmin = computed(() => user.value?.role === 'admin')
  
  const hasFeature = (feature: string) => {
    // Admin tem todas as features
    if (isAdmin.value) return true
    return currentPlan.value?.recursos?.includes(feature) || false
  }
  
  const getLimit = (key: string) => {
    // Admin tem limite infinito
    if (isAdmin.value) return -1
    return currentPlan.value?.limites?.[key] || 0
  }
  
  const canCreateMore = (key: string, currentCount: number) => {
    const limit = getLimit(key)
    // Admin ou -1 = ilimitado
    if (isAdmin.value || limit === -1) return true
    return currentCount < limit
  }
  
  return {
    currentPlan,
    isFree,
    isTrial,
    isPremium,
    isAdmin,
    hasFeature,
    getLimit,
    canCreateMore
  }
}