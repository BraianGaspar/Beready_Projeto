<template>
  <PageContainer size="md" class="quiz-view">
    <PageHeader :title="quiz.titulo || $t('common.carregando')" icon="book-open" back-to="/quizes">
      <div class="quiz-view__badges mt-3 flex flex-wrap gap-2">
        <BaseBadge v-if="quiz.nivel_dificuldade" :variant="nivelVariant">
          {{ getLevelText(quiz.nivel_dificuldade) }}
        </BaseBadge>
        <BaseBadge v-if="quiz.publico" variant="primary" icon="users">{{ $t('quizes.publicBadge') }}</BaseBadge>
        <BaseBadge v-else variant="neutral" icon="lock-closed">{{ $t('quizes.privateBadge') }}</BaseBadge>
      </div>
      <template #actions>
        <BaseButton
          variant="secondary"
          size="lg"
          icon="play"
          :to="quizId ? `/quizes/${quizId}/play` : undefined"
          :disabled="!quizId"
        >
          {{ $t('quizes.start') }}
        </BaseButton>
      </template>
    </PageHeader>

    <div class="quiz-view__stats grid grid-cols-fill-56 gap-4">
      <StatCard :label="$t('quizes.totalQuestoes')" :value="quiz.total_questoes || 0" icon="document" />
      <StatCard
        :label="$t('quizes.tempoLimiteLabel')"
        :value="quiz.tempo_limite ? $t('quizes.minutes', { n: quiz.tempo_limite }) : $t('quizes.semLimite')"
        icon="clock"
        variant="info"
      />
      <StatCard
        :label="$t('flashcards.criadoEm')"
        :value="formatDate(quiz.criado_em)"
        icon="sparkles"
        variant="neutral"
      />
    </div>

    <!-- Quiz sem questões (ex.: criado antes do editor): chamada para adicionar -->
    <BaseAlert v-if="quiz.id && !quiz.total_questoes" variant="warning" :title="$t('quizPlay.emptyTitle')">
      <p>{{ canAddQuestoes ? $t('quizPlay.emptyDescriptionOwner') : $t('quizPlay.emptyDescription') }}</p>
      <div v-if="canAddQuestoes" class="mt-2">
        <BaseButton variant="ghost" size="sm" icon="plus" :to="`/quizes/edit/${quizId}`">
          {{ $t('quizPlay.addQuestions') }}
        </BaseButton>
      </div>
    </BaseAlert>

    <BaseCard :title="$t('quizes.descricao')">
      <p class="quiz-view__description wrap-anywhere whitespace-pre-line leading-relaxed text-text-muted">{{ quiz.descricao || $t('common.semDescricao') }}</p>
    </BaseCard>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { BaseAlert, BaseBadge, BaseButton, BaseCard, PageContainer, PageHeader, StatCard } from '@/shared/components/ui'
import { getNivelVariant } from '@/shared/utils/nivelDificuldade'
import { useAuthStore } from '@/stores/auth'
import { usePermissionStore } from '@/stores/permissionStore'
import { useQuizView } from './QuizView'

const { quiz, quizId, getLevelText, formatDate } = useQuizView()
const authStore = useAuthStore()
const permissionStore = usePermissionStore()

const canAddQuestoes = computed(
  () => quiz.value.usuario_id === authStore.user?.id && permissionStore.canEdit('quizes'),
)

const nivelVariant = computed(() => getNivelVariant(quiz.value.nivel_dificuldade))
</script>
