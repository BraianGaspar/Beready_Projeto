<template>
  <component :is="rootTag" v-bind="rootAttrs" :class="classes">
    <span v-if="loading" class="ui-button__spinner motion-safe" aria-hidden="true"></span>
    <BaseIcon v-else-if="icon" :name="icon" class="ui-button__icon" />
    <span v-if="$slots.default" class="ui-button__label"><slot /></span>
    <BaseIcon v-if="iconEnd && !loading" :name="iconEnd" class="ui-button__icon" />
  </component>
</template>

<script setup lang="ts">
import { computed, useSlots } from 'vue'
import { RouterLink, type RouteLocationRaw } from 'vue-router'
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'

const props = withDefaults(
  defineProps<{
    variant?: 'primary' | 'secondary' | 'ghost' | 'danger' | 'success' | 'ghost-danger'
    size?: 'sm' | 'md' | 'lg'
    type?: 'button' | 'submit' | 'reset'
    /** Mostra indicador e desabilita o botão */
    loading?: boolean
    disabled?: boolean
    /** Ícone antes do texto */
    icon?: IconName
    /** Ícone depois do texto */
    iconEnd?: IconName
    /** Ocupa 100% da largura */
    block?: boolean
    /** Quando informado, renderiza <router-link> */
    to?: RouteLocationRaw
  }>(),
  {
    variant: 'primary',
    size: 'md',
    type: 'button',
    loading: false,
    disabled: false,
    icon: undefined,
    iconEnd: undefined,
    block: false,
    to: undefined,
  },
)

defineSlots<{ default?: () => unknown }>()
const slots = useSlots()

const isDisabled = computed(() => props.disabled || props.loading)
// Sem texto = botão só de ícone (exige aria-label no uso)
const iconOnly = computed(() => !slots.default && !!(props.icon || props.iconEnd))

const classes = computed(() => [
  'ui-button',
  `ui-button--${props.variant}`,
  `ui-button--${props.size}`,
  {
    'ui-button--block': props.block,
    'ui-button--icon-only': iconOnly.value,
    'ui-button--loading': props.loading,
    'ui-button--disabled': isDisabled.value,
  },
])

// <button> | <router-link> | <span> (link desabilitado: não navega e é anunciado como tal)
const rootTag = computed(() => {
  if (!props.to) return 'button'
  return isDisabled.value ? 'span' : RouterLink
})

const rootAttrs = computed(() => {
  if (!props.to) {
    return { type: props.type, disabled: isDisabled.value, 'aria-busy': props.loading || undefined }
  }
  if (isDisabled.value) return { role: 'link', 'aria-disabled': 'true' }
  return { to: props.to }
})
</script>

<style scoped>
.ui-button {
  --ui-button-height: var(--control-height-md);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  min-height: var(--ui-button-height);
  padding: var(--space-2) var(--space-5);
  border: var(--border-width) solid transparent;
  border-radius: var(--radius-md);
  font-family: inherit;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  line-height: var(--line-height-tight);
  text-align: center;
  text-decoration: none;
  white-space: nowrap;
  cursor: pointer;
  user-select: none;
  transition:
    background-color var(--transition-base),
    border-color var(--transition-base),
    color var(--transition-base),
    box-shadow var(--transition-base);
}

.ui-button__label {
  overflow: hidden;
  text-overflow: ellipsis;
}

.ui-button__icon {
  width: 1.25em;
  height: 1.25em;
}

/* Tamanhos */
.ui-button--sm {
  --ui-button-height: var(--control-height-sm);
  padding: var(--space-1) var(--space-3);
  font-size: var(--font-size-xs);
}

.ui-button--lg {
  --ui-button-height: var(--control-height-lg);
  padding: var(--space-3) var(--space-6);
  font-size: var(--font-size-base);
}

.ui-button--block {
  display: flex;
  width: 100%;
}

.ui-button--icon-only {
  width: var(--ui-button-height);
  padding: 0;
}

/* Variantes */
.ui-button--primary {
  background: var(--color-primary);
  color: var(--color-primary-contrast);
}

.ui-button--primary:not(.ui-button--disabled):hover {
  background: var(--color-primary-hover);
  color: var(--color-primary-contrast);
}

.ui-button--primary:not(.ui-button--disabled):active {
  background: var(--color-primary-active);
}

.ui-button--secondary {
  background: var(--color-surface);
  border-color: var(--color-border-strong);
  color: var(--color-text);
}

.ui-button--secondary:not(.ui-button--disabled):hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.ui-button--ghost {
  background: transparent;
  color: var(--color-primary);
}

.ui-button--ghost:not(.ui-button--disabled):hover {
  background: var(--color-primary-soft);
  color: var(--color-primary-soft-text);
}

.ui-button--danger {
  background: var(--color-danger);
  color: var(--color-danger-contrast);
}

.ui-button--danger:not(.ui-button--disabled):hover {
  background: var(--color-danger-hover);
  color: var(--color-danger-contrast);
}

.ui-button--success {
  background: var(--color-success);
  color: var(--color-success-contrast);
}

.ui-button--success:not(.ui-button--disabled):hover {
  background: var(--color-success-hover);
  color: var(--color-success-contrast);
}

/* Ação destrutiva secundária (ex.: excluir em listas, cancelar assinatura) */
.ui-button--ghost-danger {
  background: transparent;
  color: var(--color-danger);
}

.ui-button--ghost-danger:not(.ui-button--disabled):hover {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

/* Estados */
.ui-button:focus-visible {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: var(--focus-ring-offset);
}

.ui-button--disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.ui-button--loading {
  cursor: progress;
}

.ui-button__spinner {
  width: 1em;
  height: 1em;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: var(--radius-full);
  animation: ui-button-spin 0.7s linear infinite;
}

@keyframes ui-button-spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
