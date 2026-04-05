<template>
  <div class="forgot-password-page">
    <Card class="forgot-password-card">
      <template #header>
        <div class="forgot-password-header">
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
          <h1 class="forgot-password-title">Recuperar Senha</h1>
          <p class="forgot-password-subtitle">
            Digite seu e-mail para receber o link de recuperação
          </p>
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
        <div class="forgot-password-input-help">
          <svg
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
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          Enviaremos um link seguro para redefinir sua senha
        </div>
        <Button type="submit" :loading="loading" block>Enviar Link de Recuperação</Button>
      </form>

      <template #footer>
        <div class="forgot-password-back-link">
          <svg
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
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          <router-link to="/login">Voltar para Login</router-link>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { useAlert } from '@/composables/useAlert'
import Card from '@/components/common/Card.vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'

const router = useRouter()
const { success, error } = useAlert()
const loading = ref(false)

const { form, errors, validate } = useForm({ email: '' })

const rules = {
  email: (value: string) => (!value ? 'E-mail é obrigatório' : null),
}

const handleSubmit = async () => {
  if (!validate(rules)) return

  loading.value = true

  try {
    const response = await fetch('http://localhost:8765/auth/forgot-password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: form.email }),
    })
    const data = await response.json()

    if (data.success) {
      success('Link de recuperação enviado para seu e-mail!')
      setTimeout(() => router.push('/login'), 2000)
    } else {
      error(data.message || 'Erro ao enviar link')
    }
  } catch (err) {
    error('Erro de conexão com o servidor')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import '@/styles/views/users/forgot-password.css';
</style>
