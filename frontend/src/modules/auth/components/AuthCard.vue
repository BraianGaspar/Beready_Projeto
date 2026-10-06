<template>
  <main class="auth-card">
    <BaseCard class="auth-card__card" :class="`auth-card__card--${size}`" padding="lg">
      <template #header>
        <div class="auth-card__header">
          <span class="auth-card__icon" aria-hidden="true">
            <BaseIcon :name="icon" />
          </span>
          <h1 class="auth-card__title">{{ title }}</h1>
          <p v-if="subtitle" class="auth-card__subtitle">{{ subtitle }}</p>
        </div>
      </template>

      <slot />

      <template v-if="$slots.footer" #footer>
        <div class="auth-card__footer">
          <slot name="footer" />
        </div>
      </template>
    </BaseCard>
  </main>
</template>

<script setup lang="ts">
// Moldura comum das telas públicas de autenticação (login, cadastro,
// esqueci/redefinir senha, retorno OAuth): página centralizada + card com
// ícone, título (<h1>) e subtítulo. Só apresentação.
import { BaseCard, BaseIcon, type IconName } from '@/shared/components/ui'

withDefaults(
  defineProps<{
    title: string
    subtitle?: string
    icon?: IconName
    /** sm ≈ 28rem (formulários curtos) · lg ≈ 60rem (cadastro em colunas) */
    size?: 'sm' | 'lg'
  }>(),
  { subtitle: '', icon: 'user', size: 'sm' },
)

defineSlots<{ default?: () => unknown; footer?: () => unknown }>()
</script>

<style scoped>
@import '@/styles/views/auth/auth-card.css';
</style>
