<template>
  <AuthCard :title="error ? $t('oauth.processError') : $t('oauth.loading')" icon="login">
    <div class="oauth-callback__body">
      <BaseSpinner v-if="loading" size="lg" center :label="$t('oauth.loading')" />

      <template v-if="error">
        <BaseAlert variant="danger" :message="error" />
        <BaseButton icon="arrow-left" block @click="goToLogin">{{ $t('common.voltar') }}</BaseButton>
      </template>
    </div>
  </AuthCard>
</template>

<script setup lang="ts">
import { BaseAlert, BaseButton, BaseSpinner } from '@/shared/components/ui'
import AuthCard from '../components/AuthCard.vue'
import { useOAuthCallback } from './OAuthCallback'

const { error, loading, goToLogin } = useOAuthCallback()
</script>

<style scoped>
@import '@/styles/views/auth/oauth-callback.css';
</style>
