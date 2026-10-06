import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import i18n from './locales'
import { useAuthStore } from './stores/auth'

// Design system: tokens + temas (claro/escuro/daltônico) e depois a base global
import './styles/themes.css'
import './styles/main.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)

// Reidrata a sessão (POST /auth/refresh com o cookie httpOnly) antes de montar.
// init() é idempotente: o guard do router aguarda a mesma promise.
const initApp = async () => {
  await useAuthStore().init()
  app.mount('#app')
}

initApp()
