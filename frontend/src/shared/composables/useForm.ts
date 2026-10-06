// src/shared/composables/useForm.ts
import { reactive, ref, type Ref } from 'vue'

// Uma regra por campo: recebe o valor atual e devolve a mensagem de erro (ou null)
export type FormRules<T> = { [K in keyof T]?: (value: T[K]) => string | null }

export function useForm<T extends object>(initialData: T) {
  const form = reactive({ ...initialData }) as T
  const errors = ref({}) as Ref<Partial<Record<keyof T, string>>>
  const loading = ref(false)

  const validate = (rules: FormRules<T>) => {
    let isValid = true
    errors.value = {}
    for (const field in rules) {
      const rule = rules[field]
      if (rule) {
        const errorMsg = rule(form[field])
        if (errorMsg) {
          errors.value[field] = errorMsg
          isValid = false
        }
      }
    }
    return isValid
  }

  const reset = () => {
    Object.assign(form, initialData)
    errors.value = {}
  }

  const setField = <K extends keyof T>(field: K, value: T[K]) => {
    form[field] = value
    if (errors.value[field]) delete errors.value[field]
  }

  return { form, errors, loading, validate, reset, setField }
}
