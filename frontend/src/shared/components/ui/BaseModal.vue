<template>
  <Teleport to="body">
    <Transition name="ui-modal">
      <div v-if="open" class="ui-modal" @mousedown.self="onOverlay">
        <div
          ref="dialogRef"
          class="ui-modal__dialog"
          :class="`ui-modal__dialog--${size}`"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="title || $slots.header ? titleId : undefined"
          :aria-label="!title && !$slots.header ? ariaLabel || undefined : undefined"
          :aria-describedby="description ? descId : undefined"
          tabindex="-1"
        >
          <header v-if="title || $slots.header || !hideClose" class="ui-modal__header">
            <div class="ui-modal__heading">
              <div :id="titleId">
                <slot name="header">
                  <h2 v-if="title" class="ui-modal__title">{{ title }}</h2>
                </slot>
              </div>
              <p v-if="description" :id="descId" class="ui-modal__description">{{ description }}</p>
            </div>
            <button
              v-if="!hideClose"
              type="button"
              class="ui-modal__close"
              :aria-label="t('ui.close')"
              @click="close"
            >
              <BaseIcon name="x-mark" />
            </button>
          </header>

          <div class="ui-modal__body">
            <slot />
          </div>

          <footer v-if="$slots.footer" class="ui-modal__footer">
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
import { useFocusTrap } from '@/shared/composables/useFocusTrap'

const props = withDefaults(
  defineProps<{
    title?: string
    description?: string
    /** Nome acessível quando não há título visível */
    ariaLabel?: string
    size?: 'sm' | 'md' | 'lg' | 'xl'
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

<style scoped>
.ui-modal {
  position: fixed;
  inset: 0;
  z-index: var(--z-modal);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-4);
  background: var(--color-overlay);
  overflow-y: auto;
}

.ui-modal__dialog {
  --ui-modal-width: 32rem;
  display: flex;
  flex-direction: column;
  width: 100%;
  max-width: var(--ui-modal-width);
  max-height: calc(100dvh - var(--space-8));
  margin: auto;
  background: var(--color-surface);
  color: var(--color-text);
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-xl);
  outline: none;
}

.ui-modal__dialog--sm {
  --ui-modal-width: 26rem;
}

.ui-modal__dialog--lg {
  --ui-modal-width: 44rem;
}

.ui-modal__dialog--xl {
  --ui-modal-width: 60rem;
}

.ui-modal__header {
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  padding: var(--space-5) var(--space-6) 0;
}

.ui-modal__heading {
  flex: 1;
  min-width: 0;
}

.ui-modal__title {
  font-size: var(--font-size-xl);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
}

.ui-modal__description {
  margin-block-start: var(--space-1);
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}

.ui-modal__close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: var(--control-height-sm);
  height: var(--control-height-sm);
  margin-inline-start: auto;
  border: none;
  border-radius: var(--radius-md);
  background: transparent;
  color: var(--color-text-muted);
  font-size: 1.25rem;
}

.ui-modal__close:hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.ui-modal__body {
  flex: 1;
  padding: var(--space-5) var(--space-6);
  overflow-y: auto;
}

.ui-modal__footer {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: var(--space-3);
  padding: var(--space-4) var(--space-6);
  border-block-start: var(--border-width) solid var(--color-border);
}

@media (max-width: 479.98px) {
  .ui-modal {
    padding: var(--space-2);
  }

  .ui-modal__header,
  .ui-modal__body,
  .ui-modal__footer {
    padding-inline: var(--space-4);
  }

  /* Botões do rodapé empilhados e com largura total no mobile */
  .ui-modal__footer {
    flex-direction: column-reverse;
  }

  .ui-modal__footer > :deep(*) {
    width: 100%;
  }
}

/* Transição */
.ui-modal-enter-active,
.ui-modal-leave-active {
  transition: opacity var(--duration-base) var(--easing-standard);
}

.ui-modal-enter-active .ui-modal__dialog,
.ui-modal-leave-active .ui-modal__dialog {
  transition: transform var(--duration-base) var(--easing-standard);
}

.ui-modal-enter-from,
.ui-modal-leave-to {
  opacity: 0;
}

.ui-modal-enter-from .ui-modal__dialog,
.ui-modal-leave-to .ui-modal__dialog {
  transform: translateY(0.75rem) scale(0.98);
}
</style>
