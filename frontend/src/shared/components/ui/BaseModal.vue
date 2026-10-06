<template>
  <Teleport to="body">
    <Transition v-bind="overlayTransition">
      <div
        v-if="open"
        class="ui-modal fixed inset-0 z-modal flex items-center justify-center overflow-y-auto bg-overlay p-2 sm:p-4"
        @mousedown.self="onOverlay"
      >
        <div
          ref="dialogRef"
          class="ui-modal__dialog m-auto flex max-h-modal w-full flex-col rounded-xl border border-solid border-border bg-surface text-text shadow-xl outline-none t-active:transition-transform t-active:duration-base t-active:ease-standard t-from:translate-y-3 t-from:scale-enter"
          :class="sizeClasses[size]"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="title || $slots.header ? titleId : undefined"
          :aria-label="!title && !$slots.header ? ariaLabel || undefined : undefined"
          :aria-describedby="description ? descId : undefined"
          tabindex="-1"
        >
          <header
            v-if="title || $slots.header || !hideClose"
            class="ui-modal__header flex items-start gap-3 px-4 pt-5 sm:px-6"
          >
            <div class="ui-modal__heading min-w-0 flex-1">
              <div :id="titleId">
                <slot name="header">
                  <h2 v-if="title" class="ui-modal__title text-xl font-bold text-text">{{ title }}</h2>
                </slot>
              </div>
              <p v-if="description" :id="descId" class="ui-modal__description mt-1 text-sm text-text-muted">
                {{ description }}
              </p>
            </div>
            <button
              v-if="!hideClose"
              type="button"
              class="ui-modal__close ms-auto inline-flex size-control-sm shrink-0 items-center justify-center rounded-md border-0 bg-transparent text-xl text-text-muted hover:bg-surface-hover hover:text-text focus-visible:focus-ring"
              :aria-label="t('ui.close')"
              @click="close"
            >
              <BaseIcon name="x-mark" />
            </button>
          </header>

          <div class="ui-modal__body flex-1 overflow-y-auto px-4 py-5 sm:px-6">
            <slot />
          </div>

          <!-- < 480px: botões empilhados (ação principal em cima) e com largura total -->
          <footer
            v-if="$slots.footer"
            class="ui-modal__footer flex flex-wrap justify-end gap-3 border-0 border-t border-solid border-border px-4 py-4 max-sm:flex-col-reverse max-sm:*:w-full sm:px-6"
          >
            <slot name="footer" :close="close" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, useId } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseIcon from './BaseIcon.vue'
import { overlayTransition } from './transitions'
import { useFocusTrap } from '@/shared/composables/useFocusTrap'

type Size = 'sm' | 'md' | 'lg' | 'xl'

const props = withDefaults(
  defineProps<{
    title?: string
    description?: string
    /** Nome acessível quando não há título visível */
    ariaLabel?: string
    size?: Size
    /** Fecha ao clicar fora (padrão: true) */
    closeOnOverlay?: boolean
    /** Fecha com Esc (padrão: true) */
    closeOnEsc?: boolean
    /** Esconde o botão X */
    hideClose?: boolean
  }>(),
  {
    title: '',
    description: '',
    ariaLabel: '',
    size: 'md',
    closeOnOverlay: true,
    closeOnEsc: true,
    hideClose: false,
  },
)

/** v-model: aberto/fechado */
const open = defineModel<boolean>({ default: false })

const emit = defineEmits<{ close: [] }>()

defineSlots<{
  default?: () => unknown
  header?: () => unknown
  footer?: (props: { close: () => void }) => unknown
}>()

const sizeClasses: Record<Size, string> = {
  sm: 'max-w-modal-sm',
  md: 'max-w-modal-md',
  lg: 'max-w-modal-lg',
  xl: 'max-w-modal-xl',
}

const { t } = useI18n()
const uid = useId()
const titleId = `modal-title-${uid}`
const descId = `modal-desc-${uid}`
const dialogRef = ref<HTMLElement | null>(null)

const close = () => {
  open.value = false
  emit('close')
}

const onOverlay = () => {
  if (props.closeOnOverlay) close()
}

useFocusTrap(dialogRef, open, {
  onEscape: () => {
    if (props.closeOnEsc) close()
  },
})

defineExpose({ close })
</script>
