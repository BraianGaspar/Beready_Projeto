<template>
  <section class="quiz-editor flex flex-col gap-4" :aria-labelledby="titleId">
    <div class="quiz-editor__head flex flex-wrap items-center justify-between gap-3">
      <h2 :id="titleId" class="quiz-editor__title text-lg font-semibold text-text">{{ $t('quizEditor.titulo') }}</h2>
      <BaseBadge variant="neutral" icon="document">{{ $t('quizEditor.contagem', { n: questoes.length }) }}</BaseBadge>
    </div>

    <p v-if="errors.questoes" class="quiz-editor__error flex items-center gap-2 text-sm font-medium text-danger" role="alert">
      <BaseIcon name="x-circle" />
      {{ errors.questoes }}
    </p>

    <EmptyState
      v-if="!questoes.length"
      compact
      icon="clipboard"
      :title="$t('quizEditor.emptyTitle')"
      :description="$t('quizEditor.emptyDescription')"
    />

    <ol v-else class="quiz-editor__list m-0 flex list-none flex-col gap-4 p-0" role="list">
      <li v-for="(questao, i) in questoes" :key="questao.key">
        <BaseCard as="article" muted :title="$t('quizEditor.questaoN', { n: i + 1 })" title-tag="h3" padding="sm">
          <template #actions>
            <BaseButton
              variant="ghost"
              size="sm"
              icon="arrow-up"
              :disabled="i === 0"
              :aria-label="$t('quizEditor.moverParaCima', { n: i + 1 })"
              @click="mover(i, -1)"
            />
            <BaseButton
              variant="ghost"
              size="sm"
              icon="arrow-down"
              :disabled="i === questoes.length - 1"
              :aria-label="$t('quizEditor.moverParaBaixo', { n: i + 1 })"
              @click="mover(i, 1)"
            />
            <BaseButton
              variant="ghost-danger"
              size="sm"
              icon="trash"
              :aria-label="$t('quizEditor.removerQuestao', { n: i + 1 })"
              @click="remover(i)"
            />
          </template>

          <div class="quiz-editor__fields flex flex-col gap-4">
            <BaseSelect
              :model-value="questao.tipo"
              :label="$t('quizEditor.tipo')"
              :options="tipoOptions"
              @update:model-value="mudarTipo(questao, String($event))"
            />

            <BaseTextarea
              :id="`quiz-q-${questao.key}-enunciado`"
              v-model="questao.enunciado"
              :label="$t('quizEditor.enunciado')"
              :placeholder="questao.tipo === 'completar' ? $t('quizEditor.enunciadoCompletarPlaceholder') : $t('quizEditor.enunciadoPlaceholder')"
              :error="errors[`questoes.${i}.enunciado`]"
              :rows="2"
              required
            />

            <fieldset
              v-if="questao.tipo === 'multipla_escolha'"
              class="quiz-editor__alternativas m-0 flex min-w-0 flex-col gap-3 border-0 p-0"
              :aria-describedby="errors[`questoes.${i}.alternativas`] ? `quiz-q-${questao.key}-alt-erro` : undefined"
            >
              <legend class="quiz-editor__legend mb-1 p-0 text-sm font-semibold text-text">{{ $t('quizEditor.alternativas') }}</legend>
              <p class="quiz-editor__hint text-sm text-text-muted">{{ $t('quizEditor.alternativasHint') }}</p>

              <div
                v-for="(alternativa, j) in questao.alternativas"
                :key="alternativa.key"
                class="quiz-editor__alternativa flex items-start gap-2 sm:gap-3"
              >
                <label class="quiz-editor__correta mt-6 flex min-h-control shrink-0 cursor-pointer items-center gap-2 text-sm font-medium text-text">
                  <input
                    type="radio"
                    class="size-5 cursor-pointer accent-primary focus-visible:focus-ring"
                    :name="`quiz-q-${questao.key}-correta`"
                    :checked="alternativa.correta"
                    @change="marcarCorreta(questao, j)"
                  />
                  <span aria-hidden="true">{{ $t('quizEditor.correta') }}</span>
                  <span class="sr-only">{{ $t('quizEditor.marcarCorreta', { letra: letra(j) }) }}</span>
                </label>
                <BaseInput
                  v-model="alternativa.texto"
                  class="min-w-0 flex-1"
                  :label="$t('quizEditor.alternativaN', { letra: letra(j) })"
                  :error="errors[`questoes.${i}.alternativas.${j}.texto`]"
                />
                <BaseButton
                  variant="ghost-danger"
                  icon="trash"
                  class="mt-6 shrink-0"
                  :disabled="questao.alternativas.length <= MIN_ALTERNATIVAS"
                  :aria-label="$t('quizEditor.removerAlternativa', { letra: letra(j) })"
                  @click="removerAlternativa(questao, j)"
                />
              </div>

              <p
                v-if="errors[`questoes.${i}.alternativas`]"
                :id="`quiz-q-${questao.key}-alt-erro`"
                class="quiz-editor__error flex items-center gap-2 text-sm font-medium text-danger"
                role="alert"
              >
                <BaseIcon name="x-circle" />
                {{ errors[`questoes.${i}.alternativas`] }}
              </p>

              <BaseButton
                variant="ghost"
                size="sm"
                icon="plus"
                class="self-start"
                :disabled="questao.alternativas.length >= MAX_ALTERNATIVAS"
                @click="adicionarAlternativa(questao)"
              >
                {{ $t('quizEditor.adicionarAlternativa') }}
              </BaseButton>
            </fieldset>

            <BaseInput
              v-else
              v-model="questao.resposta_esperada"
              :label="$t('quizEditor.respostaEsperada')"
              :hint="$t('quizEditor.respostaEsperadaHint')"
              :error="errors[`questoes.${i}.resposta_esperada`]"
              required
            />

            <BaseTextarea
              v-model="questao.explicacao"
              :label="$t('quizEditor.explicacao')"
              :hint="$t('quizEditor.explicacaoHint')"
              :error="errors[`questoes.${i}.explicacao`]"
              :rows="2"
            />
          </div>
        </BaseCard>
      </li>
    </ol>

    <div class="quiz-editor__add flex flex-wrap gap-3 *:grow *:basis-full sm:*:grow-0 sm:*:basis-auto">
      <BaseButton variant="secondary" icon="plus" :disabled="questoes.length >= MAX_QUESTOES" @click="adicionar('multipla_escolha')">
        {{ $t('quizEditor.adicionarMultipla') }}
      </BaseButton>
      <BaseButton variant="secondary" icon="plus" :disabled="questoes.length >= MAX_QUESTOES" @click="adicionar('completar')">
        {{ $t('quizEditor.adicionarCompletar') }}
      </BaseButton>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, nextTick, useId } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseInput,
  BaseSelect,
  BaseTextarea,
  EmptyState,
  type SelectOption,
} from '@/shared/components/ui'
import type { QuizQuestaoTipo } from '@/core/types'
import {
  MAX_ALTERNATIVAS,
  MAX_QUESTOES,
  MIN_ALTERNATIVAS,
  novaAlternativa,
  novaQuestao,
  type QuestaoForm,
  type QuestoesErrors,
} from '../composables/useQuestoesForm'

