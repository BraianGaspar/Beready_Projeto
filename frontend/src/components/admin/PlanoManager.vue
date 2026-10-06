<template>
  <div class="plano-manager">
    <div class="plano-manager__header">
      <h2 class="plano-manager__title">{{ $t('admin.planos.title') }}</h2>
      <div class="plano-manager__toolbar">
        <div class="plano-manager__filters" role="group" :aria-label="$t('admin.status')">
          <button
            v-for="f in filters"
            :key="f.value"
            type="button"
            class="plano-manager__filter"
            :class="{ 'plano-manager__filter--active': filterStatus === f.value }"
            :aria-pressed="filterStatus === f.value"
            @click="filterStatus = f.value"
          >
            <BaseIcon v-if="f.icon" :name="f.icon" />
            {{ f.label }}
          </button>
        </div>
        <BaseButton icon="plus" @click="openCreateModal">{{ $t('admin.planos.new') }}</BaseButton>
      </div>
    </div>

    <EmptyState v-if="!filteredPlanos.length" compact icon="inbox" title-tag="h3" :title="$t('common.empty')" />

    <ul v-else class="plano-manager__grid u-grid-auto" role="list">
      <li v-for="plano in filteredPlanos" :key="plano.id" class="plano-manager__item">
        <BaseCard
          as="article"
          padding="sm"
          :title="plano.nome"
          title-tag="h3"
          class="plano-manager__card"
          :class="{ 'plano-manager__card--inactive': !plano.is_ativo }"
        >
          <template #actions>
            <BaseBadge v-if="plano.is_ativo" variant="success" icon="check-circle">{{ $t('admin.planos.active') }}</BaseBadge>
            <BaseBadge v-else icon="x-circle">{{ $t('admin.planos.inactive') }}</BaseBadge>
          </template>

          <p class="plano-manager__description">{{ plano.descricao || $t('common.semDescricao') }}</p>

          <dl class="plano-manager__prices">
            <div class="plano-manager__price">
              <dt>{{ $t('admin.planos.monthly') }}</dt>
              <dd>{{ formatCurrency(plano.preco_mensal) }}</dd>
            </div>
            <div class="plano-manager__price">
              <dt>{{ $t('admin.planos.yearly') }}</dt>
              <dd>{{ formatCurrency(plano.preco_anual) }}</dd>
            </div>
            <div v-if="plano.dias_trial > 0" class="plano-manager__price">
              <dt>{{ $t('admin.planos.trialDays') }}</dt>
              <dd>{{ plano.dias_trial }} {{ $t('admin.planos.days') }}</dd>
            </div>
          </dl>

          <div class="plano-manager__role">
            <span class="plano-manager__label">{{ $t('admin.planos.role') }}:</span>
            <span class="plano-manager__value">{{ plano.role?.nome || $t('admin.planos.noRole') }}</span>
            <BaseBadge v-if="plano.role?.nivel" size="sm" variant="primary">
              {{ $t('admin.roles.level') }} {{ plano.role.nivel }}
            </BaseBadge>
          </div>

          <section v-if="plano.recursos?.length" class="plano-manager__section">
            <h4 class="plano-manager__section-title">{{ $t('admin.planos.resources') }}</h4>
            <ul class="plano-manager__tags" role="list">
              <li v-for="recurso in plano.recursos" :key="recurso">
                <BaseBadge size="sm" icon="check">{{ formatRecurso(recurso) }}</BaseBadge>
              </li>
            </ul>
          </section>

          <section v-if="Object.keys(plano.limites || {}).length" class="plano-manager__section">
            <h4 class="plano-manager__section-title">{{ $t('admin.planos.limits') }}</h4>
            <dl class="plano-manager__limits">
              <div v-for="(value, key) in plano.limites" :key="key" class="plano-manager__limit">
                <dt>{{ formatLimiteKey(key) }}</dt>
                <dd>
                  <BaseBadge v-if="value === -1" size="sm" variant="info" icon="sparkles">
                    {{ $t('admin.planos.unlimited') }}
                  </BaseBadge>
                  <template v-else>{{ value }}</template>
                </dd>
              </div>
            </dl>
          </section>

          <p v-if="plano.ordem !== undefined" class="plano-manager__order">
            <span class="plano-manager__label">{{ $t('admin.planos.order') }}:</span>
            <span class="plano-manager__value">{{ plano.ordem }}</span>
          </p>

          <template #footer>
            <BaseButton variant="ghost" icon="pencil" @click="handleEditPlano(plano)">{{ $t('common.editar') }}</BaseButton>
            <BaseButton variant="secondary" :loading="togglingStatus === plano.id" @click="handleToggleStatus(plano)">
              {{ plano.is_ativo ? $t('admin.planos.deactivate') : $t('admin.planos.activate') }}
            </BaseButton>
            <BaseButton variant="danger" icon="trash" @click="confirmDelete(plano)">{{ $t('common.excluir') }}</BaseButton>
          </template>
        </BaseCard>
      </li>
    </ul>

    <!-- Criar / editar -->
    <BaseModal
      v-model="isModalOpen"
      size="lg"
      :title="editingPlano ? $t('admin.planos.edit') : $t('admin.planos.new')"
      @close="handleCloseModal"
    >
      <form id="plano-manager-form" class="u-stack" @submit.prevent="handleSavePlano">
        <div class="plano-manager__form-row">
          <BaseInput v-model="formData.nome" :label="$t('admin.planos.name')" :placeholder="$t('admin.planos.namePlaceholder')" required />
          <BaseInput
            v-model="formData.descricao"
            :label="$t('admin.planos.description')"
            :placeholder="$t('admin.planos.descriptionPlaceholder')"
          />
        </div>

        <div class="plano-manager__form-row">
          <BaseSelect v-model="roleIdModel" :label="$t('admin.planos.role')" :options="roleOptions" />
          <BaseInput v-model.number="formData.ordem" type="number" inputmode="numeric" :label="$t('admin.planos.order')" placeholder="0" />
        </div>

        <div class="plano-manager__form-row">
          <BaseInput
            v-model.number="formData.preco_mensal"
            type="number"
            step="0.01"
            inputmode="decimal"
            :label="$t('admin.planos.monthlyPrice')"
            placeholder="0.00"
          />
          <BaseInput
            v-model.number="formData.preco_anual"
            type="number"
            step="0.01"
            inputmode="decimal"
            :label="$t('admin.planos.yearlyPrice')"
            placeholder="0.00"
          />
          <BaseInput
            v-model.number="formData.dias_trial"
            type="number"
            inputmode="numeric"
            :label="$t('admin.planos.trialDays')"
            placeholder="0"
          />
        </div>

        <BaseInput
          v-model="recursosTextData"
          :label="$t('admin.planos.resources')"
          :placeholder="$t('admin.planos.resourcesPlaceholder')"
          :hint="$t('admin.planos.resourcesHint')"
        />

        <BaseTextarea
          v-model="limitesTextData"
          class="plano-manager__code"
          :label="$t('admin.planos.limits')"
          :hint="$t('admin.planos.limitsHint')"
          :placeholder="limitesPlaceholder"
          :rows="4"
          spellcheck="false"
        />
      </form>
      <template #footer>
        <BaseButton variant="secondary" @click="handleCloseModal">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" form="plano-manager-form" :loading="isSaving">
          {{ isSaving ? $t('common.salvando') : $t('common.salvar') }}
        </BaseButton>
      </template>
    </BaseModal>

    <ConfirmModal
      v-model="confirmModalVisible"
      :title="$t('admin.planos.confirmDelete')"
      :message="$t('admin.planos.deleteMessage')"
      :warning="$t('confirmModal.irreversible')"
      :item-name="planoToDelete?.nome"
      :confirm-text="$t('common.excluir')"
      type="danger"
      :loading="deleting"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePlanoManager } from './PlanoManager'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseInput,
  BaseModal,
  BaseSelect,
  BaseTextarea,
  ConfirmModal,
  EmptyState,
  type IconName,
  type SelectOption,
} from '@/shared/components/ui'

