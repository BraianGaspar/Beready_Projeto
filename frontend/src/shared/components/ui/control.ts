// Visual comum dos controles de formulário nativos (BaseInput, BaseTextarea, BaseSelect).
// A altura mínima fica com cada componente (min-h-control | min-h-textarea), pois duas
// classes de min-height no mesmo elemento não têm ordem garantida.
//
// Precedência (igual à versão em CSS): hover > aria-invalid > foco > base.
export const controlClass = [
  'ui-control w-full px-3 py-2',
  'border border-solid border-border-strong rounded-md bg-surface text-text',
  // 16px evita o zoom automático do iOS
  'text-base leading-base',
  'transition-control',
  'placeholder:text-text-subtle placeholder:opacity-100',
  'enabled:hover:border-text-muted',
  'focus:outline-none focus:border-primary focus:ring focus:ring-primary-soft',
  'aria-invalid:border-danger aria-invalid:focus:ring-danger-soft',
  'disabled:bg-surface-muted disabled:text-text-muted disabled:cursor-not-allowed',
].join(' ')
