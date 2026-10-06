<template>
  <PageContainer size="md" class="quiz-form">
    <PageHeader
      :title="$t('quizes.newQuiz')"
      :subtitle="$t('quizes.createSubtitle')"
      icon="document"
      back-to="/quizes"
    />

    <BaseCard>
      <!-- novalidate: a validação do título é feita em useQuizAdd (mensagem traduzida no campo) -->
      <form class="quiz-form__form" novalidate @submit.prevent="handleSubmit">
        <div class="quiz-form__grid">
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
            class="quiz-form__full"
            :label="$t('quizes.descricao')"
            :placeholder="$t('quizes.descricaoPlaceholder')"
            :rows="4"
          />
          <BaseInput
            :model-value="form.total_questoes"
            type="number"
            inputmode="numeric"
            min="0"
            :label="$t('quizes.totalQuestoes')"
            :placeholder="$t('quizes.totalQuestoesPlaceholder')"
            @update:model-value="form.total_questoes = Number($event) || 0"
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
          <BaseCheckbox v-model="form.publico" class="quiz-form__full" :label="$t('quizes.publico')" />
        </div>

        <div class="quiz-form__actions">
          <BaseButton variant="secondary" to="/quizes">{{ $t('common.cancelar') }}</BaseButton>
          <BaseButton type="submit" icon="check" :loading="loading">
            {{ loading ? $t('common.salvando') : $t('quizes.createButton') }}
          </BaseButton>
        </div>
      </form>
    </BaseCard>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
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
import { useQuizAdd } from './QuizAdd'

const { t } = useI18n()
const { form, errors, loading, handleSubmit } = useQuizAdd()

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('common.iniciante') },
  { value: 'intermediario', label: t('common.intermediario') },
  { value: 'avancado', label: t('common.avancado') },
])
</script>

<style scoped>
@import '@/styles/views/quizes/quiz-form.css';
</style>
