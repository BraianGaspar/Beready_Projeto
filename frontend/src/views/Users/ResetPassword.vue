<template>
  <div class="reset-password-page">
    <Card class="reset-password-card">
      <template #header>
        <div class="reset-password-header">
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
                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
              />
            </svg>
          </div>
          <h1 class="reset-password-title">Redefinir Senha</h1>
          <p class="reset-password-subtitle">Crie uma nova senha para sua conta</p>
        </div>
      </template>

      <form @submit.prevent="handleSubmit">
        <div class="reset-password-section">
          <h3 class="reset-password-section-title">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              />
            </svg>
            Nova Senha
          </h3>

          <div class="form-group">
            <label class="form-label">Nova Senha *</label>
            <div class="input-container password-container">
              <input
                v-model="form.senha"
                :type="showPassword ? 'text' : 'password'"
                class="form-input password-input"
                placeholder="Digite sua nova senha"
                required
                @input="checkPasswordStrength"
              />
              <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                <svg
                  v-if="!showPassword"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
            <div class="reset-password-strength">
              <div class="reset-password-strength-bar">
                <div
                  class="reset-password-strength-fill"
                  :class="strengthClass"
                  :style="{ width: strengthWidth }"
                ></div>
              </div>
              <div class="reset-password-strength-text">{{ strengthText }}</div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Confirmar Nova Senha *</label>
            <div class="input-container password-container">
              <input
                v-model="form.confirmar_senha"
                :type="showConfirmPassword ? 'text' : 'password'"
                class="form-input password-input"
                placeholder="Digite a nova senha novamente"
                required
                @input="checkPasswordMatch"
              />
              <button
                type="button"
                class="toggle-password"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <svg
                  v-if="!showConfirmPassword"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
            <div
              v-if="form.confirmar_senha"
              class="reset-password-match"
              :class="{ matching: passwordsMatch, 'not-matching': !passwordsMatch }"
            >
              <svg
                v-if="passwordsMatch"
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span>{{ passwordsMatch ? 'As senhas coincidem' : 'As senhas não coincidem' }}</span>
            </div>
          </div>
        </div>

        <div class="reset-password-form-actions">
          <Button variant="secondary" type="button" @click="$router.push('/login')"
            >Cancelar</Button
          >
          <Button type="submit" :disabled="loading || !passwordsMatch || form.senha.length < 6"
            >Redefinir Senha</Button
          >
        </div>
      </form>

      <template #footer>
        <div class="reset-password-login-redirect">
          <p>Lembrou sua senha? <router-link to="/login">Fazer Login</router-link></p>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usePasswordStrength } from '@/composables/usePasswordStrength'
import { useAlert } from '@/composables/useAlert'
import Card from '@/components/common/Card.vue'
import Button from '@/components/common/Button.vue'

const router = useRouter()
const route = useRoute()
const { success, error } = useAlert()
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } = usePasswordStrength()

const form = ref({
  senha: '',
  confirmar_senha: '',
  token: '',
})

const passwordsMatch = computed(() => form.value.senha === form.value.confirmar_senha)

const checkPasswordMatch = () => {}

const handleSubmit = async () => {
  if (!passwordsMatch.value) {
    error('As senhas não coincidem')
    return
  }
  if (form.value.senha.length < 6) {
    error('A senha deve ter pelo menos 6 caracteres')
    return
  }

  loading.value = true

  try {
    const response = await fetch(`http://localhost:8765/auth/reset-password/${form.value.token}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ senha: form.value.senha }),
    })
    const data = await response.json()

    if (data.success) {
      success('Senha redefinida com sucesso! Redirecionando...')
      setTimeout(() => router.push('/login'), 2000)
    } else {
      error(data.message || 'Erro ao redefinir senha')
    }
  } catch (err) {
    error('Erro de conexão com o servidor')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  form.value.token = (route.params.token as string) || (route.query.token as string)
  if (!form.value.token) {
    error('Token inválido ou expirado')
  }
})
</script>

<style scoped>
@import '@/styles/views/users/reset-password.css';
</style>
