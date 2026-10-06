<template>
  <div class="role-manager">
    <div class="role-manager__header">
      <h2 class="role-manager__title">{{ $t('admin.roles.title') }}</h2>
      <BaseButton icon="plus" @click="openCreateModal">{{ $t('admin.roles.new') }}</BaseButton>
    </div>

    <ul class="role-manager__grid u-grid-auto" role="list">
      <li v-for="role in rolesData" :key="role.id" class="role-manager__item">
        <BaseCard as="article" padding="sm" :title="role.nome" title-tag="h3" class="role-manager__card">
          <template #actions>
            <BaseButton
              variant="ghost"
              icon="pencil"
              :aria-label="`${$t('common.editar')}: ${role.nome}`"
              @click="handleEditRole(role)"
            />
            <BaseButton
              v-if="!role.is_sistema"
              variant="ghost-danger"
              icon="trash"
              :aria-label="`${$t('common.excluir')}: ${role.nome}`"
              @click="handleConfirmDelete(role)"
            />
          </template>

          <div class="role-manager__badges">
            <BaseBadge v-if="role.is_sistema" variant="info" icon="lock-closed">{{ $t('admin.roles.system') }}</BaseBadge>
            <BaseBadge v-else icon="pencil">{{ $t('admin.roles.custom') }}</BaseBadge>
            <BaseBadge variant="primary" icon="chart-bar">{{ $t('admin.roles.level') }} {{ role.nivel }}</BaseBadge>
          </div>

          <ul v-if="role.permissoes?.length" class="role-manager__permissions" role="list" :aria-label="$t('admin.roles.permissions')">
            <li v-for="perm in role.permissoes" :key="perm.id">
              <BaseBadge size="sm">{{ perm.descricao }}</BaseBadge>
            </li>
          </ul>
          <p v-else class="role-manager__empty">{{ $t('admin.roles.noPermissions') }}</p>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar (permissões agrupadas por recurso) -->
    <BaseModal
      v-model="isModalOpen"
      size="lg"
      :title="editingRole ? $t('admin.roles.edit') : $t('admin.roles.new')"
      @close="handleCloseModal"
    >
      <form id="role-manager-form" class="u-stack" @submit.prevent="handleSaveRole">
        <div class="role-manager__form-row">
          <BaseInput v-model="formData.nome" :label="$t('admin.roles.name')" :placeholder="$t('admin.roles.namePlaceholder')" required />
          <BaseInput
            v-model.number="formData.nivel"
            type="number"
            inputmode="numeric"
            :label="$t('admin.roles.level')"
            :hint="$t('admin.roles.levelHelper')"
            placeholder="0"
          />
        </div>
        <BaseInput
          v-model="formData.descricao"
          :label="$t('admin.roles.description')"
          :placeholder="$t('admin.roles.descriptionPlaceholder')"
        />

        <fieldset class="role-manager__fieldset">
          <legend class="role-manager__legend">{{ $t('admin.roles.permissions') }}</legend>

          <div v-for="(perms, recurso) in groupedPermissions" :key="recurso" class="role-manager__group">
            <h3 class="role-manager__group-title">{{ formatRecurso(recurso) }}</h3>
            <div class="role-manager__checks">
              <BaseCheckbox
                v-for="perm in perms"
                :key="perm.id"
                :model-value="formData.permission_ids.includes(perm.id)"
                :label="perm.descricao"
                @update:model-value="togglePermission(perm.id, $event)"
              />
            </div>
          </div>

          <p v-if="Object.keys(groupedPermissions).length === 0" class="role-manager__empty">
            {{ $t('admin.roles.noPermissionsAvailable') }}
          </p>
        </fieldset>
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="handleCloseModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="role-manager-form" :loading="isSaving">
          {{ isSaving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('admin.roles.confirmDelete')"
      :message="$t('admin.roles.deleteMessage')"
      :warning="$t('confirmModal.irreversible')"
      :item-name="roleToDelete?.nome"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleDeleteRole"
    />
  </div>
</template>

<script setup lang="ts">
import { useRoleManager } from './RoleManager'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseCheckbox,
  BaseInput,
  BaseModal,
  ConfirmModal,
} from '@/shared/components/ui'

const {
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
  handleCloseModal,
} = useRoleManager()

// BaseCheckbox é booleano: adapta para a lista de ids (mesmo efeito do v-model de array anterior)
const togglePermission = (id: number, checked: boolean) => {
  const ids = formData.value.permission_ids
  if (checked && !ids.includes(id)) ids.push(id)
  if (!checked) formData.value.permission_ids = ids.filter((pid) => pid !== id)
}
</script>

<style scoped>
@import '@/styles/components/RoleManager.css';
</style>
