<template>
  <PageContainer size="xl">
    <PageHeader
      :title="$t('dashboard.welcome', { name: userName })"
      :subtitle="motivationalMessage"
      icon="home"
    />

    <!-- PAINEL ADMIN -->
    <BaseCard v-if="isAdmin" as="section" muted padding="sm" class="dashboard__admin">
      <div class="dashboard__admin-row">
        <BaseBadge variant="primary" icon="shield-check">{{ $t('admin.badge') }}</BaseBadge>
        <div class="dashboard__admin-links">
          <BaseButton variant="secondary" size="sm" icon="users" @click="goToAdmin('users')">
            {{ $t('admin.users') }}
          </BaseButton>
          <BaseButton variant="secondary" size="sm" icon="shield-check" @click="goToAdmin('roles')">
            {{ $t('admin.roles.title') }}
          </BaseButton>
          <BaseButton variant="secondary" size="sm" icon="star" @click="goToAdmin('planos')">
            {{ $t('admin.planos.title') }}
          </BaseButton>
          <BaseButton variant="secondary" size="sm" icon="chart-bar" @click="goToAdmin('stats')">
            {{ $t('admin.statistics') }}
          </BaseButton>
        </div>
      </div>
    </BaseCard>

    <!-- Estatísticas -->
    <div class="dashboard__stats u-grid-auto">
      <StatCard
        :label="$t('dashboard.flashcardsCount')"
        :value="stats.flashcardsCount || 0"
        icon="document"
        variant="info"
      />
      <StatCard
        :label="$t('dashboard.acertoRate')"
        :value="`${stats.acertoRate || 0}%`"
        icon="check-circle"
        variant="success"
      />
      <StatCard
        :label="$t('dashboard.sequencia')"
        :value="stats.sequenciaAtual || 0"
        icon="lightning-bolt"
        variant="primary"
      />
      <StatCard
        :label="$t('dashboard.tempoEstudo')"
        :value="stats.tempoEstudo || $t('time.minutes', { n: 0 })"
        icon="clock"
        variant="warning"
      />
    </div>

    <!-- Progresso geral -->
    <BaseCard as="section" :title="$t('dashboard.progressoGeral')">
      <BaseProgress
        :value="stats.progressoGeral || 0"
        :label="$t('dashboard.progressoGeral')"
        :value-text="$t('dashboard.percentComplete', { percent: stats.progressoGeral || 0 })"
        size="lg"
      />
      <p class="dashboard__progress-text">
        {{ $t('dashboard.percentComplete', { percent: stats.progressoGeral || 0 }) }} -
        {{ $t('dashboard.continueAssim') }}
      </p>
    </BaseCard>

    <!-- Funcionalidades -->
    <ul class="dashboard__features u-grid-auto">
      <li v-for="feature in features" :key="feature.to">
        <BaseCard as="article" interactive class="dashboard__feature">
          <div class="dashboard__feature-body">
            <span class="dashboard__feature-icon" aria-hidden="true">
              <BaseIcon :name="feature.icon" />
            </span>
            <div class="dashboard__feature-content">
              <h2 class="dashboard__feature-title">
                <router-link :to="feature.to" class="dashboard__feature-link">
                  {{ feature.title }}
                </router-link>
              </h2>
              <p class="dashboard__feature-description">{{ feature.description }}</p>
              <span class="dashboard__feature-cta" aria-hidden="true">
                {{ feature.cta }}
                <BaseIcon name="arrow-right" class="dashboard__feature-cta-icon" />
              </span>
            </div>
          </div>
        </BaseCard>
      </li>
    </ul>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import {
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseProgress,
  PageContainer,
  PageHeader,
  StatCard,
  type IconName,
} from '@/shared/components/ui'
import { useDashboard } from './DashboardPage'

const router = useRouter()
const { t } = useI18n()
const { userName, stats, motivationalMessage, isAdmin } = useDashboard()

// FUNÇÃO PARA NAVEGAR PARA ADMIN COM TAB ESPECÍFICA
const goToAdmin = (tab: string) => {
  router.push(`/admin?tab=${tab}`)
}

interface DashboardFeature {
  to: string
  icon: IconName
  title: string
  description: string
  cta: string
}

const features = computed<DashboardFeature[]>(() => [
  {
    to: '/flashcards',
    icon: 'document',
    title: t('common.flashcards'),
    description: t('dashboard.features.flashcards.desc'),
    cta: t('dashboard.features.flashcards.link'),
  },
  {
    to: '/quizes',
    icon: 'clipboard',
    title: t('common.quizes'),
    description: t('dashboard.features.quizes.desc'),
    cta: t('dashboard.features.quizes.link'),
  },
  {
    to: '/prompts',
    icon: 'chat',
    title: t('prompts.title'),
    description: t('dashboard.features.prompts.desc'),
    cta: t('dashboard.features.prompts.link'),
  },
  {
    to: '/tags',
    icon: 'tag',
    title: t('common.tags'),
    description: t('dashboard.features.tags.desc'),
    cta: t('dashboard.features.tags.link'),
  },
  {
    to: '/progresso',
    icon: 'chart-bar',
    title: t('common.progresso'),
    description: t('dashboard.features.progresso.desc'),
    cta: t('dashboard.features.progresso.link'),
  },
  {
    to: '/preferencias',
    icon: 'cog',
    title: t('common.preferencias'),
    description: t('dashboard.features.preferencias.desc'),
    cta: t('dashboard.features.preferencias.link'),
  },
])
</script>

<style scoped>
@import '@/styles/views/dashboard.css';
</style>
