<template>
  <div class="ui-field flex min-w-0 flex-col gap-1">
    <label
      v-if="label"
      :for="fieldId"
      class="ui-field__label text-sm font-semibold"
      :class="disabled ? 'text-text-muted' : 'text-text'"
    >
      {{ label }}
      <span v-if="required" class="ui-field__required ms-1 text-danger" aria-hidden="true">*</span>
    </label>
    <slot />
    <p
      v-if="error"
      :id="`${fieldId}-error`"
      class="ui-field__error flex items-start gap-1 text-xs font-medium text-danger"
    >
      <BaseIcon name="exclamation-circle" class="ui-field__error-icon mt-nudge-em" />
      <span>{{ error }}</span>
    </p>
    <p v-else-if="hint" :id="`${fieldId}-hint`" class="ui-field__hint text-xs text-text-muted">
      {{ hint }}
    </p>
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
