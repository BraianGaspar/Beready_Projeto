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
        Voltar
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
        <h1 class="hero-title">Preferências</h1>
        <p class="hero-subtitle">Personalize sua experiência no aplicativo</p>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Carregando preferências...</p>
    </div>

    <form v-else @submit.prevent="save" class="preferencias-form">
      <div class="form-group">
        <label class="form-label">Tema</label>
        <select v-model="form.tema" class="form-select">
          <option value="claro">Claro</option>
          <option value="escuro">Escuro</option>
        </select>
      </div>

      <div class="form-checkbox">
        <label class="checkbox-label">
          <input type="checkbox" v-model="form.modo_daltonico" />
          <span>Modo Daltônico</span>
        </label>
      </div>

      <div class="form-checkbox">
        <label class="checkbox-label">
          <input type="checkbox" v-model="form.notificacoes_ativas" />
          <span>Notificações Ativas</span>
        </label>
      </div>

      <div class="form-checkbox">
        <label class="checkbox-label">
          <input type="checkbox" v-model="form.som_ativo" />
          <span>Som Ativo</span>
        </label>
      </div>

      <div class="form-checkbox">
        <label class="checkbox-label">
          <input type="checkbox" v-model="form.traducao_automatica" />
          <span>Tradução Automática</span>
        </label>
      </div>

      <div class="form-group">
        <label class="form-label">Preferência de Dificuldade</label>
        <select v-model="form.preferencia_dificuldade" class="form-select">
          <option value="iniciante">Iniciante</option>
          <option value="intermediario">Intermediário</option>
          <option value="avancado">Avançado</option>
          <option value="adaptativo">Adaptativo</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Meta Diária (minutos)</label>
        <input
          type="number"
          v-model.number="form.meta_diaria_minutos"
          min="0"
          max="240"
          class="form-input"
        />
      </div>

      <button type="submit" :disabled="saving" class="btn-save">
        {{ saving ? 'Salvando...' : 'Salvar Preferências' }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { usePreferencias } from './Preferencias'
const { form, loading, saving, save } = usePreferencias()
</script>

<style scoped>
@import '@/styles/views/preferencias/preferencias.css';
</style>