<template>
  <span
    class="ui-badge inline-flex max-w-full items-center gap-1 rounded-full border border-solid align-middle font-semibold leading-base"
    :class="[sizeClasses[size], (solid ? solidClasses : softClasses)[variant], wrap ? 'whitespace-normal' : 'whitespace-nowrap']"
  >
    <BaseIcon v-if="iconName" :name="iconName" class="ui-badge__icon size-em-md" />
    <slot />
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseIcon from './BaseIcon.vue'
import { statusIcons, type IconName, type StatusVariant } from './icons'

type Variant = 'neutral' | 'primary' | StatusVariant
type Size = 'sm' | 'md'

const props = withDefaults(
  defineProps<{
    variant?: Variant
    size?: Size
    /**
     * Ícone: nome do registro, ou `true` para o ícone padrão do status
     * (recomendado em success/warning/danger/info: status não pode depender só de cor).
     */
    icon?: IconName | boolean
    /** Fundo sólido em vez de suave */
    solid?: boolean
    /** Texto longo pode quebrar linha (padrão: uma linha só) */
    wrap?: boolean
  }>(),
  { variant: 'neutral', size: 'md', icon: false, solid: false, wrap: false },
)

defineSlots<{ default?: () => unknown }>()

const sizeClasses: Record<Size, string> = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-3 py-1 text-sm',
}

// Suave: fundo -soft + texto no tom (neutral ganha borda)
const softClasses: Record<Variant, string> = {
  neutral: 'border-border bg-surface-muted text-text-muted',
  primary: 'border-transparent bg-primary-soft text-primary-soft-text',
  success: 'border-transparent bg-success-soft text-success',
  warning: 'border-transparent bg-warning-soft text-warning',
  danger: 'border-transparent bg-danger-soft text-danger',
  info: 'border-transparent bg-info-soft text-info',
}

// Sólido: fundo no tom + texto -contrast (a borda é a mesma da versão suave)
const solidClasses: Record<Variant, string> = {
  neutral: 'border-border bg-text-muted text-surface',
  primary: 'border-transparent bg-primary text-primary-contrast',
  success: 'border-transparent bg-success text-success-contrast',
  warning: 'border-transparent bg-warning text-warning-contrast',
  danger: 'border-transparent bg-danger text-danger-contrast',
  info: 'border-transparent bg-info text-info-contrast',
}

const iconName = computed<IconName | null>(() => {
  if (typeof props.icon === 'string') return props.icon
  if (props.icon === true && props.variant in statusIcons) {
    return statusIcons[props.variant as StatusVariant]
  }
  return null
})
</script>
