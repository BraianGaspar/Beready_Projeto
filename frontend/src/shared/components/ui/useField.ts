import { computed, useAttrs, useId } from 'vue'

/**
 * Lógica comum dos campos (BaseInput, BaseTextarea, BaseSelect, BaseCheckbox, BaseSwitch):
 * - id estável (prop `id` ou gerado) para associar label/erro/dica;
 * - `class`/`style` vão para o wrapper; os demais atributos (name, maxlength,
 *   autocomplete, inputmode...) vão para o controle nativo.
 * Os componentes usam `defineOptions({ inheritAttrs: false })`.
 */
export function useField(props: { id?: string; error?: string; hint?: string }) {
  const attrs = useAttrs()
  const autoId = useId()

  const fieldId = computed(() => props.id || `field-${autoId}`)

  const rootAttrs = computed(() => ({ class: attrs.class, style: attrs.style }))

  const controlAttrs = computed(() => {
    const rest: Record<string, unknown> = {}
    for (const [key, value] of Object.entries(attrs)) {
      if (key !== 'class' && key !== 'style') rest[key] = value
    }
    return rest
  })

  const describedBy = computed(() => {
    if (props.error) return `${fieldId.value}-error`
    if (props.hint) return `${fieldId.value}-hint`
    return undefined
  })

  return { fieldId, rootAttrs, controlAttrs, describedBy }
}
