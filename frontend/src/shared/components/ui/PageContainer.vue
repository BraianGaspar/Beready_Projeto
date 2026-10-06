<template>
  <component :is="as" class="ui-page" :class="`ui-page--${size}`">
    <slot />
  </component>
</template>

<script setup lang="ts">
// Envelope padrão de toda tela: largura máxima + gutter responsivo
// (16px no mobile, 24px >= 768px, 32px >= 1024px) + espaçamento vertical entre blocos.
withDefaults(
  defineProps<{
    /** sm 640px (formulários/auth) · md 960px (detalhe) · lg 1200px (listas, padrão) · xl 1400px (dashboards) · full */
    size?: 'sm' | 'md' | 'lg' | 'xl' | 'full'
    as?: string
  }>(),
  { size: 'lg', as: 'div' },
)

defineSlots<{ default?: () => unknown }>()
</script>

<style scoped>
.ui-page {
  --ui-page-max: var(--container-lg);
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
  width: 100%;
  max-width: calc(var(--ui-page-max) + var(--page-gutter) * 2);
  margin-inline: auto;
  padding: var(--space-6) var(--page-gutter) var(--space-12);
}

.ui-page--sm {
  --ui-page-max: var(--container-sm);
}

.ui-page--md {
  --ui-page-max: var(--container-md);
}

.ui-page--xl {
  --ui-page-max: var(--container-xl);
}

.ui-page--full {
  max-width: none;
}

@media (min-width: 1024px) {
  .ui-page {
    padding-block-start: var(--space-8);
  }
}
</style>
