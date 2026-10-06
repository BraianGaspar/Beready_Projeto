// Classes de <Transition>/<TransitionGroup> do Vue com utilitários do Tailwind.
// Uso: <Transition v-bind="overlayTransition">…</Transition>
//
// A raiz recebe também os marcadores `ui-t-active` (enter/leave-active) e `ui-t-from`
// (enter-from/leave-to); com as variantes `t-active:` e `t-from:` (tailwind.config.js)
// um descendente anima junto — ex.: o diálogo dentro do overlay:
//   class="t-active:transition-transform t-from:translate-y-3 t-from:scale-enter"
// Durações vêm dos tokens (zeradas com prefers-reduced-motion).

export interface TransitionClasses {
  enterFromClass: string
  enterActiveClass: string
  leaveActiveClass: string
  leaveToClass: string
}

const make = (active: string, from: string): TransitionClasses => ({
  enterFromClass: `ui-t-from ${from}`,
  enterActiveClass: `ui-t-active ${active}`,
  leaveActiveClass: `ui-t-active ${active}`,
  leaveToClass: `ui-t-from ${from}`,
})

/** Fundo de modal/drawer: esmaece (duration-base). Anime o painel com `t-active:`/`t-from:`. */
export const overlayTransition = make('transition-opacity duration-base ease-standard', 'opacity-0')

/** Notificação (toast): esmaece, sobe 0.5rem e cresce de 0.97 (duration-slow). */
export const toastTransition = make(
  'transition-enter duration-slow ease-standard',
  'opacity-0 -translate-y-2 scale-enter-sm',
)
