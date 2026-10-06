<template>
  <PageContainer class="prompt-list">
    <PageHeader
      :title="$t('prompts.title')"
      :subtitle="$t('prompts.subtitle')"
      icon="chat"
      back-to="/dashboard"
    />

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('prompts.carregando')" />

    <!-- Sem permissão -->
    <EmptyState
      v-else-if="!canView"
      icon="lock-closed"
      :title="$t('common.acessoNegado')"
      :description="$t('common.permissionDenied')"
    >
      <BaseButton to="/dashboard" icon="arrow-left">{{ $t('common.voltarDashboard') }}</BaseButton>
    </EmptyState>

    <!-- Vazio -->
    <template v-else-if="prompts.length === 0">
      <EmptyState icon="chat" :title="$t('prompts.emptyTitle')" :description="$t('prompts.emptyDescription')">
        <BaseButton v-if="canCreatePrompt" icon="plus" @click="openModal">
          {{ $t('prompts.createFirst') }}
        </BaseButton>
        <template v-else>
          <BaseButton icon="plus" disabled>{{ $t('prompts.createFirst') }}</BaseButton>
          <BaseBadge variant="warning" icon>{{ canCreateMorePrompts ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </template>
      </EmptyState>

      <BaseAlert v-if="!canCreateMorePrompts" variant="warning">
        <div class="prompt-list__limit flex flex-wrap items-center gap-3">
          <span>{{ $t('prompts.limitReached') }}</span>
          <BaseButton to="/planos" variant="secondary" size="sm" icon-end="arrow-right">
            {{ $t('prompts.upgradeToCreateMore') }}
          </BaseButton>
        </div>
      </BaseAlert>
    </template>

    <!-- Grade -->
    <ul v-else class="prompt-list__grid grid list-none grid-cols-fill gap-6" role="list">
      <li class="prompt-list__item flex min-w-0">
        <button
          v-if="canCreatePrompt"
          type="button"
          class="prompt-list__create flex min-h-56 flex-1 cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border-strong bg-surface p-6 text-center text-text font-inherit transition-colors hover:border-primary hover:bg-primary-soft focus-visible:focus-ring"
          @click="openModal"
        >
          <span class="prompt-list__create-icon inline-flex size-14 items-center justify-center rounded-full bg-primary-soft text-2xl text-primary-soft-text" aria-hidden="true">
            <BaseIcon name="plus" />
          </span>
          <span class="prompt-list__create-title text-lg font-semibold">{{ $t('prompts.newPrompt') }}</span>
          <span class="prompt-list__create-subtitle text-sm text-text-muted">{{ $t('prompts.createSubtitle') }}</span>
        </button>
        <div v-else class="prompt-list__create prompt-list__create--disabled flex min-h-56 flex-1 cursor-not-allowed flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border-strong bg-surface-muted p-6 text-center text-text font-inherit" aria-disabled="true">
          <span class="prompt-list__create-icon inline-flex size-14 items-center justify-center rounded-full bg-surface-hover text-2xl text-text-muted" aria-hidden="true">
            <BaseIcon name="plus" />
          </span>
          <span class="prompt-list__create-title text-lg font-semibold">{{ $t('prompts.newPrompt') }}</span>
          <span class="prompt-list__create-subtitle text-sm text-text-muted">{{ $t('prompts.createSubtitle') }}</span>
          <BaseBadge variant="warning" icon>{{ canCreateMorePrompts ? $t('common.semPermissao') : $t('common.limiteAtingido') }}</BaseBadge>
        </div>
      </li>

      <li v-for="prompt in prompts" :key="prompt.id" class="prompt-list__item flex min-w-0">
        <BaseCard as="article" padding="sm" class="prompt-list__card h-full flex-1">
          <div class="prompt-list__card-top flex items-center justify-between gap-2">
            <BaseBadge variant="primary" icon="language">{{ getLanguageName(prompt.idioma_original) }}</BaseBadge>
            <div v-if="canEdit || canDelete" class="prompt-list__actions flex gap-1">
              <BaseButton
                v-if="canEdit"
                variant="ghost"
                icon="pencil"
                :aria-label="$t('common.editar')"
                @click="editPrompt(prompt)"
              />
              <BaseButton
                v-if="canDelete"
                variant="ghost-danger"
                icon="trash"
                :aria-label="$t('common.excluir')"
                @click="confirmDelete(prompt)"
              />
            </div>
          </div>
          <p class="prompt-list__text my-3 wrap-anywhere line-clamp-4 leading-relaxed text-text">{{ prompt.texto_original }}</p>
          <div class="prompt-list__meta flex flex-wrap items-center justify-between gap-2">
            <BaseBadge size="sm">{{ getContextName(prompt.contexto) }}</BaseBadge>
            <span class="prompt-list__date inline-flex items-center gap-1 text-xs text-text-muted">
              <BaseIcon name="clock" />
              {{ formatDate(prompt.criado_em) }}
            </span>
          </div>
          <template #footer>
            <BaseButton variant="secondary" icon="language" block @click="viewTranslations(prompt.id)">
              {{ $t('prompts.verTraducoes') }}
            </BaseButton>
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar -->
    <BaseModal
      v-model="modalOpen"
      :title="editingPrompt ? $t('prompts.editPrompt') : $t('prompts.newPrompt')"
      :description="editingPrompt ? $t('prompts.editSubtitle') : $t('prompts.createSubtitle')"
    >
      <form id="prompt-list-form" class="prompt-list__form flex flex-col gap-4" @submit.prevent="savePrompt">
        <BaseTextarea
          v-model="form.texto_original"
          :label="$t('prompts.textoOriginal')"
          :placeholder="$t('prompts.textoPlaceholder')"
          :rows="4"
          required
        />
        <BaseSelect v-model="form.idioma_original" :label="$t('prompts.idiomaOriginal')" :options="idiomaOptions" />
        <BaseSelect v-model="form.contexto" :label="$t('prompts.contexto')" :options="contextoOptions" />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="prompt-list-form" :loading="saving">
          {{ saving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('prompts.confirmDelete')"
      :message="$t('prompts.deleteMessage')"
      :warning="$t('confirmModal.irreversible')"
      :item-name="promptToDelete?.texto_original?.substring(0, 50)"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePrompts } from './Prompts'
import {
  BaseAlert,
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseModal,
  BaseSelect,
  BaseSpinner,
  BaseTextarea,
  ConfirmModal,
  EmptyState,
  PageContainer,
  PageHeader,
  type SelectOption,
} from '@/shared/components/ui'

const { t } = useI18n()

const {
  loading,
  prompts,
  modalOpen,
  saving,
  form,
  editingPrompt,
  openModal,
  editPrompt,
  closeModal,
  savePrompt,
  confirmDelete,
  formatDate,
  viewTranslations,
  confirmModalVisible,
  promptToDelete,
  deleting,
  handleConfirmDelete,
  getLanguageName,
  getContextName,
  canView,
  canEdit,
  canDelete,
  canCreatePrompt,
  canCreateMorePrompts,
} = usePrompts()

const idiomaOptions = computed<SelectOption[]>(() => [
  { value: 'pt-BR', label: t('idiomas.pt') },
  { value: 'en', label: t('idiomas.en') },
  { value: 'es', label: t('idiomas.es') },
  { value: 'fr', label: t('idiomas.fr') },
])

const contextoOptions = computed<SelectOption[]>(() => [
  { value: 'manual', label: t('prompts.contextoManual') },
  { value: 'conversacao', label: t('prompts.contextoConversacao') },
  { value: 'negocios', label: t('prompts.contextoNegocios') },
  { value: 'viagem', label: t('prompts.contextoViagem') },
  { value: 'estudo', label: t('prompts.contextoEstudo') },
])
</script>

