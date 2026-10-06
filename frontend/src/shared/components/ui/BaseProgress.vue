<template>
  <div class="ui-progress flex min-w-0 flex-col gap-2">
    <div
      v-if="showLabel && (label || $slots.label)"
      class="ui-progress__header flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 text-sm"
      aria-hidden="true"
    >
      <span class="ui-progress__label min-w-0 font-semibold text-text wrap-anywhere">
        <slot name="label">{{ label }}</slot>
      </span>
      <span class="ui-progress__value tabular-nums text-text-muted">{{ displayText }}</span>
    </div>
    <!-- ring-inset: contorno para a trilha aparecer também sobre fundos muted -->
    <div
      class="ui-progress__track block w-full overflow-hidden rounded-full bg-surface-muted ring-1 ring-inset ring-border"
      :class="heightClasses[size]"
      v-bind="decorative ? { 'aria-hidden': 'true' } : ariaAttrs"
    >
      <!-- inline-size acompanha a direção do texto (RTL enche da direita para a esquerda);
           a duração vira 0 com prefers-reduced-motion (tokens) -->
      <span
        class="ui-progress__fill block h-full rounded-full transition-size duration-slow"
        :class="colorClasses[variant]"
        :style="{ inlineSize: `${percent}%` }"
      ></span>
    </div>
  </div>
</template>

<script setup lang="ts">
// Barra de progresso acessível (role="progressbar"). A cor só reforça: o valor é
// anunciado por aria-valuenow/aria-valuetext e pode ser exibido com `showLabel`.
// `decorative`: quando o valor já está em texto visível/aria-live ao lado (ex.: força da senha).
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

type Variant = 'primary' | 'success' | 'warning' | 'danger' | 'info'
type Size = 'sm' | 'md' | 'lg'

const props = withDefaults(
  defineProps<{
    /** Valor atual (limitado entre min e max) */
    value: number
    min?: number
    max?: number
    /** Nome acessível (obrigatório quando não é decorativa) */
    label?: string
    /** Texto anunciado no lugar do número (padrão: porcentagem formatada no idioma) */
    valueText?: string
    variant?: Variant
    size?: Size
    /** Mostra rótulo e valor acima da barra */
    showLabel?: boolean
    /** Só visual: esconde de leitores de tela (o valor deve estar em texto em outro lugar) */
    decorative?: boolean
  }>(),
  {
    min: 0,
    max: 100,
    label: '',
    valueText: '',
    variant: 'primary',
    size: 'md',
    showLabel: false,
    decorative: false,
  },
)

defineSlots<{ label?: () => unknown }>()

const heightClasses: Record<Size, string> = {
  sm: 'h-1.5',
  md: 'h-2',
  lg: 'h-3',
}

const colorClasses: Record<Variant, string> = {
  primary: 'bg-primary',
  success: 'bg-success',
  warning: 'bg-warning',
  danger: 'bg-danger',
  info: 'bg-info',
}

const { locale } = useI18n()

const clamped = computed(() => {
  const n = Number.isFinite(props.value) ? props.value : props.min
  return Math.min(props.max, Math.max(props.min, n))
})

const percent = computed(() => {
  const range = props.max - props.min
  return range > 0 ? ((clamped.value - props.min) / range) * 100 : 0
})

const displayText = computed(
  () =>
    props.valueText ||
    new Intl.NumberFormat(locale.value, { style: 'percent', maximumFractionDigits: 0 }).format(
      percent.value / 100,
    ),
)

const ariaAttrs = computed(() => ({
  role: 'progressbar',
  'aria-label': props.label || undefined,
  'aria-valuemin': props.min,
  'aria-valuemax': props.max,
  'aria-valuenow': clamped.value,
  'aria-valuetext': displayText.value,
}))
</script>
