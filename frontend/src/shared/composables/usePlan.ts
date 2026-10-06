// frontend/src/shared/composables/usePlan.ts
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { usePermissionStore } from '@/stores/permissionStore'

// Limites a partir deste valor (ou negativos, ex.: -1) são tratados como ilimitados
const UNLIMITED_THRESHOLD = 999999

const isUnlimited = (limit: number): boolean => limit < 0 || limit >= UNLIMITED_THRESHOLD

/**
 * Plano/limites do usuário logado, a partir da assinatura carregada no
 * permissionStore (GET /user/assinatura), que é carregada após login e no boot.
 */
export function usePlan() {
  const authStore = useAuthStore()
  const permissionStore = usePermissionStore()

  const currentPlan = computed(
    () => permissionStore.assinaturaAtiva?.plano ?? permissionStore.getPlanoAtual,
  )

  const isFree = computed(() => currentPlan.value?.nome === 'Gratuito')
  const isTrial = computed(() => currentPlan.value?.nome === 'Trial')
  const isPremium = computed(() => currentPlan.value?.nome === 'Premium')

  const isAdmin = computed(() => authStore.isAdmin || permissionStore.isAdmin)

  const hasFeature = (feature: string): boolean => {
    if (isAdmin.value) return true
    return currentPlan.value?.recursos?.includes(feature) ?? false
  }

  // -1 = ilimitado
  const getLimit = (key: string): number => {
    if (isAdmin.value) return -1
    const limit = Number(currentPlan.value?.limites?.[key] ?? 0)
    return isUnlimited(limit) ? -1 : limit
  }

  const canCreateMore = (key: string, currentCount: number): boolean => {
    const limit = getLimit(key)
    if (limit === -1) return true
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
    canCreateMore,
  }
}
