<template>
  <header
    class="ui-page-header flex flex-col items-start gap-4"
    :class="hero && 'rounded-2xl bg-brand p-6 text-hero-text shadow-md md:p-8'"
  >
    <BaseButton
      v-if="backTo"
      :to="backTo"
      :variant="hero ? 'secondary' : 'ghost'"
      size="sm"
      icon="arrow-left"
      class="ui-page-header__back"
    >
      {{ backLabel || t('common.voltar') }}
    </BaseButton>

    <div class="ui-page-header__main flex w-full flex-wrap items-center gap-4">
      <span
        v-if="icon"
        class="ui-page-header__icon inline-flex size-14 shrink-0 items-center justify-center rounded-xl text-icon-lg"
        :class="hero ? 'bg-surface text-primary' : 'bg-primary-soft text-primary-soft-text'"
        aria-hidden="true"
      >
        <BaseIcon :name="icon" />
      </span>
      <div class="ui-page-header__text min-w-0 shrink grow basis-64">
        <h1
          class="ui-page-header__title text-2xl font-bold md:text-3xl"
          :class="hero ? 'text-hero-text' : 'text-text'"
        >
          {{ title }}
        </h1>
        <p
          v-if="subtitle"
          class="ui-page-header__subtitle mt-1"
          :class="hero ? 'text-hero-text-muted' : 'text-text-muted'"
        >
          {{ subtitle }}
        </p>
        <slot />
      </div>
      <!-- < 480px: ações em largura total, dividindo a linha -->
      <div v-if="$slots.actions" class="ui-page-header__actions flex flex-wrap gap-2 max-sm:w-full max-sm:*:flex-auto">
        <slot name="actions" />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { RouteLocationRaw } from 'vue-router'
import BaseButton from './BaseButton.vue'
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'

const props = withDefaults(
  defineProps<{
    /** Título da tela (vira o <h1> — uma por página) */
    title: string
    subtitle?: string
    icon?: IconName
    /** Mostra o botão "Voltar" apontando para esta rota */
    backTo?: RouteLocationRaw
    backLabel?: string
    /** hero = faixa com o gradiente da marca · plain = sem fundo */
    variant?: 'hero' | 'plain'
  }>(),
  { subtitle: '', icon: undefined, backTo: undefined, backLabel: '', variant: 'hero' },
)

defineSlots<{
  /** Conteúdo extra abaixo do subtítulo (ex.: badges, filtros) */
  default?: () => unknown
  /** Ações principais (ex.: BaseButton "Novo") — vão para baixo do título no mobile */
  actions?: () => unknown
}>()

const hero = computed(() => props.variant === 'hero')

const { t } = useI18n()
</script>
