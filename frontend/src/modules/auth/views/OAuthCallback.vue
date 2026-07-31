<template>
  <div class="oauth-callback-page">
    <div class="oauth-callback-container">
      <div v-if="loading" class="loading-spinner">
        <div class="spinner"></div>
        <p class="loading-text">{{ $t('oauth.loading') }}</p>
      </div>

      <div v-if="error" class="error-message">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p>{{ error }}</p>
        <button @click="goToLogin" class="btn-retry">{{ $t('common.voltar') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useOAuthCallback } from './OAuthCallback'

const { error, loading, goToLogin } = useOAuthCallback()
</script>

<style scoped>
@import '@/styles/views/auth/oauth-callback.css';
</style>