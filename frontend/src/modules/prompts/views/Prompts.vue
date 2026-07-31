<template>
  <div class="prompts-page">
    <div class="prompts-hero">
      <button class="hero-back-btn" @click="$router.push('/dashboard')">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
        {{ $t('common.voltar') }}
      </button>
      <div class="hero-content">
        <div class="hero-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
            />
          </svg>
        </div>
        <h1 class="hero-title">{{ $t('prompts.title') }}</h1>
        <p class="hero-subtitle">{{ $t('prompts.subtitle') }}</p>
        <!-- Botão removido daqui -->
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ $t('prompts.carregando') }}</p>
    </div>

    <!-- Sem Permissao -->
    <div v-else-if="!canView" class="no-permission-state">
      <div class="no-permission-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h2 class="no-permission-title">{{ $t('common.acessoNegado') }}</h2>
      <p class="no-permission-description">{{ $t('common.permissionDenied') }}</p>
      <button class="no-permission-btn" @click="$router.push('/dashboard')">{{ $t('common.voltarDashboard') }}</button>
    </div>

    <!-- Empty -->
    <div v-else-if="prompts.length === 0" class="empty-state">
      <div class="empty-icon">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
          />
        </svg>
      </div>
      <h2 class="empty-title">{{ $t('prompts.emptyTitle') }}</h2>
      <p class="empty-description">{{ $t('prompts.emptyDescription') }}</p>
      <div class="empty-actions">
        <button 
          v-if="canCreatePrompt" 
          class="empty-btn" 
          @click="openModal"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
          {{ $t('prompts.createFirst') }}
        </button>
        <div v-else class="empty-btn-disabled">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
          {{ $t('prompts.createFirst') }}
          <span class="limit-badge">{{ $t('common.limiteAtingido') }}</span>
        </div>
      </div>
      <div v-if="!canCreateMorePrompts" class="limit-message empty-limit">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>{{ $t('prompts.limitReached') }}</span>
        <button class="upgrade-link" @click="$router.push('/planos')">
          {{ $t('prompts.upgradeToCreateMore') }}
        </button>
      </div>
    </div>

    <!-- Grid -->
    <div v-else class="prompts-grid">
      <!-- Card de criar -->
      <div 
        v-if="canCreatePrompt" 
        class="prompt-card create-card" 
        @click="openModal"
      >
        <div class="create-card-content">
          <div class="create-card-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
          </div>
          <h3 class="create-card-title">{{ $t('prompts.newPrompt') }}</h3>
          <p class="create-card-subtitle">{{ $t('prompts.createSubtitle') }}</p>
        </div>
      </div>
      <!-- Card de criar desabilitado -->
      <div v-else class="prompt-card create-card create-card-disabled">
        <div class="create-card-content">
          <div class="create-card-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
          </div>
          <h3 class="create-card-title">{{ $t('prompts.newPrompt') }}</h3>
          <p class="create-card-subtitle">{{ $t('prompts.createSubtitle') }}</p>
          <span class="create-limit">{{ $t('common.limiteAtingido') }}</span>
        </div>
      </div>

      <div v-for="prompt in prompts" :key="prompt.id" class="prompt-card">
        <div class="prompt-header">
          <div class="prompt-badge">
            <span class="badge">{{ getLanguageName(prompt.idioma_original) }}</span>
          </div>
          <div class="prompt-actions">
            <button v-if="canEdit" class="btn-icon edit" @click="editPrompt(prompt)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                />
              </svg>
            </button>
            <button v-if="canDelete" class="btn-icon delete" @click="confirmDelete(prompt)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
              </svg>
            </button>
          </div>
        </div>
        <div class="prompt-content">
          <p class="prompt-text">{{ prompt.texto_original }}</p>
          <div class="prompt-meta">
            <span class="meta-contexto">{{ getContextName(prompt.contexto) }}</span>
            <span class="meta-data">{{ formatDate(prompt.criado_em) }}</span>
          </div>
        </div>
        <div class="prompt-footer">
          <button class="btn-translate" @click="viewTranslations(prompt.id)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
              />
            </svg>
            {{ $t('prompts.verTraducoes') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-container">
        <div class="modal-header">
          <div>
            <h2 class="modal-title">
              {{ editingPrompt ? $t('prompts.editPrompt') : $t('prompts.newPrompt') }}
            </h2>
            <p class="modal-subtitle">
              {{ editingPrompt ? $t('prompts.editSubtitle') : $t('prompts.createSubtitle') }}
            </p>
          </div>
          <button class="modal-close" @click="closeModal">×</button>
        </div>
        <form @submit.prevent="savePrompt">
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">{{ $t('prompts.textoOriginal') }} *</label>
              <textarea
                v-model="form.texto_original"
                rows="4"
                required
                class="form-textarea"
                :placeholder="$t('prompts.textoPlaceholder')"
              ></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('prompts.idiomaOriginal') }}</label>
              <select v-model="form.idioma_original" class="form-select">
                <option value="pt-BR">{{ $t('idiomas.pt') }}</option>
                <option value="en">{{ $t('idiomas.en') }}</option>
                <option value="es">{{ $t('idiomas.es') }}</option>
                <option value="fr">{{ $t('idiomas.fr') }}</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('prompts.contexto') }}</label>
              <select v-model="form.contexto" class="form-select">
                <option value="manual">{{ $t('prompts.contextoManual') }}</option>
                <option value="conversacao">{{ $t('prompts.contextoConversacao') }}</option>
                <option value="negocios">{{ $t('prompts.contextoNegocios') }}</option>
                <option value="viagem">{{ $t('prompts.contextoViagem') }}</option>
                <option value="estudo">{{ $t('prompts.contextoEstudo') }}</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="closeModal">
              {{ $t('common.cancelar') }}
            </button>
            <button type="submit" class="btn-create" :disabled="saving">
              {{ saving ? $t('common.salvando') : $t('common.salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('prompts.confirmDelete')"
      :message="$t('prompts.deleteMessage')"
      :item-name="promptToDelete?.texto_original?.substring(0, 50)"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { usePrompts } from './Prompts'
import ConfirmModal from '@/shared/components/common/ConfirmModal.vue'

const {
  loading,
  prompts,
  modalOpen,
  saving,
  form,
  editingPrompt,
  openModal,
  editPrompt,
  closeModal,
  savePrompt,
  confirmDelete,
  formatDate,
  viewTranslations,
  confirmModalVisible,
  promptToDelete,
  deleting,
  handleConfirmDelete,
  getLanguageName,
  getContextName,
  canView,
  canEdit,
  canDelete,
  canCreatePrompt,
  canCreateMorePrompts,
} = usePrompts()
</script>

<style scoped>
@import '@/styles/views/prompts/prompts.css';
</style>