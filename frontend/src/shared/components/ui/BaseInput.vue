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
    <div class="ui-input" :class="{ 'ui-input--icon': icon, 'ui-input--toggle': isPassword }">
      <BaseIcon v-if="icon" :name="icon" class="ui-input__icon" />
      <input
        v-bind="controlAttrs"
        :id="fieldId"
        class="ui-control ui-input__control"
        :type="isPassword && showPassword ? 'text' : type"
        :value="model ?? ''"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :aria-invalid="error ? 'true' : undefined"
        :aria-describedby="describedBy"
        @input="model = ($event.target as HTMLInputElement).value"
        @blur="emit('blur', $event)"
      />
      <button
        v-if="isPassword"
        type="button"
        class="ui-input__toggle"
        :aria-label="showPassword ? t('ui.hidePassword') : t('ui.showPassword')"
        :aria-pressed="showPassword"
        :aria-controls="fieldId"
        :disabled="disabled"
        @click="showPassword = !showPassword"
      >
        <BaseIcon :name="showPassword ? 'eye-slash' : 'eye'" />
      </button>
    </div>
  </BaseField>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseField from './BaseField.vue'
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'
import { useField } from './useField'

defineOptions({ inheritAttrs: false })

const props = withDefaults(
  defineProps<{
    label?: string
    type?: 'text' | 'email' | 'password' | 'number' | 'search' | 'tel' | 'url' | 'date' | 'time'
    placeholder?: string
    hint?: string
    error?: string
    required?: boolean
    disabled?: boolean
    /** Ícone à esquerda (início, em RTL fica à direita) */
    icon?: IconName
    id?: string
  }>(),
  {
    label: '',
    type: 'text',
    placeholder: '',
    hint: '',
    error: '',
    required: false,
    disabled: false,
    icon: undefined,
    id: undefined,
  },
)

/** v-model (aceita os modificadores .trim e .number) */
const model = defineModel<string | number | null>()

const emit = defineEmits<{ blur: [event: FocusEvent] }>()

const { t } = useI18n()
const { fieldId, rootAttrs, controlAttrs, describedBy } = useField(props)

const isPassword = computed(() => props.type === 'password')
const showPassword = ref(false)
</script>

<style scoped>
@import './control.css';

.ui-input {
  position: relative;
  display: flex;
  align-items: center;
}

.ui-input__icon {
  position: absolute;
  inset-inline-start: var(--space-3);
  width: 1.25rem;
  height: 1.25rem;
  color: var(--color-text-subtle);
  pointer-events: none;
}

.ui-input--icon .ui-input__control {
  padding-inline-start: calc(var(--space-3) * 2 + 1.25rem);
}

.ui-input--toggle .ui-input__control {
  padding-inline-end: calc(var(--control-height-md));
}

.ui-input__toggle {
  position: absolute;
  inset-inline-end: var(--space-1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: calc(var(--control-height-md) - var(--space-2));
  height: calc(var(--control-height-md) - var(--space-2));
  border: none;
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--color-text-muted);
  font-size: 1.25rem;
}

.ui-input__toggle:hover:not(:disabled) {
  background: var(--color-surface-hover);
  color: var(--color-text);
}
</style>
