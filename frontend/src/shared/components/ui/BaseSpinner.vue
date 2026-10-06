<template>
  <span
    class="ui-spinner items-center gap-3 text-sm text-text-muted"
    :class="center ? 'flex w-full flex-col justify-center px-4 py-12' : 'inline-flex'"
    role="status"
    aria-live="polite"
  >
    <!-- .motion-safe: continua girando (mais devagar) com prefers-reduced-motion, ver o reset base em tailwind.css -->
    <span
      class="ui-spinner__ring motion-safe shrink-0 animate-spinner rounded-full border-solid border-border border-t-primary"
      :class="ringClasses[size]"
      aria-hidden="true"
    ></span>
    <span :class="showLabel ? 'ui-spinner__label text-text-muted' : 'sr-only'">
      {{ label || t('common.carregando') }}
    </span>
  </span>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

type Size = 'sm' | 'md' | 'lg'

withDefaults(
  defineProps<{
    size?: Size
    /** Texto lido por leitores de tela (padrão: common.carregando) */
    label?: string
    /** Mostra o texto ao lado/abaixo do indicador */
    showLabel?: boolean
    /** Ocupa a largura toda, centralizado, com respiro vertical (carregamento de página/seção) */
    center?: boolean
  }>(),
  { size: 'md', label: '', showLabel: false, center: false },
)

const ringClasses: Record<Size, string> = {
  sm: 'size-4 border-2',
  md: 'size-6 border-3',
  lg: 'size-11 border-4',
}

const { t } = useI18n()
</script>
