import { computed, onMounted, ref } from 'vue'
import { usePermissionStore, type Plano } from '@/stores/permissionStore'
import { useAlert } from '@/shared/composables/useAlert'

export function usePlanosPage() {
    const permissionStore = usePermissionStore()
    const { success, error } = useAlert()
    const isLoading = ref(false)

    // Dados mock para fallback caso a API não retorne nada
    const mockPlanos: Plano[] = [
        {
            id: 1,
            nome: 'Gratuito',
            descricao: 'Plano básico para começar',
            role_id: null,
            preco_mensal: 0,
            preco_anual: 0,
            dias_trial: 0,
            recursos: ['flashcards_basico', 'quizes_basico'],
            limites: { flashcards: 50, quizes: 10, prompts: 5 },
            is_ativo: true,
            ordem: 1
        },
        {
            id: 2,
            nome: 'Trial',
            descricao: 'Período de teste',
            role_id: null,
            preco_mensal: 0,
            preco_anual: 0,
            dias_trial: 7,
            recursos: ['flashcards_ilimitados', 'quizes_ilimitados'],
            limites: { flashcards: 999, quizes: 999, prompts: 20 },
            is_ativo: true,
            ordem: 2
        },
        {
            id: 3,
            nome: 'Premium',
            descricao: 'Acesso completo',
            role_id: null,
            preco_mensal: 29.90,
            preco_anual: 299.90,
            dias_trial: 0,
            recursos: ['flashcards_ilimitados', 'quizes_ilimitados', 'ia_prompts', 'exportacao'],
            limites: { flashcards: 999999, quizes: 999999, prompts: 999 },
            is_ativo: true,
            ordem: 3
        }
    ]

    const planosData = computed(() => {
        if (permissionStore.planos && permissionStore.planos.length > 0) {
            return permissionStore.planos
        }
        return mockPlanos
    })

    const calcularEconomia = (plano: Plano): number => {
        if (plano.preco_mensal === 0 || plano.preco_anual === 0) return 0
        const anual = plano.preco_mensal * 12
        const economia = ((anual - plano.preco_anual) / anual) * 100
        return Math.round(economia)
    }

    const formatRecurso = (recurso: string): string => {
        const map: Record<string, string> = {
            flashcards_ilimitados: 'Flashcards Ilimitados',
            quizes_ilimitados: 'Quizes Ilimitados',
            ia_prompts: 'Prompts com IA',
            exportacao: 'Exportação de Dados',
            api_acesso: 'Acesso à API',
            flashcards_basico: 'Flashcards Básico',
            quizes_basico: 'Quizes Básico'
        }
        return map[recurso] || recurso.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
    }

    const formatLimiteKey = (key: string): string => {
        const map: Record<string, string> = {
            flashcards: 'Flashcards',
            quizes: 'Quizes',
            prompts: 'Prompts IA'
        }
        return map[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
    }

    const handleAssinarPlano = async (plano: Plano): Promise<void> => {
        isLoading.value = true
        try {
            const response = await permissionStore.assinarPlano(plano.id, 'mensal')
            if (response.requires_payment && response.checkout_url) {
                window.location.href = response.checkout_url
            } else {
                success(`Plano ${plano.nome} ativado com sucesso!`)
            }
        } catch (err: unknown) {
            const errorMessage = err && typeof err === 'object' && 'response' in err
                ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
                : 'Erro ao assinar plano'
            error(errorMessage || 'Erro ao assinar plano')
        } finally {
            isLoading.value = false
        }
    }

    onMounted(() => {
        permissionStore.loadPlanos()
    })

    return {
        planosData,
        isLoading,
        calcularEconomia,
        formatRecurso,
        formatLimiteKey,
        handleAssinarPlano
    }
}