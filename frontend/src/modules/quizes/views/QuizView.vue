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

    <BaseCard :title="$t('quizes.descricao')">
      <p class="quiz-view__description wrap-anywhere whitespace-pre-line leading-relaxed text-text-muted">{{ quiz.descricao || $t('common.semDescricao') }}</p>
    </BaseCard>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { BaseBadge, BaseButton, BaseCard, PageContainer, PageHeader, StatCard } from '@/shared/components/ui'
import { getNivelVariant } from '@/shared/utils/nivelDificuldade'
import { useQuizView } from './QuizView'

const { quiz, quizId, getLevelText, formatDate } = useQuizView()

const nivelVariant = computed(() => getNivelVariant(quiz.value.nivel_dificuldade))
</script>
