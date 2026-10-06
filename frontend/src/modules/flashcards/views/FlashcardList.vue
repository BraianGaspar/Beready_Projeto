<template>
  <PageContainer class="fcard-list">
    <PageHeader
      :title="$t('flashcards.title')"
      :subtitle="$t('flashcards.subtitle')"
      icon="book-open"
      back-to="/dashboard"
    >
      <template v-if="!loading && canView && flashcards.length > 0" #actions>
        <BaseButton v-if="canCreateFlashcard" variant="secondary" icon="plus" @click="openCreateModal">
          {{ $t('flashcards.newFlashcard') }}
        </BaseButton>
        <template v-else>
          <BaseButton variant="secondary" icon="plus" disabled>{{ $t('flashcards.newFlashcard') }}</BaseButton>
          <BaseBadge variant="warning" icon>{{ canCreateMoreFlashcards ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </template>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('flashcards.carregando')" />

    <EmptyState
      v-else-if="!canView"
      icon="lock-closed"
      :title="$t('common.acessoNegado')"
      :description="$t('common.permissionDenied')"
    >
      <BaseButton to="/dashboard" icon="arrow-left">{{ $t('common.voltarDashboard') }}</BaseButton>
    </EmptyState>

    <template v-else-if="flashcards.length === 0">
      <EmptyState
        icon="book-open"
        :title="$t('flashcards.emptyTitle')"
        :description="$t('flashcards.emptyDescription')"
      >
        <BaseButton v-if="canCreateFlashcard" icon="plus" @click="openCreateModal">
          {{ $t('flashcards.createFirst') }}
        </BaseButton>
        <template v-else>
          <BaseButton icon="plus" disabled>{{ $t('flashcards.createFirst') }}</BaseButton>
          <BaseBadge variant="warning" icon>{{ canCreateMoreFlashcards ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </template>
      </EmptyState>

      <BaseAlert v-if="!canCreateMoreFlashcards" variant="warning" :message="$t('flashcards.limitReached')">
        <BaseButton variant="ghost" size="sm" icon-end="arrow-right" to="/planos">
          {{ $t('flashcards.upgradeToCreateMore') }}
        </BaseButton>
      </BaseAlert>
    </template>

    <ul v-else class="fcard-list__grid grid list-none grid-cols-fill gap-6" role="list">
      <li v-for="flashcard in flashcards" :key="flashcard.id">
        <BaseCard as="article" padding="none" interactive class="fcard-list__card h-full">
          <button
            type="button"
            class="fcard-list__content flex min-h-full w-full cursor-pointer flex-col gap-1 border-0 bg-transparent p-5 text-start text-text font-inherit hover:bg-surface-hover focus-visible:focus-ring-inset"
            :title="$t('flashcards.detailsTitle')"
            @click="flashcard.id !== undefined && viewFlashcard(flashcard.id)"
          >
            <span class="fcard-list__label text-xs font-semibold uppercase tracking-wider text-text-muted">{{ $t('flashcards.perguntaLabel') }}</span>
            <span class="fcard-list__text fcard-list__text--question mb-3 line-clamp-3 wrap-anywhere text-lg font-semibold text-text">{{ flashcard.frente }}</span>
            <span class="fcard-list__label text-xs font-semibold uppercase tracking-wider text-text-muted">{{ $t('flashcards.resposta') }}</span>
            <span class="fcard-list__text line-clamp-3 text-text-muted wrap-anywhere">{{ flashcard.verso }}</span>
          </button>

          <template #footer>
            <BaseButton
              icon="academic-cap"
              class="fcard-list__study me-auto"
              @click="flashcard.id !== undefined && studyFlashcard(flashcard.id)"
            >
              {{ $t('flashcards.estudar') }}
            </BaseButton>
            <BaseButton
              v-if="canEdit"
              variant="ghost"
              icon="pencil"
              :aria-label="$t('common.editar')"
              @click="openEditModal(flashcard)"
            />
            <BaseButton
              v-if="canDelete"
              variant="ghost-danger"
              icon="trash"
              :aria-label="$t('common.excluir')"
              @click="confirmDelete(flashcard)"
            />
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Modal de criar/editar -->
    <BaseModal
      v-model="showModal"
      :title="isEditing ? $t('flashcards.editFlashcard') : $t('flashcards.newFlashcard')"
      :description="isEditing ? $t('flashcards.editSubtitle') : $t('flashcards.createSubtitle')"
      @close="closeModal"
    >
      <form id="fcard-list-form" class="flex flex-col gap-4" @submit.prevent="submitForm">
        <BaseTextarea
          v-model="form.frente"
          :label="$t('flashcards.pergunta')"
          :placeholder="$t('flashcards.perguntaPlaceholder')"
          :rows="3"
          required
        />
        <BaseTextarea
          v-model="form.verso"
          :label="$t('flashcards.resposta')"
          :placeholder="$t('flashcards.respostaPlaceholder')"
          :rows="3"
          required
        />
        <BaseSelect
          :model-value="form.nivel_dificuldade"
          :label="$t('flashcards.dificuldade')"
          :options="nivelOptions"
          @update:model-value="form.nivel_dificuldade = normalizeNivel(String($event))"
        />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="fcard-list-form" :loading="submitting">
          {{ submitting ? $t('common.salvando') : isEditing ? $t('common.atualizar') : $t('common.criar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <!-- Confirmação de exclusão -->
    <ConfirmModal
      v-model="showDeleteModal"
      :title="$t('flashcards.confirmDelete')"
      :message="$t('flashcards.deleteConfirmMessage')"
      :warning="$t('flashcards.deleteWarning')"
      :item-name="deletingFlashcard?.frente"
      :confirm-text="$t('flashcards.confirmDeleteButton')"
      :loading="deleting"
      @confirm="handleDelete"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  BaseAlert,
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseModal,
  BaseSelect,
  BaseSpinner,
  BaseTextarea,
  ConfirmModal,
  EmptyState,
  PageContainer,
  PageHeader,
  type SelectOption,
} from '@/shared/components/ui'
import { normalizeNivel } from '@/shared/utils/nivelDificuldade'
import { useFlashcardsView } from './Flashcards'

const { t } = useI18n()

const {
  flashcards,
  loading,
  showModal,
  showDeleteModal,
  isEditing,
  deletingFlashcard,
  submitting,
  deleting,
  form,
  openCreateModal,
  openEditModal,
  viewFlashcard,
  studyFlashcard,
  confirmDelete,
  handleDelete,
  submitForm,
  closeModal,
  canView,
  canEdit,
  canDelete,
  canCreateFlashcard,
  canCreateMoreFlashcards,
} = useFlashcardsView()

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])
</script>