const { t } = useI18n()

const {
  filteredPlanos,
  filterStatus,
  rolesData,
  isModalOpen,
  editingPlano,
  isSaving,
  formData,
  recursosTextData,
  limitesTextData,
  confirmModalVisible,
  planoToDelete,
  deleting,
  togglingStatus,
  openCreateModal,
  handleEditPlano,
  handleSavePlano,
  handleToggleStatus,
  confirmDelete,
  handleConfirmDelete,
  handleCloseModal,
  formatRecurso,
  formatLimiteKey,
  formatCurrency,
} = usePlanoManager()

const limitesPlaceholder = '{"flashcards": 100, "quizes": 50, "prompts": 10}'

const filters = computed<{ value: 'all' | 'active' | 'inactive'; label: string; icon?: IconName }[]>(() => [
  { value: 'all', label: t('admin.planos.all') },
  { value: 'active', label: t('admin.planos.active'), icon: 'check-circle' },
  { value: 'inactive', label: t('admin.planos.inactive'), icon: 'x-circle' },
])

// "Nenhuma" = '' no <select>; o formulário continua recebendo null / id numérico
const roleOptions = computed<SelectOption[]>(() => [
  { value: '', label: t('admin.planos.noRole') },
  ...rolesData.value.map((role) => ({ value: role.id, label: role.nome })),
])

const roleIdModel = computed<string | number>({
  get: () => formData.value.role_id ?? '',
  set: (value) => {
    formData.value.role_id = value === '' || value === null ? null : Number(value)
  },
})
</script>

<style scoped>
@import '@/styles/components/PlanoManager.css';
</style>