withDefaults(defineProps<{ errors?: QuestoesErrors }>(), { errors: () => ({}) })

const questoes = defineModel<QuestaoForm[]>({ required: true })

const { t } = useI18n()
const titleId = useId()

const tipoOptions = computed<SelectOption[]>(() => [
  { value: 'multipla_escolha', label: t('quizEditor.tipoMultipla') },
  { value: 'completar', label: t('quizEditor.tipoCompletar') },
])

// A, B, C... (rótulo das alternativas)
const letra = (indice: number) => String.fromCharCode(65 + indice)

const focarEnunciado = async (questao: QuestaoForm) => {
  await nextTick()
  document.getElementById(`quiz-q-${questao.key}-enunciado`)?.focus()
}

const adicionar = (tipo: QuizQuestaoTipo) => {
  const questao = novaQuestao(tipo)
  questoes.value.push(questao)
  focarEnunciado(questao)
}

const remover = (indice: number) => {
  questoes.value.splice(indice, 1)
}

const mover = (indice: number, delta: number) => {
  const destino = indice + delta
  if (destino < 0 || destino >= questoes.value.length) return
  const [questao] = questoes.value.splice(indice, 1)
  if (questao) questoes.value.splice(destino, 0, questao)
}

const mudarTipo = (questao: QuestaoForm, tipo: string) => {
  questao.tipo = tipo === 'completar' ? 'completar' : 'multipla_escolha'
  if (questao.tipo === 'multipla_escolha' && questao.alternativas.length === 0) {
    questao.alternativas = [novaAlternativa(true), novaAlternativa()]
  }
}

const marcarCorreta = (questao: QuestaoForm, indice: number) => {
  questao.alternativas.forEach((alternativa, j) => {
    alternativa.correta = j === indice
  })
}

const adicionarAlternativa = (questao: QuestaoForm) => {
  if (questao.alternativas.length >= MAX_ALTERNATIVAS) return
  questao.alternativas.push(novaAlternativa(questao.alternativas.length === 0))
}

const removerAlternativa = (questao: QuestaoForm, indice: number) => {
  if (questao.alternativas.length <= MIN_ALTERNATIVAS) return
  const [removida] = questao.alternativas.splice(indice, 1)
  // Sem correta depois de remover a marcada: a primeira passa a ser a correta (o usuário pode trocar)
  const primeira = questao.alternativas[0]
  if (removida?.correta && primeira) primeira.correta = true
}
</script>
