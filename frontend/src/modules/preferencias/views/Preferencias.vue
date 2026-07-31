<template>
  <div class="preferencias-page">
    <div class="preferencias-hero">
      <button class="hero-back-btn" @click="$router.push('/dashboard')">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
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
      <div class="hero-content">
        <div class="hero-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
        </div>
        <h1 class="hero-title">{{ $t('preferencias.title') }}</h1>
        <p class="hero-subtitle">{{ $t('preferencias.subtitle') }}</p>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>{{ $t('preferencias.carregando') }}</p>
    </div>

    <form v-else @submit.prevent="handleSave" class="preferencias-form">
      <!-- Idioma -->
      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.idioma') }}</label>
        <select v-model="selectedLocale" class="form-select" @change="changeLocale">
          <option value="pt">{{ $t('idiomas.pt') }}</option>
          <option value="en">{{ $t('idiomas.en') }}</option>
          <option value="es">{{ $t('idiomas.es') }}</option>
          <option value="fr">{{ $t('idiomas.fr') }}</option>
          <option value="de">{{ $t('idiomas.de') }}</option>
          <option value="it">{{ $t('idiomas.it') }}</option>
          <option value="ja">{{ $t('idiomas.ja') }}</option>
          <option value="ko">{{ $t('idiomas.ko') }}</option>
          <option value="ru">{{ $t('idiomas.ru') }}</option>
          <option value="nl">{{ $t('idiomas.nl') }}</option>
          <option value="sv">{{ $t('idiomas.sv') }}</option>
          <option value="pl">{{ $t('idiomas.pl') }}</option>
          <option value="tr">{{ $t('idiomas.tr') }}</option>
          <option value="ar">{{ $t('idiomas.ar') }}</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.tema') }}</label>
        <div class="theme-buttons">
          <button
            type="button"
            class="theme-btn"
            :class="{ active: form.tema === 'claro' }"
            @click="form.tema = 'claro'"
          >
             {{ $t('preferencias.claro') }}
          </button>
          <button
            type="button"
            class="theme-btn"
            :class="{ active: form.tema === 'escuro' }"
            @click="form.tema = 'escuro'"
          >
             {{ $t('preferencias.escuro') }}
          </button>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.modoDaltonico') }}</label>
        <label class="toggle-switch">
          <input type="checkbox" v-model="form.modo_daltonico" />
          <span class="toggle-slider"></span>
        </label>
        <span class="helper-text">{{ $t('preferencias.modoDaltonicoHelper') }}</span>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.notificacoes') }}</label>
        <label class="toggle-switch">
          <input type="checkbox" v-model="form.notificacoes_ativas" />
          <span class="toggle-slider"></span>
        </label>
        <span class="helper-text">{{ $t('preferencias.notificacoesHelper') }}</span>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.som') }}</label>
        <label class="toggle-switch">
          <input type="checkbox" v-model="form.som_ativo" />
          <span class="toggle-slider"></span>
        </label>
        <span class="helper-text">{{ $t('preferencias.somHelper') }}</span>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.traducaoAutomatica') }}</label>
        <label class="toggle-switch">
          <input type="checkbox" v-model="form.traducao_automatica" />
          <span class="toggle-slider"></span>
        </label>
        <span class="helper-text">{{ $t('preferencias.traducaoAutomaticaHelper') }}</span>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.dificuldadePreferida') }}</label>
        <div class="difficulty-buttons">
          <button
            type="button"
            class="difficulty-btn"
            :class="{ active: form.preferencia_dificuldade === 'iniciante' }"
            @click="form.preferencia_dificuldade = 'iniciante'"
          >
             {{ $t('preferencias.iniciante') }}
          </button>
          <button
            type="button"
            class="difficulty-btn"
            :class="{ active: form.preferencia_dificuldade === 'intermediario' }"
            @click="form.preferencia_dificuldade = 'intermediario'"
          >
             {{ $t('preferencias.intermediario') }}
          </button>
          <button
            type="button"
            class="difficulty-btn"
            :class="{ active: form.preferencia_dificuldade === 'avancado' }"
            @click="form.preferencia_dificuldade = 'avancado'"
          >
             {{ $t('preferencias.avancado') }}
          </button>
          <button
            type="button"
            class="difficulty-btn"
            :class="{ active: form.preferencia_dificuldade === 'adaptativo' }"
            @click="form.preferencia_dificuldade = 'adaptativo'"
          >
             {{ $t('preferencias.adaptativo') }}
          </button>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">{{ $t('preferencias.metaDiaria') }}</label>
        <div class="meta-input">
          <input
            type="range"
            v-model.number="form.meta_diaria_minutos"
            min="5"
            max="120"
            step="5"
            class="meta-range"
          />
          <div class="meta-value">
            <span class="meta-number">{{ form.meta_diaria_minutos }}</span>
            <span class="meta-unit">{{ $t('preferencias.metaDiariaHelper') }}</span>
          </div>
        </div>
        <div class="meta-labels">
          <span>5 min</span>
          <span>30 min</span>
          <span>60 min</span>
          <span>90 min</span>
          <span>120 min</span>
        </div>
      </div>

      <button type="submit" :disabled="saving" class="btn-save">
        {{ saving ? $t('preferencias.salvando') : $t('preferencias.salvar') }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePreferencias } from '../composables/usePreferencias'

const { locale } = useI18n()
const selectedLocale = ref(locale.value)

const changeLocale = () => {
  locale.value = selectedLocale.value
  localStorage.setItem('app_locale', selectedLocale.value)
}

const { form, loading, saving, fetchPreferencias, savePreferencias } = usePreferencias()

const handleSave = async () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    const user = JSON.parse(userData)
    await savePreferencias(user.id)
  }
}

onMounted(async () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      const user = JSON.parse(userData)
      if (user && user.id) {
        await fetchPreferencias(user.id)
      } else {
        console.error('Usuário inválido')
      }
    } catch (e) {
      console.error('Erro ao fazer parse do user:', e)
    }
  }
})
</script>

<style scoped>
@import '@/styles/views/preferencias/preferencias.css';
</style>
