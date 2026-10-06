<template>
  <component
    :is="as"
    class="ui-card"
    :class="[
      `ui-card--pad-${padding}`,
      highlight && `ui-card--highlight ui-card--highlight-${highlight}`,
      { 'ui-card--interactive': interactive, 'ui-card--muted': muted },
    ]"
  >
    <header v-if="$slots.header || title" class="ui-card__header">
      <slot name="header">
        <div class="ui-card__header-row">
          <div class="ui-card__heading">
            <component :is="titleTag" class="ui-card__title">{{ title }}</component>
            <p v-if="subtitle" class="ui-card__subtitle">{{ subtitle }}</p>
          </div>
          <div v-if="$slots.actions" class="ui-card__actions">
            <slot name="actions" />
          </div>
        </div>
      </slot>
    </header>
    <div class="ui-card__body">
      <slot />
    </div>
    <footer v-if="$slots.footer" class="ui-card__footer">
      <slot name="footer" />
    </footer>
  </component>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    /** Elemento raiz (article, section, li, div...) */
    as?: string
    title?: string
    subtitle?: string
    /** Nível do título (h2 padrão) para manter a hierarquia da página */
    titleTag?: 'h2' | 'h3' | 'h4'
    padding?: 'none' | 'sm' | 'md' | 'lg'
    /** Realce em hover/foco interno (cards clicáveis) */
    interactive?: boolean
    /** Fundo --color-surface-muted (blocos secundários) */
    muted?: boolean
    /**
     * Realce de borda no tom indicado (ex.: plano atual = success, destaque = primary).
     * É reforço visual: o significado deve vir também de texto/badge dentro do card.
     */
    highlight?: 'primary' | 'success' | 'warning' | 'danger'
  }>(),
  {
    as: 'div',
    title: '',
    subtitle: '',
    titleTag: 'h2',
    padding: 'md',
    interactive: false,
    muted: false,
    highlight: undefined,
  },
)

defineSlots<{
  default?: () => unknown
  header?: () => unknown
  actions?: () => unknown
  footer?: () => unknown
}>()
</script>

<style scoped>
.ui-card {
  --ui-card-pad: var(--space-6);
  display: flex;
  flex-direction: column;
  min-width: 0;
  background: var(--color-surface);
  color: var(--color-text);
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.ui-card--muted {
  background: var(--color-surface-muted);
  box-shadow: none;
}

/* Realce: borda no tom + anel externo de 1px (borda visual de 2px sem mudar o tamanho do card) */
.ui-card--highlight {
  --ui-card-highlight: var(--color-primary);
  border-color: var(--ui-card-highlight);
  box-shadow:
    0 0 0 1px var(--ui-card-highlight),
    var(--shadow-md);
}

.ui-card--highlight-success {
  --ui-card-highlight: var(--color-success);
}

.ui-card--highlight-warning {
  --ui-card-highlight: var(--color-warning);
}

.ui-card--highlight-danger {
  --ui-card-highlight: var(--color-danger);
}

.ui-card--pad-none {
  --ui-card-pad: 0;
}

.ui-card--pad-sm {
  --ui-card-pad: var(--space-4);
}

.ui-card--pad-lg {
  --ui-card-pad: var(--space-8);
}

@media (max-width: 479.98px) {
  .ui-card--pad-md,
  .ui-card--pad-lg {
    --ui-card-pad: var(--space-4);
  }
}

.ui-card__header {
  padding: var(--ui-card-pad);
  padding-block-end: 0;
}

.ui-card__header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-3);
}

.ui-card__heading {
  min-width: 0;
  flex: 1 1 12rem;
}

.ui-card__title {
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
}

.ui-card__subtitle {
  margin-block-start: var(--space-1);
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}

.ui-card__actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
}

.ui-card__body {
  flex: 1;
  padding: var(--ui-card-pad);
}

.ui-card__footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: flex-end;
  gap: var(--space-2);
  padding: var(--space-3) var(--ui-card-pad);
  border-block-start: var(--border-width) solid var(--color-border);
  background: var(--color-surface-muted);
}

/* Conteúdo de texto único no rodapé ocupa a linha toda (alinhamento fica com quem usa) */
.ui-card__footer > :deep(:only-child:not(button):not(a)) {
  flex: 1;
}

.ui-card--interactive {
  transition:
    box-shadow var(--transition-base),
    border-color var(--transition-base),
    transform var(--transition-base);
}

.ui-card--interactive:hover,
.ui-card--interactive:focus-within {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-md);
}

/* Card realçado e clicável mantém o tom do realce no hover/foco */
.ui-card--highlight.ui-card--interactive:hover,
.ui-card--highlight.ui-card--interactive:focus-within {
  border-color: var(--ui-card-highlight);
  box-shadow:
    0 0 0 1px var(--ui-card-highlight),
    var(--shadow-lg);
}

@media (hover: hover) {
  .ui-card--interactive:hover {
    transform: translateY(-2px);
  }
}
</style>
