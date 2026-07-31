import { usePermissionStore } from '@/stores/permissionStore'
import { usePlan } from '@/shared/composables/usePlan'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'

export function usePermissionGuard() {
  const permissionStore = usePermissionStore()
  const plan = usePlan()
  const { error } = useAlert()
  const { t } = useI18n()

  const checkPermission = (recurso: string, acao: 'view' | 'create' | 'edit' | 'delete' | 'manage'): boolean => {
    const hasPerm = permissionStore.hasPermission(`${recurso}.${acao}`)
    if (!hasPerm) {
      error(t('permissions.denied', { recurso, acao }))
      return false
    }
    return true
  }

  const checkPlanLimit = (recurso: string, currentCount: number): boolean => {
    const canCreate = plan.canCreateMore(recurso, currentCount)
    if (!canCreate) {
      error(t('plan.limitReached', { recurso }))
      return false
    }
    return true
  }

  const checkResourceAccess = (recurso: string, acao: 'view' | 'create' | 'edit' | 'delete' | 'manage', currentCount?: number): boolean => {
    // Verificar permissão primeiro
    if (!checkPermission(recurso, acao)) {
      return false
    }

    // Se for criação, verificar limite do plano
    if (acao === 'create' && currentCount !== undefined) {
      return checkPlanLimit(recurso, currentCount)
    }

    return true
  }

  return {
    checkPermission,
    checkPlanLimit,
    checkResourceAccess
  }
}