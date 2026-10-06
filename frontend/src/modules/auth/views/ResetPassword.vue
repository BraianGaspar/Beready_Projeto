<template>
  <AuthCard
    :title="$t('resetPassword.title')"
    :subtitle="$t('resetPassword.subtitle')"
    icon="key"
  >
    <form class="reset-password__form" @submit.prevent="handleSubmit">
      <BaseInput
        v-model="form.senha"
        type="password"
        :label="$t('resetPassword.newPassword')"
        autocomplete="new-password"
        :placeholder="$t('resetPassword.newPasswordPlaceholder')"
        :hint="$t('passwordValidation.minLength')"
        required
      />
      <BaseInput
        v-model="form.confirmar_senha"
        type="password"
        :label="$t('resetPassword.confirmPassword')"
        autocomplete="new-password"
        :placeholder="$t('resetPassword.confirmPasswordPlaceholder')"
        :error="
          form.confirmar_senha && form.senha !== form.confirmar_senha
            ? $t('passwordValidation.doNotMatch')
            : ''
        "
        required
      />

      <div class="reset-password__actions">
        <BaseButton variant="secondary" to="/login">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton
          type="submit"
          :loading="loading"
          :disabled="form.senha.length < 6 || form.senha !== form.confirmar_senha"
        >
          {{ loading ? $t('common.salvando') : $t('resetPassword.submitButton') }}
        </BaseButton>
      </div>
    </form>

    <template #footer>
      {{ $t('resetPassword.rememberPassword') }}
      <router-link to="/login">{{ $t('resetPassword.loginLink') }}</router-link>
    </template>
  </AuthCard>
</template>

<script setup lang="ts">
import { BaseButton, BaseInput } from '@/shared/components/ui'
import AuthCard from '../components/AuthCard.vue'
import { useResetPassword } from './useResetPassword'

const { form, loading, handleSubmit } = useResetPassword()
</script>

<style scoped>
@import '@/styles/views/users/reset-password.css';
</style>
