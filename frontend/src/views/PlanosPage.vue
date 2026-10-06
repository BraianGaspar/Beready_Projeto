<!-- frontend/src/views/PlanosPage.vue -->

<template>
  <PageContainer as="main" class="planos">
    <PageHeader
      :title="$t('planos.title')"
      :subtitle="$t('planos.subtitle')"
      icon="star"
      back-to="/dashboard"
    />

    <BaseSpinner v-if="isLoadingPlanos" center size="lg" />
    <EmptyState v-else-if="planosData.length === 0" :title="$t('planos.noPlans')" icon="star" />

    <ul v-else class="planos__grid">
      <li v-for="plano in planosData" :key="plano.id" class="planos__item">
        <BaseCard
          as="article"
          class="planos__card"
          :highlight="isPlanoAtual(plano) ? 'success' : plano.preco_mensal > 0 ? 'primary' : undefined"
        >
          <div class="planos__card-body">
            <div class="planos__badges">
              <BaseBadge v-if="isPlanoAtual(plano)" variant="success" solid icon>
                {{ $t('planos.currentPlan') }}
              </BaseBadge>
              <BaseBadge v-else-if="plano.preco_mensal > 0" variant="primary" solid icon="star">
                {{ $t('planos.mostPopular') }}
              </BaseBadge>
            </div>

            <header class="planos__card-header">
              <h2 class="planos__name">{{ plano.nome }}</h2>
              <p class="planos__description">{{ plano.descricao }}</p>
            </header>

            <p class="planos__price">
              <span class="planos__price-value">{{ formatCurrency(plano.preco_mensal) }}</span>
              <span class="planos__price-period">{{ $t('planos.perMonth') }}</span>
            </p>

            <p v-if="plano.preco_anual > 0" class="planos__yearly">
              <span>{{ $t('planos.orYearly', { price: formatCurrency(plano.preco_anual) }) }}</span>
              <BaseBadge variant="success" size="sm" icon="trending-down">
                {{ $t('planos.save', { percent: calcularEconomia(plano) }) }}
              </BaseBadge>
            </p>

            <ul class="planos__features">
              <li v-for="recurso in plano.recursos" :key="recurso" class="planos__feature">
                <BaseIcon name="check" class="planos__feature-icon" />
                <span>{{ formatRecurso(recurso) }}</span>
              </li>
            </ul>

            <dl class="planos__limits">
              <div v-for="(limite, key) in plano.limites" :key="key" class="planos__limit">
                <dt class="planos__limit-label">{{ formatLimiteKey(key) }}</dt>
                <dd class="planos__limit-value">{{ limite === 999999 ? '∞' : limite }}</dd>
              </div>
            </dl>

            <BaseBadge v-if="plano.dias_trial > 0" variant="warning" icon="clock" class="planos__trial">
              {{ $t('planos.trialDays', { days: plano.dias_trial }) }}
            </BaseBadge>

            <div class="planos__actions">
              <BaseButton v-if="isPlanoAtual(plano)" variant="secondary" icon="check" block disabled>
                {{ $t('planos.currentPlan') }}
              </BaseButton>
              <BaseButton
                v-else
                :variant="plano.preco_mensal > 0 ? 'primary' : 'secondary'"
                block
                :loading="isLoading"
                @click="handleAssinarPlano(plano)"
              >
                {{
                  isLoading
                    ? $t('common.salvando')
                    : plano.preco_mensal === 0
                      ? $t('planos.startFree')
                      : $t('planos.subscribeNow')
                }}
              </BaseButton>

              <BaseButton
                v-if="isPlanoAtual(plano) && podeCancelar"
                variant="ghost-danger"
                size="sm"
                icon="x-circle"
                block
                :disabled="isLoading"
                @click="showCancelModal = true"
              >
                {{ $t('planos.cancelSubscription') }}
              </BaseButton>
            </div>
          </div>
        </BaseCard>
      </li>
    </ul>

    <ConfirmModal
      v-model="showCancelModal"
      type="warning"
      :title="$t('planos.cancelSubscription')"
      :message="$t('planos.cancelConfirmMessage')"
      :confirm-text="$t('planos.cancelSubscription')"
      :loading="isLoading"
      @confirm="handleCancelarAssinatura"
    />
  </PageContainer>
</template>

<script setup lang="ts">
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseSpinner,
  ConfirmModal,
  EmptyState,
  PageContainer,
  PageHeader,
} from '@/shared/components/ui'
import { usePlanosPage } from './PlanosPage'

const {
  planosData,
  isLoading,
  isLoadingPlanos,
  showCancelModal,
  podeCancelar,
  isPlanoAtual,
  calcularEconomia,
  formatRecurso,
  formatLimiteKey,
  formatCurrency,
  handleAssinarPlano,
  handleCancelarAssinatura,
} = usePlanosPage()
</script>

<style scoped>
@import '@/styles/views/PlanosPage.css';
</style>
