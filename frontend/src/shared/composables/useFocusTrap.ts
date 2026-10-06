import { nextTick, onBeforeUnmount, watch, type Ref } from 'vue'

const FOCUSABLE = [
  'a[href]',
  'area[href]',
  'button:not([disabled])',
  'input:not([disabled]):not([type="hidden"])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'iframe',
  '[tabindex]:not([tabindex="-1"])',
  '[contenteditable="true"]',
].join(',')

const getFocusable = (container: HTMLElement): HTMLElement[] =>
  Array.from(container.querySelectorAll<HTMLElement>(FOCUSABLE)).filter(
    (el) => !el.hasAttribute('inert') && el.getClientRects().length > 0,
  )

// Bloqueio de rolagem do <body> com contador (vários overlays abertos ao mesmo tempo)
let scrollLocks = 0
const lockScroll = () => {
  if (scrollLocks++ === 0) document.body.style.overflow = 'hidden'
}
const unlockScroll = () => {
  scrollLocks = Math.max(0, scrollLocks - 1)
  if (scrollLocks === 0) document.body.style.overflow = ''
}

interface FocusTrapOptions {
  /** Chamado ao pressionar Esc enquanto ativo */
  onEscape?: () => void
  /** Bloqueia a rolagem do body enquanto ativo (padrão: true) */
  lockScroll?: boolean
  /** Elemento a focar ao abrir (padrão: primeiro focável, ou o container) */
  initialFocus?: () => HTMLElement | null | undefined
}

/**
 * Focus trap simples para modais e drawers:
 * - ao ativar, guarda o foco atual e foca o primeiro elemento focável;
 * - Tab / Shift+Tab ficam presos dentro do container; Esc chama onEscape;
 * - ao desativar, devolve o foco ao elemento que abriu.
 * O container precisa de tabindex="-1" para receber foco quando não há focáveis.
 */
export function useFocusTrap(
  container: Ref<HTMLElement | null>,
  active: Ref<boolean>,
  options: FocusTrapOptions = {},
) {
  let previouslyFocused: HTMLElement | null = null
  let locked = false

  const onKeydown = (event: KeyboardEvent) => {
    const el = container.value
    if (!el) return
    if (event.key === 'Escape' && options.onEscape) {
      event.stopPropagation()
      options.onEscape()
      return
    }
    if (event.key !== 'Tab') return
    const items = getFocusable(el)
    if (items.length === 0) {
      event.preventDefault()
      el.focus()
      return
    }
    const first = items[0] as HTMLElement
    const last = items[items.length - 1] as HTMLElement
    const current = document.activeElement
    if (event.shiftKey && (current === first || current === el)) {
      event.preventDefault()
      last.focus()
    } else if (!event.shiftKey && current === last) {
      event.preventDefault()
      first.focus()
    }
  }

  const activate = async () => {
    previouslyFocused = document.activeElement as HTMLElement | null
    if (options.lockScroll !== false && !locked) {
      lockScroll()
      locked = true
    }
    await nextTick()
    const el = container.value
    if (!el) return
    el.addEventListener('keydown', onKeydown)
    const target = options.initialFocus?.() || getFocusable(el)[0] || el
    target.focus()
  }

  const deactivate = () => {
    container.value?.removeEventListener('keydown', onKeydown)
    if (locked) {
      unlockScroll()
      locked = false
    }
    if (previouslyFocused && document.contains(previouslyFocused)) {
      previouslyFocused.focus()
    }
    previouslyFocused = null
  }

  watch(
    active,
    (isActive, wasActive) => {
      if (isActive) activate()
      else if (wasActive) deactivate()
    },
    { immediate: true },
  )

  onBeforeUnmount(() => {
    if (active.value) deactivate()
  })
}
