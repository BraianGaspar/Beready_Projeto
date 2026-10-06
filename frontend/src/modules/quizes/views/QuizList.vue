<template>
  <PageContainer class="quiz-list">
    <PageHeader :title="$t('quizes.title')" :subtitle="$t('quizes.subtitle')" icon="light-bulb" back-to="/dashboard">
      <template v-if="!loading && canView && quizes.length > 0" #actions>
        <BaseButton v-if="canCreateQuiz" variant="secondary" icon="plus" @click="openCreateModal">
          {{ $t('quizes.newQuiz') }}
        </BaseButton>
        <template v-else>
          <BaseButton variant="secondary" icon="plus" disabled>{{ $t('quizes.newQuiz') }}</BaseButton>
          <BaseBadge variant="warning" icon>{{ canCreateMoreQuizes ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </template>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('quizes.carregando')" />

    <EmptyState
      v-else-if="!canView"
      icon="lock-closed"
      :title="$t('common.acessoNegado')"
      :description="$t('common.permissionDenied')"
    >
      <BaseButton to="/dashboard" icon="arrow-left">{{ $t('common.voltarDashboard') }}</BaseButton>
    </EmptyState>

    <template v-else-if="quizes.length === 0">
      <EmptyState icon="light-bulb" :title="$t('quizes.emptyTitle')" :description="$t('quizes.emptyDescription')">
        <BaseButton v-if="canCreateQuiz" icon="plus" @click="openCreateModal">{{ $t('quizes.createFirst') }}</BaseButton>
        <template v-else>
          <BaseButton icon="plus" disabled>{{ $t('quizes.createFirst') }}</BaseButton>
          <BaseBadge variant="warning" icon>{{ canCreateMoreQuizes ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </template>
      </EmptyState>

      <BaseAlert v-if="!canCreateMoreQuizes" variant="warning" :message="$t('quizes.limitReached')">
        <BaseButton variant="ghost" size="sm" icon-end="arrow-right" to="/planos">
          {{ $t('quizes.upgradeToCreateMore') }}
        </BaseButton>
      </BaseAlert>
    </template>

    <ul v-else class="quiz-list__grid u-grid-auto" role="list">
      <li v-for="quiz in quizes" :key="quiz.id">
        <BaseCard as="article" padding="none" interactive class="quiz-list__card">
          <button type="button" class="quiz-list__content" @click="viewQuiz(quiz.id)">
            <BaseBadge :variant="getNivelVariant(quiz.nivel_dificuldade)" size="sm">
              {{ getDifficultyText(quiz.nivel_dificuldade) }}
            </BaseBadge>
            <span class="quiz-list__title">{{ quiz.titulo }}</span>
            <span class="quiz-list__description">{{ quiz.descricao || $t('quizes.semDescricao') }}</span>
            <span class="quiz-list__meta">
              <span class="quiz-list__meta-item">
                <BaseIcon name="document" />
                {{ quiz.total_questoes || 0 }} {{ $t('quizes.questoes') }}
              </span>
              <span class="quiz-list__meta-item">
                <BaseIcon name="clock" />
                {{ quiz.tempo_limite ? $t('time.minutes', { n: quiz.tempo_limite }) : $t('quizes.semLimite') }}
              </span>
            </span>
          </button>

          <template #footer>
            <BaseButton icon="play" class="quiz-list__play" @click="playQuiz(quiz.id)">
              {{ $t('quizes.jogar') }}
            </BaseButton>
            <BaseButton
              v-if="canEdit"
              variant="ghost"
              icon="pencil"
              :aria-label="$t('common.editar')"
              @click="openEditModal(quiz)"
            />
            <BaseButton
              v-if="canDelete"
              variant="ghost-danger"
              icon="trash"
              :aria-label="$t('common.excluir')"
              @click="confirmDelete(quiz)"
            />
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Modal de criar/editar -->
    <BaseModal
      v-model="showModal"
      :title="isEditing ? $t('quizes.editQuiz') : $t('quizes.newQuiz')"
      :description="isEditing ? $t('quizes.editSubtitle') : $t('quizes.createSubtitle')"
      @close="closeModal"
    >
      <form id="quiz-list-form" class="u-stack" @submit.prevent="submitForm">
        <BaseInput
          v-model="form.titulo"
          :label="$t('quizes.titulo')"
          :placeholder="$t('quizes.tituloPlaceholder')"
          required
        />
        <BaseTextarea
          v-model="form.descricao"
          :label="$t('quizes.descricao')"
          :placeholder="$t('quizes.descricaoPlaceholder')"
          :rows="3"
        />
        <div class="quiz-list__form-row">
          <BaseSelect
            :model-value="form.nivel_dificuldade"
            :label="$t('quizes.nivel')"
            :options="nivelOptions"
            @update:model-value="form.nivel_dificuldade = normalizeNivel(String($event))"
          />
          <BaseInput
            :model-value="form.tempo_limite ?? ''"
            type="number"
            inputmode="numeric"
            min="0"
            :label="$t('quizes.tempoLimite')"
            :placeholder="$t('quizes.tempoPlaceholder')"
            @update:model-value="form.tempo_limite = toOptionalNumber($event)"
          />
        </div>
        <BaseCheckbox v-model="form.publico" :label="$t('quizes.publico')" />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="quiz-list-form" :loading="submitting">
          {{ submitting ? $t('common.salvando') : isEditing ? $t('common.atualizar') : $t('common.criar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <!-- Confirmação de exclusão -->
    <ConfirmModal
      v-model="showDeleteModal"
      :title="$t('quizes.confirmDelete')"
      :message="$t('quizes.deleteQuizConfirm', { titulo: deletingQuiz?.titulo ?? '' })"
      :warning="$t('quizes.deleteQuizWarning')"
      :confirm-text="$t('quizes.confirmDeleteButton')"
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
  BaseCheckbox,
  BaseIcon,
  BaseInput,
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
import { getNivelVariant, normalizeNivel } from '@/shared/utils/nivelDificuldade'
import { useQuizesView } from './Quizes'

const { t } = useI18n()

const {
  quizes,
  loading,
  showModal,
  showDeleteModal,
  isEditing,
  deletingQuiz,
  submitting,
  deleting,
  form,
  openCreateModal,
  openEditModal,
  viewQuiz,
  playQuiz,
  confirmDelete,
  handleDelete,
  submitForm,
  closeModal,
  getDifficultyText,
  canView,
  canEdit,
  canDelete,
  canCreateQuiz,
  canCreateMoreQuizes,
} = useQuizesView()

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])

const toOptionalNumber = (value: string | number | null | undefined): number | undefined =>
  value === '' || value == null ? undefined : Number(value)
</script>

<style scoped>
@import '@/styles/views/quizes/quizes.css';
</style>
