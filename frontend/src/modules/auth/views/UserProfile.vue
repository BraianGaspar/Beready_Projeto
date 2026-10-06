<template>
  <PageContainer>
    <PageHeader
      :title="user?.nome || $t('common.carregando')"
      :subtitle="user?.email || ''"
      icon="user"
      back-to="/dashboard"
    >
      <template #actions>
        <BaseButton variant="secondary" icon="pencil" to="/profile/edit">
          {{ $t('common.editar') }} {{ $t('common.perfil') }}
        </BaseButton>
        <BaseButton variant="danger" icon="trash" @click="showDeleteModal = true">
          {{ $t('profile.deleteAccount') }}
        </BaseButton>
      </template>
    </PageHeader>

    <div class="user-profile__grid">
      <BaseCard as="section" :title="$t('profile.personalInfo')">
        <div class="user-profile__identity">
          <span class="user-profile__avatar">
            <img
              v-if="user?.foto_perfil"
              :src="user.foto_perfil"
              :alt="$t('profile.fotoPerfil')"
              class="user-profile__avatar-image"
            />
            <BaseIcon v-else name="user" />
          </span>
          <dl class="user-profile__list">
            <div class="user-profile__item">
              <dt class="user-profile__label">{{ $t('register.nome') }}</dt>
              <dd class="user-profile__value">{{ user?.nome || '-' }}</dd>
            </div>
            <div class="user-profile__item">
              <dt class="user-profile__label">{{ $t('login.email') }}</dt>
              <dd class="user-profile__value">{{ user?.email || '-' }}</dd>
            </div>
            <div class="user-profile__item">
              <dt class="user-profile__label">{{ $t('profile.telefone') }}</dt>
              <dd class="user-profile__value">{{ formattedPhone || $t('profile.naoInformado') }}</dd>
            </div>
          </dl>
        </div>
      </BaseCard>

      <BaseCard as="section" :title="$t('profile.learningPreferences')">
        <dl class="user-profile__list">
          <div class="user-profile__item">
            <dt class="user-profile__label">{{ $t('profile.nivelIngles') }}</dt>
            <dd class="user-profile__value">{{ getNivelIngles(user?.nivel_ingles) }}</dd>
          </div>
          <div class="user-profile__item">
            <dt class="user-profile__label">{{ $t('profile.idiomaPreferido') }}</dt>
            <dd class="user-profile__value">{{ getIdiomaPreferido(user?.idioma_preferido) }}</dd>
          </div>
          <div class="user-profile__item">
            <dt class="user-profile__label">{{ $t('profile.status') }}</dt>
            <dd class="user-profile__value">
              <BaseBadge :variant="user?.status === 'ativo' ? 'success' : 'danger'" icon>
                {{ user?.status === 'ativo' ? $t('profile.ativo') : $t('profile.inativo') }}
              </BaseBadge>
            </dd>
          </div>
        </dl>
      </BaseCard>
    </div>

    <BaseCard as="section" :title="$t('profile.learningGoals')">
      <p class="user-profile__goals">
        {{ user?.objetivos_aprendizado || $t('profile.noGoals') }}
      </p>
    </BaseCard>

    <BaseModal v-model="showDeleteModal" :title="$t('profile.deleteAccount')" size="sm">
      <div class="user-profile__delete u-stack">
        <p class="user-profile__delete-text">{{ $t('profile.deleteConfirmMessage') }}</p>
        <BaseAlert variant="danger" :message="$t('profile.deleteWarning')" />
        <BaseInput
          v-model="confirmEmail"
          type="email"
          :label="`${$t('profile.deleteConfirmLabel')} ${user?.email || ''}:`"
          :placeholder="user?.email || ''"
          autocomplete="off"
        />
      </div>
      <template #footer="{ close }">
        <BaseButton variant="secondary" @click="close">{{ $t('common.cancelar') }}</BaseButton>
        <BaseButton
          variant="danger"
          icon="trash"
          :loading="deleteLoading"
          :disabled="confirmEmail !== user?.email"
          @click="handleDeleteAccount"
        >
          {{ deleteLoading ? $t('common.carregando') : $t('profile.confirmDelete') }}
        </BaseButton>
      </template>
    </BaseModal>
  </PageContainer>
</template>

<script setup lang="ts">
import {
  BaseAlert,
  BaseBadge,
  BaseButton,
  BaseCard,
  BaseIcon,
  BaseInput,
  BaseModal,
  PageContainer,
  PageHeader,
} from '@/shared/components/ui'
import { useProfile } from './useProfile'

const {
  user,
  formattedPhone,
  showDeleteModal,
  confirmEmail,
  deleteLoading,
  getNivelIngles,
  getIdiomaPreferido,
  handleDeleteAccount,
} = useProfile()
</script>

<style scoped>
@import '@/styles/views/users/profile.css';
</style>
