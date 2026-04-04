import { reactive, ref } from 'vue'

export function useForm<T extends Record<string, any>>(initialData: T) {
  const form = reactive<T>({ ...initialData })
  const errors = ref<Partial<Record<keyof T, string>>>({})
  const loading = ref(false)

  const validate = (rules: Partial<Record<keyof T, (value: any) => string | null>>) => {
    let isValid = true
    errors.value = {}

    for (const [field, rule] of Object.entries(rules)) {
      const error = rule(form[field as keyof T])
      if (error) {
        errors.value[field as keyof T] = error
        isValid = false
      }
    }
    return isValid
  }

  const reset = () => {
    Object.assign(form, initialData)
    errors.value = {}
  }

  const setField = (field: keyof T, value: any) => {
    form[field] = value
    if (errors.value[field]) {
      delete errors.value[field]
    }
  }

  return { form, errors, loading, validate, reset, setField }
}
