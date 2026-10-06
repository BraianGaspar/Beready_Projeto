<template>
  <div
    class="ui-switch flex flex-col gap-1"
    :class="{ 'opacity-disabled': disabled }"
    v-bind="rootAttrs"
  >
    <div class="ui-switch__row flex items-center justify-between gap-4">
      <label
        :for="fieldId"
        class="ui-switch__label text-sm font-semibold text-text"
        :class="cursorClass"
      >
        <slot>{{ label }}</slot>
      </label>
      <span class="ui-switch__control relative inline-flex shrink-0">
        <!-- input invisível por cima da trilha é o `peer` (trilha = irmão; thumb = descendente) -->
        <input
          v-bind="controlAttrs"
          :id="fieldId"
          v-model="model"
          type="checkbox"
          role="switch"
          class="ui-switch__input peer absolute inset-0 z-raised m-0 size-full opacity-0"
          :class="cursorClass"
          :aria-checked="model"
          :disabled="disabled"
          :aria-describedby="describedBy"
        />
        <span
          class="ui-switch__track inline-flex h-6 w-11 items-center rounded-full bg-border-strong p-0.5 transition-colors peer-checked:bg-primary peer-focus-visible:focus-ring"
          aria-hidden="true"
        >
          <span
            class="ui-switch__thumb inline-flex size-5 items-center justify-center rounded-full bg-surface text-primary shadow-sm transition-transform peer-checked-deep:translate-x-5 rtl:peer-checked-deep:-translate-x-5"
          >
            <BaseIcon v-if="model" name="check" class="ui-switch__icon size-3" :stroke-width="3" />
          </span>
        </span>
      </span>
    </div>
    <p v-if="hint" :id="`${fieldId}-hint`" class="ui-switch__hint text-xs text-text-muted">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
// Interruptor liga/desliga: checkbox nativo com role="switch" (Espaço alterna).
// O estado também é indicado pelo ícone de check no "thumb" (não só por cor).
import { computed } from 'vue'
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

const cursorClass = computed(() => (props.disabled ? 'cursor-not-allowed' : 'cursor-pointer'))
</script>
