<template>
  <PageContainer size="sm" class="quiz-play">
    <PageHeader variant="plain" :title="quiz?.titulo || $t('quizes.jogar')" icon="light-bulb" back-to="/quizes">
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

    <!-- Quiz antigo (ou novo) sem questões -->
    <EmptyState
      v-else-if="total === 0"
      icon="clipboard"
      :title="$t('quizPlay.emptyTitle')"
      :description="canAddQuestoes ? $t('quizPlay.emptyDescriptionOwner') : $t('quizPlay.emptyDescription')"
    >
      <BaseButton v-if="canAddQuestoes && quiz" icon="plus" :to="`/quizes/edit/${quiz.id}`">
        {{ $t('quizPlay.addQuestions') }}
      </BaseButton>
      <BaseButton variant="secondary" icon="arrow-left" @click="voltar">{{ $t('quizPlay.backToList') }}</BaseButton>
    </EmptyState>

    <!-- Resultado final -->
    <div v-else-if="isFinished && resultado" class="quiz-play__result flex flex-col gap-6">
      <BaseCard as="section">
        <div class="quiz-play__summary flex flex-col items-center gap-3 text-center">
          <span class="quiz-play__trophy inline-flex size-18 items-center justify-center rounded-full bg-primary-soft text-icon-xl text-primary-soft-text" aria-hidden="true">
            <BaseIcon name="trophy" />
          </span>
          <h2 id="quiz-play-result" tabindex="-1" class="quiz-play__result-title text-2xl font-bold text-text outline-hidden">
            {{ $t('quizPlay.finished') }}
          </h2>
          <p class="quiz-play__result-text text-lg text-text-muted">
            {{ $t('quizPlay.result', { acertos: resultado.acertos, total: resultado.total }) }}
          </p>
          <BaseProgress
            class="w-full"
            :value="resultado.percentual"
            :label="$t('quizPlay.score')"
            :value-text="$t('quizPlay.scoreText', { percent: resultado.percentual })"
            :variant="scoreVariant"
            size="lg"
            show-label
          />
        </div>
        <div class="quiz-play__stats mt-6 grid grid-cols-fit-40 gap-3">
          <StatCard :label="$t('flashcardStudy.correctCount')" :value="resultado.acertos" icon="check-circle" variant="success" />
          <StatCard :label="$t('flashcardStudy.wrongCount')" :value="resultado.erros" icon="x-circle" variant="danger" />
        </div>
      </BaseCard>

      <BaseCard v-if="erradas.length" as="section" :title="$t('quizPlay.reviewWrong')" title-tag="h2">
        <ol class="quiz-play__wrong-list m-0 flex list-none flex-col gap-4 p-0" role="list">
          <li
            v-for="item in erradas"
            :key="item.questao.id"
            class="quiz-play__wrong flex flex-col gap-2 rounded-lg border-0 border-s-4 border-solid border-danger bg-surface-muted p-4"
          >
            <p class="quiz-play__wrong-question wrap-anywhere font-semibold text-text">{{ item.questao.enunciado }}</p>
            <p class="quiz-play__wrong-line flex items-start gap-2 text-sm text-text">
              <BaseIcon name="x-circle" class="mt-nudge shrink-0 text-danger" />
              <span class="min-w-0 wrap-anywhere">
                <span class="font-semibold after:content-colon">{{ $t('quizPlay.yourAnswer') }}</span>
                {{ item.suaResposta || $t('quizPlay.noAnswerGiven') }}
              </span>
            </p>
            <p class="quiz-play__wrong-line flex items-start gap-2 text-sm text-text">
              <BaseIcon name="check-circle" class="mt-nudge shrink-0 text-success" />
              <span class="min-w-0 wrap-anywhere">
                <span class="font-semibold after:content-colon">{{ $t('quizPlay.correctAnswer') }}</span>
                {{ item.respostaCorreta }}
              </span>
            </p>
            <p v-if="item.correcao.explicacao" class="quiz-play__wrong-line flex items-start gap-2 text-sm text-text-muted">
              <BaseIcon name="information-circle" class="mt-nudge shrink-0 text-info" />
              <span class="min-w-0 wrap-anywhere">
                <span class="font-semibold after:content-colon">{{ $t('quizPlay.explanation') }}</span>
                {{ item.correcao.explicacao }}
              </span>
            </p>
          </li>
        </ol>
      </BaseCard>

      <BaseAlert v-else variant="success" :title="$t('quizPlay.perfectTitle')" :message="$t('quizPlay.perfectMessage')" />

      <div class="quiz-play__result-actions flex flex-wrap justify-center gap-3 *:grow *:basis-full sm:*:grow-0 sm:*:basis-auto">
        <BaseButton icon="refresh" @click="jogarNovamente">{{ $t('quizPlay.playAgain') }}</BaseButton>
        <BaseButton variant="secondary" icon="arrow-left" @click="voltar">{{ $t('quizPlay.backToList') }}</BaseButton>
      </div>
    </div>

    <!-- Questão atual -->
    <BaseCard v-else-if="questaoAtual" as="section" class="quiz-play__card">
      <form class="quiz-play__form flex flex-col gap-5" @submit.prevent="correcaoAtual ? proxima() : responder()">
        <div class="quiz-play__question flex flex-col items-start gap-3">
          <BaseBadge variant="primary" size="sm">
            {{ questaoAtual.tipo === 'completar' ? $t('quizEditor.tipoCompletar') : $t('quizEditor.tipoMultipla') }}
          </BaseBadge>
          <h2
            id="quiz-play-question"
            tabindex="-1"
            class="quiz-play__question-text wrap-anywhere whitespace-pre-line text-xl font-semibold leading-base text-text outline-hidden md:text-2xl"
          >
            {{ questaoAtual.enunciado }}
          </h2>
        </div>

        <fieldset
          v-if="questaoAtual.tipo === 'multipla_escolha'"
          class="quiz-play__options m-0 flex min-w-0 flex-col gap-3 border-0 p-0"
          :disabled="!!correcaoAtual"
        >
          <legend class="sr-only">{{ $t('quizPlay.chooseOption') }}</legend>
          <label
            v-for="alternativa in questaoAtual.alternativas"
            :key="alternativa.id"
            :class="optionClasses(alternativa.id)"
          >
            <input
              v-model="alternativaSelecionada"
              type="radio"
              name="quiz-play-option"
              class="size-5 shrink-0 accent-primary focus-visible:focus-ring"
              :value="alternativa.id"
            />
            <span class="quiz-play__option-text min-w-0 flex-1 wrap-anywhere">{{ alternativa.texto }}</span>
            <BaseBadge
              v-if="correcaoAtual && alternativa.id === correcaoAtual.alternativa_correta_id"
              variant="success"
              size="sm"
              icon
            >
              {{ $t('quizPlay.correctOption') }}
            </BaseBadge>
            <BaseBadge
              v-else-if="correcaoAtual && alternativa.id === alternativaSelecionada"
              variant="danger"
              size="sm"
              icon
            >
              {{ $t('quizPlay.yourAnswer') }}
            </BaseBadge>
          </label>
        </fieldset>

        <BaseInput
          v-else
          v-model="respostaTexto"
          :label="$t('quizPlay.typeAnswer')"
          :hint="$t('quizPlay.typeAnswerHint')"
          :disabled="!!correcaoAtual"
          autocomplete="off"
        />

        <!-- Feedback (o BaseAlert anuncia: role=alert no erro, status no acerto) -->
        <BaseAlert
          v-if="correcaoAtual"
          :variant="correcaoAtual.correta ? 'success' : 'danger'"
          :title="correcaoAtual.correta ? $t('quizPlay.feedbackCorrect') : $t('quizPlay.feedbackWrong')"
        >
          <p v-if="!correcaoAtual.correta">
            {{ $t('quizPlay.correctWas', { resposta: respostaCorretaAtual }) }}
          </p>
          <p v-else-if="!correcaoAtual.explicacao">{{ $t('quizPlay.feedbackCorrectMessage') }}</p>
          <p v-if="correcaoAtual.explicacao" class="mt-1">
            <span class="font-semibold after:content-colon">{{ $t('quizPlay.explanation') }}</span>
            {{ correcaoAtual.explicacao }}
          </p>
        </BaseAlert>

        <BaseButton
          v-if="!correcaoAtual"
          type="submit"
          size="lg"
          icon="check"
          block
          :loading="verificando"
          :disabled="!podeResponder"
        >
          {{ $t('quizPlay.submitAnswer') }}
        </BaseButton>
        <BaseButton
          v-else
          id="quiz-play-next"
          type="submit"
          size="lg"
          :icon-end="isLast ? 'trophy' : 'arrow-right'"
          block
          :loading="finalizando"
        >
          {{ isLast ? $t('quizPlay.seeResult') : $t('quizPlay.next') }}
        </BaseButton>
      </form>
    </BaseCard>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  BaseAlert,
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseInput,
  BaseProgress,
  BaseSpinner,
  EmptyState,
  PageContainer,
  PageHeader,
  StatCard,
} from '@/shared/components/ui'
import { useQuizPlay } from './QuizPlay'

