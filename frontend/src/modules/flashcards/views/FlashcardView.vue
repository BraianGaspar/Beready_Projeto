<template>
  <PageContainer size="md" class="fcard-view">
    <PageHeader variant="plain" :title="$t('flashcards.detailsTitle')" icon="book-open" back-to="/flashcards">
      <BaseBadge v-if="flashcard" :variant="nivelVariant" class="fcard-view__level">
        {{ getLevelText(flashcard.nivel_dificuldade) }}
      </BaseBadge>
      <template v-if="flashcard && !loading" #actions>
        <BaseButton icon="academic-cap" @click="studyFlashcard">{{ $t('flashcards.estudar') }}</BaseButton>
        <BaseButton v-if="canDelete()" variant="danger" icon="trash" @click="openDeleteModal">
          {{ $t('common.excluir') }}
        </BaseButton>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label />

    <BaseCard v-else-if="flashcard" as="article" padding="none">
      <section class="fcard-view__section">
        <BaseBadge variant="primary" size="sm">{{ $t('flashcards.perguntaLabel') }}</BaseBadge>
        <p class="fcard-view__text fcard-view__text--question">{{ flashcard.frente }}</p>
      </section>
      <section class="fcard-view__section fcard-view__section--answer">
        <BaseBadge variant="info" size="sm">{{ $t('flashcards.respostalabel') }}</BaseBadge>
        <p class="fcard-view__text">{{ flashcard.verso }}</p>
      </section>

      <template v-if="flashcard.criado_em" #footer>
        <dl class="fcard-view__meta">
          <dt>{{ $t('flashcards.criadoEm') }}</dt>
          <dd>{{ formatDate(flashcard.criado_em) }}</dd>
        </dl>
      </template>
    </BaseCard>

    <ConfirmModal
      v-model="showConfirmModal"
      :title="$t('flashcards.confirmDelete')"
      :message="$t('flashcards.deleteConfirmMessage')"
      :warning="$t('flashcards.deleteWarning')"
      :item-name="flashcard?.frente"
      :confirm-text="$t('flashcards.confirmDeleteButton')"
      type="danger"
      :loading="deleting"
      @confirm="confirmDelete"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseSpinner,
  ConfirmModal,
  PageContainer,
  PageHeader,
} from '@/shared/components/ui'
import { getNivelVariant } from '@/shared/utils/nivelDificuldade'
import { useFlashcardView } from './FlashcardView'

const {
  flashcard,
  loading,
  deleting,
  showConfirmModal,
  canDelete,
  studyFlashcard,
  openDeleteModal,
  confirmDelete,
  getLevelText,
  formatDate,
} = useFlashcardView()

const nivelVariant = computed(() => getNivelVariant(flashcard.value?.nivel_dificuldade))
</script>

<style scoped>
@import '@/styles/views/flashcards/flashcard-view.css';
</style>
