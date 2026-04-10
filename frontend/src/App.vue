<template>
  <div id="app">
    <router-view />
    <AlertContainer />
  </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import AlertContainer from '@/shared/components/common/AlertContainer.vue'

// Páginas públicas que NÃO devem ter tema escuro/daltônico
const publicRoutes = ['/', '/login', '/register', '/forgot-password', '/reset-password']

const loadUserPreferences = async () => {
  const userData = localStorage.getItem('user')
  const currentRoute = window.location.pathname

  // Verificar se é página pública
  const isPublicRoute = publicRoutes.some(
    (route) => currentRoute === route || currentRoute.startsWith('/reset-password'),
  )

  // Se for página pública, remove todos os temas
  if (isPublicRoute) {
    document.documentElement.classList.remove('dark-mode')
    document.body.classList.remove('dark-mode')
    document.documentElement.classList.remove('daltonico-mode')
    document.body.classList.remove('daltonico-mode')
    return
  }

  if (!userData) return

  const user = JSON.parse(userData)

  try {
    const response = await fetch(`http://localhost:8765/preferencias/usuario/${user.id}`)
    if (response.ok) {
      const data = await response.json()
      if (data.success && data.data) {
        // Aplicar tema escuro
        if (data.data.tema === 'escuro') {
          document.documentElement.classList.add('dark-mode')
          document.body.classList.add('dark-mode')
        } else {
          document.documentElement.classList.remove('dark-mode')
          document.body.classList.remove('dark-mode')
        }

        // Aplicar modo daltônico
        if (data.data.modo_daltonico) {
          document.documentElement.classList.add('daltonico-mode')
          document.body.classList.add('daltonico-mode')
        } else {
          document.documentElement.classList.remove('daltonico-mode')
          document.body.classList.remove('daltonico-mode')
        }
      }
    }
  } catch (err) {
    // Se não encontrar preferências, usa valores padrão (claro)
    document.documentElement.classList.remove('dark-mode')
    document.body.classList.remove('dark-mode')
    document.documentElement.classList.remove('daltonico-mode')
    document.body.classList.remove('daltonico-mode')
  }
}

// Monitorar mudanças de rota
const route = useRoute()
watch(
  () => route.path,
  () => {
    loadUserPreferences()
  },
  { immediate: true },
)

onMounted(() => {
  loadUserPreferences()
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body,
#app {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
  font-family:
    'Inter',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    'Helvetica Neue',
    Arial,
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

svg {
  width: 1em;
  height: 1em;
  flex-shrink: 0;
}

button svg {
  pointer-events: none;
}
</style>
