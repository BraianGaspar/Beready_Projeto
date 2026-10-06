<template>
  <div class="ui-progress" :class="[`ui-progress--${variant}`, `ui-progress--${size}`]">
    <div v-if="showLabel && (label || $slots.label)" class="ui-progress__header" aria-hidden="true">
      <span class="ui-progress__label">
        <slot name="label">{{ label }}</slot>
      </span>
      <span class="ui-progress__value">{{ displayText }}</span>
    </div>
    <div
      class="ui-progress__track"
      v-bind="decorative ? { 'aria-hidden': 'true' } : ariaAttrs"
    >
      <span class="ui-progress__fill" :style="{ '--ui-progress-value': `${percent}%` }"></span>
    </div>
  </div>
</template>

<script setup lang="ts">
// Barra de progresso acessível (role="progressbar"). A cor só reforça: o valor é
// anunciado por aria-valuenow/aria-valuetext e pode ser exibido com `showLabel`.
// `decorative`: quando o valor já está em texto visível/aria-live ao lado (ex.: força da senha).
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

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
    variant?: 'primary' | 'success' | 'warning' | 'danger' | 'info'
    size?: 'sm' | 'md' | 'lg'
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

<style scoped>
.ui-progress {
  --ui-progress-color: var(--color-primary);
  --ui-progress-height: var(--space-2);
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  min-width: 0;
}

.ui-progress__header {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  justify-content: space-between;
  gap: var(--space-1) var(--space-3);
  font-size: var(--font-size-sm);
}

.ui-progress__label {
  min-width: 0;
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
  overflow-wrap: anywhere;
}

.ui-progress__value {
  font-variant-numeric: tabular-nums;
  color: var(--color-text-muted);
}

.ui-progress__track {
  display: block;
  inline-size: 100%;
  block-size: var(--ui-progress-height);
  border-radius: var(--radius-full);
  background: var(--color-surface-muted);
  /* contorno para a trilha aparecer também sobre fundos muted */
  box-shadow: inset 0 0 0 var(--border-width) var(--color-border);
  overflow: hidden;
}

/* inline-size acompanha a direção do texto (RTL enche da direita para a esquerda) */
.ui-progress__fill {
  display: block;
  inline-size: var(--ui-progress-value);
  block-size: 100%;
  border-radius: var(--radius-full);
  background: var(--ui-progress-color);
  /* duração vira 0 com prefers-reduced-motion (tokens) */
  transition: inline-size var(--duration-slow) var(--easing-standard);
}

/* Tamanhos */
.ui-progress--sm {
  --ui-progress-height: 0.375rem;
}

.ui-progress--lg {
  --ui-progress-height: var(--space-3);
}

/* Tons */
.ui-progress--success {
  --ui-progress-color: var(--color-success);
}

.ui-progress--warning {
  --ui-progress-color: var(--color-warning);
}

.ui-progress--danger {
  --ui-progress-color: var(--color-danger);
}

.ui-progress--info {
  --ui-progress-color: var(--color-info);
}
</style>
