<template>
  <div
    class="ui-alert relative flex w-full items-start gap-3 overflow-hidden rounded-lg border border-s-4 border-solid border-border px-4 py-3 text-text"
    :class="[tone.border, toast ? 'bg-surface shadow-lg' : tone.bg]"
    :role="variant === 'danger' ? 'alert' : 'status'"
  >
    <BaseIcon :name="statusIcons[variant]" class="ui-alert__icon mt-nudge size-5" :class="tone.text" />
    <div class="ui-alert__content min-w-0 flex-1">
      <!-- Rótulo textual do status: a informação não depende só da cor -->
      <p class="ui-alert__title text-sm font-bold" :class="tone.text">
        {{ title || t(`ui.status.${variant}`) }}
      </p>
      <div class="ui-alert__message text-sm text-text wrap-anywhere">
        <slot>{{ message }}</slot>
      </div>
    </div>
    <button
      v-if="dismissible"
      type="button"
      class="ui-alert__close -m-1 inline-flex size-8 shrink-0 items-center justify-center rounded-md border-0 bg-transparent text-base text-text-muted hover:bg-surface-hover hover:text-text focus-visible:focus-ring"
      :aria-label="t('ui.closeNotification')"
      @click="emit('close')"
    >
      <BaseIcon name="x-mark" />
    </button>
    <!-- Barra de tempo do toast: encolhe (inline-size) em direção ao início da linha, também em RTL -->
    <span
      v-if="toast && duration > 0"
      class="ui-alert__progress absolute bottom-0 start-0 h-toast-progress w-full animate-toast-progress opacity-60"
      :class="tone.bar"
      :style="{ animationDuration: `${duration}ms` }"
      aria-hidden="true"
    ></span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseIcon from './BaseIcon.vue'
import { statusIcons, type StatusVariant } from './icons'

const props = withDefaults(
  defineProps<{
    variant?: StatusVariant
    /** Título; padrão = rótulo do status traduzido (ui.status.*) */
    title?: string
    message?: string
    dismissible?: boolean
    /** Estilo de notificação flutuante (sombra + barra de tempo) */
    toast?: boolean
    /** Duração em ms (só para a barra de progresso do toast) */
    duration?: number
  }>(),
  { variant: 'info', title: '', message: '', dismissible: false, toast: false, duration: 0 },
)

const emit = defineEmits<{ close: [] }>()
defineSlots<{ default?: () => unknown }>()

const { t } = useI18n()

interface Tone {
  border: string
  bg: string
  text: string
  bar: string
}

const tones: Record<StatusVariant, Tone> = {
  success: { border: 'border-s-success', bg: 'bg-success-soft', text: 'text-success', bar: 'bg-success' },
  warning: { border: 'border-s-warning', bg: 'bg-warning-soft', text: 'text-warning', bar: 'bg-warning' },
  danger: { border: 'border-s-danger', bg: 'bg-danger-soft', text: 'text-danger', bar: 'bg-danger' },
  info: { border: 'border-s-info', bg: 'bg-info-soft', text: 'text-info', bar: 'bg-info' },
}

const tone = computed(() => tones[props.variant])
</script>
