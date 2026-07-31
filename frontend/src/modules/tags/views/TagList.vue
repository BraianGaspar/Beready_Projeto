<template>
  <div class="tags-page">
    <div class="tags-hero">
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
              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"
            />
          </svg>
        </div>
        <h1 class="hero-title">{{ $t('tags.title') }}</h1>
        <p class="hero-subtitle">{{ $t('tags.subtitle') }}</p>
        <button class="hero-btn" @click="openModal">
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
          {{ $t('tags.newTag') }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ $t('tags.carregando') }}</p>
    </div>

    <div v-else-if="tags.length === 0" class="empty-state">
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
            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"
          />
        </svg>
      </div>
      <h2 class="empty-title">{{ $t('tags.emptyTitle') }}</h2>
      <p class="empty-description">{{ $t('tags.emptyDescription') }}</p>
      <button class="empty-btn" @click="openModal">
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
        {{ $t('tags.createFirst') }}
      </button>
    </div>

    <div v-else class="tags-grid">
      <div v-for="tag in tags" :key="tag.id" class="tag-card" :style="{ borderLeftColor: tag.cor }">
        <div class="tag-info">
          <h3 class="tag-name">{{ tag.nome }}</h3>
          <p class="tag-description">{{ tag.descricao || $t('tags.semDescricao') }}</p>
          <div class="tag-meta">
            <span class="tag-color" :style="{ backgroundColor: tag.cor }"></span>
          </div>
        </div>
        <div class="tag-actions">
          <button class="btn-icon edit" @click="editTag(tag)">
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
          <button class="btn-icon delete" @click="confirmDelete(tag)">
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
    </div>

    <!-- Modal -->
    <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-container">
        <div class="modal-header">
          <div>
            <h2 class="modal-title">
              {{ editingTag ? $t('tags.editTag') : $t('tags.newTag') }}
            </h2>
            <p class="modal-subtitle">
              {{ editingTag ? $t('tags.editSubtitle') : $t('tags.createSubtitle') }}
            </p>
          </div>
          <button class="modal-close" @click="closeModal">×</button>
        </div>
        <form @submit.prevent="saveTag">
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">{{ $t('tags.nome') }} *</label>
              <input
                v-model="form.nome"
                type="text"
                required
                class="form-input"
                :placeholder="$t('tags.nomePlaceholder')"
              />
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('tags.cor') }}</label>
              <input v-model="form.cor" type="color" class="form-color" />
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('tags.descricao') }}</label>
              <textarea
                v-model="form.descricao"
                rows="3"
                class="form-textarea"
                :placeholder="$t('tags.descricaoPlaceholder')"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="closeModal">
              {{ $t('common.cancelar') }}
            </button>
            <button type="submit" class="btn-save" :disabled="saving">
              {{ saving ? $t('common.salvando') : $t('common.salvar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('tags.confirmDelete')"
      :message="$t('tags.deleteMessage')"
      :item-name="tagToDelete?.nome"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { useTags } from './Tags'
import ConfirmModal from '@/shared/components/common/ConfirmModal.vue'

const {
  tags,
  loading,
  modalOpen,
  saving,
  form,
  editingTag,
  confirmModalVisible,
  tagToDelete,
  deleting,
  openModal,
  editTag,
  closeModal,
  saveTag,
  confirmDelete,
  handleConfirmDelete,
} = useTags()
</script>

<style scoped>
@import '@/styles/views/tags/tags.css';
</style>
