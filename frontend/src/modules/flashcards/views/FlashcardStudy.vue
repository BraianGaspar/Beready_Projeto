<template>
  <PageContainer size="sm" class="fcard-study">
    <PageHeader variant="plain" :title="$t('flashcards.estudar')" back-to="/flashcards">
      <BaseBadge v-if="flashcard" :variant="nivelVariant" class="fcard-study__level">
        {{ getLevelText(flashcard.nivel_dificuldade) }}
      </BaseBadge>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('flashcardStudy.loading')" />

    <section v-else-if="flashcard" class="fcard-study__body">
      <!-- <button> nativo: Enter/Espaço viram o card; aria-pressed anuncia o lado -->
      <button
        type="button"
        class="fcard-study__card"
        :class="{ 'fcard-study__card--flipped': isFlipped }"
        :aria-pressed="isFlipped"
        @click="flipCard"
      >
        <span class="fcard-study__inner">
          <span class="fcard-study__face fcard-study__face--front" :aria-hidden="isFlipped">
            <BaseBadge variant="primary" size="sm">{{ $t('flashcards.perguntaLabel') }}</BaseBadge>
            <span class="fcard-study__text">{{ flashcard.pergunta }}</span>
            <span class="fcard-study__hint">
              <BaseIcon name="refresh" />
              {{ $t('flashcardStudy.flipHintKeyboard') }}
            </span>
          </span>

          <span class="fcard-study__face fcard-study__face--back" :aria-hidden="!isFlipped">
            <BaseBadge variant="info" size="sm">{{ $t('flashcards.respostalabel') }}</BaseBadge>
            <span class="fcard-study__text">{{ flashcard.resposta }}</span>
            <span class="fcard-study__hint">
              <BaseIcon name="refresh" />
              {{ $t('flashcardStudy.flipBackHint') }}
            </span>
          </span>
        </span>
      </button>

      <!-- Avaliação: invisível (e fora da ordem de Tab) até o card ser virado -->
      <div
        class="fcard-study__dock"
        :class="{ 'fcard-study__dock--visible': isFlipped }"
        role="group"
        :aria-label="$t('flashcardStudy.rateTitle')"
      >
        <p class="fcard-study__dock-title">{{ $t('flashcardStudy.rateTitle') }}</p>
        <div class="fcard-study__rates">
          <BaseButton
            variant="danger"
            size="lg"
            icon="x-circle"
            class="fcard-study__rate"
            @click.stop="rateCard('hard')"
          >
            {{ $t('flashcardStudy.rateHard') }}
          </BaseButton>
          <BaseButton
            variant="primary"
            size="lg"
            icon="check-circle"
            class="fcard-study__rate"
            @click.stop="rateCard('good')"
          >
            {{ $t('flashcardStudy.rateGood') }}
          </BaseButton>
          <BaseButton
            variant="success"
            size="lg"
            icon="sparkles"
            class="fcard-study__rate"
            @click.stop="rateCard('easy')"
          >
            {{ $t('flashcardStudy.rateEasy') }}
          </BaseButton>
        </div>
      </div>
    </section>

    <BaseModal
      v-model="showCompletionModal"
      :title="$t('flashcardStudy.completedTitle')"
      :description="$t('flashcardStudy.completedMessage')"
      size="sm"
      hide-close
      :close-on-overlay="false"
      :close-on-esc="false"
    >
      <div class="fcard-study__stats">
        <StatCard
          :label="$t('flashcardStudy.wrongCount')"
          :value="stats.hard"
          icon="x-circle"
          variant="danger"
        />
        <StatCard
          :label="$t('flashcardStudy.correctCount')"
          :value="stats.good + stats.easy"
          icon="check-circle"
          variant="success"
        />
      </div>
      <template #footer>
        <BaseButton variant="secondary" icon="refresh" @click="studyAgain">
          {{ $t('flashcardStudy.studyAgain') }}
        </BaseButton>
        <BaseButton icon="arrow-left" @click="goBack">{{ $t('flashcardStudy.backToDecks') }}</BaseButton>
      </template>
    </BaseModal>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import {
  BaseBadge,
  BaseButton,
  BaseIcon,
  BaseModal,
  BaseSpinner,
  PageContainer,
  PageHeader,
  StatCard,
} from '@/shared/components/ui'
import { getNivelVariant } from '@/shared/utils/nivelDificuldade'
import { useFlashcardStudy } from './FlashcardStudy'

const {
  flashcard,
  loading,
  isFlipped,
  showCompletionModal,
  stats,
  goBack,
  flipCard,
  rateCard,
  studyAgain,
  getLevelText,
} = useFlashcardStudy()

const nivelVariant = computed(() => getNivelVariant(flashcard.value?.nivel_dificuldade))
</script>

<style scoped>
@import '@/styles/views/flashcards/flashcard-study.css';
</style>
