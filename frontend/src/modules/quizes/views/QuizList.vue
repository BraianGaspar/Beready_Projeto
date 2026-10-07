<template>
  <PageContainer class="quiz-list">
    <PageHeader :title="$t('quizes.title')" :subtitle="$t('quizes.subtitle')" icon="light-bulb" back-to="/dashboard">
      <template v-if="!loading && canView" #actions>
        <template v-if="canCreateQuiz">
          <BaseButton variant="secondary" icon="sparkles" @click="openGerarModal">
            {{ $t('quizGerar.botao') }}
          </BaseButton>
          <BaseButton v-if="quizes.length > 0" variant="secondary" icon="plus" @click="openCreate">
            {{ $t('quizes.newQuiz') }}
          </BaseButton>
        </template>
        <template v-else-if="quizes.length > 0">
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
        <BaseButton v-if="canCreateQuiz" icon="plus" @click="openCreate">{{ $t('quizes.createFirst') }}</BaseButton>
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

    <ul v-else class="quiz-list__grid grid list-none grid-cols-fill gap-6" role="list">
      <li v-for="quiz in quizes" :key="quiz.id">
        <BaseCard as="article" padding="none" interactive class="quiz-list__card h-full">
          <button type="button" class="quiz-list__content flex min-h-full w-full cursor-pointer flex-col items-start gap-2 border-0 bg-transparent p-5 text-start text-text font-inherit hover:bg-surface-hover focus-visible:focus-ring-inset" @click="viewQuiz(quiz.id)">
            <span class="quiz-list__badges flex flex-wrap gap-2">
              <BaseBadge :variant="getNivelVariant(quiz.nivel_dificuldade)" size="sm">
                {{ getDifficultyText(quiz.nivel_dificuldade) }}
              </BaseBadge>
              <BaseBadge v-if="quiz.tipo_criacao === 'flashcards'" variant="info" size="sm" icon="sparkles">
                {{ $t('quizGerar.badge') }}
              </BaseBadge>
            </span>
            <span class="quiz-list__title wrap-anywhere text-lg font-semibold">{{ quiz.titulo }}</span>
            <span class="quiz-list__description line-clamp-3 wrap-anywhere text-sm text-text-muted">{{ quiz.descricao || $t('quizes.semDescricao') }}</span>
            <span class="quiz-list__meta mt-auto flex flex-wrap items-center gap-x-4 gap-y-2 pt-2 text-sm text-text-muted">
              <BaseBadge v-if="!quiz.total_questoes" variant="warning" size="sm" icon>
                {{ $t('quizEditor.semQuestoes') }}
              </BaseBadge>
              <span v-else class="quiz-list__meta-item inline-flex items-center gap-1">
                <BaseIcon name="document" />
                {{ quiz.total_questoes }} {{ $t('quizes.questoes') }}
              </span>
              <span class="quiz-list__meta-item inline-flex items-center gap-1">
                <BaseIcon name="clock" />
                {{ quiz.tempo_limite ? $t('time.minutes', { n: quiz.tempo_limite }) : $t('quizes.semLimite') }}
              </span>
            </span>
          </button>

          <template #footer>
            <BaseButton icon="play" class="quiz-list__play me-auto" @click="playQuiz(quiz.id)">
              {{ $t('quizes.jogar') }}
            </BaseButton>
            <BaseButton
              v-if="canEdit"
              variant="ghost"
              icon="pencil"
              :aria-label="$t('common.editar')"
              @click="openEdit(quiz)"
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

    <!-- Gerar quiz a partir dos flashcards -->
    <BaseModal
      v-model="showGerarModal"
      :title="$t('quizGerar.titulo')"
      :description="$t('quizGerar.descricao', { n: GERAR_MIN_FLASHCARDS })"
    >
      <form id="quiz-gerar-form" class="flex flex-col gap-4" novalidate @submit.prevent="submitGerar">
        <BaseAlert v-if="gerador.erro.value" variant="danger">
          <p>{{ gerador.erro.value }}</p>
          <div v-if="gerador.erro.value === semFlashcardsMsg" class="mt-2">
            <BaseButton variant="ghost" size="sm" icon-end="arrow-right" to="/flashcards">
              {{ $t('quizGerar.irFlashcards') }}
            </BaseButton>
          </div>
        </BaseAlert>
        <BaseInput
          v-model="gerador.form.titulo"
          :label="$t('quizes.titulo')"
          :placeholder="$t('quizGerar.tituloPadrao')"
          maxlength="200"
        />
        <div class="quiz-gerar__row grid grid-cols-fit-48 gap-4">
          <BaseInput
            :model-value="gerador.form.quantidade"
            type="number"
            inputmode="numeric"
            min="1"
            :max="GERAR_MAX_QUESTOES"
            :label="$t('quizGerar.quantidade')"
            :error="gerador.erroQuantidade.value"
            required
            @update:model-value="gerador.form.quantidade = Number($event) || 0"
          />
          <BaseSelect
            :model-value="gerador.form.nivel"
            :label="$t('quizes.nivel')"
            :options="nivelOptions"
            @update:model-value="gerador.form.nivel = toNivel($event)"
          />
        </div>
        <BaseSelect
          :model-value="gerador.form.tag_id"
          :label="$t('quizGerar.tag')"
          :options="tagOptions"
          :hint="$t('quizGerar.tagHint')"
          @update:model-value="gerador.form.tag_id = $event === '' || $event == null ? '' : Number($event)"
        />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="showGerarModal = false">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="quiz-gerar-form" icon="sparkles" :loading="gerador.gerando.value">
          {{ $t('quizGerar.gerar') }}
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
  BaseIcon,
  BaseInput,
  BaseModal,
  BaseSelect,
  BaseSpinner,
  ConfirmModal,
  EmptyState,
  PageContainer,
  PageHeader,
  type SelectOption,
} from '@/shared/components/ui'
import { getNivelVariant, type NivelDificuldade } from '@/shared/utils/nivelDificuldade'
import { GERAR_MAX_QUESTOES, GERAR_MIN_FLASHCARDS } from '../composables/useGerarQuiz'
import { useQuizesView } from './Quizes'

const { t } = useI18n()

const {
  quizes,
  loading,
  showDeleteModal,
  showGerarModal,
  deletingQuiz,
  deleting,
  gerador,
  openCreate,
  openEdit,
  openGerarModal,
  submitGerar,
  viewQuiz,
  playQuiz,
  confirmDelete,
  handleDelete,
  getDifficultyText,
  canView,
  canEdit,
  canDelete,
  canCreateQuiz,
  canCreateMoreQuizes,
} = useQuizesView()

const NIVEIS: NivelDificuldade[] = ['iniciante', 'intermediario', 'avancado']

const nivelOptions = computed<SelectOption[]>(() => [
  { value: '', label: t('quizGerar.todosNiveis') },
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])

const tagOptions = computed<SelectOption[]>(() => [
  { value: '', label: t('quizGerar.todasTags') },
  ...gerador.tags.value.map((tag) => ({ value: tag.id, label: tag.nome })),
])

const semFlashcardsMsg = computed(() => t('quizGerar.erroMinimo', { n: GERAR_MIN_FLASHCARDS }))

const toNivel = (value: string | number | null | undefined): NivelDificuldade | '' =>
  NIVEIS.find((nivel) => nivel === value) ?? ''
</script>
