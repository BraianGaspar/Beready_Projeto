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
      :class="[controlClass, 'ui-textarea min-h-textarea resize-y', mono && 'font-mono']"
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
    <span
      v-if="maxlength && showCount"
      class="ui-textarea__count self-end text-xs text-text-muted"
      aria-hidden="true"
    >
      {{ (model ?? '').length }}/{{ maxlength }}
    </span>
  </BaseField>
</template>

<script setup lang="ts">
import BaseField from './BaseField.vue'
import { controlClass } from './control'
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
    /** Fonte monoespaçada (ex.: JSON) */
    mono?: boolean
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
    mono: false,
    id: undefined,
  },
)

const model = defineModel<string | null>()
const emit = defineEmits<{ blur: [event: FocusEvent] }>()

const { fieldId, rootAttrs, controlAttrs, describedBy } = useField(props)
</script>
