<template>
  <header class="ui-page-header" :class="`ui-page-header--${variant}`">
    <BaseButton
      v-if="backTo"
      :to="backTo"
      :variant="variant === 'hero' ? 'secondary' : 'ghost'"
      size="sm"
      icon="arrow-left"
      class="ui-page-header__back"
    >
      {{ backLabel || t('common.voltar') }}
    </BaseButton>

    <div class="ui-page-header__main">
      <span v-if="icon" class="ui-page-header__icon" aria-hidden="true">
        <BaseIcon :name="icon" />
      </span>
      <div class="ui-page-header__text">
        <h1 class="ui-page-header__title">{{ title }}</h1>
        <p v-if="subtitle" class="ui-page-header__subtitle">{{ subtitle }}</p>
        <slot />
      </div>
      <div v-if="$slots.actions" class="ui-page-header__actions">
        <slot name="actions" />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { RouteLocationRaw } from 'vue-router'
import BaseButton from './BaseButton.vue'
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'

withDefaults(
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

const { t } = useI18n()
</script>

<style scoped>
.ui-page-header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--space-4);
}

.ui-page-header--hero {
  padding: var(--space-6);
  border-radius: var(--radius-2xl);
  background: var(--gradient-brand);
  color: var(--color-hero-text);
  box-shadow: var(--shadow-md);
}

.ui-page-header__main {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-4);
  width: 100%;
}

.ui-page-header__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 3.5rem;
  height: 3.5rem;
  border-radius: var(--radius-xl);
  background: var(--color-primary-soft);
  color: var(--color-primary-soft-text);
  font-size: 1.75rem;
}

.ui-page-header--hero .ui-page-header__icon {
  background: var(--color-surface);
  color: var(--color-primary);
}

.ui-page-header__text {
  flex: 1 1 16rem;
  min-width: 0;
}

.ui-page-header__title {
  font-size: var(--font-size-2xl);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
}

.ui-page-header__subtitle {
  margin-block-start: var(--space-1);
  color: var(--color-text-muted);
}

.ui-page-header--hero .ui-page-header__title {
  color: var(--color-hero-text);
}

.ui-page-header--hero .ui-page-header__subtitle {
  color: var(--color-hero-text-muted);
}

.ui-page-header__actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
}

@media (min-width: 768px) {
  .ui-page-header--hero {
    padding: var(--space-8);
  }

  .ui-page-header__title {
    font-size: var(--font-size-3xl);
  }
}

@media (max-width: 479.98px) {
  .ui-page-header__actions {
    width: 100%;
  }

  .ui-page-header__actions > :deep(*) {
    flex: 1 1 auto;
  }
}
</style>
