<template>
  <div class="password-meter" :class="`password-meter--${variant}`">
    <!-- Barra só visual: o nível é anunciado pelo texto (aria-live) abaixo -->
    <BaseProgress :value="percent" :variant="variant" size="sm" decorative />
    <p class="password-meter__text" aria-live="polite">
      <BaseIcon :name="iconName" class="password-meter__icon" />
      <span>{{ text }}</span>
    </p>
  </div>
</template>

<script setup lang="ts">
// Indicador de força da senha: barra + ícone + texto (não depende só da cor).
// Recebe os valores de usePasswordStrength (strengthClass/strengthText/strengthWidth).
import { computed } from 'vue'
import { BaseIcon, BaseProgress, statusIcons, type IconName } from '@/shared/components/ui'

const props = defineProps<{
  /** weak | medium | strong | very-strong */
  level: string
  text: string
  width: string
}>()

const variant = computed<'danger' | 'warning' | 'success'>(() => {
  if (props.level === 'weak') return 'danger'
  if (props.level === 'medium') return 'warning'
  return 'success'
})

const iconName = computed<IconName>(() => statusIcons[variant.value])

// width chega como '25%' (usePasswordStrength)
const percent = computed(() => Number.parseFloat(props.width) || 0)
</script>

<style scoped>
@import '@/styles/views/auth/password-strength.css';
</style>
