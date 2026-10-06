<template>
  <PageContainer class="tag-list">
    <PageHeader :title="$t('tags.title')" :subtitle="$t('tags.subtitle')" icon="tag" back-to="/dashboard">
      <template #actions>
        <BaseButton variant="secondary" icon="plus" @click="openModal">{{ $t('tags.newTag') }}</BaseButton>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('tags.carregando')" />

    <EmptyState v-else-if="tags.length === 0" icon="tag" :title="$t('tags.emptyTitle')" :description="$t('tags.emptyDescription')">
      <BaseButton icon="plus" @click="openModal">{{ $t('tags.createFirst') }}</BaseButton>
    </EmptyState>

    <ul v-else class="tag-list__grid u-grid-auto" role="list">
      <!-- A cor da tag é dado do usuário: vai numa custom property local (não é cor fixa de tema) -->
      <li v-for="tag in tags" :key="tag.id" class="tag-list__item" :style="{ '--tag-color': tag.cor }">
        <BaseCard as="article" padding="sm" class="tag-list__card">
          <div class="tag-list__row">
            <span class="tag-list__swatch" aria-hidden="true"></span>
            <div class="tag-list__info">
              <h2 class="tag-list__name">{{ tag.nome }}</h2>
              <p class="tag-list__description" :class="{ 'tag-list__description--empty': !tag.descricao }">
                {{ tag.descricao || $t('tags.semDescricao') }}
              </p>
            </div>
            <div class="tag-list__actions">
              <BaseButton variant="ghost" icon="pencil" :aria-label="`${$t('common.editar')}: ${tag.nome}`" @click="editTag(tag)" />
              <BaseButton
                variant="ghost-danger"
                icon="trash"
                :aria-label="`${$t('common.excluir')}: ${tag.nome}`"
                @click="confirmDelete(tag)"
              />
            </div>
          </div>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar -->
    <BaseModal
      v-model="modalOpen"
      :title="editingTag ? $t('tags.editTag') : $t('tags.newTag')"
      :description="editingTag ? $t('tags.editSubtitle') : $t('tags.createSubtitle')"
    >
      <form id="tag-list-form" class="u-stack" @submit.prevent="saveTag">
        <BaseInput v-model="form.nome" :label="$t('tags.nome')" :placeholder="$t('tags.nomePlaceholder')" required />
        <BaseField field-id="tag-list-cor" :label="$t('tags.cor')">
          <div class="tag-list__color">
            <input id="tag-list-cor" v-model="form.cor" type="color" class="tag-list__color-input" />
            <span class="tag-list__color-value">{{ form.cor }}</span>
          </div>
        </BaseField>
        <BaseTextarea
          v-model="form.descricao"
          :label="$t('tags.descricao')"
          :placeholder="$t('tags.descricaoPlaceholder')"
          :rows="3"
        />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="closeModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="tag-list-form" :loading="saving">
          {{ saving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('tags.confirmDelete')"
      :message="$t('tags.deleteMessage')"
      :warning="$t('confirmModal.irreversible')"
      :item-name="tagToDelete?.nome"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import { useTags } from './Tags'
import {
  BaseButton,
  BaseCard,
  BaseField,
  BaseInput,
  BaseModal,
  BaseSpinner,
  BaseTextarea,
  ConfirmModal,
  EmptyState,
  PageContainer,
  PageHeader,
} from '@/shared/components/ui'

const {
  tags,
  loading,
  modalOpen,
  saving,
  form,
  editingTag,
  confirmModalVisible,
  tagToDelete,
  deleting,
  openModal,
  editTag,
  closeModal,
  saveTag,
  confirmDelete,
  handleConfirmDelete,
} = useTags()
</script>

<style scoped>
@import '@/styles/views/tags/tags.css';
</style>
