<template>
  <div class="ui-field" :class="{ 'ui-field--invalid': !!error, 'ui-field--disabled': disabled }">
    <label v-if="label" :for="fieldId" class="ui-field__label">
      {{ label }}
      <span v-if="required" class="ui-field__required" aria-hidden="true">*</span>
    </label>
    <slot />
    <p v-if="error" :id="`${fieldId}-error`" class="ui-field__error">
      <BaseIcon name="exclamation-circle" class="ui-field__error-icon" />
      <span>{{ error }}</span>
    </p>
    <p v-else-if="hint" :id="`${fieldId}-hint`" class="ui-field__hint">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
// Moldura comum de campos (label + controle + dica/erro). Usada por BaseInput,
// BaseTextarea e BaseSelect; também pode envolver um controle customizado.
import BaseIcon from './BaseIcon.vue'

withDefaults(
  defineProps<{
    /** id do controle (o label aponta para ele; erro/dica usam `${id}-error|hint`) */
    fieldId: string
    label?: string
    hint?: string
    error?: string
    required?: boolean
    disabled?: boolean
  }>(),
  { label: '', hint: '', error: '', required: false, disabled: false },
)

defineSlots<{ default: () => unknown }>()
</script>

<style scoped>
.ui-field {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  min-width: 0;
}

.ui-field__label {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
}

.ui-field__required {
  color: var(--color-danger);
  margin-inline-start: var(--space-1);
}

.ui-field__hint {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}

.ui-field__error {
  display: flex;
  align-items: flex-start;
  gap: var(--space-1);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-medium);
  color: var(--color-danger);
}

.ui-field__error-icon {
  margin-block-start: 0.1em;
}

.ui-field--disabled .ui-field__label {
  color: var(--color-text-muted);
}
</style>
