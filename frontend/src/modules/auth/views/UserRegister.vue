<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card">
        <div class="register-header">
          <div class="header-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
              />
            </svg>
          </div>
          <h1 class="register-title">{{ $t('register.title') }}</h1>
          <p class="register-subtitle">{{ $t('register.subtitle') }}</p>
        </div>

        <div class="register-body">
          <form @submit.prevent="handleSubmit">
            <div class="register-form-grid">
              <!-- Seção 1: Informações Pessoais -->
              <div class="register-section">
                <h2 class="register-section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  {{ $t('register.personalInfo') }}
                </h2>

                <div class="form-group">
                  <label class="form-label">{{ $t('register.nome') }}</label>
                  <input
                    v-model="form.nome"
                    type="text"
                    class="form-input"
                    :placeholder="$t('register.nomePlaceholder')"
                    required
                  />
                  <span v-if="errors.nome" class="input-error">{{ errors.nome }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('login.email') }}</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-input"
                    :placeholder="$t('register.emailPlaceholder')"
                    required
                  />
                  <span v-if="errors.email" class="input-error">{{ errors.email }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('profile.telefone') }}</label>
                  <input
                    v-model="form.telefone"
                    type="tel"
                    class="form-input"
                    :placeholder="$t('register.telefonePlaceholder')"
                    @input="handlePhoneInput"
                    @keydown="handlePhoneKeydown"
                  />
                  <span v-if="phoneError" class="input-error">{{ phoneError }}</span>
                </div>
              </div>

              <!-- Seção 2: Segurança -->
              <div class="register-section">
                <h2 class="register-section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  {{ $t('register.security') }}
                </h2>

                <div class="form-group">
                  <label class="form-label">{{ $t('register.senha') }}</label>
                  <div class="password-wrapper">
                    <input
                      v-model="form.senha"
                      type="password"
                      class="form-input password-input"
                      :placeholder="$t('register.senhaPlaceholder')"
                      required
                    />
                  </div>
                  <span v-if="errors.senha" class="input-error">{{ errors.senha }}</span>
                  <div v-if="form.senha" class="register-password-strength">
                    <div class="register-strength-bar">
                      <div
                        class="register-strength-fill"
                        :class="strengthClass"
                        :style="{ width: strengthWidth }"
                      ></div>
                    </div>
                    <span class="register-strength-text">{{ strengthText }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('register.confirmarSenha') }}</label>
                  <div class="password-wrapper">
                    <input
                      v-model="form.confirmar_senha"
                      type="password"
                      class="form-input password-input"
                      :placeholder="$t('register.confirmarSenhaPlaceholder')"
                      required
                    />
                  </div>
                  <span v-if="errors.confirmar_senha" class="input-error">{{ errors.confirmar_senha }}</span>
                  <div
                    v-if="form.confirmar_senha"
                    class="register-password-match"
                    :class="{ matching: passwordsMatch, 'not-matching': !passwordsMatch }"
                  >
                    <span>{{
                      passwordsMatch
                        ? $t('register.passwordsMatch')
                        : $t('register.passwordsDoNotMatch')
                    }}</span>
                  </div>
                </div>
              </div>

              <!-- Seção 3: Preferências -->
              <div class="register-section">
                <h2 class="register-section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  {{ $t('register.learningPreferences') }}
                </h2>

                <div class="form-group">
                  <label class="form-label">{{ $t('profile.nivelIngles') }}</label>
                  <select v-model="form.nivel_ingles" class="form-input">
                    <option v-for="opt in nivelOptions" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('profile.idiomaPreferido') }}</label>
                  <select v-model="form.idioma_preferido" class="form-input">
                    <option v-for="opt in idiomaOptions" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label class="form-label">{{ $t('profile.objetivos') }}</label>
                  <textarea
                    v-model="form.objetivos_aprendizado"
                    class="form-input"
                    rows="3"
                    :placeholder="$t('register.objetivosPlaceholder')"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="register-form-actions">
              <button type="button" class="btn-cancel" @click="$router.push('/login')">
                {{ $t('common.cancelar') }}
              </button>
              <button type="submit" class="btn-submit" :disabled="loading">
                {{ loading ? $t('common.salvando') : $t('register.createAccount') }}
              </button>
            </div>
          </form>

          <div class="register-login-redirect">
            <p>
              {{ $t('register.jaTemConta') }}
              <router-link to="/login">{{ $t('register.loginLink') }}</router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineOptions({
  name: 'UserRegister'
})

import { useRegister } from './Register'

const {
  form,
  errors,
  loading,
  strengthClass,
  strengthText,
  strengthWidth,
  phoneError,
  passwordsMatch,
  nivelOptions,
  idiomaOptions,
  handlePhoneInput,
  handlePhoneKeydown,
  handleSubmit
} = useRegister()
</script>

<style scoped>
@import '@/styles/views/users/register.css';
</style>