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
    <div class="ui-select">
      <select
        v-bind="controlAttrs"
        :id="fieldId"
        class="ui-control ui-select__control"
        :required="required"
        :disabled="disabled"
        :aria-invalid="error ? 'true' : undefined"
        :aria-describedby="describedBy"
        :value="model ?? ''"
        @change="onChange"
      >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <slot>
          <option
            v-for="opt in options"
            :key="String(opt.value)"
            :value="opt.value"
            :disabled="opt.disabled"
          >
            {{ opt.label }}
          </option>
        </slot>
      </select>
      <BaseIcon name="chevron-down" class="ui-select__chevron" />
    </div>
  </BaseField>
</template>

<script setup lang="ts">
import BaseField from './BaseField.vue'
import BaseIcon from './BaseIcon.vue'
import { useField } from './useField'

export interface SelectOption {
  value: string | number
  label: string
  disabled?: boolean
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(
  defineProps<{
    label?: string
    /** Opções (alternativa: passar <option> pelo slot padrão) */
    options?: SelectOption[]
    /** Primeira opção desabilitada, exibida quando não há valor */
    placeholder?: string
    hint?: string
    error?: string
    required?: boolean
    disabled?: boolean
    id?: string
  }>(),
  {
    label: '',
    options: () => [],
    placeholder: '',
    hint: '',
    error: '',
    required: false,
    disabled: false,
    id: undefined,
  },
)

defineSlots<{ default?: () => unknown }>()

const model = defineModel<string | number | null>()
const { fieldId, rootAttrs, controlAttrs, describedBy } = useField(props)

// Preserva o tipo numérico das opções em `options`
const onChange = (event: Event) => {
  const el = event.target as HTMLSelectElement
  const match = props.options.find((opt) => String(opt.value) === el.value)
  model.value = match ? match.value : el.value
}
</script>

<style scoped>
@import './control.css';

.ui-select {
  position: relative;
  display: flex;
  align-items: center;
}

.ui-select__control {
  appearance: none;
  padding-inline-end: calc(var(--space-3) * 2 + 1rem);
  cursor: pointer;
}

.ui-select__chevron {
  position: absolute;
  inset-inline-end: var(--space-3);
  width: 1rem;
  height: 1rem;
  color: var(--color-text-muted);
  pointer-events: none;
}
</style>
