import { computed } from 'vue'
import { usePermissionStore, type Role, type Permission } from '@/stores/permissionStore'

export function usePermissions() {
  const permissionStore = usePermissionStore()
  
  // Getters
  const userRoles = computed<Role[]>(() => permissionStore.roles)
  const permissions = computed<string[]>(() => permissionStore.userPermissions)
  const isAdmin = computed<boolean>(() => permissionStore.isAdmin)
  
  // Verificar se tem uma permissão específica
  const hasPermission = (permission: string): boolean => {
    return permissionStore.hasPermission(permission)
  }
  
  // Verificar se tem pelo menos uma das permissões
  const hasAnyPermission = (perms: string[]): boolean => {
    return permissionStore.hasAnyPermission(perms)
  }
  
  // Verificar se tem todas as permissões
  const hasAllPermissions = (perms: string[]): boolean => {
    return permissionStore.hasAllPermissions(perms)
  }
  
  // Verificar nível da role
  const hasMinLevel = (minLevel: number): boolean => {
    return userRoles.value.some((role: Role) => role.nivel >= minLevel)
  }
  
  // Helpers específicos
  const canView = (recurso: string): boolean => {
    return permissionStore.canView(recurso)
  }
  
  const canCreate = (recurso: string): boolean => {
    return permissionStore.canCreate(recurso)
  }
  
  const canEdit = (recurso: string): boolean => {
    return permissionStore.canEdit(recurso)
  }
  
  const canDelete = (recurso: string): boolean => {
    return permissionStore.canDelete(recurso)
  }
  
  const canManage = (recurso: string): boolean => {
    return permissionStore.canManage(recurso)
  }
  
  return {
    // State
    userRoles,
    permissions,
    isAdmin,
    
    // Methods
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasMinLevel,
    canView,
    canCreate,
    canEdit,
    canDelete,
    canManage,
  }
}