import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePermissionStore, type Plano } from '@/stores/permissionStore'
import { useAuthStore } from '@/stores/auth'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { getApiErrorMessage } from '@/core/services/api'
import { formatRecursoPlano, formatLimitePlano } from '@/shared/utils/planoLabels'
import { formatCurrency } from '@/shared/utils/intl'

export function usePlanosPage() {
    const permissionStore = usePermissionStore()
    const authStore = useAuthStore()
    const route = useRoute()
    const router = useRouter()
    const { success, error, warning } = useAlert()
    const { t } = useI18n()
    const isLoading = ref(false)
    const isLoadingPlanos = ref(true)
    const showCancelModal = ref(false)

    const planosData = computed(() => permissionStore.planos)
    const planoAtual = computed(() => permissionStore.getPlanoAtual)
    const isPlanoAtual = (plano: Plano): boolean => planoAtual.value?.id === plano.id
    // Mesma regra do backend (AssinaturaService::isGratuito): só o plano gratuito não é cancelável
    const podeCancelar = computed(() => {
        const plano = planoAtual.value
        if (!plano) return false
        return plano.preco_mensal > 0 || plano.preco_anual > 0 || plano.dias_trial > 0
    })

    const calcularEconomia = (plano: Plano): number => {
        if (plano.preco_mensal === 0 || plano.preco_anual === 0) return 0
        const anual = plano.preco_mensal * 12
        const economia = ((anual - plano.preco_anual) / anual) * 100
        return Math.round(economia)
    }

    const formatRecurso = formatRecursoPlano
    const formatLimiteKey = formatLimitePlano

    const handleAssinarPlano = async (plano: Plano): Promise<void> => {
        if (isPlanoAtual(plano)) return

        isLoading.value = true
        try {
            const response = await permissionStore.assinarPlano(plano.id, 'mensal')
            const checkoutUrl = response.data?.checkout_url

            if (response.data?.requires_payment && checkoutUrl) {
                window.location.href = checkoutUrl
            } else {
                success(t('planos.activatedSuccess', { nome: plano.nome }))
            }
        } catch (err: unknown) {
            error(getApiErrorMessage(err) || t('planos.errorSubscribe'))
        } finally {
            isLoading.value = false
        }
    }

    const handleCancelarAssinatura = async (): Promise<void> => {
        isLoading.value = true
        try {
            await permissionStore.cancelarAssinatura()
            showCancelModal.value = false
            success(t('planos.canceledSuccess'))
        } catch (err: unknown) {
            error(getApiErrorMessage(err) || t('planos.errorCancel'))
        } finally {
            isLoading.value = false
        }
    }

    onMounted(async () => {
        // Retorno do checkout do Stripe sem concluir o pagamento
        if (route.query.canceled === 'true') {
            warning(t('planos.paymentCanceled'))
            router.replace({ query: {} })
        }

        await Promise.all([
            permissionStore.loadPlanosAtivos(),
            authStore.isAuthenticated ? permissionStore.loadAssinatura() : Promise.resolve()
        ])
        isLoadingPlanos.value = false
    })

    return {
        planosData,
        isLoading,
        isLoadingPlanos,
        showCancelModal,
        podeCancelar,
        isPlanoAtual,
        calcularEconomia,
        formatRecurso,
        formatLimiteKey,
        formatCurrency,
        handleAssinarPlano,
        handleCancelarAssinatura
    }
}
