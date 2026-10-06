<template>
  <div class="ui-check" :class="{ 'ui-check--disabled': disabled }" v-bind="rootAttrs">
    <input
      v-bind="controlAttrs"
      :id="fieldId"
      v-model="model"
      type="checkbox"
      class="ui-check__input"
      :disabled="disabled"
      :aria-describedby="describedBy"
    />
    <label :for="fieldId" class="ui-check__label">
      <span class="ui-check__box" aria-hidden="true">
        <BaseIcon name="check" class="ui-check__mark" :stroke-width="3" />
      </span>
      <span class="ui-check__text">
        <slot>{{ label }}</slot>
      </span>
    </label>
    <p v-if="hint" :id="`${fieldId}-hint`" class="ui-check__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
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
.ui-check {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.ui-check__input {
  position: absolute;
  opacity: 0;
  width: 1.25rem;
  height: 1.25rem;
  margin: 0;
}

.ui-check__label {
  display: inline-flex;
  align-items: flex-start;
  gap: var(--space-3);
  min-height: 1.5rem;
  color: var(--color-text);
  font-size: var(--font-size-sm);
  cursor: pointer;
}

.ui-check__box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 1.25rem;
  height: 1.25rem;
  margin-block-start: 0.1rem;
  border: 2px solid var(--color-border-strong);
  border-radius: var(--radius-sm);
  background: var(--color-surface);
  color: var(--color-primary-contrast);
  transition:
    background-color var(--transition-base),
    border-color var(--transition-base);
}

.ui-check__mark {
  width: 0.85rem;
  height: 0.85rem;
  opacity: 0;
}

.ui-check__input:checked + .ui-check__label .ui-check__box {
  background: var(--color-primary);
  border-color: var(--color-primary);
}

.ui-check__input:checked + .ui-check__label .ui-check__mark {
  opacity: 1;
}

.ui-check__input:focus-visible + .ui-check__label .ui-check__box {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: var(--focus-ring-offset);
}

.ui-check__hint {
  padding-inline-start: calc(1.25rem + var(--space-3));
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}

.ui-check--disabled .ui-check__label {
  opacity: 0.55;
  cursor: not-allowed;
}
</style>
