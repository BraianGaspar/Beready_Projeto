<template>
  <BaseModal v-model="open" size="sm" hide-close>
    <template #header>
      <div class="ui-confirm__header flex flex-col items-center gap-3 pt-2 text-center">
        <span
          class="ui-confirm__icon inline-flex size-14 items-center justify-center rounded-full text-icon-lg"
          :class="iconClasses[type]"
          aria-hidden="true"
        >
          <BaseIcon :name="iconName" />
        </span>
        <h2 class="ui-confirm__title text-xl font-bold text-text">{{ title || t('confirmModal.title') }}</h2>
      </div>
    </template>

    <p class="ui-confirm__message text-center text-sm text-text-muted">{{ message || t('confirmModal.message') }}</p>
    <p
      v-if="itemName"
      class="ui-confirm__item mt-4 rounded-md px-3 py-2 text-center text-sm font-bold wrap-anywhere"
      :class="type === 'danger' ? 'bg-danger-soft text-danger' : 'bg-surface-muted text-text'"
    >
      "{{ itemName }}"
    </p>
    <p
      v-if="warning"
      class="ui-confirm__warning mt-4 flex items-start justify-center gap-2 text-start text-sm font-semibold"
      :class="warningClasses[type]"
    >
      <BaseIcon name="exclamation-triangle" class="ui-confirm__warning-icon size-em-lg shrink-0" />
      <span>{{ warning }}</span>
    </p>
    <div v-if="$slots.default" class="ui-confirm__extra mt-4">
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

type ConfirmType = 'danger' | 'warning' | 'info'

const props = withDefaults(
  defineProps<{
    title?: string
    message?: string
    confirmText?: string
    type?: ConfirmType
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

const iconClasses: Record<ConfirmType, string> = {
  danger: 'bg-danger-soft text-danger',
  warning: 'bg-warning-soft text-warning',
  info: 'bg-info-soft text-info',
}

const warningClasses: Record<ConfirmType, string> = {
  danger: 'text-danger',
  warning: 'text-warning',
  info: 'text-info',
}
</script>
