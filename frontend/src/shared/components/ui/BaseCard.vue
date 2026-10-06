<template>
  <component :is="as" :class="rootClasses">
    <header v-if="$slots.header || title" class="ui-card__header" :class="headerPad[padding]">
      <slot name="header">
        <div class="ui-card__header-row flex flex-wrap items-start justify-between gap-3">
          <div class="ui-card__heading min-w-0 shrink grow basis-48">
            <component :is="titleTag" class="ui-card__title text-lg font-semibold text-text">
              {{ title }}
            </component>
            <p v-if="subtitle" class="ui-card__subtitle mt-1 text-sm text-text-muted">{{ subtitle }}</p>
          </div>
          <div v-if="$slots.actions" class="ui-card__actions flex flex-wrap gap-2">
            <slot name="actions" />
          </div>
        </div>
      </slot>
    </header>
    <div class="ui-card__body flex-1" :class="bodyPad[padding]">
      <slot />
    </div>
    <!-- texto único no rodapé ocupa a linha toda (only-text:); alinhamento fica com quem usa -->
    <footer
      v-if="$slots.footer"
      class="ui-card__footer flex flex-wrap items-center justify-end gap-2 border-0 border-t border-solid border-border bg-surface-muted py-3 only-text:flex-1"
      :class="footerPad[padding]"
    >
      <slot name="footer" />
    </footer>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'

type Padding = 'none' | 'sm' | 'md' | 'lg'
type Highlight = 'primary' | 'success' | 'warning' | 'danger'

const props = withDefaults(
  defineProps<{
    /** Elemento raiz (article, section, li, div...) */
    as?: string
    title?: string
    subtitle?: string
    /** Nível do título (h2 padrão) para manter a hierarquia da página */
    titleTag?: 'h2' | 'h3' | 'h4'
    padding?: Padding
    /** Realce em hover/foco interno (cards clicáveis) */
    interactive?: boolean
    /** Fundo --color-surface-muted (blocos secundários) */
    muted?: boolean
    /**
     * Realce de borda no tom indicado (ex.: plano atual = success, destaque = primary).
     * É reforço visual: o significado deve vir também de texto/badge dentro do card.
     */
    highlight?: Highlight
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

// Respiro: md/lg caem para 16px abaixo de 480px
const bodyPad: Record<Padding, string> = {
  none: 'p-0',
  sm: 'p-4',
  md: 'p-4 sm:p-6',
  lg: 'p-4 sm:p-8',
}
// Cabeçalho: mesmo respiro, sem o inferior (o corpo já tem)
const headerPad: Record<Padding, string> = {
  none: 'px-0 pt-0',
  sm: 'px-4 pt-4',
  md: 'px-4 pt-4 sm:px-6 sm:pt-6',
  lg: 'px-4 pt-4 sm:px-8 sm:pt-8',
}
const footerPad: Record<Padding, string> = {
  none: 'px-0',
  sm: 'px-4',
  md: 'px-4 sm:px-6',
  lg: 'px-4 sm:px-8',
}

// Realce: borda no tom + anel externo de 1px (borda visual de 2px sem mudar o tamanho)
const highlightClasses: Record<Highlight, string> = {
  primary: 'border-primary ring-1 ring-primary',
  success: 'border-success ring-1 ring-success',
  warning: 'border-warning ring-1 ring-warning',
  danger: 'border-danger ring-1 ring-danger',
}

const rootClasses = computed(() => {
  const { highlight, muted, interactive } = props
  return [
    'ui-card flex min-w-0 flex-col overflow-hidden rounded-xl border border-solid text-text',
    muted ? 'bg-surface-muted' : 'bg-surface',
    highlight ? highlightClasses[highlight] : 'border-border',
    highlight ? 'shadow-md' : muted ? 'shadow-none' : 'shadow-sm',
    interactive && 'transition-card can-hover:-translate-y-0.5',
    // Realçado e clicável mantém o tom do realce no hover/foco
    interactive &&
      (highlight
        ? 'hover:shadow-lg focus-within:shadow-lg'
        : 'hover:border-primary hover:shadow-md focus-within:border-primary focus-within:shadow-md'),
  ]
})
</script>
