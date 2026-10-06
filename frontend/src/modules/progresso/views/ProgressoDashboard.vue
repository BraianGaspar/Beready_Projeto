<template>
  <PageContainer size="xl" class="progress-dash">
    <PageHeader
      :title="$t('progresso.title')"
      :subtitle="$t('progresso.subtitle')"
      icon="chart-bar"
      back-to="/dashboard"
    />

    <BaseSpinner v-if="loading" center size="lg" show-label :label="$t('progresso.carregando')" />

    <ul v-else class="progress-dash__grid u-grid-auto" role="list">
      <li v-for="stat in stats" :key="stat.label">
        <StatCard :label="stat.label" :value="stat.value" :icon="stat.icon" :variant="stat.variant" />
      </li>
    </ul>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  BaseSpinner,
  PageContainer,
  PageHeader,
  StatCard,
  type IconName,
  type StatusVariant,
} from '@/shared/components/ui'
import { useProgressoDashboard } from './ProgressoDashboard'

const { t } = useI18n()
const { progresso, loading, formatarTempo } = useProgressoDashboard()

interface ProgressStat {
  label: string
  value: string | number
  icon: IconName
  variant: 'primary' | 'neutral' | StatusVariant
}

// A cor do ícone é só decorativa: o rótulo de cada card diz o que o número representa
const stats = computed<ProgressStat[]>(() => [
  {
    label: t('progresso.vocabularioAprendido'),
    value: progresso.value.vocabulario_aprendido || 0,
    icon: 'book-open',
    variant: 'primary',
  },
  {
    label: t('progresso.flashcardsConcluidos'),
    value: progresso.value.flashcards_concluidos || 0,
    icon: 'check-circle',
    variant: 'success',
  },
  {
    label: t('progresso.quizesConcluidos'),
    value: progresso.value.quizes_concluidos || 0,
    icon: 'clipboard',
    variant: 'info',
  },
  {
    label: t('progresso.tempoTotalEstudo'),
    value: formatarTempo(progresso.value.tempo_total_estudo || 0),
    icon: 'clock',
    variant: 'neutral',
  },
  {
    label: t('progresso.sequenciaAtual'),
    value: progresso.value.sequencia_atual || 0,
    icon: 'lightning-bolt',
    variant: 'warning',
  },
  {
    label: t('progresso.maiorSequencia'),
    value: progresso.value.maior_sequencia || 0,
    icon: 'sparkles',
    variant: 'primary',
  },
])
</script>

<style scoped>
@import '@/styles/views/progresso/progresso.css';
</style>
