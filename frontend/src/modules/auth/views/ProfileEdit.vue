<template>
  <PageContainer size="md">
    <PageHeader
      :title="`${$t('common.editar')} ${$t('common.perfil')}`"
      :subtitle="$t('profile.editProfileSubtitle')"
      icon="pencil"
      back-to="/profile"
    />

    <form class="edit-profile" @submit.prevent="handleSubmit">
      <div class="edit-profile__grid">
        <BaseCard as="section" :title="$t('profile.personalInfo')">
          <div class="edit-profile__fields">
            <div class="edit-profile__photo">
              <span class="edit-profile__avatar">
                <img
                  v-if="imagePreview || form.foto_perfil"
                  :src="imagePreview || form.foto_perfil"
                  :alt="$t('profile.fotoPerfil')"
                  class="edit-profile__avatar-image"
                />
                <BaseIcon v-else name="user" />
              </span>
              <BaseField :field-id="photoId" :label="$t('profile.fotoPerfil')" class="edit-profile__photo-field">
                <input
                  :id="photoId"
                  type="file"
                  accept="image/*"
                  class="edit-profile__file"
                  @change="handleImageChange"
                />
              </BaseField>
            </div>

            <BaseInput
              v-model="form.nome"
              :label="$t('register.nome')"
              autocomplete="name"
              :placeholder="$t('profile.nomePlaceholder')"
              required
            />
            <BaseInput
              v-model="form.email"
              type="email"
              :label="$t('login.email')"
              autocomplete="email"
              :placeholder="$t('profile.emailPlaceholder')"
              required
            />
            <BaseInput
              v-model="form.telefone"
              type="tel"
              :label="$t('profile.telefone')"
              autocomplete="tel"
              inputmode="numeric"
              :placeholder="$t('profile.telefonePlaceholder')"
              :error="phoneError"
              @input="handlePhoneInput"
              @keydown="handlePhoneKeydown"
            />
          </div>
        </BaseCard>

        <BaseCard as="section" :title="$t('profile.learningPreferences')">
          <div class="edit-profile__fields">
            <BaseSelect
              v-model="form.nivel_ingles"
              :label="$t('profile.nivelIngles')"
              :placeholder="$t('profile.selecioneNivel')"
              :options="nivelOptions"
            />
            <BaseSelect
              v-model="form.idioma_preferido"
              :label="$t('profile.idiomaPreferido')"
              :placeholder="$t('profile.selecioneIdioma')"
              :options="idiomaOptions"
            />
            <BaseTextarea
              v-model="form.objetivos_aprendizado"
              :label="$t('profile.objetivos')"
              :rows="3"
              :placeholder="$t('profile.objetivosPlaceholder')"
            />
          </div>
        </BaseCard>
      </div>

      <BaseCard as="section" :title="$t('profile.alterarSenha')" :subtitle="$t('profile.senhaHint')">
        <div class="edit-profile__password">
          <div class="edit-profile__fields">
            <BaseInput
              v-model="form.nova_senha"
              type="password"
              :label="$t('profile.novaSenha')"
              autocomplete="new-password"
              :placeholder="$t('profile.novaSenhaPlaceholder')"
              @input="handlePasswordInput"
            />
            <PasswordStrength
              v-if="form.nova_senha"
              :level="strengthClass"
              :text="strengthText"
              :width="strengthWidth"
            />
          </div>
          <BaseInput
            v-model="form.confirmar_senha"
            type="password"
            :label="$t('profile.confirmarNovaSenha')"
            autocomplete="new-password"
            :placeholder="$t('profile.confirmarSenhaPlaceholder')"
            :error="!passwordsMatch && form.confirmar_senha ? $t('errors.passwordMatch') : ''"
            @input="handleConfirmPasswordInput"
          />
        </div>
      </BaseCard>

      <div class="edit-profile__actions">
        <BaseButton variant="secondary" to="/profile">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton type="submit" icon="check" :loading="loading">
          {{ loading ? $t('common.carregando') : $t('common.salvar') + ' ' + $t('profile.alteracoes') }}
        </BaseButton>
      </div>
    </form>
  </PageContainer>
</template>

<script setup lang="ts">
import { computed, useId } from 'vue'
import { useI18n } from 'vue-i18n'
import {
  BaseButton,
  BaseCard,
  BaseField,
  BaseIcon,
  BaseInput,
  BaseSelect,
  BaseTextarea,
  PageContainer,
  PageHeader,
  type SelectOption,
} from '@/shared/components/ui'
import PasswordStrength from '../components/PasswordStrength.vue'
import { useProfileEdit } from './ProfileEdit'

const { t } = useI18n()
const photoId = `profile-photo-${useId()}`

const nivelOptions = computed<SelectOption[]>(() => [
  { value: 'iniciante', label: t('profile.nivelIniciante') },
  { value: 'intermediario', label: t('profile.nivelIntermediario') },
  { value: 'avancado', label: t('profile.nivelAvancado') },
])

const idiomaOptions = computed<SelectOption[]>(() => [
  { value: 'pt-BR', label: t('idiomas.pt') },
  { value: 'en', label: t('idiomas.en') },
  { value: 'es', label: t('idiomas.es') },
  { value: 'fr', label: t('idiomas.fr') },
])

const {
  form,
  loading,
  strengthClass,
  strengthText,
  strengthWidth,
  phoneError,
  passwordsMatch,
  imagePreview,
  handlePhoneInput,
  handlePhoneKeydown,
  handlePasswordInput,
  handleConfirmPasswordInput,
  handleImageChange,
  handleSubmit,
} = useProfileEdit()
</script>

<style scoped>
@import '@/styles/views/users/profile-edit.css';
</style>
