<template>
  <AuthCard :title="$t('register.title')" :subtitle="$t('register.subtitle')" icon="users" size="lg">
    <form class="user-register__form" @submit.prevent="handleSubmit">
      <div class="user-register__grid">
        <!-- Seção 1: Informações Pessoais -->
        <fieldset class="user-register__section">
          <legend class="user-register__section-title">
            <BaseIcon name="user" />
            {{ $t('register.personalInfo') }}
          </legend>

          <BaseInput
            v-model="form.nome"
            :label="$t('register.nome')"
            autocomplete="name"
            :placeholder="$t('register.nomePlaceholder')"
            :error="errors.nome"
            required
          />
          <BaseInput
            v-model="form.email"
            type="email"
            :label="$t('login.email')"
            autocomplete="email"
            :placeholder="$t('register.emailPlaceholder')"
            :error="errors.email"
            required
          />
          <BaseInput
            v-model="form.telefone"
            type="tel"
            :label="$t('profile.telefone')"
            autocomplete="tel"
            inputmode="numeric"
            :placeholder="$t('register.telefonePlaceholder')"
            :error="phoneError"
            @input="handlePhoneInput"
            @keydown="handlePhoneKeydown"
          />
        </fieldset>

        <!-- Seção 2: Segurança -->
        <fieldset class="user-register__section">
          <legend class="user-register__section-title">
            <BaseIcon name="lock-closed" />
            {{ $t('register.security') }}
          </legend>

          <div class="user-register__field">
            <BaseInput
              v-model="form.senha"
              type="password"
              :label="$t('register.senha')"
              autocomplete="new-password"
              :placeholder="$t('register.senhaPlaceholder')"
              :error="errors.senha"
              required
            />
            <PasswordStrength
              v-if="form.senha"
              :level="strengthClass"
              :text="strengthText"
              :width="strengthWidth"
            />
          </div>

          <div class="user-register__field">
            <BaseInput
              v-model="form.confirmar_senha"
              type="password"
              :label="$t('register.confirmarSenha')"
              autocomplete="new-password"
              :placeholder="$t('register.confirmarSenhaPlaceholder')"
              :error="errors.confirmar_senha"
              required
            />
            <p
              v-if="form.confirmar_senha"
              class="user-register__match"
              :class="passwordsMatch ? 'user-register__match--ok' : 'user-register__match--error'"
              aria-live="polite"
            >
              <BaseIcon :name="passwordsMatch ? 'check-circle' : 'x-circle'" />
              <span>{{
                passwordsMatch ? $t('register.passwordsMatch') : $t('register.passwordsDoNotMatch')
              }}</span>
            </p>
          </div>
        </fieldset>

        <!-- Seção 3: Preferências -->
        <fieldset class="user-register__section">
          <legend class="user-register__section-title">
            <BaseIcon name="book-open" />
            {{ $t('register.learningPreferences') }}
          </legend>

          <BaseSelect
            v-model="form.nivel_ingles"
            :label="$t('profile.nivelIngles')"
            :options="nivelOptions"
          />
          <BaseSelect
            v-model="form.idioma_preferido"
            :label="$t('profile.idiomaPreferido')"
            :options="idiomaOptions"
          />
          <BaseTextarea
            v-model="form.objetivos_aprendizado"
            :label="$t('profile.objetivos')"
            :rows="3"
            :placeholder="$t('register.objetivosPlaceholder')"
          />
        </fieldset>
      </div>

      <div class="user-register__actions">
        <BaseButton variant="secondary" to="/login">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" :loading="loading">
          {{ loading ? $t('common.salvando') : $t('register.createAccount') }}
        </BaseButton>
      </div>
    </form>

    <template #footer>
      {{ $t('register.jaTemConta') }}
      <router-link to="/login">{{ $t('register.loginLink') }}</router-link>
    </template>
  </AuthCard>
</template>

<script setup lang="ts">
defineOptions({
  name: 'UserRegister',
})

import { BaseButton, BaseIcon, BaseInput, BaseSelect, BaseTextarea } from '@/shared/components/ui'
import AuthCard from '../components/AuthCard.vue'
import PasswordStrength from '../components/PasswordStrength.vue'
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
  handleSubmit,
} = useRegister()
</script>

<style scoped>
@import '@/styles/views/users/register.css';
</style>
