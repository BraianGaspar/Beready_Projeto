<template>
  <!-- Tamanho padrão (1em ou a prop size) vem de .ui-icon em src/styles/tailwind.css, na camada
       `components`: qualquer utilitário de tamanho passado por quem usa (size-5, size-em-lg…) vence. -->
  <svg
    class="ui-icon shrink-0 align-middle"
    :class="{ 'rtl:-scale-x-100': DIRECTIONAL.has(name) }"
    :style="size ? { '--ui-icon-size': size } : undefined"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    :stroke-width="strokeWidth"
    stroke-linecap="round"
    stroke-linejoin="round"
    :role="label ? 'img' : undefined"
    :aria-label="label || undefined"
    :aria-hidden="label ? undefined : 'true'"
    focusable="false"
  >
    <path v-for="(d, i) in icons[name]" :key="i" :d="d" />
  </svg>
</template>

<script setup lang="ts">
import { icons, type IconName } from './icons'

// Ícones que indicam direção (voltar/avançar) são espelhados em RTL (árabe)
const DIRECTIONAL = new Set<IconName>(['arrow-left', 'arrow-right', 'chevron-left', 'chevron-right'])

withDefaults(
  defineProps<{
    /** Nome do ícone no registro (icons.ts) */
    name: IconName
    /** Tamanho CSS (ex.: '1.5rem'). Padrão: 1em (acompanha a fonte) */
    size?: string
    /** Texto acessível. Sem label o ícone é decorativo (aria-hidden) */
    label?: string
    strokeWidth?: number
  }>(),
  { size: undefined, label: undefined, strokeWidth: 2 },
)
</script>
