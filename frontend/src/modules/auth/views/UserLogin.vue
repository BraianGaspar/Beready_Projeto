<template>
  <div class="login-page">
    <Card class="login-card">
      <template #header>
        <div class="login-header">
          <div class="header-icon">
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
                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
              />
            </svg>
          </div>
          <h1 class="login-title">{{ $t('common.entrar') }}</h1>
          <p class="login-subtitle">{{ $t('login.subtitle') }}</p>
        </div>
      </template>

      <form @submit.prevent="handleSubmit">
        <Input
          v-model="form.email"
          label="E-mail"
          type="email"
          placeholder="seu.email@exemplo.com"
          required
          :error="errors.email"
        />
        <Input
          v-model="form.password"
          label="Senha"
          type="password"
          placeholder="Sua senha"
          required
          :error="errors.password"
        />
        <div class="login-forgot-link">
          <router-link to="/forgot-password">{{ $t('login.forgotPassword') }}</router-link>
        </div>
        <Button type="submit" :loading="loading" block>{{ $t('common.entrar') }}</Button>
      </form>

      <!-- Divider -->
      <div class="divider">
        <span>ou</span>
      </div>

      <!-- Botões Sociais -->
      <div class="social-buttons">
        <!-- Google -->
        <button
          type="button"
          class="btn-social btn-google"
          @click="loginWithProvider('google')"
          :disabled="loading"
        >
          <img
            src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
            alt="Google"
            class="social-icon"
          />
          {{ $t('login.loginWithGoogle') }}
        </button>

        <!-- Facebook -->
        <button
          type="button"
          class="btn-social btn-facebook"
          @click="loginWithProvider('facebook')"
          :disabled="loading"
        >
          <svg viewBox="0 0 24 24" class="social-icon" fill="#1877F2">
            <path
              d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
            />
          </svg>
          {{ $t('login.loginWithFacebook') }}
        </button>

        <!-- LinkedIn -->
        <button
          type="button"
          class="btn-social btn-linkedin"
          @click="loginWithProvider('linkedin')"
          :disabled="loading"
        >
          <svg viewBox="0 0 24 24" class="social-icon" fill="#0A66C2">
            <path
              d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
            />
          </svg>
          {{ $t('login.loginWithLinkedIn') }}
        </button>
      </div>

      <template #footer>
        <div class="login-register-link">
          {{ $t('login.noAccount') }}
          <router-link to="/register">{{ $t('register.title') }}</router-link>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { useLogin } from './Login'
import Card from '@/shared/components/common/AppCard.vue'
import Input from '@/shared/components/common/AppInput.vue'
import Button from '@/shared/components/common/AppButton.vue'

const { form, errors, loading, handleSubmit, loginWithProvider } = useLogin()
</script>

<style scoped>
@import '@/styles/views/users/login.css';
</style>
