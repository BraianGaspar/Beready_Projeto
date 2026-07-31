<!-- frontend/src/views/PlanosPage.vue -->

<template>
  <div class="planos-page">
    <div class="planos-header">
      <button class="back-btn" @click="$router.push('/dashboard')">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
        {{ $t('common.voltar') }}
      </button>
      <h1>{{ $t('planos.title') }}</h1>
      <p>{{ $t('planos.subtitle') }}</p>
    </div>

    <div class="planos-container">
      <div
        v-for="plano in planosData"
        :key="plano.id"
        class="plano-card"
        :class="{ 
          premium: plano.preco_mensal > 0,
          'plano-gratuito': plano.preco_mensal === 0
        }"
      >
        <div v-if="plano.preco_mensal > 0" class="plano-badge">
          {{ $t('planos.mostPopular') }}
        </div>

        <div class="plano-card-header">
          <h3>{{ plano.nome }}</h3>
          <p class="plano-descricao">{{ plano.descricao }}</p>
        </div>

        <div class="plano-preco">
          <span class="preco">R$ {{ plano.preco_mensal.toFixed(2) }}</span>
          <span class="periodo">{{ $t('planos.perMonth') }}</span>
        </div>

        <div v-if="plano.preco_anual > 0" class="plano-preco-anual">
          <span>{{ $t('planos.orYearly', { price: plano.preco_anual.toFixed(2) }) }}</span>
          <span class="economia">({{ $t('planos.save', { percent: calcularEconomia(plano) }) }})</span>
        </div>

        <div class="plano-recursos">
          <div v-for="recurso in plano.recursos" :key="recurso" class="recurso-item">
            <svg class="check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>
            <span>{{ formatRecurso(recurso) }}</span>
          </div>
        </div>

        <div class="plano-limites">
          <div v-for="(limite, key) in plano.limites" :key="key" class="limite-item">
            <span class="limite-label">{{ formatLimiteKey(key) }}</span>
            <span class="limite-valor">{{ limite === 999999 ? '∞' : limite }}</span>
          </div>
        </div>

        <div v-if="plano.dias_trial > 0" class="plano-trial">
          {{ $t('planos.trialDays', { days: plano.dias_trial }) }}
        </div>

        <button
          class="btn-assinar"
          :class="{ premium: plano.preco_mensal > 0 }"
          @click="handleAssinarPlano(plano)"
          :disabled="isLoading"
        >
          {{ isLoading ? $t('common.salvando') : plano.preco_mensal === 0 ? $t('planos.startFree') : $t('planos.subscribeNow') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { usePlanosPage } from './PlanosPage'

const {
  planosData,
  isLoading,
  calcularEconomia,
  formatRecurso,
  formatLimiteKey,
  handleAssinarPlano
} = usePlanosPage()
</script>

<style scoped>
@import '@/styles/views/PlanosPage.css';
</style>