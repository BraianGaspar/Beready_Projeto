import { createRouter, createWebHistory } from 'vue-router'
import { usePermissionStore } from '@/stores/permissionStore'

const Home = () => import('../views/_global/HomePage.vue')
const DashboardPage = () => import('../views/_global/DashboardPage.vue')

const UserLogin = () => import('../modules/auth/views/UserLogin.vue')
const UserRegister = () => import('../modules/auth/views/UserRegister.vue')
const UserProfile = () => import('../modules/auth/views/UserProfile.vue')
const ProfileEdit = () => import('../modules/auth/views/ProfileEdit.vue')
const ForgotPassword = () => import('../modules/auth/views/ForgotPassword.vue')
const ResetPassword = () => import('../modules/auth/views/ResetPassword.vue')
const OAuthCallback = () => import('../modules/auth/views/OAuthCallback.vue')

const FlashcardList = () => import('../modules/flashcards/views/FlashcardList.vue')
const FlashcardView = () => import('../modules/flashcards/views/FlashcardView.vue')
const FlashcardStudy = () => import('../modules/flashcards/views/FlashcardStudy.vue')

const QuizList = () => import('../modules/quizes/views/QuizList.vue')
const QuizView = () => import('../modules/quizes/views/QuizView.vue')
const QuizPlay = () => import('../modules/quizes/views/QuizPlay.vue')
const QuizAdd = () => import('../modules/quizes/views/QuizAdd.vue')
const QuizEdit = () => import('../modules/quizes/views/QuizEdit.vue')

const PromptList = () => import('../modules/prompts/views/PromptList.vue')
const PromptDetail = () => import('../modules/prompts/views/PromptDetail.vue')
const TagList = () => import('../modules/tags/views/TagList.vue')

// CORRIGIDO: Caminho correto para ProgressDashboard
const ProgressDashboard = () => import('../modules/progresso/views/ProgressoDashboard.vue')

const UserPreferences = () => import('../modules/preferencias/views/UserPreferences.vue')
const TraducoesPrompt = () => import('../modules/traducoes/views/TraducoesPrompt.vue')
const ImagensPrompt = () => import('../modules/imagens/views/ImagensPrompt.vue')
const FrasesPrompt = () => import('../modules/frases/views/FrasesPrompt.vue')

// Admin Panel
const AdminPanel = () => import('../modules/admin/views/AdminPanel.vue')

// CORRIGIDO: Caminho correto para PlanosPage
const PlansPage = () => import('../views/PlanosPage.vue')

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: Home, meta: { requiresAuth: false } },
    { path: '/login', name: 'login', component: UserLogin, meta: { requiresAuth: false } },
    { path: '/register', name: 'register', component: UserRegister, meta: { requiresAuth: false } },
    { path: '/dashboard', name: 'dashboard', component: DashboardPage, meta: { requiresAuth: true } },
    { path: '/profile', name: 'profile', component: UserProfile, meta: { requiresAuth: true } },
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
    {
      path: '/oauth-callback',
      name: 'oauth-callback',
      component: OAuthCallback,
      meta: { requiresAuth: false },
    },
    {
      path: '/flashcards',
      name: 'flashcards',
      component: FlashcardList,
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
    { path: '/quizes', name: 'quizes', component: QuizList, meta: { requiresAuth: true } },
    { path: '/quizes/add', name: 'quiz-add', component: QuizAdd, meta: { requiresAuth: true } },
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
    { path: '/quizes/:id', name: 'quiz-view', component: QuizView, meta: { requiresAuth: true } },
    { path: '/tags', name: 'tags', component: TagList, meta: { requiresAuth: true } },
    { path: '/prompts', name: 'prompts', component: PromptList, meta: { requiresAuth: true } },
    {
      path: '/prompts/:id',
      name: 'prompt-detail',
      component: PromptDetail,
      meta: { requiresAuth: true },
    },
    {
      path: '/progresso',
      name: 'progresso',
      component: ProgressDashboard,
      meta: { requiresAuth: true },
    },
    {
      path: '/preferencias',
      name: 'preferencias',
      component: UserPreferences,
      meta: { requiresAuth: true },
    },
    {
      path: '/prompts/:promptId/traducoes',
      name: 'traducoes-prompt',
      component: TraducoesPrompt,
      meta: { requiresAuth: true },
    },
    {
      path: '/prompts/:promptId/imagens',
      name: 'imagens-prompt',
      component: ImagensPrompt,
      meta: { requiresAuth: true },
    },
    {
      path: '/prompts/:promptId/frases',
      name: 'frases-prompt',
      component: FrasesPrompt,
      meta: { requiresAuth: true },
    },
    // Planos Page (Publica)
    {
      path: '/planos',
      name: 'planos',
      component: PlansPage,
      meta: { requiresAuth: false },
    },
    // Rota Admin com permissao
    {
      path: '/admin',
      name: 'admin',
      component: AdminPanel,
      meta: { 
        requiresAuth: true, 
        requiresAdmin: true,
        permission: 'admin.access' 
      },
    },
  ],
})

// Guarda de rota atualizada com verificacao de admin e permissao
router.beforeEach(async (to, from) => {
  const token = localStorage.getItem('access_token')
  const userData = localStorage.getItem('user')

  let isAuthenticated = false
  let isAdmin = false

  if (token && userData) {
    try {
      const user = JSON.parse(userData)
      isAuthenticated = true
      isAdmin = user.role === 'admin'
    } catch (e) {
      isAuthenticated = false
    }
  }

  // Verifica se rota requer admin
  if (to.meta.requiresAdmin && !isAdmin) {
    return '/dashboard'
  }

  // Verifica se rota requer permissao especifica
  if (to.meta.permission) {
    const permissionStore = usePermissionStore()
    
    // Carregar permissoes se nao estiverem carregadas
    if (permissionStore.userPermissions.length === 0) {
      await permissionStore.loadPermissions()
    }
    
    // Verifica se a permissao existe
    const requiredPermission = to.meta.permission as string
    if (!permissionStore.hasPermission(requiredPermission)) {
      return { path: '/dashboard', query: { error: 'no_permission' } }
    }
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login'
  }

  if ((to.path === '/login' || to.path === '/register' || to.path === '/') && isAuthenticated) {
    return '/dashboard'
  }

  return true
})

export default router