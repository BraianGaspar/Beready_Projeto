// frontend/src/components/admin/RoleManager.ts

import { ref, computed, onMounted } from 'vue'
import { usePermissionStore, type Role, type Permission } from '@/stores/permissionStore'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'

export function useRoleManager() {
    const permissionStore = usePermissionStore()
    const { success, error } = useAlert()
    const { t, te } = useI18n()

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
        const key = `admin.roles.groups.${recurso}`
        return te(key) ? t(key) : recurso
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

    // Confirmação de exclusão via ConfirmModal (antes: window.confirm nativo)
    const confirmModalVisible = ref(false)
    const roleToDelete = ref<Role | null>(null)
    const deleting = ref(false)

    const handleConfirmDelete = (role: Role): void => {
        roleToDelete.value = role
        confirmModalVisible.value = true
    }

    const handleDeleteRole = async (): Promise<void> => {
        if (!roleToDelete.value) return
        deleting.value = true
        try {
            await permissionStore.deleteRole(roleToDelete.value.id)
            success(t('admin.roles.deleteSuccess'))
        } catch (err: unknown) {
            const errorMessage = err && typeof err === 'object' && 'response' in err
                ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
                : t('admin.roles.errorDelete')
            error(errorMessage || t('admin.roles.errorDelete'))
        } finally {
            deleting.value = false
            confirmModalVisible.value = false
            roleToDelete.value = null
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
        handleDeleteRole,
        confirmModalVisible,
        roleToDelete,
        deleting,
        handleCloseModal
    }
}