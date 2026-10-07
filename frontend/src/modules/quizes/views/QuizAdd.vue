<template>
  <PageContainer size="md" class="quiz-form">
    <PageHeader
      :title="$t('quizes.newQuiz')"
      :subtitle="$t('quizes.createSubtitle')"
      icon="document"
      back-to="/quizes"
    />

    <!-- novalidate: a validação é feita em useQuizAdd (mensagens traduzidas nos campos) -->
    <form class="quiz-form__form flex flex-col gap-6" novalidate @submit.prevent="handleSubmit">
      <BaseCard>
        <div class="quiz-form__grid grid grid-cols-fit-56 gap-5">
          <BaseInput
            v-model="form.titulo"
            :label="$t('quizes.titulo')"
            :placeholder="$t('quizes.tituloPlaceholder')"
            :error="errors.titulo"
            required
          />
          <BaseSelect
            :model-value="form.nivel_dificuldade"
            :label="$t('quizes.nivel')"
            :options="nivelOptions"
            @update:model-value="form.nivel_dificuldade = normalizeNivel(String($event))"
          />
          <BaseTextarea
            v-model="form.descricao"
            class="quiz-form__full col-span-full"
            :label="$t('quizes.descricao')"
            :placeholder="$t('quizes.descricaoPlaceholder')"
            :rows="3"
          />
          <BaseInput
            :model-value="form.tempo_limite ?? ''"
            type="number"
            inputmode="numeric"
            min="0"
            :label="$t('quizes.tempoLimite')"
            :placeholder="$t('quizes.tempoPlaceholder')"
            @update:model-value="form.tempo_limite = $event === '' || $event == null ? null : Number($event)"
          />
          <BaseCheckbox v-model="form.publico" class="quiz-form__full col-span-full" :label="$t('quizes.publico')" />
        </div>
      </BaseCard>

      <BaseCard>
        <QuizQuestoesEditor v-model="questoes" :errors="questoesErrors" />
      </BaseCard>

      <BaseAlert v-if="hasQuestaoErrors" variant="danger" :message="$t('quizEditor.erroRevisar')" />

      <div class="quiz-form__actions flex flex-wrap justify-end gap-3 *:shrink *:grow *:basis-full sm:*:shrink-0 sm:*:grow-0 sm:*:basis-auto">
        <BaseButton variant="secondary" to="/quizes">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" icon="check" :loading="loading">
          {{ loading ? $t('common.salvando') : $t('quizes.createButton') }}
        </BaseButton>
      </div>
    </form>
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
  BaseTextarea,
  PageContainer,
  PageHeader,
  type SelectOption,
} from '@/shared/components/ui'
import { normalizeNivel } from '@/shared/utils/nivelDificuldade'
import QuizQuestoesEditor from '../components/QuizQuestoesEditor.vue'
import { useQuizAdd } from './QuizAdd'

const { t } = useI18n()
const { form, errors, questoes, questoesErrors, loading, handleSubmit } = useQuizAdd()

const hasQuestaoErrors = computed(() => Object.keys(questoesErrors.value).length > 0)

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])
</script>
