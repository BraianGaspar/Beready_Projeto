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
    <div class="ui-input relative flex items-center">
      <BaseIcon
        v-if="icon"
        :name="icon"
        class="ui-input__icon pointer-events-none absolute start-3 size-5 text-text-subtle"
      />
      <input
        v-bind="controlAttrs"
        :id="fieldId"
        :class="inputClasses"
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
        class="ui-input__toggle absolute end-1 inline-flex size-control-inner items-center justify-center rounded-sm border-0 bg-transparent text-xl text-text-muted focus-visible:focus-ring enabled:hover:bg-surface-hover enabled:hover:text-text"
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
import { controlClass } from './control'
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

// ps-/pe- vêm depois de px- no CSS do Tailwind, então abrem espaço para ícone/botão
const inputClasses = computed(() => [
  controlClass,
  'ui-input__control min-h-control',
  { 'ps-control-icon': props.icon, 'pe-control': isPassword.value },
])
</script>
