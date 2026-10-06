<template>
  <div class="ui-switch" :class="{ 'ui-switch--disabled': disabled }" v-bind="rootAttrs">
    <div class="ui-switch__row">
      <label :for="fieldId" class="ui-switch__label">
        <slot>{{ label }}</slot>
      </label>
      <span class="ui-switch__control">
        <input
          v-bind="controlAttrs"
          :id="fieldId"
          v-model="model"
          type="checkbox"
          role="switch"
          class="ui-switch__input"
          :aria-checked="model"
          :disabled="disabled"
          :aria-describedby="describedBy"
        />
        <span class="ui-switch__track" aria-hidden="true">
          <span class="ui-switch__thumb">
            <BaseIcon v-if="model" name="check" class="ui-switch__icon" :stroke-width="3" />
          </span>
        </span>
      </span>
    </div>
    <p v-if="hint" :id="`${fieldId}-hint`" class="ui-switch__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
// Interruptor liga/desliga: checkbox nativo com role="switch" (Espaço alterna).
// O estado também é indicado pelo ícone de check no "thumb" (não só por cor).
import BaseIcon from './BaseIcon.vue'
import { useField } from './useField'

defineOptions({ inheritAttrs: false })

const props = withDefaults(
  defineProps<{
    label?: string
    hint?: string
    disabled?: boolean
    id?: string
  }>(),
  { label: '', hint: '', disabled: false, id: undefined },
)

defineSlots<{ default?: () => unknown }>()

const model = defineModel<boolean>({ default: false })
const { fieldId, rootAttrs, controlAttrs, describedBy } = useField(props)
</script>

<style scoped>
.ui-switch {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.ui-switch__row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
}

.ui-switch__label {
  color: var(--color-text);
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  cursor: pointer;
}

.ui-switch__control {
  position: relative;
  display: inline-flex;
  flex-shrink: 0;
}

.ui-switch__input {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  opacity: 0;
  cursor: pointer;
  z-index: 1;
}

.ui-switch__track {
  display: inline-flex;
  align-items: center;
  width: 2.75rem;
  height: 1.5rem;
  padding: 2px;
  border-radius: var(--radius-full);
  background: var(--color-border-strong);
  transition: background-color var(--transition-base);
}

.ui-switch__thumb {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.25rem;
  height: 1.25rem;
  border-radius: var(--radius-full);
  background: var(--color-surface);
  color: var(--color-primary);
  box-shadow: var(--shadow-sm);
  transition: transform var(--transition-base);
}

.ui-switch__icon {
  width: 0.75rem;
  height: 0.75rem;
}

.ui-switch__input:checked + .ui-switch__track {
  background: var(--color-primary);
}

.ui-switch__input:checked + .ui-switch__track .ui-switch__thumb {
  transform: translateX(1.25rem);
}

[dir='rtl'] .ui-switch__input:checked + .ui-switch__track .ui-switch__thumb {
  transform: translateX(-1.25rem);
}

.ui-switch__input:focus-visible + .ui-switch__track {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: var(--focus-ring-offset);
}

.ui-switch__hint {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}

.ui-switch--disabled {
  opacity: 0.55;
}

.ui-switch--disabled .ui-switch__input,
.ui-switch--disabled .ui-switch__label {
  cursor: not-allowed;
}
</style>
