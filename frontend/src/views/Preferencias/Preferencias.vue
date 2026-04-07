<template>
  <div class="preferencias-container">
    <h1 class="preferencias-title">Preferências</h1>

    <div v-if="loading" class="loading-state">Carregando...</div>
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

      <button type="submit" :disabled="saving" class="btn-save">Salvar Preferências</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { usePreferencias } from './Preferencias'
</script>

<style scoped>
@import '@/styles/views/preferencias/preferencias.css';
</style>
