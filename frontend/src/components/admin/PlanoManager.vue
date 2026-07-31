<template>
  <div class="plano-manager">
    <div class="plano-manager-header">
      <h2>{{ $t('admin.planos.title') }}</h2>
      <div class="header-actions">
        <div class="filter-group">
          <button 
            class="filter-btn" 
            :class="{ active: filterStatus === 'all' }"
            @click="filterStatus = 'all'"
          >
            {{ $t('admin.planos.all') }}
          </button>
          <button 
            class="filter-btn active-filter" 
            :class="{ active: filterStatus === 'active' }"
            @click="filterStatus = 'active'"
          >
            {{ $t('admin.planos.active') }}
          </button>
          <button 
            class="filter-btn inactive-filter" 
            :class="{ active: filterStatus === 'inactive' }"
            @click="filterStatus = 'inactive'"
          >
            {{ $t('admin.planos.inactive') }}
          </button>
        </div>
        <button class="btn-primary" @click="openCreateModal">
          <svg class="icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 4v16m8-8H4" />
          </svg>
          {{ $t('admin.planos.new') }}
        </button>
      </div>
    </div>

    <div class="planos-grid">
      <div v-for="plano in filteredPlanos" :key="plano.id" class="plano-card">
        <div class="plano-card-header">
          <div class="plano-info">
            <h3>{{ plano.nome }}</h3>
          </div>
          <span :class="['plano-status', plano.is_ativo ? 'ativo' : 'inativo']">
            {{ plano.is_ativo ? $t('admin.planos.active') : $t('admin.planos.inactive') }}
          </span>
        </div>

        <div class="plano-body">
          <p class="plano-descricao">{{ plano.descricao || $t('common.semDescricao') }}</p>

          <div class="plano-precos">
            <div class="preco-item">
              <span class="preco-label">{{ $t('admin.planos.monthly') }}</span>
              <span class="preco-valor">R$ {{ Number(plano.preco_mensal)?.toFixed(2) || '0,00' }}</span>
            </div>
            <div class="preco-item">
              <span class="preco-label">{{ $t('admin.planos.yearly') }}</span>
              <span class="preco-valor">R$ {{ Number(plano.preco_anual)?.toFixed(2) || '0,00' }}</span>
            </div>
            <div class="preco-item" v-if="plano.dias_trial > 0">
              <span class="preco-label">{{ $t('admin.planos.trialDays') }}</span>
              <span class="preco-valor">{{ plano.dias_trial }} dias</span>
            </div>
          </div>

          <div class="plano-role">
            <span class="role-label">{{ $t('admin.planos.role') }}:</span>
            <span class="role-value">{{ plano.role?.nome || $t('admin.planos.noRole') }}</span>
            <span v-if="plano.role?.nivel" class="role-level-badge">Nível {{ plano.role.nivel }}</span>
          </div>

          <div class="plano-recursos" v-if="plano.recursos?.length">
            <h4>{{ $t('admin.planos.resources') }}</h4>
            <div class="recursos-tags">
              <span v-for="recurso in plano.recursos" :key="recurso" class="recurso-tag">
                {{ formatRecurso(recurso) }}
              </span>
            </div>
          </div>

          <div class="plano-limites" v-if="Object.keys(plano.limites || {}).length">
            <h4>{{ $t('admin.planos.limits') }}</h4>
            <div class="limites-grid">
              <div v-for="(value, key) in plano.limites" :key="key" class="limite-item">
                <span class="limite-chave">{{ formatLimiteKey(key) }}:</span>
                <span class="limite-valor">{{ value === -1 ? '♾️ Ilimitado' : value }}</span>
              </div>
            </div>
          </div>

          <div class="plano-ordem" v-if="plano.ordem !== undefined">
            <span class="ordem-label">{{ $t('admin.planos.order') }}:</span>
            <span class="ordem-valor">{{ plano.ordem }}</span>
          </div>
        </div>

        <div class="plano-card-footer">
          <button class="btn-edit" @click="handleEditPlano(plano)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            {{ $t('common.editar') }}
          </button>
          <button 
            class="btn-toggle" 
            @click="handleToggleStatus(plano)"
            :disabled="togglingStatus === plano.id"
          >
            {{ togglingStatus === plano.id ? '...' : (plano.is_ativo ? $t('admin.planos.deactivate') : $t('admin.planos.activate')) }}
          </button>
          <button class="btn-delete" @click="confirmDelete(plano)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
            </svg>
            {{ $t('common.excluir') }}
          </button>
        </div>
      </div>

      <div v-if="!filteredPlanos.length" class="empty-state">
        <p>{{ $t('common.empty') }}</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="modal-overlay" @click.self="handleCloseModal">
      <div class="modal-container">
        <div class="modal-header">
          <h3>{{ editingPlano ? $t('admin.planos.edit') : $t('admin.planos.new') }}</h3>
          <button class="modal-close" @click="handleCloseModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleSavePlano" class="modal-form">
          <div class="form-row">
            <div class="form-group">
              <label>{{ $t('admin.planos.name') }}</label>
              <input v-model="formData.nome" :placeholder="$t('admin.planos.namePlaceholder')" required />
            </div>
            <div class="form-group">
              <label>{{ $t('admin.planos.description') }}</label>
              <input v-model="formData.descricao" :placeholder="$t('admin.planos.descriptionPlaceholder')" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>{{ $t('admin.planos.role') }}</label>
              <select v-model="formData.role_id">
                <option :value="null">{{ $t('admin.planos.noRole') }}</option>
                <option v-for="role in rolesData" :key="role.id" :value="role.id">
                  {{ role.nome }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>{{ $t('admin.planos.order') }}</label>
              <input v-model.number="formData.ordem" type="number" placeholder="0" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>{{ $t('admin.planos.monthlyPrice') }}</label>
              <input v-model.number="formData.preco_mensal" type="number" step="0.01" placeholder="0.00" />
            </div>
            <div class="form-group">
              <label>{{ $t('admin.planos.yearlyPrice') }}</label>
              <input v-model.number="formData.preco_anual" type="number" step="0.01" placeholder="0.00" />
            </div>
            <div class="form-group">
              <label>{{ $t('admin.planos.trialDays') }}</label>
              <input v-model.number="formData.dias_trial" type="number" placeholder="0" />
            </div>
          </div>

          <div class="form-group">
            <label>{{ $t('admin.planos.resources') }}</label>
            <input v-model="recursosTextData" :placeholder="$t('admin.planos.resourcesPlaceholder')" />
            <small class="form-hint">Separe por vírgula. Ex: flashcards_ilimitados, quizes_ilimitados</small>
          </div>

          <div class="form-group">
            <label>{{ $t('admin.planos.limits') }}</label>
            <textarea v-model="limitesTextData" placeholder='{"flashcards": 100, "quizes": 50, "prompts": 10}' rows="3"></textarea>
            <small class="form-hint">Use formato JSON. -1 = ilimitado</small>
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

    <!-- Confirm Modal para exclusão -->
    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('admin.planos.confirmDelete')"
      :message="$t('admin.planos.deleteMessage')"
      :item-name="planoToDelete?.nome"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { usePlanoManager } from './PlanoManager'
import ConfirmModal from '@/shared/components/common/ConfirmModal.vue'

const {
  filteredPlanos,
  filterStatus,
  rolesData,
  isModalOpen,
  editingPlano,
  isSaving,
  formData,
  recursosTextData,
  limitesTextData,
  confirmModalVisible,
  planoToDelete,
  deleting,
  togglingStatus,
  openCreateModal,
  handleEditPlano,
  handleSavePlano,
  handleToggleStatus,
  confirmDelete,
  handleConfirmDelete,
  handleCloseModal,
  formatRecurso,
  formatLimiteKey,
} = usePlanoManager()
</script>

<style scoped>
@import '@/styles/components/PlanoManager.css';
</style>