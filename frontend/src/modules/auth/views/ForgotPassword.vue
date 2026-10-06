<template>
  <AuthCard
    :title="$t('forgotPassword.title')"
    :subtitle="$t('forgotPassword.subtitle')"
    icon="key"
  >
    <form class="forgot-password__form" @submit.prevent="handleSubmit">
      <BaseInput
        v-model="form.email"
        type="email"
        :label="$t('login.email')"
        autocomplete="email"
        :placeholder="$t('forgotPassword.emailPlaceholder')"
        :hint="$t('forgotPassword.helpText')"
        required
      />
      <BaseButton type="submit" :loading="loading" block>
        {{ loading ? $t('common.salvando') : $t('forgotPassword.submitButton') }}
      </BaseButton>
    </form>

    <template #footer>
      <BaseButton variant="ghost" icon="arrow-left" to="/login">
        {{ $t('forgotPassword.backToLogin') }}
      </BaseButton>
    </template>
  </AuthCard>
</template>

<script setup lang="ts">
import { BaseButton, BaseInput } from '@/shared/components/ui'
import AuthCard from '../components/AuthCard.vue'
import { useForgotPassword } from './useForgotPassword'

const { form, loading, handleSubmit } = useForgotPassword()
</script>

<style scoped>
@import '@/styles/views/users/forgot-password.css';
</style>
