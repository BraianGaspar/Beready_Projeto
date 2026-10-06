<template>
  <component :is="to ? RouterLink : 'div'" :to="to" class="ui-stat" :class="{ 'ui-stat--link': to }">
    <span v-if="icon" class="ui-stat__icon" :class="`ui-stat__icon--${variant}`" aria-hidden="true">
      <BaseIcon :name="icon" />
    </span>
    <div class="ui-stat__body">
      <p class="ui-stat__label">{{ label }}</p>
      <p class="ui-stat__value">{{ value }}</p>
      <p v-if="hint || $slots.hint" class="ui-stat__hint">
        <slot name="hint">{{ hint }}</slot>
      </p>
    </div>
    <BaseBadge v-if="trend" :variant="trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'neutral'" size="sm" :icon="trendIcon">
      {{ trendLabel }}
    </BaseBadge>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink, type RouteLocationRaw } from 'vue-router'
import BaseBadge from './BaseBadge.vue'
import BaseIcon from './BaseIcon.vue'
import type { IconName, StatusVariant } from './icons'

const props = withDefaults(
  defineProps<{
    label: string
    value: string | number
    icon?: IconName
    /** Cor do ícone (não carrega significado sozinha; o rótulo diz o que é) */
    variant?: 'primary' | 'neutral' | StatusVariant
    hint?: string
    /** Tendência opcional: mostra badge com ícone + texto (trendLabel) */
    trend?: 'up' | 'down' | 'flat'
    trendLabel?: string
    /** Torna o card um link */
    to?: RouteLocationRaw
  }>(),
  {
    icon: undefined,
    variant: 'primary',
    hint: '',
    trend: undefined,
    trendLabel: '',
    to: undefined,
  },
)

defineSlots<{ hint?: () => unknown }>()

const trendIcon = computed<IconName | false>(() => {
  if (props.trend === 'up') return 'trending-up'
  if (props.trend === 'down') return 'trending-down'
  return false
})
</script>

<style scoped>
.ui-stat {
  display: flex;
  align-items: flex-start;
  gap: var(--space-4);
  min-width: 0;
  padding: var(--space-5);
  background: var(--color-surface);
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  color: var(--color-text);
  text-decoration: none;
}

.ui-stat--link {
  transition:
    border-color var(--transition-base),
    box-shadow var(--transition-base);
}

.ui-stat--link:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-md);
  color: var(--color-text);
}

.ui-stat__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 3rem;
  height: 3rem;
  border-radius: var(--radius-lg);
  font-size: 1.5rem;
}

.ui-stat__icon--primary {
  background: var(--color-primary-soft);
  color: var(--color-primary-soft-text);
}

.ui-stat__icon--neutral {
  background: var(--color-surface-muted);
  color: var(--color-text-muted);
}

.ui-stat__icon--success {
  background: var(--color-success-soft);
  color: var(--color-success);
}

.ui-stat__icon--warning {
  background: var(--color-warning-soft);
  color: var(--color-warning);
}

.ui-stat__icon--danger {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

.ui-stat__icon--info {
  background: var(--color-info-soft);
  color: var(--color-info);
}

.ui-stat__body {
  flex: 1;
  min-width: 0;
}

.ui-stat__label {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}

.ui-stat__value {
  font-size: var(--font-size-2xl);
  font-weight: var(--font-weight-bold);
  line-height: var(--line-height-tight);
  color: var(--color-text);
  overflow-wrap: anywhere;
}

.ui-stat__hint {
  margin-block-start: var(--space-1);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}
</style>
