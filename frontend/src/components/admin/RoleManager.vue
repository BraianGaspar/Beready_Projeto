<template>
  <div class="role-manager">
    <div class="role-manager-header">
      <h2>{{ $t('admin.roles.title') }}</h2>
      <button class="btn-primary" @click="openCreateModal">
        <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14" />
        </svg>
        {{ $t('admin.roles.new') }}
      </button>
    </div>

    <div class="role-cards">
      <div v-for="role in rolesData" :key="role.id" class="role-card">
        <div class="role-card-header">
          <div class="role-info">
            <h3>{{ role.nome }}</h3>
            <span class="role-badge" :class="{ sistema: role.is_sistema }">
              {{ role.is_sistema ? $t('admin.roles.system') : $t('admin.roles.custom') }}
            </span>
            <span class="role-level">{{ $t('admin.roles.level') }} {{ role.nivel }}</span>
          </div>
          <div class="role-actions">
            <button @click="handleEditRole(role)" class="btn-icon" :title="$t('common.editar')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
            </button>
            <button
              v-if="!role.is_sistema"
              @click="handleConfirmDelete(role.id)"
              class="btn-icon danger"
              :title="$t('common.excluir')"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
              </svg>
            </button>
          </div>
        </div>
        <div class="role-permissions">
          <span v-for="perm in role.permissoes" :key="perm.id" class="permission-tag">
            {{ perm.descricao }}
          </span>
          <span v-if="!role.permissoes?.length" class="no-permissions">
            {{ $t('admin.roles.noPermissions') }}
          </span>
        </div>
      </div>
    </div>

    <!-- MODAL COM AGRUPAMENTO DINÂMICO DE PERMISSÕES -->
    <div v-if="isModalOpen" class="modal-overlay" @click.self="handleCloseModal">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ editingRole ? $t('admin.roles.edit') : $t('admin.roles.new') }}</h3>
          <button class="modal-close" @click="handleCloseModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleSaveRole" class="modal-form">
          <div class="form-group">
            <label>{{ $t('admin.roles.name') }}</label>
            <input v-model="formData.nome" :placeholder="$t('admin.roles.namePlaceholder')" required />
          </div>

          <div class="form-group">
            <label>{{ $t('admin.roles.description') }}</label>
            <input v-model="formData.descricao" :placeholder="$t('admin.roles.descriptionPlaceholder')" />
          </div>

          <div class="form-group">
            <label>{{ $t('admin.roles.level') }}</label>
            <input v-model.number="formData.nivel" type="number" placeholder="0" />
            <small>{{ $t('admin.roles.levelHelper') }}</small>
          </div>

          <!-- PERMISSÕES AGRUPADAS DINAMICAMENTE -->
          <div class="form-group">
            <label>{{ $t('admin.roles.permissions') }}</label>
            
            <div v-for="(perms, recurso) in groupedPermissions" :key="recurso" class="permission-group">
              <h4 class="permission-group-title">{{ formatRecurso(recurso) }}</h4>
              <div class="permissions-grid">
                <label v-for="perm in perms" :key="perm.id" class="permission-check">
                  <input type="checkbox" :value="perm.id" v-model="formData.permission_ids" />
                  {{ perm.descricao }}
                </label>
              </div>
            </div>

            <div v-if="Object.keys(groupedPermissions).length === 0" class="no-permissions-message">
              {{ $t('admin.roles.noPermissionsAvailable') }}
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="handleCloseModal" class="btn-secondary">{{ $t('common.cancelar') }}</button>
            <button type="submit" class="btn-primary" :disabled="isSaving">
              {{ isSaving ? $t('common.salvando') : $t('common.salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoleManager } from './RoleManager'

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
  handleCloseModal
} = useRoleManager()
</script>

<style scoped>
@import '@/styles/components/RoleManager.css';
</style>