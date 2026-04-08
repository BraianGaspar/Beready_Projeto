import { createRouter, createWebHistory } from 'vue-router'

// Views globais
import Home from '../views/_global/Home.vue'
import Dashboard from '../views/_global/Dashboard.vue'

// Auth Views (Users) - TODOS OS IMPORTS ATIVADOS
import Login from '../modules/auth/views/Login.vue'
import Register from '../modules/auth/views/Register.vue'
import Profile from '../modules/auth/views/Profile.vue'
import ProfileEdit from '../modules/auth/views/ProfileEdit.vue'
import ForgotPassword from '../modules/auth/views/ForgotPassword.vue'
import ResetPassword from '../modules/auth/views/ResetPassword.vue'

// Flashcards Views
import Flashcards from '../modules/flashcards/views/Flashcards.vue'
import FlashcardView from '../modules/flashcards/views/FlashcardView.vue'
import FlashcardStudy from '../modules/flashcards/views/FlashcardStudy.vue'

// Quizes Views
import Quizes from '../modules/quizes/views/Quizes.vue'
import QuizView from '../modules/quizes/views/QuizView.vue'
import QuizPlay from '../modules/quizes/views/QuizPlay.vue'
import QuizAdd from '../modules/quizes/views/QuizAdd.vue'
import QuizEdit from '../modules/quizes/views/QuizEdit.vue'

// Prompts Views
import Prompts from '../modules/prompts/views/Prompts.vue'
import PromptDetail from '../modules/prompts/views/PromptDetail.vue'

// Tags Views
import Tags from '../modules/tags/views/Tags.vue'

// Progresso Views
import ProgressoDashboard from '../modules/progresso/views/ProgressoDashboard.vue'

// Preferencias Views
import Preferencias from '../modules/preferencias/views/Preferencias.vue'

// Traducoes Views
import TraducoesPrompt from '../modules/traducoes/views/TraducoesPrompt.vue'

// Imagens Views
import ImagensPrompt from '../modules/imagens/views/ImagensPrompt.vue'

// Frases Views
import FrasesPrompt from '../modules/frases/views/FrasesPrompt.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      meta: { requiresAuth: false },
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { requiresAuth: false },
    },
    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: { requiresAuth: false },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: Profile,
      meta: { requiresAuth: true },
    },
    {
      path: '/profile/edit',
      name: 'profile-edit',
      component: ProfileEdit,
      meta: { requiresAuth: true },
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPassword,
      meta: { requiresAuth: false },
    },
    {
      path: '/reset-password/:token',
      name: 'reset-password',
      component: ResetPassword,
      meta: { requiresAuth: false },
    },
    // Flashcards Routes
    {
      path: '/flashcards',
      name: 'flashcards',
      component: Flashcards,
      meta: { requiresAuth: true },
    },
    {
      path: '/flashcards/:id',
      name: 'flashcard-view',
      component: FlashcardView,
      meta: { requiresAuth: true },
    },
    {
      path: '/flashcards/:id/study',
      name: 'flashcard-study',
      component: FlashcardStudy,
      meta: { requiresAuth: true },
    },
    // Quizes Routes
    {
      path: '/quizes',
      name: 'quizes',
      component: Quizes,
      meta: { requiresAuth: true },
    },
    {
      path: '/quizes/add',
      name: 'quiz-add',
      component: QuizAdd,
      meta: { requiresAuth: true },
    },
    {
      path: '/quizes/edit/:id',
      name: 'quiz-edit',
      component: QuizEdit,
      meta: { requiresAuth: true },
    },
    {
      path: '/quizes/:id/play',
      name: 'quiz-play',
      component: QuizPlay,
      meta: { requiresAuth: true },
    },
    {
      path: '/quizes/:id',
      name: 'quiz-view',
      component: QuizView,
      meta: { requiresAuth: true },
    },
    // Tags Routes
    {
      path: '/tags',
      name: 'tags',
      component: Tags,
      meta: { requiresAuth: true },
    },
    // Prompts Routes
    {
      path: '/prompts',
      name: 'prompts',
      component: Prompts,
      meta: { requiresAuth: true },
    },
    {
      path: '/prompts/:id',
      name: 'prompt-detail',
      component: PromptDetail,
      meta: { requiresAuth: true },
    },
    // Progresso Routes
    {
      path: '/progresso',
      name: 'progresso',
      component: ProgressoDashboard,
      meta: { requiresAuth: true },
    },
    // Preferencias Routes
    {
      path: '/preferencias',
      name: 'preferencias',
      component: Preferencias,
      meta: { requiresAuth: true },
    },
    // Traducoes Routes
    {
      path: '/prompts/:promptId/traducoes',
      name: 'traducoes-prompt',
      component: TraducoesPrompt,
      meta: { requiresAuth: true },
    },
    // Imagens Routes
    {
      path: '/prompts/:promptId/imagens',
      name: 'imagens-prompt',
      component: ImagensPrompt,
      meta: { requiresAuth: true },
    },
    // Frases Routes
    {
      path: '/prompts/:promptId/frases',
      name: 'frases-prompt',
      component: FrasesPrompt,
      meta: { requiresAuth: true },
    },
  ],
})

// Guarda de rotas
router.beforeEach((to, from) => {
  const user = localStorage.getItem('user')
  const isAuthenticated = user !== null

  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login'
  }

  if ((to.path === '/login' || to.path === '/register' || to.path === '/') && isAuthenticated) {
    return '/dashboard'
  }

  return true
})

export default router
