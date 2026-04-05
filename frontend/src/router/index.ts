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
  ],
})

router.beforeEach((to, from) => {
  const isAuthenticated = localStorage.getItem('user') !== null

  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login'
  }

  if ((to.path === '/login' || to.path === '/register' || to.path === '/') && isAuthenticated) {
    return '/dashboard'
  }

  return true
})

export default router
