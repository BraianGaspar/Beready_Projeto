<template>
  <div class="ui-check relative flex flex-col gap-1" v-bind="rootAttrs">
    <!-- input invisível é o `peer`: a caixa (dentro do label irmão) reage com peer-*-deep: -->
    <input
      v-bind="controlAttrs"
      :id="fieldId"
      v-model="model"
      type="checkbox"
      class="ui-check__input peer absolute m-0 size-5 opacity-0"
      :disabled="disabled"
      :aria-describedby="describedBy"
    />
    <label
      :for="fieldId"
      class="ui-check__label inline-flex min-h-6 items-start gap-3 text-sm text-text"
      :class="disabled ? 'cursor-not-allowed opacity-disabled' : 'cursor-pointer'"
    >
      <span
        class="ui-check__box mt-nudge inline-flex size-5 shrink-0 items-center justify-center rounded-sm border-2 border-solid border-border-strong bg-surface text-primary-contrast transition-colors peer-checked-deep:border-primary peer-checked-deep:bg-primary peer-focus-visible-deep:focus-ring"
        aria-hidden="true"
      >
        <BaseIcon
          name="check"
          class="ui-check__mark size-check opacity-0 peer-checked-deep:opacity-100"
          :stroke-width="3"
        />
      </span>
      <span class="ui-check__text">
        <slot>{{ label }}</slot>
      </span>
    </label>
    <p v-if="hint" :id="`${fieldId}-hint`" class="ui-check__hint ps-check-indent text-xs text-text-muted">
      {{ hint }}
    </p>
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
