<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900">Beready</h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-gray-700">Olá, {{ user?.nome || user?.name || 'Usuário' }}</span>
            <button
              @click="handleLogout"
              :disabled="loading"
              class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ loading ? 'Saindo...' : 'Sair' }}
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
          <p class="text-gray-600">Bem-vindo ao Beready! Seu aprendizado de inglês começa aqui.</p>

          <!-- Cards -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
              @click="router.push('/flashcards')"
              class="bg-blue-50 p-4 rounded-lg hover:shadow-md transition-shadow cursor-pointer"
            >
              <h3 class="font-semibold text-blue-900">Flashcards</h3>
              <p class="text-blue-700">Estude com flashcards</p>
            </div>
            <div
              @click="router.push('/quizes')"
              class="bg-green-50 p-4 rounded-lg hover:shadow-md transition-shadow cursor-pointer"
            >
              <h3 class="font-semibold text-green-900">Quizes</h3>
              <p class="text-green-700">Teste seus conhecimentos</p>
            </div>
            <div
              @click="router.push('/prompts')"
              class="bg-purple-50 p-4 rounded-lg hover:shadow-md transition-shadow cursor-pointer"
            >
              <h3 class="font-semibold text-purple-900">Prompts</h3>
              <p class="text-purple-700">Gerador de prompts com IA</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const user = ref<any>(null)

onMounted(() => {
  // Recuperar usuário do localStorage
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      user.value = JSON.parse(userData)
    } catch (e) {
      console.error('Erro ao parsear user:', e)
    }
  }

  // Verificar se está autenticado
  if (!user.value) {
    router.push('/login')
  }
})

const handleLogout = async () => {
  loading.value = true

  try {
    await fetch('http://localhost:8765/auth/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    })
  } catch (err) {
    console.error('Erro no logout:', err)
  } finally {
    localStorage.removeItem('user')
    localStorage.removeItem('auth_token')
    router.push('/login')
    loading.value = false
  }
}
</script>
