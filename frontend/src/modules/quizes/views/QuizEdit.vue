<template>
  <PageContainer size="md" class="quiz-form">
    <PageHeader
      :title="$t('quizes.editQuiz')"
      :subtitle="$t('quizes.editSubtitle')"
      icon="pencil"
      back-to="/quizes"
    />

    <BaseSpinner v-if="loading && !form.id" center size="lg" show-label :label="$t('quizPlay.loading')" />

    <EmptyState
      v-else-if="!canEdit"
      icon="lock-closed"
      :title="$t('common.acessoNegado')"
      :description="$t('permissions.editDenied', { recurso: $t('common.quizes') })"
    >
      <BaseButton to="/quizes" icon="arrow-left">{{ $t('quizPlay.backToList') }}</BaseButton>
    </EmptyState>

    <!-- novalidate: a validação é feita em useQuizEdit (mensagens traduzidas nos campos) -->
    <form v-else class="quiz-form__form flex flex-col gap-6" novalidate @submit.prevent="handleSubmit">
      <BaseCard>
        <div class="quiz-form__grid grid grid-cols-fit-56 gap-5">
          <BaseInput v-model="form.titulo" :label="$t('quizes.titulo')" :error="errors.titulo" required />
          <BaseSelect
            :model-value="form.nivel_dificuldade"
            :label="$t('flashcards.dificuldade')"
            :options="nivelOptions"
            @update:model-value="form.nivel_dificuldade = normalizeNivel(String($event))"
          />
          <BaseTextarea v-model="form.descricao" class="quiz-form__full col-span-full" :label="$t('quizes.descricao')" :rows="3" />
          <BaseInput
            :model-value="form.tempo_limite ?? ''"
            type="number"
            inputmode="numeric"
            min="0"
            :label="$t('quizes.tempoLimite')"
            @update:model-value="form.tempo_limite = $event === '' || $event == null ? null : Number($event)"
          />
          <BaseCheckbox v-model="form.publico" class="quiz-form__full col-span-full" :label="$t('quizes.publicBadge')" />
        </div>
      </BaseCard>

      <BaseCard>
        <QuizQuestoesEditor v-model="questoes" :errors="questoesErrors" />
      </BaseCard>

      <BaseAlert v-if="hasQuestaoErrors" variant="danger" :message="$t('quizEditor.erroRevisar')" />

      <div class="quiz-form__actions flex flex-wrap justify-end gap-3 *:shrink *:grow *:basis-full sm:*:shrink-0 sm:*:grow-0 sm:*:basis-auto">
        <BaseButton variant="danger" icon="trash" class="quiz-form__delete sm:me-auto" @click="handleDelete">
          {{ $t('quizes.deleteQuiz') }}
        </BaseButton>
        <BaseButton variant="secondary" to="/quizes">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" icon="check" :loading="loading">
          {{ loading ? $t('common.salvando') : $t('quizes.saveChanges') }}
        </BaseButton>
      </div>
    </form>

    <ConfirmModal
      v-model="showDeleteModal"
      :title="$t('quizes.deleteQuiz')"
      :message="$t('quizes.deleteQuizConfirm', { titulo: form.titulo })"
      :warning="$t('quizes.deleteQuizWarning')"
      :confirm-text="$t('quizes.confirmDeleteButton')"
      :loading="deleteLoading"
      @confirm="confirmDelete"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  BaseAlert,
  BaseButton,
  BaseCard,
  BaseCheckbox,
  BaseInput,
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
import QuizQuestoesEditor from '../components/QuizQuestoesEditor.vue'
import { useQuizEdit } from './QuizEdit'

const { t } = useI18n()

const {
  form,
  errors,
  questoes,
  questoesErrors,
  loading,
  deleteLoading,
  showDeleteModal,
  canEdit,
  handleSubmit,
  handleDelete,
  confirmDelete,
} = useQuizEdit()

const hasQuestaoErrors = computed(() => Object.keys(questoesErrors.value).length > 0)

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])
</script>
