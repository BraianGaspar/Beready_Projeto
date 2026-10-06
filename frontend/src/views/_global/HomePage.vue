<template>
  <main class="home">
    <section class="home__hero">
      <div class="home__container home__hero-content">
        <h1 class="home__title">Beready</h1>
        <p class="home__subtitle">{{ $t('home.subtitle') }}</p>
        <div class="home__actions">
          <BaseButton variant="primary" size="lg" to="/register">
            {{ $t('home.comecar') }}
          </BaseButton>
          <BaseButton variant="secondary" size="lg" icon="login" to="/login">
            {{ $t('common.entrar') }}
          </BaseButton>
        </div>
      </div>
    </section>

    <section class="home__features" aria-labelledby="home-features-title">
      <div class="home__container">
        <h2 id="home-features-title" class="home__section-title">{{ $t('home.comoFunciona') }}</h2>
        <ul class="home__grid">
          <li v-for="feature in features" :key="feature.title">
            <BaseCard as="article" muted padding="lg" class="home__feature">
              <span class="home__feature-icon" aria-hidden="true">
                <BaseIcon :name="feature.icon" />
              </span>
              <h3 class="home__feature-title">{{ feature.title }}</h3>
              <p class="home__feature-text">{{ feature.description }}</p>
            </BaseCard>
          </li>
        </ul>
      </div>
    </section>

    <section class="home__cta">
      <div class="home__container home__cta-content">
        <h2 class="home__cta-title">{{ $t('home.ctaTitle') }}</h2>
        <p class="home__cta-text">{{ $t('home.ctaDesc') }}</p>
        <BaseButton variant="secondary" size="lg" to="/register">
          {{ $t('home.criarContaGratuita') }}
        </BaseButton>
      </div>
    </section>
  </main>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useHome } from './Home'
import { BaseButton, BaseCard, BaseIcon, type IconName } from '@/shared/components/ui'

useHome()

const { t } = useI18n()

const features = computed<{ icon: IconName; title: string; description: string }[]>(() => [
  { icon: 'light-bulb', title: t('home.flashcardsInteligentes'), description: t('home.flashcardsDesc') },
  { icon: 'document', title: t('home.quizzesPersonalizados'), description: t('home.quizzesDesc') },
  { icon: 'chat', title: t('prompts.title'), description: t('home.promptsDesc') },
])
</script>

<style scoped>
@import '@/styles/views/home.css';
</style>
