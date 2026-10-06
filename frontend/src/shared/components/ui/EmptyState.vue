<template>
  <div class="ui-empty" :class="{ 'ui-empty--compact': compact }">
    <span class="ui-empty__icon" aria-hidden="true">
      <BaseIcon :name="icon" />
    </span>
    <component :is="titleTag" class="ui-empty__title">{{ title }}</component>
    <p v-if="description" class="ui-empty__description">{{ description }}</p>
    <div v-if="$slots.default" class="ui-empty__actions">
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

<style scoped>
.ui-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-12) var(--space-4);
  text-align: center;
}

.ui-empty--compact {
  padding: var(--space-6) var(--space-4);
}

.ui-empty__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  border-radius: var(--radius-full);
  background: var(--color-primary-soft);
  color: var(--color-primary-soft-text);
  font-size: 2rem;
}

.ui-empty__title {
  font-size: var(--font-size-xl);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
}

.ui-empty__description {
  max-width: 32rem;
  color: var(--color-text-muted);
  font-size: var(--font-size-sm);
}

.ui-empty__actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: var(--space-3);
  margin-block-start: var(--space-2);
}
</style>
