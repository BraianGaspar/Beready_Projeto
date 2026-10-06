<template>
  <component
    :is="to ? RouterLink : 'div'"
    :to="to"
    class="ui-stat flex min-w-0 items-start gap-4 rounded-xl border border-solid border-border bg-surface p-5 text-text no-underline shadow-sm"
    :class="to && 'transition-card hover:border-primary hover:text-text hover:shadow-md'"
  >
    <span
      v-if="icon"
      class="ui-stat__icon inline-flex size-12 shrink-0 items-center justify-center rounded-lg text-2xl"
      :class="iconClasses[variant]"
      aria-hidden="true"
    >
      <BaseIcon :name="icon" />
    </span>
    <div class="ui-stat__body min-w-0 flex-1">
      <p class="ui-stat__label text-sm text-text-muted">{{ label }}</p>
      <p class="ui-stat__value text-2xl font-bold leading-tight text-text wrap-anywhere">{{ value }}</p>
      <p v-if="hint || $slots.hint" class="ui-stat__hint mt-1 text-xs text-text-muted">
        <slot name="hint">{{ hint }}</slot>
      </p>
    </div>
    <BaseBadge v-if="trend" :variant="trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'neutral'" size="sm" :icon="trendIcon">
      {{ trendLabel }}
    </BaseBadge>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink, type RouteLocationRaw } from 'vue-router'
import BaseBadge from './BaseBadge.vue'
import BaseIcon from './BaseIcon.vue'
import type { IconName, StatusVariant } from './icons'

type Variant = 'primary' | 'neutral' | StatusVariant

const props = withDefaults(
  defineProps<{
    label: string
    value: string | number
    icon?: IconName
    /** Cor do ícone (não carrega significado sozinha; o rótulo diz o que é) */
    variant?: Variant
    hint?: string
    /** Tendência opcional: mostra badge com ícone + texto (trendLabel) */
    trend?: 'up' | 'down' | 'flat'
    trendLabel?: string
    /** Torna o card um link */
    to?: RouteLocationRaw
  }>(),
  {
    icon: undefined,
    variant: 'primary',
    hint: '',
    trend: undefined,
    trendLabel: '',
    to: undefined,
  },
)

defineSlots<{ hint?: () => unknown }>()

const iconClasses: Record<Variant, string> = {
  primary: 'bg-primary-soft text-primary-soft-text',
  neutral: 'bg-surface-muted text-text-muted',
  success: 'bg-success-soft text-success',
  warning: 'bg-warning-soft text-warning',
  danger: 'bg-danger-soft text-danger',
  info: 'bg-info-soft text-info',
}

const trendIcon = computed<IconName | false>(() => {
  if (props.trend === 'up') return 'trending-up'
  if (props.trend === 'down') return 'trending-down'
  return false
})
</script>
