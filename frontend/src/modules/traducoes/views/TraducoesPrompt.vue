<template>
  <PageContainer class="traducoes-prompt">
    <PageHeader
      :title="$t('traducoes.title')"
      :subtitle="$t('common.promptLabel', { texto: promptTexto })"
      icon="language"
      :back-to="`/prompts/${promptId}`"
    >
      <template #actions>
        <BaseButton variant="secondary" icon="plus" @click="openModal">{{ $t('traducoes.new') }}</BaseButton>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('traducoes.loading')" />

    <EmptyState
      v-else-if="traducoes.length === 0"
      icon="language"
      :title="$t('traducoes.emptyTitle')"
      :description="$t('traducoes.emptyDescription')"
    >
      <BaseButton icon="plus" @click="openModal">{{ $t('traducoes.add') }}</BaseButton>
    </EmptyState>

    <ul v-else class="traducoes-prompt__grid u-grid-auto" role="list">
      <li v-for="traducao in traducoes" :key="traducao.id" class="traducoes-prompt__item">
        <BaseCard as="article" padding="sm" class="traducoes-prompt__card">
          <div class="traducoes-prompt__card-top">
            <BaseBadge variant="primary" icon="language">{{ traducao.idioma_destino?.toUpperCase() || 'PT' }}</BaseBadge>
            <div class="traducoes-prompt__actions">
              <BaseButton
                variant="ghost"
                icon="pencil"
                :aria-label="$t('common.editar')"
                @click="editTraducao(traducao)"
              />
              <BaseButton
                variant="ghost-danger"
                icon="trash"
                :aria-label="$t('common.excluir')"
                @click="confirmDelete(traducao)"
              />
            </div>
          </div>
          <p class="traducoes-prompt__text">{{ traducao.texto_traduzido }}</p>
          <template #footer>
            <div class="traducoes-prompt__meta">
              <span>{{ $t('traducoes.confidence', { valor: Math.round((traducao.pontuacao_confianca || 0) * 100) }) }}</span>
              <span>{{ formatDate(traducao.criado_em) }}</span>
            </div>
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar -->
    <BaseModal v-model="modalOpen" :title="editingId ? $t('traducoes.edit') : $t('traducoes.new')">
      <form id="traducoes-prompt-form" class="u-stack" @submit.prevent="save">
        <BaseTextarea
          v-model="form.texto_traduzido"
          :label="$t('traducoes.textLabel')"
          :placeholder="$t('traducoes.textPlaceholder')"
          :rows="4"
          required
        />
        <BaseSelect v-model="form.idioma_destino" :label="$t('traducoes.targetLanguage')" :options="idiomaOptions" />
        <BaseField field-id="traducoes-prompt-confianca" :label="$t('traducoes.confidenceLabel')">
          <div class="traducoes-prompt__range">
            <input
              id="traducoes-prompt-confianca"
              v-model.number="form.pontuacao_confianca"
              type="range"
              min="0"
              max="1"
              step="0.01"
              class="traducoes-prompt__range-input"
              :aria-valuetext="`${Math.round((form.pontuacao_confianca || 0) * 100)}%`"
            />
            <output for="traducoes-prompt-confianca" class="traducoes-prompt__range-value">
              {{ Math.round((form.pontuacao_confianca || 0) * 100) }}%
            </output>
          </div>
        </BaseField>
        <BaseSelect v-model="form.servico_traducao" :label="$t('traducoes.serviceLabel')" :options="servicoOptions" />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="traducoes-prompt-form" :loading="saving">
          {{ saving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('prompts.confirmDelete')"
      :message="$t('traducoes.deleteMessage')"
      :warning="$t('confirmModal.irreversible')"
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
import { useTraducoesPrompt } from './TraducoesPrompt'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseField,
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
  promptId,
  promptTexto,
  loading,
  traducoes,
  modalOpen,
  saving,
  form,
  editingId,
  deleting,
  confirmModalVisible,
  formatDate,
  openModal,
  closeModal,
  editTraducao,
  save,
  confirmDelete,
  handleConfirmDelete,
} = useTraducoesPrompt()

const idiomaOptions = computed<SelectOption[]>(() => [
  { value: 'pt-BR', label: t('idiomas.ptBR') },
  { value: 'en', label: t('idiomas.en') },
  { value: 'es', label: t('idiomas.es') },
  { value: 'fr', label: t('idiomas.fr') },
  { value: 'de', label: t('idiomas.de') },
  { value: 'it', label: t('idiomas.it') },
])

// Nomes de produto (não traduzidos)
const servicoOptions: SelectOption[] = [
  { value: 'google', label: 'Google Translate' },
  { value: 'deepl', label: 'DeepL' },
  { value: 'microsoft', label: 'Microsoft Translator' },
  { value: 'openai', label: 'OpenAI' },
]
</script>

<style scoped>
@import '@/styles/views/traducoes/traducoes.css';
</style>
