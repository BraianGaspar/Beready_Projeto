<template>
  <div
    class="ui-empty flex flex-col items-center gap-3 px-4 text-center"
    :class="compact ? 'py-6' : 'py-12'"
  >
    <span
      class="ui-empty__icon inline-flex size-16 items-center justify-center rounded-full bg-primary-soft text-icon-xl text-primary-soft-text"
      aria-hidden="true"
    >
      <BaseIcon :name="icon" />
    </span>
    <component :is="titleTag" class="ui-empty__title text-xl font-semibold text-text">{{ title }}</component>
    <p v-if="description" class="ui-empty__description max-w-measure text-sm text-text-muted">
      {{ description }}
    </p>
    <div v-if="$slots.default" class="ui-empty__actions mt-2 flex flex-wrap justify-center gap-3">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import BaseIcon from './BaseIcon.vue'
import type { IconName } from './icons'

withDefaults(
  defineProps<{
    title: string
    description?: string
    icon?: IconName
    titleTag?: 'h2' | 'h3'
    /** Menos respiro (dentro de cards) */
    compact?: boolean
  }>(),
  { description: '', icon: 'inbox', titleTag: 'h2', compact: false },
)

/** Slot padrão: ações (ex.: BaseButton "Criar primeiro item") */
defineSlots<{ default?: () => unknown }>()
</script>
