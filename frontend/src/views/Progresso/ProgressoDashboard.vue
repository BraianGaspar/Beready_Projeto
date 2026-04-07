<template>
  <div class="progresso-container">
    <h1 class="progresso-title">Meu Progresso</h1>

    <div v-if="loading" class="loading-state">Carregando...</div>
    <div v-else class="progresso-grid">
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.vocabulario_aprendido || 0 }}</div>
        <div class="progresso-label">Vocabulário Aprendido</div>
      </div>
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.flashcards_concluidos || 0 }}</div>
        <div class="progresso-label">Flashcards Concluídos</div>
      </div>
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.quizes_concluidos || 0 }}</div>
        <div class="progresso-label">Quizes Concluídos</div>
      </div>
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.tempo_total_estudo || 0 }} min</div>
        <div class="progresso-label">Tempo Total de Estudo</div>
      </div>
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.sequencia_atual || 0 }}</div>
        <div class="progresso-label">Sequência Atual (dias)</div>
      </div>
      <div class="progresso-card">
        <div class="progresso-value">{{ progresso?.maior_sequencia || 0 }}</div>
        <div class="progresso-label">Maior Sequência</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useProgresso } from './ProgressoDashboard'
</script>

<style scoped>
@import '@/styles/views/progresso/progresso.css';
</style>
