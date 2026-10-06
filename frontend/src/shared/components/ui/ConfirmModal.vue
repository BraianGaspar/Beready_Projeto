<template>
  <BaseModal v-model="open" size="sm" hide-close>
    <template #header>
      <div class="ui-confirm__header">
        <span class="ui-confirm__icon" :class="`ui-confirm__icon--${type}`" aria-hidden="true">
          <BaseIcon :name="iconName" />
        </span>
        <h2 class="ui-confirm__title">{{ title || t('confirmModal.title') }}</h2>
      </div>
    </template>

    <p class="ui-confirm__message">{{ message || t('confirmModal.message') }}</p>
    <p v-if="itemName" class="ui-confirm__item" :class="`ui-confirm__item--${type}`">
      "{{ itemName }}"
    </p>
    <p v-if="warning" class="ui-confirm__warning" :class="`ui-confirm__warning--${type}`">
      <BaseIcon name="exclamation-triangle" class="ui-confirm__warning-icon" />
      <span>{{ warning }}</span>
    </p>
    <div v-if="$slots.default" class="ui-confirm__extra">
      <slot />
    </div>

    <template #footer>
      <BaseButton variant="secondary" @click="open = false">{{ t('common.cancelar') }}</BaseButton>
      <BaseButton
        :variant="type === 'danger' ? 'danger' : 'primary'"
        :loading="loading"
        @click="emit('confirm')"
      >
        {{ loading ? t('confirmModal.processing') : confirmText || t('confirmModal.confirm') }}
      </BaseButton>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
// Confirmação padrão (exclusões etc.). API mantida da versão antiga:
// v-model, title, message, confirmText, type, itemName, loading e evento `confirm`.
// Novos: `warning` (segunda linha de aviso, ex. "não pode ser desfeita") e slot default
// (conteúdo extra abaixo da mensagem). Textos vazios usam os padrões traduzidos (confirmModal.*).
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseButton from './BaseButton.vue'
import BaseIcon from './BaseIcon.vue'
import BaseModal from './BaseModal.vue'
import type { IconName } from './icons'

const props = withDefaults(
  defineProps<{
    title?: string
    message?: string
    confirmText?: string
    type?: 'danger' | 'warning' | 'info'
    itemName?: string
    /** Aviso complementar exibido com ícone (ex.: t('confirmModal.irreversible')) */
    warning?: string
    loading?: boolean
  }>(),
  {
    title: '',
    message: '',
    confirmText: '',
    type: 'danger',
    itemName: '',
    warning: '',
    loading: false,
  },
)

defineSlots<{ default?: () => unknown }>()

const open = defineModel<boolean>({ default: false })
const emit = defineEmits<{ confirm: [] }>()

const { t } = useI18n()

const iconName = computed<IconName>(() => {
  if (props.type === 'danger') return 'exclamation-triangle'
  if (props.type === 'warning') return 'exclamation-circle'
  return 'information-circle'
})
</script>

<style scoped>
.ui-confirm__header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-3);
  text-align: center;
  padding-block-start: var(--space-2);
}

.ui-confirm__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.5rem;
  height: 3.5rem;
  border-radius: var(--radius-full);
  font-size: 1.75rem;
}

.ui-confirm__icon--danger {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

.ui-confirm__icon--warning {
  background: var(--color-warning-soft);
  color: var(--color-warning);
}

.ui-confirm__icon--info {
  background: var(--color-info-soft);
  color: var(--color-info);
}

.ui-confirm__title {
  font-size: var(--font-size-xl);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
}

.ui-confirm__message {
  text-align: center;
  color: var(--color-text-muted);
  font-size: var(--font-size-sm);
}

.ui-confirm__item {
  margin-block-start: var(--space-4);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-md);
  text-align: center;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-bold);
  overflow-wrap: anywhere;
  background: var(--color-surface-muted);
  color: var(--color-text);
}

.ui-confirm__item--danger {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

.ui-confirm__warning {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: var(--space-2);
  margin-block-start: var(--space-4);
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  text-align: start;
}

.ui-confirm__warning-icon {
  flex-shrink: 0;
  width: 1.25em;
  height: 1.25em;
}

.ui-confirm__warning--danger {
  color: var(--color-danger);
}

.ui-confirm__warning--warning {
  color: var(--color-warning);
}

.ui-confirm__warning--info {
  color: var(--color-info);
}

.ui-confirm__extra {
  margin-block-start: var(--space-4);
}
</style>
