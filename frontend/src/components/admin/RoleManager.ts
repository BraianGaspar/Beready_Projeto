// frontend/src/components/admin/RoleManager.ts

import { ref, computed, onMounted } from 'vue'
import { usePermissionStore, type Role, type Permission } from '@/stores/permissionStore'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'

export function useRoleManager() {
    const permissionStore = usePermissionStore()
    const { success, error } = useAlert()
    const { t } = useI18n()

    const rolesData = computed(() => permissionStore.roles)
    const permissionsData = computed(() => permissionStore.permissions)

    const isModalOpen = ref(false)
    const editingRole = ref<Role | null>(null)
    const isSaving = ref(false)

    const formData = ref({
        nome: '',
        descricao: '',
        nivel: 0,
        permission_ids: [] as number[]
    })

    // Grupo de permissoes dinamico
    const groupedPermissions = computed(() => {
        const groups: Record<string, Permission[]> = {}
        
        permissionsData.value.forEach((perm: Permission) => {
            const recurso = perm.recurso || 'geral'
            if (!groups[recurso]) {
                groups[recurso] = []
            }
            groups[recurso].push(perm)
        })
        
        return groups
    })

    const formatRecurso = (recurso: string): string => {
        const map: Record<string, string> = {
            'admin': 'Administracao',
            'flashcards': 'Flashcards',
            'quizes': 'Quizzes',
            'prompts': 'Prompts IA'
        }
        return map[recurso] || recurso
    }

    const resetForm = (): void => {
        editingRole.value = null
        formData.value = {
            nome: '',
            descricao: '',
            nivel: 0,
            permission_ids: []
        }
    }

    const openCreateModal = (): void => {
        resetForm()
        isModalOpen.value = true
    }

    const handleEditRole = (role: Role): void => {
        editingRole.value = role
        formData.value = {
            nome: role.nome,
            descricao: role.descricao || '',
            nivel: role.nivel || 0,
            permission_ids: role.permissoes?.map((p: Permission) => p.id) || []
        }
        isModalOpen.value = true
    }

    const handleSaveRole = async (): Promise<void> => {
        isSaving.value = true
        try {
            const data = {
                nome: formData.value.nome,
                descricao: formData.value.descricao,
                nivel: formData.value.nivel,
                permission_ids: formData.value.permission_ids
            }

            if (editingRole.value) {
                await permissionStore.updateRole(editingRole.value.id, data)
                success(t('admin.roles.updateSuccess'))
            } else {
                await permissionStore.createRole(data)
                success(t('admin.roles.createSuccess'))
            }

            handleCloseModal()
        } catch (err: unknown) {
            const errorMessage = err && typeof err === 'object' && 'response' in err
                ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
                : t('admin.roles.errorSave')
            error(errorMessage || t('admin.roles.errorSave'))
        } finally {
            isSaving.value = false
        }
    }

    const handleConfirmDelete = async (roleId: number): Promise<void> => {
        if (!confirm(t('admin.roles.confirmDelete') + '?')) return
        try {
            await permissionStore.deleteRole(roleId)
            success(t('admin.roles.deleteSuccess'))
        } catch (err: unknown) {
            const errorMessage = err && typeof err === 'object' && 'response' in err
                ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
                : t('admin.roles.errorDelete')
            error(errorMessage || t('admin.roles.errorDelete'))
        }
    }

    const handleCloseModal = (): void => {
        isModalOpen.value = false
        resetForm()
    }

    onMounted(() => {
        permissionStore.loadRoles()
        permissionStore.loadPermissionsList()
    })

    return {
        rolesData,
        groupedPermissions,
        formatRecurso,
        isModalOpen,
        editingRole,
        isSaving,
        formData,
        openCreateModal,
        handleEditRole,
        handleSaveRole,
        handleConfirmDelete,
        handleCloseModal
    }
}