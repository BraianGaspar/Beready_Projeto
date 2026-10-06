<template>
  <PageContainer size="sm" class="quiz-play">
    <PageHeader variant="plain" :title="$t('quizes.jogar')" icon="light-bulb" back-to="/quizes">
      <div v-if="!loading && !isFinished && total > 0" class="quiz-play__progress mt-3 flex flex-col gap-2">
        <span class="quiz-play__progress-label text-sm font-semibold text-text-muted" aria-hidden="true">
          {{ $t('quizPlay.progress', { current: currentIndex + 1, total }) }}
        </span>
        <BaseProgress
          :value="currentIndex + 1"
          :max="total"
          :label="$t('common.progresso')"
          :value-text="$t('quizPlay.progress', { current: currentIndex + 1, total })"
        />
      </div>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('quizPlay.loading')" />

    <BaseCard v-else-if="isFinished" as="section">
      <EmptyState icon="trophy" :title="$t('quizPlay.finished')" :description="$t('quizPlay.result', { acertos, total })">
        <BaseButton icon="refresh" @click="jogarNovamente">{{ $t('quizPlay.playAgain') }}</BaseButton>
        <BaseButton variant="secondary" icon="arrow-left" @click="voltar">{{ $t('quizPlay.backToList') }}</BaseButton>
      </EmptyState>
    </BaseCard>

    <BaseCard v-else-if="currentQuiz" as="section" class="quiz-play__card">
      <div class="quiz-play__question flex flex-col items-start gap-3">
        <BaseBadge variant="primary" size="sm">{{ $t('quizPlay.question') }}</BaseBadge>
        <p class="quiz-play__question-text wrap-anywhere text-xl font-semibold leading-base text-text md:text-2xl">{{ currentQuiz.titulo }}</p>
      </div>

      <div aria-live="polite">
        <div v-if="respostaVisivel" class="quiz-play__answer mt-6 border-0 border-s-4 border-solid border-info flex flex-col items-start gap-3 rounded-lg bg-surface-muted p-4">
          <BaseBadge variant="info" size="sm">{{ $t('quizPlay.answer') }}</BaseBadge>
          <p v-if="currentQuiz.descricao" class="quiz-play__answer-text wrap-anywhere whitespace-pre-wrap leading-relaxed text-text">{{ currentQuiz.descricao }}</p>
          <p v-else class="quiz-play__answer-text quiz-play__answer-text--empty wrap-anywhere whitespace-pre-wrap leading-relaxed text-text-muted">{{ $t('quizPlay.noAnswer') }}</p>
        </div>
        <p v-else class="quiz-play__instructions mt-6 flex items-center gap-2 text-text-muted">
          <BaseIcon name="information-circle" />
          {{ $t('quizPlay.instructions') }}
        </p>
      </div>

      <template #footer>
        <BaseButton v-if="!respostaVisivel" size="lg" icon="eye" block @click="mostrarResposta">
          {{ $t('quizPlay.showAnswer') }}
        </BaseButton>
        <div v-else class="quiz-play__answers grid w-full grid-cols-2 gap-3">
          <BaseButton
            variant="danger"
            size="lg"
            icon="x-circle"
            wrap
            @click="responder(false)"
          >
            {{ $t('quizPlay.wrong') }}
          </BaseButton>
          <BaseButton
            variant="success"
            size="lg"
            icon="check-circle"
            wrap
            @click="responder(true)"
          >
            {{ $t('quizPlay.correct') }}
          </BaseButton>
        </div>
      </template>
    </BaseCard>
  </PageContainer>
</template>

<script setup lang="ts">
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseProgress,
  BaseSpinner,
  EmptyState,
  PageContainer,
  PageHeader,
} from '@/shared/components/ui'
import { useQuizPlay } from './QuizPlay'

const {
  currentQuiz,
  currentIndex,
  total,
  acertos,
  respostaVisivel,
  loading,
  isFinished,
  mostrarResposta,
  responder,
  jogarNovamente,
  voltar,
} = useQuizPlay()
</script>
