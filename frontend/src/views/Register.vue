<template>
  <div class="register-container">
    <div class="register-card">
      <div class="card-header">
        <div class="header-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8"
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
        <h1 class="card-title">Criar Conta</h1>
        <p class="card-subtitle">Junte-se à nossa comunidade de aprendizado</p>
      </div>

      <div class="card-body">
        <!-- Mensagens Flash -->
        <div v-if="success" class="flash-message success">
          {{ success }}
        </div>
        <div v-if="error" class="flash-message error">
          {{ error }}
        </div>

        <form @submit.prevent="handleRegister" class="register-form">
          <div class="form-grid">
            <!-- Informações Básicas -->
            <div class="form-section">
              <h3 class="section-title">
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
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                Informações Pessoais
              </h3>

              <div class="form-group">
                <label class="form-label">Nome Completo *</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                  <input
                    v-model="form.nome"
                    type="text"
                    required
                    class="form-input"
                    placeholder="Seu nome completo"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">E-mail *</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                  </svg>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="form-input"
                    placeholder="seu.email@exemplo.com"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Telefone</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                    />
                  </svg>
                  <input
                    v-model="form.telefone"
                    type="tel"
                    class="form-input"
                    placeholder="(99) 99999-9999"
                    maxlength="15"
                    @input="formatTelefone"
                  />
                </div>
              </div>
            </div>

            <!-- Segurança -->
            <div class="form-section">
              <h3 class="section-title">
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
                Segurança
              </h3>

              <div class="form-group">
                <label class="form-label">Senha *</label>
                <div class="input-container password-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
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
                  <input
                    v-model="form.senha"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    class="form-input password-input"
                    placeholder="Crie uma senha segura"
                    @input="checkPasswordStrength"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="toggle-password"
                  >
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
                <div class="password-strength">
                  <div class="strength-bar">
                    <div
                      class="strength-fill"
                      :class="strengthClass"
                      :style="{ width: strengthWidth }"
                    ></div>
                  </div>
                  <div class="strength-text">{{ strengthText }}</div>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Confirmar Senha *</label>
                <div class="input-container password-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
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
                  <input
                    v-model="confirmarSenha"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    required
                    class="form-input password-input"
                    placeholder="Digite a senha novamente"
                    @input="checkPasswordMatch"
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="toggle-password"
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
                  v-if="confirmarSenha"
                  class="password-match"
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
                  <span>{{
                    passwordsMatch ? 'As senhas coincidem' : 'As senhas não coincidem'
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Preferências de Aprendizado -->
            <div class="form-section">
              <h3 class="section-title">
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
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                  />
                </svg>
                Preferências de Aprendizado
              </h3>

              <div class="form-group">
                <label class="form-label">Nível de Inglês</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
                    />
                  </svg>
                  <select v-model="form.nivel_ingles" class="form-input select-input">
                    <option value="">Selecione seu nível</option>
                    <option value="iniciante">Iniciante</option>
                    <option value="intermediario">Intermediário</option>
                    <option value="avancado">Avançado</option>
                  </select>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="select-arrow"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Idioma Preferido</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <select v-model="form.idioma_preferido" class="form-input select-input">
                    <option value="">Selecione o idioma</option>
                    <option value="pt-BR">Português (Brasil)</option>
                    <option value="en">Inglês</option>
                    <option value="es">Espanhol</option>
                  </select>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="select-arrow"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Objetivos de Aprendizado</label>
                <div class="input-container">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="input-icon"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                  </svg>
                  <textarea
                    v-model="form.objetivos_aprendizado"
                    rows="3"
                    class="form-input textarea-input"
                    placeholder="Descreva seus objetivos..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="$router.push('/login')" class="btn btn-secondary">
              Cancelar
            </button>
            <button type="submit" :disabled="loading" class="btn btn-primary submit-btn">
              {{ loading ? 'Cadastrando...' : 'Criar Minha Conta' }}
            </button>
          </div>
        </form>

        <div class="login-redirect">
          <p>
            Já tem uma conta?
            <a href="/login" class="login-link">Fazer Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../services/api'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const success = ref('')

const form = ref({
  nome: '',
  email: '',
  senha: '',
  telefone: '',
  nivel_ingles: '',
  idioma_preferido: '',
  objetivos_aprendizado: '',
})

const confirmarSenha = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const strengthClass = ref('')
const strengthText = ref('Força da senha')
const strengthWidth = ref('0%')

const passwordsMatch = computed(() => {
  return form.value.senha && confirmarSenha.value && form.value.senha === confirmarSenha.value
})

const formatTelefone = (e: Event) => {
  const target = e.target as HTMLInputElement
  let value = target.value.replace(/\D/g, '')

  if (value.length <= 11) {
    if (value.length <= 2) {
      value = value.replace(/^(\d{0,2})/, '($1')
    } else if (value.length <= 6) {
      value = value.replace(/^(\d{2})(\d{0,4})/, '($1) $2')
    } else if (value.length <= 10) {
      value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
    } else {
      value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
    }
    target.value = value
    form.value.telefone = value
  }
}

const checkPasswordStrength = () => {
  const password = form.value.senha
  let strength = 0

  if (password.length >= 8) strength++
  if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++
  if (password.match(/\d/)) strength++
  if (password.match(/[^a-zA-Z\d]/)) strength++

  switch (strength) {
    case 0:
      strengthText.value = 'Força da senha: Muito Fraca'
      strengthClass.value = ''
      strengthWidth.value = '0%'
      break
    case 1:
      strengthText.value = 'Força da senha: Fraca'
      strengthClass.value = 'weak'
      strengthWidth.value = '25%'
      break
    case 2:
      strengthText.value = 'Força da senha: Moderada'
      strengthClass.value = 'medium'
      strengthWidth.value = '50%'
      break
    case 3:
      strengthText.value = 'Força da senha: Forte'
      strengthClass.value = 'strong'
      strengthWidth.value = '75%'
      break
    case 4:
      strengthText.value = 'Força da senha: Muito Forte'
      strengthClass.value = 'very-strong'
      strengthWidth.value = '100%'
      break
  }
}

const checkPasswordMatch = () => {
  // Apenas para forçar reatividade
}

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  if (form.value.senha !== confirmarSenha.value) {
    error.value = 'As senhas não coincidem'
    loading.value = false
    return
  }

  try {
    const response = await auth.register(form.value)

    if (response.data.success) {
      success.value = 'Cadastro realizado com sucesso! Redirecionando...'
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else {
      error.value = response.data.message || 'Erro ao cadastrar'
    }
  } catch (err: any) {
    console.error('Erro na requisição:', err)
    if (err.response) {
      error.value = err.response.data?.message || 'Erro no servidor'
    } else if (err.request) {
      error.value = 'Servidor não respondeu. Verifique se o backend está rodando.'
    } else {
      error.value = err.message || 'Erro ao conectar com o servidor'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.register-container {
  @apply py-5 px-4 max-w-4xl mx-auto;
}

.register-card {
  @apply bg-white rounded-2xl shadow-xl overflow-hidden border border-white/20;
}

.card-header {
  @apply bg-gradient-to-r from-purple-600 to-purple-800 text-white py-10 px-8 text-center relative;
}

.card-header::before {
  content: '';
  @apply absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 to-purple-300;
}

.header-icon {
  @apply w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-5 backdrop-blur-sm border-2 border-white/30;
}

.card-title {
  @apply text-3xl font-bold mb-2 text-white;
}

.card-subtitle {
  @apply text-base opacity-90;
}

.card-body {
  @apply p-10;
}

.flash-message {
  @apply fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 px-6 py-4 rounded-xl text-center font-medium shadow-2xl max-w-md w-[95%] transition-opacity duration-500;
}

.flash-message.success {
  @apply bg-green-600 text-white;
}

.flash-message.error {
  @apply bg-red-600 text-white;
}

.form-grid {
  @apply grid gap-8;
}

.form-section {
  @apply bg-slate-50 p-6 rounded-xl border border-slate-200;
}

.section-title {
  @apply flex items-center gap-2 text-lg font-semibold text-gray-700 mb-5 pb-3 border-b-2 border-gray-200;
}

.section-title svg {
  @apply text-purple-600;
}

.form-group {
  @apply mb-5;
}

.form-label {
  @apply block text-sm font-semibold text-gray-700 mb-2;
}

.input-container {
  @apply relative;
}

.input-icon {
  @apply absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5;
}

.form-input {
  @apply w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl text-base transition-all duration-200 bg-white text-gray-700;
}

.form-input:focus {
  @apply border-purple-500 ring-2 ring-purple-500/10 outline-none -translate-y-px;
}

.password-input {
  @apply pr-12;
}

.select-input {
  @apply appearance-none cursor-pointer;
}

.select-arrow {
  @apply absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4 pointer-events-none;
}

.textarea-input {
  @apply resize-y min-h-[80px] py-3;
}

.password-strength {
  @apply mt-2;
}

.strength-bar {
  @apply h-1.5 bg-gray-200 rounded-full overflow-hidden mb-1.5;
}

.strength-fill {
  @apply h-full transition-all duration-300 rounded-full;
}

.strength-fill.weak {
  @apply bg-red-500;
}
.strength-fill.medium {
  @apply bg-yellow-500;
}
.strength-fill.strong {
  @apply bg-green-500;
}
.strength-fill.very-strong {
  @apply bg-emerald-600;
}

.strength-text {
  @apply text-xs text-gray-500 font-medium;
}

.toggle-password {
  @apply absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer bg-transparent border-none p-0 w-5 h-5 flex items-center justify-center;
}

.password-match {
  @apply flex items-center gap-2 mt-2 text-xs font-medium;
}

.password-match.matching {
  @apply text-emerald-600;
}

.password-match.not-matching {
  @apply text-red-600;
}

.form-actions {
  @apply flex gap-4 justify-end mt-8 pt-5 border-t border-gray-200;
}

.btn {
  @apply inline-flex items-center justify-center gap-2 px-7 py-3.5 border-none rounded-xl text-base font-semibold cursor-pointer transition-all duration-200 min-w-[140px];
}

.btn-primary {
  @apply bg-gradient-to-r from-purple-600 to-purple-800 text-white;
}

.btn-primary:hover {
  @apply transform -translate-y-px shadow-lg shadow-purple-500/30;
}

.btn-primary:disabled {
  @apply opacity-50 cursor-not-allowed transform-none;
}

.btn-secondary {
  @apply bg-white text-gray-700 border-2 border-gray-300;
}

.btn-secondary:hover {
  @apply border-gray-400 transform -translate-y-px;
}

.login-redirect {
  @apply text-center mt-8 pt-5 border-t border-gray-200 text-gray-500;
}

.login-link {
  @apply text-purple-600 font-semibold hover:underline;
}

/* Responsive */
@media (max-width: 768px) {
  .register-container {
    @apply py-2 px-2;
  }
  .card-body {
    @apply p-6;
  }
  .card-header {
    @apply py-8 px-6;
  }
  .header-icon {
    @apply w-16 h-16;
  }
  .form-grid {
    @apply gap-5;
  }
  .form-section {
    @apply p-5;
  }
  .form-actions {
    @apply flex-col;
  }
  .btn {
    @apply w-full;
  }
}
</style>
