import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import type { User } from '@/core/types/User'

export function useNavbarLogic() {
  const router = useRouter()
  const { success } = useAlert()
  const { t } = useI18n()

  // Estado do usuário
  const user = ref<User | null>(null)

  // Estado do menu
  const isMenuOpen = ref(false)
  const navMenu = ref<HTMLElement | null>(null)
  const menuWrapper = ref<HTMLElement | null>(null)
  const showLeftArrow = ref(false)
  const showRightArrow = ref(false)

  // Computed para informações do usuário
  const userName = computed(() => user.value?.nome || t('common.usuario'))
  const userEmail = computed(() => user.value?.email || '')
  const userInitial = computed(() => {
    const nome = user.value?.nome || ''
    return nome.charAt(0).toUpperCase() || 'U'
  })

  // Itens do menu
  const menuItems = [
    {
      name: 'common.dashboard',
      path: '/dashboard',
      iconPath:
        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    },
    {
      name: 'common.perfil',
      path: '/profile',
      iconPath: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    },
    {
      name: 'common.planos',
      path: '/planos',
      iconPath:
        'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    },
    {
      name: 'common.flashcards',
      path: '/flashcards',
      iconPath:
        'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
      name: 'common.quizes',
      path: '/quizes',
      iconPath:
        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    },
    {
      name: 'common.prompts',
      path: '/prompts',
      iconPath:
        'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
    },
    {
      name: 'common.tags',
      path: '/tags',
      iconPath:
        'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    },
    {
      name: 'common.progresso',
      path: '/progresso',
      iconPath:
        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    },
    {
      name: 'common.preferencias',
      path: '/preferencias',
      iconPath:
        'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    },
  ]

  // Verificar se o menu precisa de scroll
  const checkScroll = () => {
    if (!navMenu.value) return
    
    const el = navMenu.value
    const hasOverflow = el.scrollWidth > el.clientWidth
    
    if (hasOverflow) {
      showLeftArrow.value = el.scrollLeft > 5
      showRightArrow.value = el.scrollLeft < el.scrollWidth - el.clientWidth - 5
    } else {
      showLeftArrow.value = false
      showRightArrow.value = false
    }
  }

  // Rolar o menu
  const scrollMenu = (direction: 'left' | 'right') => {
    if (!navMenu.value) return
    
    const el = navMenu.value
    const scrollAmount = el.clientWidth * 0.6
    
    if (direction === 'left') {
      el.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
    } else {
      el.scrollBy({ left: scrollAmount, behavior: 'smooth' })
    }
  }

  // Observar mudanças no tamanho do menu
  let resizeObserver: ResizeObserver | null = null

  onMounted(() => {
    nextTick(() => {
      checkScroll()
      
      if (navMenu.value) {
        navMenu.value.addEventListener('scroll', checkScroll)
        
        resizeObserver = new ResizeObserver(() => {
          checkScroll()
        })
        resizeObserver.observe(navMenu.value)
      }
      
      window.addEventListener('resize', checkScroll)
    })
  })

  onUnmounted(() => {
    if (navMenu.value) {
      navMenu.value.removeEventListener('scroll', checkScroll)
    }
    if (resizeObserver) {
      resizeObserver.disconnect()
    }
    window.removeEventListener('resize', checkScroll)
  })

  // Watch para quando os itens do menu mudarem
  watch(menuItems, () => {
    nextTick(() => {
      checkScroll()
    })
  })

  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value
    document.body.style.overflow = isMenuOpen.value ? 'hidden' : ''
  }

  const closeMenu = () => {
    isMenuOpen.value = false
    document.body.style.overflow = ''
  }

  const handleLogout = () => {
    closeMenu()
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    success(t('success.logout'))
    setTimeout(() => {
      router.push('/login')
    }, 500)
  }

  // Função para definir o usuário
  const setUser = (newUser: User | null) => {
    user.value = newUser
  }

  return {
    user,
    userName,
    userEmail,
    userInitial,
    menuItems,
    isMenuOpen,
    navMenu,
    menuWrapper,
    showLeftArrow,
    showRightArrow,
    setUser,
    toggleMenu,
    closeMenu,
    scrollMenu,
    handleLogout,
  }
}