import { createRouter, createWebHistory } from 'vue-router'

// Views
import Home from '../views/Home.vue'
import Dashboard from '../views/Dashboard.vue'

// Users Views
import Login from '../views/Users/Login.vue'
import Register from '../views/Users/Register.vue'
import Profile from '../views/Users/Profile.vue'
import ProfileEdit from '../views/Users/ProfileEdit.vue'
import ForgotPassword from '../views/Users/ForgotPassword.vue'
import ResetPassword from '../views/Users/ResetPassword.vue'

// Flashcards Views
import Flashcards from '../views/flashcards/Flashcards.vue'
import FlashcardView from '../views/flashcards/FlashcardView.vue'
import FlashcardStudy from '../views/flashcards/FlashcardStudy.vue'

// Quizes Views
import Quizes from '../views/Quizes/Quizes.vue'
import QuizView from '../views/Quizes/QuizView.vue'
import QuizPlay from '../views/Quizes/QuizPlay.vue'
import QuizAdd from '../views/Quizes/QuizAdd.vue'
import QuizEdit from '../views/Quizes/QuizEdit.vue'

// Prompts Views
import Prompts from '../views/Prompts/Prompts.vue'

// ========== NOVAS VIEWS ==========
// Tags Views
import Tags from '../views/Tags/Tags.vue'

// Progresso Views
import ProgressoDashboard from '../views/Progresso/ProgressoDashboard.vue'

// Preferencias Views
import Preferencias from '../views/Preferencias/Preferencias.vue'

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
    // Prompts Routes
    {
      path: '/prompts',
      name: 'prompts',
      component: Prompts,
      meta: { requiresAuth: true },
    },
    // ========== NOVAS ROTAS ==========
    // Tags Routes
    {
      path: '/tags',
      name: 'tags',
      component: Tags,
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