const {
  quiz,
  loading,
  total,
  currentIndex,
  questaoAtual,
  alternativaSelecionada,
  respostaTexto,
  correcaoAtual,
  verificando,
  finalizando,
  podeResponder,
  isLast,
  isFinished,
  resultado,
  erradas,
  canAddQuestoes,
  responder,
  proxima,
  jogarNovamente,
  voltar,
} = useQuizPlay()

const optionBase =
  'quiz-play__option flex min-h-control items-center gap-3 rounded-lg border border-solid p-4 text-text transition-control'

// Estado da alternativa: antes da correção (selecionada ou não) e depois (correta, errada marcada, demais)
const optionClasses = (id: number) => {
  const correcao = correcaoAtual.value
  if (correcao) {
    if (id === correcao.alternativa_correta_id) return `${optionBase} cursor-default border-success bg-success-soft`
    if (id === alternativaSelecionada.value) return `${optionBase} cursor-default border-danger bg-danger-soft`
    return `${optionBase} cursor-default border-border bg-surface opacity-disabled`
  }
  return id === alternativaSelecionada.value
    ? `${optionBase} cursor-pointer border-primary bg-primary-soft`
    : `${optionBase} cursor-pointer border-border bg-surface hover:border-primary hover:bg-surface-hover`
}

const respostaCorretaAtual = computed(() => {
  const correcao = correcaoAtual.value
  const questao = questaoAtual.value
  if (!correcao || !questao) return ''
  if (questao.tipo === 'completar') return correcao.resposta_esperada ?? ''
  return questao.alternativas.find((a) => a.id === correcao.alternativa_correta_id)?.texto ?? ''
})

const scoreVariant = computed(() => {
  const percentual = resultado.value?.percentual ?? 0
  if (percentual >= 70) return 'success'
  if (percentual >= 40) return 'warning'
  return 'danger'
})
</script>
