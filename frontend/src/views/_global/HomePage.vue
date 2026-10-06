<template>
  <main class="home min-h-screen bg-bg">
    <section class="home__hero bg-brand py-fluid-hero text-center text-hero-text">
      <div class="home__container home__hero-content mx-auto px-gutter flex w-full max-w-container-lg flex-col items-center gap-4">
        <h1 class="home__title text-display font-bold leading-tight text-hero-text">Beready</h1>
        <p class="home__subtitle max-w-container-sm text-fluid-lg-2xl text-hero-text-muted">{{ $t('home.subtitle') }}</p>
        <div class="home__actions mt-4 flex w-full flex-wrap justify-center gap-3 *:grow *:basis-full sm:*:shrink-0 sm:*:grow-0 sm:*:basis-auto">
          <BaseButton variant="primary" size="lg" to="/register">
            {{ $t('home.comecar') }}
          </BaseButton>
          <BaseButton variant="secondary" size="lg" icon="login" to="/login">
            {{ $t('common.entrar') }}
          </BaseButton>
        </div>
      </div>
    </section>

    <section class="home__features bg-surface py-fluid-section" aria-labelledby="home-features-title">
      <div class="home__container mx-auto w-full px-gutter max-w-container-lg">
        <h2 id="home-features-title" class="home__section-title mb-10 text-center text-fluid-2xl-4xl font-bold text-text">{{ $t('home.comoFunciona') }}</h2>
        <ul class="home__grid grid list-none grid-cols-fit-72 gap-6">
          <li v-for="feature in features" :key="feature.title">
            <BaseCard as="article" muted padding="lg" class="home__feature h-full text-center">
              <span class="home__feature-icon mb-4 inline-flex size-18 items-center justify-center rounded-full bg-brand text-3xl text-hero-text" aria-hidden="true">
                <BaseIcon :name="feature.icon" />
              </span>
              <h3 class="home__feature-title mb-2 text-xl font-semibold text-text">{{ feature.title }}</h3>
              <p class="home__feature-text leading-relaxed text-text-muted">{{ feature.description }}</p>
            </BaseCard>
          </li>
        </ul>
      </div>
    </section>

    <section class="home__cta bg-brand py-fluid-section text-center text-hero-text">
      <div class="home__container home__cta-content mx-auto px-gutter flex w-full max-w-container-lg flex-col items-center gap-4">
        <h2 class="home__cta-title text-fluid-2xl-4xl font-bold text-hero-text">{{ $t('home.ctaTitle') }}</h2>
        <p class="home__cta-text mb-4 max-w-container-sm text-lg text-hero-text-muted">{{ $t('home.ctaDesc') }}</p>
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
