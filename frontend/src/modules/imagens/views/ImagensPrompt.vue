<template>
  <PageContainer class="imagens-prompt">
    <PageHeader
      :title="$t('imagens.title')"
      :subtitle="$t('common.promptLabel', { texto: promptTexto })"
      icon="photo"
      :back-to="`/prompts/${promptId}`"
    >
      <template #actions>
        <BaseButton variant="secondary" icon="plus" @click="openModal">{{ $t('imagens.new') }}</BaseButton>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('imagens.loading')" />

    <EmptyState
      v-else-if="imagens.length === 0"
      icon="photo"
      :title="$t('imagens.emptyTitle')"
      :description="$t('imagens.emptyDescription')"
    >
      <BaseButton icon="plus" @click="openModal">{{ $t('imagens.add') }}</BaseButton>
    </EmptyState>

    <ul v-else class="imagens-prompt__grid u-grid-auto" role="list">
      <li v-for="imagem in imagens" :key="imagem.id" class="imagens-prompt__item">
        <BaseCard as="article" padding="none" class="imagens-prompt__card">
          <div class="imagens-prompt__media">
            <img
              :src="imagem.url_imagem"
              :alt="imagem.prompt_imagem || $t('imagens.altFallback')"
              class="imagens-prompt__img"
              loading="lazy"
            />
            <BaseButton
              variant="danger"
              icon="trash"
              class="imagens-prompt__media-action"
              :aria-label="$t('common.excluir')"
              @click="confirmDelete(imagem)"
            />
          </div>
          <template #footer>
            <div class="imagens-prompt__meta">
              <span>{{ imagem.servico_geracao || $t('imagens.aiFallback') }}</span>
              <BaseBadge size="sm">{{ getQualidadeLabel(imagem.qualidade_imagem) }}</BaseBadge>
              <span>{{ formatDate(imagem.criado_em) }}</span>
            </div>
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar -->
    <BaseModal v-model="modalOpen" :title="editingId ? $t('imagens.edit') : $t('imagens.new')">
      <form id="imagens-prompt-form" class="u-stack" @submit.prevent="save">
        <BaseInput
          v-model="form.url_imagem"
          type="url"
          :label="$t('imagens.urlLabel')"
          :placeholder="$t('imagens.urlPlaceholder')"
          inputmode="url"
          required
        />
        <BaseTextarea
          v-model="form.prompt_imagem"
          :label="$t('imagens.promptLabel')"
          :placeholder="$t('imagens.promptPlaceholder')"
          :rows="3"
        />
        <div class="imagens-prompt__form-row">
          <BaseSelect v-model="form.servico_geracao" :label="$t('imagens.serviceLabel')" :options="servicoOptions" />
          <BaseSelect v-model="form.qualidade_imagem" :label="$t('imagens.qualityLabel')" :options="qualidadeOptions" />
          <BaseSelect v-model="form.dimensoes" :label="$t('imagens.dimensionsLabel')" :options="dimensoesOptions" />
        </div>
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="imagens-prompt-form" :loading="saving">
          {{ saving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('prompts.confirmDelete')"
      :message="$t('imagens.deleteMessage')"
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
import { useImagensPrompt } from './ImagensPrompt'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseInput,
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
  imagens,
  modalOpen,
  saving,
  form,
  editingId,
  deleting,
  confirmModalVisible,
  formatDate,
  getQualidadeLabel,
  openModal,
  closeModal,
  save,
  confirmDelete,
  handleConfirmDelete,
} = useImagensPrompt()

// Nomes de produto (não traduzidos), exceto "Outro"
const servicoOptions = computed<SelectOption[]>(() => [
  { value: 'dalle', label: 'DALL-E' },
  { value: 'midjourney', label: 'Midjourney' },
  { value: 'stable', label: 'Stable Diffusion' },
  { value: 'other', label: t('imagens.serviceOther') },
])

const qualidadeOptions = computed<SelectOption[]>(() => [
  { value: 'baixa', label: t('imagens.qualidades.baixa') },
  { value: 'media', label: t('imagens.qualidades.media') },
  { value: 'alta', label: t('imagens.qualidades.alta') },
])

const dimensoesOptions: SelectOption[] = ['512x512', '1024x1024', '1024x1792', '1792x1024'].map((d) => ({
  value: d,
  label: d,
}))
</script>

<style scoped>
@import '@/styles/views/imagens/imagens.css';
</style>
