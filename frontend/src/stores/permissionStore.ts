// frontend/src/stores/permissionStore.ts

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/core/services/api'

// ============================================
// TYPES
// ============================================
export interface Role {
    id: number
    nome: string
    descricao: string
    nivel: number
    is_sistema: boolean
    is_ativo: boolean
    permissoes: Permission[]
}

export interface Permission {
    id: number
    nome: string
    descricao: string
    recurso: string
    acao: string
    is_ativo: boolean
}

export interface Plano {
    id: number
    nome: string
    descricao: string
    role_id: number | null
    preco_mensal: number
    preco_anual: number
    dias_trial: number
    recursos: string[]
    limites: Record<string, number>
    is_ativo: boolean
    ordem: number
    role?: Role
}

export interface Assinatura {
    id: number
    usuario_id: number
    plano_id: number
    status: 'pending' | 'active' | 'canceled' | 'expired' | 'trial'
    data_inicio: string
    data_fim: string
    data_cancelamento: string | null
    plano?: Plano
}

export interface AssinarPlanoResponse {
    success: boolean
    requires_payment?: boolean
    checkout_url?: string
    data?: {
        assinatura: Assinatura
        requires_payment: boolean
        preco: number
    }
}

// ============================================
// STORE
// ============================================
export const usePermissionStore = defineStore('permissions', () => {
    // State
    const roles = ref<Role[]>([])
    const permissions = ref<Permission[]>([])
    const planos = ref<Plano[]>([])
    const assinaturaAtiva = ref<Assinatura | null>(null)
    const userPermissions = ref<string[]>([])
    const loading = ref(false)

    // ============================================
    // GETTERS - COM FALLBACK PARA ADMIN
    // ============================================
    const isAdmin = computed((): boolean => {
        // Primeiro verifica se tem a permissão admin.access
        if (userPermissions.value.includes('admin.access')) return true
        
        // Fallback: verifica se o usuário tem role 'admin' no localStorage
        try {
            const userData = localStorage.getItem('user')
            if (userData) {
                const user = JSON.parse(userData)
                if (user.role === 'admin') return true
            }
        } catch (e) {
            // Ignora erro
        }
        
        return false
    })

    const hasPermission = (permission: string): boolean => {
        if (isAdmin.value) return true
        return userPermissions.value.includes(permission)
    }

    const hasAnyPermission = (perms: string[]): boolean => {
        if (isAdmin.value) return true
        return perms.some((p: string) => hasPermission(p))
    }

    const hasAllPermissions = (perms: string[]): boolean => {
        if (isAdmin.value) return true
        return perms.every((p: string) => hasPermission(p))
    }

    const canView = (recurso: string): boolean => {
        if (isAdmin.value) return true
        return hasPermission(`${recurso}.view`)
    }

    const canCreate = (recurso: string): boolean => {
        if (isAdmin.value) return true
        return hasPermission(`${recurso}.create`)
    }

    const canEdit = (recurso: string): boolean => {
        if (isAdmin.value) return true
        return hasPermission(`${recurso}.edit`)
    }

    const canDelete = (recurso: string): boolean => {
        if (isAdmin.value) return true
        return hasPermission(`${recurso}.delete`)
    }

    const canManage = (recurso: string): boolean => {
        if (isAdmin.value) return true
        return hasPermission(`${recurso}.manage_all`)
    }

    const getPlanoAtual = computed((): Plano | null => {
        if (!assinaturaAtiva.value) return null
        return planos.value.find((p: Plano) => p.id === assinaturaAtiva.value?.plano_id) || null
    })

    const getLimites = computed((): Record<string, number> => {
        // Admin tem limites ilimitados
        if (isAdmin.value) {
            return { flashcards: 999999, quizes: 999999, prompts: 999999 }
        }
        const plano = getPlanoAtual.value
        if (!plano) return { flashcards: 0, quizes: 0, prompts: 0 }
        return plano.limites
    })

    // ============================================
    // ACTIONS
    // ============================================
    const loadPermissions = async (): Promise<void> => {
        if (loading.value) return

        loading.value = true
        try {
            const token = localStorage.getItem('access_token')
            if (!token) {
                console.warn('Token não encontrado, permissões não carregadas')
                userPermissions.value = []
                loading.value = false
                return
            }

            const response = await api.get('/user/permissions')
            if (response.data.success) {
                userPermissions.value = response.data.data || []
                console.log('Permissões carregadas:', userPermissions.value)
            } else {
                console.warn('Resposta sem sucesso:', response.data)
                userPermissions.value = []
            }
        } catch (error: unknown) {
            console.error('Erro ao carregar permissões:', error)
            userPermissions.value = []
        } finally {
            loading.value = false
        }
    }

    const loadRoles = async (): Promise<void> => {
        try {
            const response = await api.get('/admin/roles')
            if (response.data.success) {
                roles.value = response.data.data
            }
        } catch (error: unknown) {
            console.error('Erro ao carregar roles:', error)
        }
    }

    const loadPermissionsList = async (): Promise<void> => {
        try {
            const response = await api.get('/admin/permissions')
            if (response.data.success) {
                permissions.value = response.data.data
            }
        } catch (error: unknown) {
            console.error('Erro ao carregar permissões:', error)
        }
    }

    const loadPlanos = async (): Promise<void> => {
        try {
            // Usar /admin/planos para ver todos (ativos e inativos)
            const response = await api.get('/admin/planos')
            if (response.data.success) {
                planos.value = response.data.data
            }
        } catch (error: unknown) {
            console.error('Erro ao carregar planos:', error)
        }
    }

    const loadAssinatura = async (): Promise<void> => {
        try {
            const response = await api.get('/user/assinatura')
            if (response.data.success) {
                assinaturaAtiva.value = response.data.data
            }
        } catch (error: unknown) {
            if (error && typeof error === 'object' && 'response' in error) {
                const err = error as { response?: { status?: number } }
                if (err.response?.status !== 404) {
                    console.error('Erro ao carregar assinatura:', error)
                }
            } else {
                console.error('Erro ao carregar assinatura:', error)
            }
        }
    }

    const loadAll = async (): Promise<void> => {
        await Promise.all([
            loadPermissions(),
            loadRoles(),
            loadPermissionsList(),
            loadPlanos(),
            loadAssinatura()
        ])
    }

    const assignRole = async (usuarioId: number, roleId: number): Promise<unknown> => {
        try {
            const response = await api.post('/admin/users/role', {
                user_id: usuarioId,
                role_id: roleId
            })
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao atribuir role:', error)
            throw error
        }
    }

    const createRole = async (data: Partial<Role>): Promise<unknown> => {
        try {
            const response = await api.post('/admin/roles', data)
            if (response.data.success) {
                await loadRoles()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao criar role:', error)
            throw error
        }
    }

    const updateRole = async (roleId: number, data: Partial<Role>): Promise<unknown> => {
        try {
            const response = await api.put(`/admin/roles/${roleId}`, data)
            if (response.data.success) {
                await loadRoles()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao atualizar role:', error)
            throw error
        }
    }

    const updateRolePermissions = async (roleId: number, permissionIds: number[]): Promise<unknown> => {
        try {
            const response = await api.put(`/admin/roles/${roleId}/permissions`, {
                permissions: permissionIds
            })
            if (response.data.success) {
                await loadRoles()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao atualizar permissões:', error)
            throw error
        }
    }

    // Rota para excluir função
    const deleteRole = async (roleId: number): Promise<unknown> => {
        try {
            const response = await api.delete(`/admin/roles/${roleId}`)
            if (response.data.success) {
                await loadRoles()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao excluir role:', error)
            throw error
        }
    }

    const createPlano = async (data: Partial<Plano>): Promise<unknown> => {
        try {
            const response = await api.post('/admin/planos', data)
            if (response.data.success) {
                await loadPlanos()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao criar plano:', error)
            throw error
        }
    }

    // Rota para atualizar plano
    const updatePlano = async (planoId: number, data: Partial<Plano>): Promise<unknown> => {
        try {
            const response = await api.put(`/admin/planos/edit/${planoId}`, data)
            if (response.data.success) {
                await loadPlanos()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao atualizar plano:', error)
            throw error
        }
    }

    // Rotapara deletar plano
    const deletePlano = async (planoId: number): Promise<unknown> => {
        try {
            const response = await api.delete(`/admin/planos/delete/${planoId}`)
            if (response.data.success) {
                await loadPlanos()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao excluir plano:', error)
            throw error
        }
    }

    const assinarPlano = async (planoId: number, ciclo: 'mensal' | 'anual' | 'trial'): Promise<AssinarPlanoResponse> => {
        try {
            const response = await api.post(`/planos/${planoId}/assinar`, { ciclo })
            if (response.data.success) {
                await loadAssinatura()
                await loadPermissions()
            }
            return response.data as AssinarPlanoResponse
        } catch (error: unknown) {
            console.error('Erro ao assinar plano:', error)
            throw error
        }
    }

    const cancelarAssinatura = async (): Promise<unknown> => {
        try {
            const response = await api.post('/planos/cancelar')
            if (response.data.success) {
                await loadAssinatura()
                await loadPermissions()
            }
            return response.data
        } catch (error: unknown) {
            console.error('Erro ao cancelar assinatura:', error)
            throw error
        }
    }

    // ============================================
    // RETURN
    // ============================================
    return {
        // State
        roles,
        permissions,
        planos,
        assinaturaAtiva,
        userPermissions,
        loading,

        // Getters
        isAdmin,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        canView,
        canCreate,
        canEdit,
        canDelete,
        canManage,
        getPlanoAtual,
        getLimites,

        // Actions
        loadPermissions,
        loadRoles,
        loadPermissionsList,
        loadPlanos,
        loadAssinatura,
        loadAll,
        assignRole,
        createRole,
        updateRole,
        updateRolePermissions,
        deleteRole,
        createPlano,
        updatePlano,
        deletePlano,
        assinarPlano,
        cancelarAssinatura
    }
})