<template>
  <div class="password-meter flex flex-col gap-1">
    <!-- Barra só visual: o nível é anunciado pelo texto (aria-live) abaixo -->
    <BaseProgress :value="percent" :variant="variant" size="sm" decorative />
    <p class="password-meter__text inline-flex items-center gap-1 text-xs font-semibold" :class="textClasses[variant]" aria-live="polite">
      <BaseIcon :name="iconName" class="password-meter__icon size-em-md" />
      <span>{{ text }}</span>
    </p>
  </div>
</template>

<script setup lang="ts">
// Indicador de força da senha: barra + ícone + texto (não depende só da cor).
// Recebe os valores de usePasswordStrength (strengthClass/strengthText/strengthWidth).
import { computed } from 'vue'
import { BaseIcon, BaseProgress, statusIcons, type IconName } from '@/shared/components/ui'

type Variant = 'danger' | 'warning' | 'success'

const props = defineProps<{
  /** weak | medium | strong | very-strong */
  level: string
  text: string
  width: string
}>()

const variant = computed<Variant>(() => {
  if (props.level === 'weak') return 'danger'
  if (props.level === 'medium') return 'warning'
  return 'success'
})

const textClasses: Record<Variant, string> = {
  danger: 'text-danger',
  warning: 'text-warning',
  success: 'text-success',
}

const iconName = computed<IconName>(() => statusIcons[variant.value])

// width chega como '25%' (usePasswordStrength)
const percent = computed(() => Number.parseFloat(props.width) || 0)
</script>
