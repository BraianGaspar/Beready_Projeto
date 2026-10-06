<template>
  <BaseField
    v-bind="rootAttrs"
    :field-id="fieldId"
    :label="label"
    :hint="hint"
    :error="error"
    :required="required"
    :disabled="disabled"
  >
    <textarea
      v-bind="controlAttrs"
      :id="fieldId"
      class="ui-control ui-textarea"
      :rows="rows"
      :maxlength="maxlength"
      :value="model ?? ''"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :aria-invalid="error ? 'true' : undefined"
      :aria-describedby="describedBy"
      @input="model = ($event.target as HTMLTextAreaElement).value"
      @blur="emit('blur', $event)"
    ></textarea>
    <span v-if="maxlength && showCount" class="ui-textarea__count" aria-hidden="true">
      {{ (model ?? '').length }}/{{ maxlength }}
    </span>
  </BaseField>
</template>

<script setup lang="ts">
import BaseField from './BaseField.vue'
import { useField } from './useField'

defineOptions({ inheritAttrs: false })

const props = withDefaults(
  defineProps<{
    label?: string
    placeholder?: string
    hint?: string
    error?: string
    required?: boolean
    disabled?: boolean
    rows?: number
    maxlength?: number
    /** Mostra contador "n/max" (requer maxlength) */
    showCount?: boolean
    id?: string
  }>(),
  {
    label: '',
    placeholder: '',
    hint: '',
    error: '',
    required: false,
    disabled: false,
    rows: 4,
    maxlength: undefined,
    showCount: false,
    id: undefined,
  },
)

const model = defineModel<string | null>()
const emit = defineEmits<{ blur: [event: FocusEvent] }>()

const { fieldId, rootAttrs, controlAttrs, describedBy } = useField(props)
</script>

<style scoped>
@import './control.css';

.ui-textarea {
  resize: vertical;
  min-height: calc(var(--control-height-md) * 2);
}

.ui-textarea__count {
  align-self: flex-end;
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}
</style>
