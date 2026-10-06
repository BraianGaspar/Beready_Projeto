<template>
  <component :is="rootTag" v-bind="rootAttrs" :class="classes">
    <span
      v-if="loading"
      class="ui-button__spinner motion-safe size-em rounded-full border-2 border-solid border-current border-e-transparent animate-spinner-fast"
      aria-hidden="true"
    ></span>
    <BaseIcon v-else-if="icon" :name="icon" class="ui-button__icon size-em-lg" />
    <span v-if="$slots.default" class="ui-button__label overflow-hidden text-ellipsis"><slot /></span>
    <BaseIcon v-if="iconEnd && !loading" :name="iconEnd" class="ui-button__icon size-em-lg" />
  </component>
</template>

<script setup lang="ts">
import { computed, useSlots } from 'vue'
import { RouterLink, type RouteLocationRaw } from 'vue-router'
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'

type Variant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'success' | 'ghost-danger'
type Size = 'sm' | 'md' | 'lg'

const props = withDefaults(
  defineProps<{
    variant?: Variant
    size?: Size
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
    /** Texto pode quebrar linha e o respiro lateral diminui (botões lado a lado em coluna estreita) */
    wrap?: boolean
    /** Ícone acima do texto, respiro compacto e texto que quebra (vários botões numa linha em 360px) */
    stacked?: boolean
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
    wrap: false,
    stacked: false,
    to: undefined,
  },
)

defineSlots<{ default?: () => unknown }>()
const slots = useSlots()

const isDisabled = computed(() => props.disabled || props.loading)
// Sem texto = botão só de ícone (exige aria-label no uso)
const iconOnly = computed(() => !slots.default && !!(props.icon || props.iconEnd))

const base =
  'ui-button items-center justify-center border border-solid rounded-md font-semibold leading-tight ' +
  'text-center no-underline select-none transition-control focus-visible:focus-ring'

// Cor de repouso (a borda transparente mantém a mesma altura da variante secondary)
const variantClasses: Record<Variant, string> = {
  primary: 'bg-primary text-primary-contrast border-transparent',
  secondary: 'bg-surface text-text border-border-strong',
  ghost: 'bg-transparent text-primary border-transparent',
  danger: 'bg-danger text-danger-contrast border-transparent',
  success: 'bg-success text-success-contrast border-transparent',
  'ghost-danger': 'bg-transparent text-danger border-transparent',
}

// Hover/active só quando habilitado (o texto é repetido para vencer `a:hover` do reset base (tailwind.css))
const interactiveClasses: Record<Variant, string> = {
  primary: 'hover:bg-primary-hover hover:text-primary-contrast active:bg-primary-active',
  secondary: 'hover:bg-surface-hover hover:text-text',
  ghost: 'hover:bg-primary-soft hover:text-primary-soft-text',
  danger: 'hover:bg-danger-hover hover:text-danger-contrast',
  success: 'hover:bg-success-hover hover:text-success-contrast',
  'ghost-danger': 'hover:bg-danger-soft hover:text-danger',
}

const sizeClasses: Record<Size, string> = {
  sm: 'min-h-control-sm text-xs',
  md: 'min-h-control text-sm',
  lg: 'min-h-control-lg text-base',
}

// Respiro: padrão | `wrap` (lateral menor) | `stacked` (compacto, ícone acima do texto)
const padClasses: Record<Size, string> = { sm: 'py-1 px-3', md: 'py-2 px-5', lg: 'py-3 px-6' }
const wrapPadClasses: Record<Size, string> = { sm: 'py-1 px-3', md: 'py-2 px-3', lg: 'py-3 px-3' }

// Só ícone: quadrado com a altura do controle
const iconOnlyClasses: Record<Size, string> = {
  sm: 'min-h-control-sm w-control-sm p-0 text-xs',
  md: 'min-h-control w-control p-0 text-sm',
  lg: 'min-h-control-lg w-control-lg p-0 text-base',
}

const classes = computed(() => [
  base,
  props.block ? 'flex w-full' : 'inline-flex',
  variantClasses[props.variant],
  props.stacked ? 'flex-col gap-1' : 'gap-2',
  props.wrap || props.stacked ? 'whitespace-normal' : 'whitespace-nowrap',
  iconOnly.value
    ? iconOnlyClasses[props.size]
    : [
        sizeClasses[props.size],
        props.stacked ? 'py-3 px-2' : (props.wrap ? wrapPadClasses : padClasses)[props.size],
      ],
  isDisabled.value ? 'opacity-disabled' : interactiveClasses[props.variant],
  // `disabled:` vence `button:disabled { cursor: not-allowed }` do reset base (tailwind.css)
  props.loading
    ? 'cursor-progress disabled:cursor-progress'
    : isDisabled.value
      ? 'cursor-not-allowed'
      : 'cursor-pointer',
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
