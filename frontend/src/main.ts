import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import i18n from './locales'
import { usePermissionStore } from './stores/permissionStore'

// Importar CSS de temas
import './styles/themes.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)

// INICIALIZAR PERMISSÕES ANTES DE MONTAR
const initApp = async () => {
  try {
    const permissionStore = usePermissionStore()
    await permissionStore.loadPermissions()
    console.log('Permissões carregadas com sucesso!')
  } catch (error) {
    console.error('Erro ao carregar permissões:', error)
  }
  
  app.mount('#app')
}

initApp()