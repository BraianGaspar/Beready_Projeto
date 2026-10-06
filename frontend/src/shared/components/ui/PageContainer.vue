<template>
  <component
    :is="as"
    class="ui-page mx-auto flex w-full flex-col gap-6 px-gutter pb-12 pt-6 lg:pt-8"
    :class="sizeClasses[size]"
  >
    <slot />
  </component>
</template>

<script setup lang="ts">
// Envelope padrão de toda tela: largura máxima + gutter responsivo
// (16px no mobile, 24px >= 768px, 32px >= 1024px) + espaçamento vertical entre blocos.
type Size = 'sm' | 'md' | 'lg' | 'xl' | 'full'

withDefaults(
  defineProps<{
    /** sm 640px (formulários/auth) · md 960px (detalhe) · lg 1200px (listas, padrão) · xl 1400px (dashboards) · full */
    size?: Size
    as?: string
  }>(),
  { size: 'lg', as: 'div' },
)

defineSlots<{ default?: () => unknown }>()

// max-w-page-* = largura do conteúdo + gutter dos dois lados
const sizeClasses: Record<Size, string> = {
  sm: 'max-w-page-sm',
  md: 'max-w-page-md',
  lg: 'max-w-page-lg',
  xl: 'max-w-page-xl',
  full: 'max-w-none',
}
</script>
