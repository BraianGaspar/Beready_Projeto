<template>
  <span class="ui-badge" :class="[`ui-badge--${variant}`, `ui-badge--${size}`, { 'ui-badge--solid': solid }]">
    <BaseIcon v-if="iconName" :name="iconName" class="ui-badge__icon" />
    <slot />
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseIcon from './BaseIcon.vue'
import { statusIcons, type IconName, type StatusVariant } from './icons'

const props = withDefaults(
  defineProps<{
    variant?: 'neutral' | 'primary' | StatusVariant
    size?: 'sm' | 'md'
    /**
     * Ícone: nome do registro, ou `true` para o ícone padrão do status
     * (recomendado em success/warning/danger/info: status não pode depender só de cor).
     */
    icon?: IconName | boolean
    /** Fundo sólido em vez de suave */
    solid?: boolean
  }>(),
  { variant: 'neutral', size: 'md', icon: false, solid: false },
)

defineSlots<{ default?: () => unknown }>()

const iconName = computed<IconName | null>(() => {
  if (typeof props.icon === 'string') return props.icon
  if (props.icon === true && props.variant in statusIcons) {
    return statusIcons[props.variant as StatusVariant]
  }
  return null
})
</script>

<style scoped>
.ui-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  max-width: 100%;
  padding: 0.125rem var(--space-2);
  border: var(--border-width) solid transparent;
  border-radius: var(--radius-full);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-semibold);
  line-height: var(--line-height-base);
  white-space: nowrap;
  vertical-align: middle;
}

.ui-badge--md {
  padding: 0.25rem var(--space-3);
  font-size: var(--font-size-sm);
}

.ui-badge__icon {
  width: 1.1em;
  height: 1.1em;
}

.ui-badge--neutral {
  background: var(--color-surface-muted);
  border-color: var(--color-border);
  color: var(--color-text-muted);
}

.ui-badge--primary {
  background: var(--color-primary-soft);
  color: var(--color-primary-soft-text);
}

.ui-badge--success {
  background: var(--color-success-soft);
  color: var(--color-success);
}

.ui-badge--warning {
  background: var(--color-warning-soft);
  color: var(--color-warning);
}

.ui-badge--danger {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

.ui-badge--info {
  background: var(--color-info-soft);
  color: var(--color-info);
}

/* Sólidos */
.ui-badge--solid.ui-badge--neutral {
  background: var(--color-text-muted);
  color: var(--color-surface);
}

.ui-badge--solid.ui-badge--primary {
  background: var(--color-primary);
  color: var(--color-primary-contrast);
}

.ui-badge--solid.ui-badge--success {
  background: var(--color-success);
  color: var(--color-success-contrast);
}

.ui-badge--solid.ui-badge--warning {
  background: var(--color-warning);
  color: var(--color-warning-contrast);
}

.ui-badge--solid.ui-badge--danger {
  background: var(--color-danger);
  color: var(--color-danger-contrast);
}

.ui-badge--solid.ui-badge--info {
  background: var(--color-info);
  color: var(--color-info-contrast);
}
</style>
