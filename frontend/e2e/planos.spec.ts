import type { Page } from '@playwright/test'
import { fail } from './support/api'
import { makeAssinaturaGratuita, makeAssinaturaPremiumRecorrente, makeUser } from './support/data'
import { expect, open, test } from './support/fixtures'
import { escapeRegExp, t } from './support/i18n'

// data_fim das fixtures do Premium (07/11/2026 no formato pt-BR)
const DATA_FIM = '07/11/2026'

const cardDoPlano = (page: Page, nome: string) =>
  page.getByRole('article').filter({ has: page.getByRole('heading', { level: 2, name: nome, exact: true }) })

test.describe('Planos', () => {
  test('visitante vê os planos ativos sem plano atual', async ({ page, api }) => {
    await open(page, '/planos')

    await expect(page.getByRole('heading', { level: 1, name: t('planos.title') })).toBeVisible()
    await expect(page.getByRole('article')).toHaveCount(3)
    await expect(page.getByText(t('planos.currentPlan'))).toHaveCount(0)
    await expect(cardDoPlano(page, 'Premium')).toContainText('R$ 29,90')
    expect(api.calls('GET', '/user/assinatura')).toHaveLength(0)
  })

  test('plano atual (gratuito) fica marcado e os outros podem ser assinados', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaGratuita() })
    await open(page, '/planos')

    const gratuito = cardDoPlano(page, 'Gratuito')
    await expect(gratuito.getByText(t('planos.currentPlan')).first()).toBeVisible()
    await expect(gratuito.getByRole('button', { name: t('planos.currentPlan') })).toBeDisabled()
    // Gratuito não é cancelável
    await expect(gratuito.getByRole('button', { name: t('planos.cancelSubscription') })).toHaveCount(0)
    await expect(cardDoPlano(page, 'Premium').getByRole('button', { name: t('planos.subscribeNow') })).toBeEnabled()
  })

  test('assinar o Premium redireciona para o checkout do Stripe', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaGratuita() })
    await open(page, '/planos')

    const assinar = api.waitFor('POST', '/planos/3/assinar')
    await cardDoPlano(page, 'Premium').getByRole('button', { name: t('planos.subscribeNow') }).click()
    expect((await assinar).postDataJSON()).toEqual({ ciclo: 'mensal' })
    await expect(page).toHaveURL(/https:\/\/checkout\.stripe\.com\//)
  })

  test('Premium recorrente: renovação automática, gerenciar pagamento e troca bloqueada', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaPremiumRecorrente() })
    await open(page, '/planos')

    const premium = cardDoPlano(page, 'Premium')
    await expect(premium.getByText(t('planos.currentPlan')).first()).toBeVisible()
    await expect(premium).toContainText(t('planos.renewsOn', { date: DATA_FIM }))
    await expect(premium.getByRole('button', { name: t('planos.cancelSubscription') })).toBeVisible()

    // Os outros planos não oferecem assinar: mostram a mensagem de troca bloqueada
    for (const nome of ['Gratuito', 'Trial']) {
      const card = cardDoPlano(page, nome)
      await expect(card.getByText(t('planos.switchBlockedRecurring'))).toBeVisible()
      await expect(card.getByRole('button', { name: new RegExp(`${escapeRegExp(t('planos.subscribeNow'))}|${escapeRegExp(t('planos.startFree'))}`) })).toHaveCount(0)
    }

    const portal = api.waitFor('POST', '/planos/portal')
    await premium.getByRole('button', { name: t('planos.managePayment') }).click()
    await portal
    await expect(page).toHaveURL('https://billing.stripe.com/p/session/e2e_test')
  })

  test('falha ao abrir o portal mostra o erro', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaPremiumRecorrente() })
    api.on('POST', '/planos/portal', fail(400, 'Assinatura sem cliente no Stripe'))
    await open(page, '/planos')

    await cardDoPlano(page, 'Premium').getByRole('button', { name: t('planos.managePayment') }).click()
    await expect(page.getByRole('alert').filter({ hasText: 'Assinatura sem cliente no Stripe' })).toBeVisible()
    await expect(page).toHaveURL(/\/planos$/)
  })

  test('cancelar o Premium recorrente agenda o cancelamento para o fim do período', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaPremiumRecorrente() })
    await open(page, '/planos')

    const premium = cardDoPlano(page, 'Premium')
    await premium.getByRole('button', { name: t('planos.cancelSubscription') }).click()
    const dialog = page.getByRole('dialog', { name: t('planos.cancelSubscription') })
    await expect(dialog).toContainText(t('planos.cancelAtPeriodEndMessage', { date: DATA_FIM }))

    const cancelar = api.waitFor('POST', '/planos/cancelar')
    await dialog.getByRole('button', { name: t('planos.cancelSubscription') }).click()
    await cancelar

    await expect(page.getByRole('status').filter({ hasText: t('planos.cancelScheduledSuccess', { date: DATA_FIM }) })).toBeVisible()
    await expect(premium.getByText(t('planos.cancelScheduled'))).toBeVisible()
    await expect(premium).toContainText(t('planos.activeUntil', { date: DATA_FIM }))
    await expect(premium.getByRole('button', { name: t('planos.cancelSubscription') })).toHaveCount(0)
  })

  test('cancelamento já agendado: "Ativo até" e sem novo cancelamento', async ({ page, api }) => {
    api.asUser(makeUser(), {
      assinatura: makeAssinaturaPremiumRecorrente({ cancelar_no_fim_periodo: true }),
    })
    await open(page, '/planos')

    const premium = cardDoPlano(page, 'Premium')
    await expect(premium.getByText(t('planos.cancelScheduled'))).toBeVisible()
    await expect(premium).toContainText(t('planos.activeUntil', { date: DATA_FIM }))
    await expect(premium).not.toContainText(t('planos.renewsOn', { date: DATA_FIM }))
    await expect(premium.getByRole('button', { name: t('planos.cancelSubscription') })).toHaveCount(0)
    // O portal continua disponível até o fim do período
    await expect(premium.getByRole('button', { name: t('planos.managePayment') })).toBeVisible()
  })

  test('pagamento pendente (past_due) mostra o aviso', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaPremiumRecorrente({ stripe_status: 'past_due' }) })
    await open(page, '/planos')

    await expect(cardDoPlano(page, 'Premium').getByText(t('planos.paymentPastDue'))).toBeVisible()
  })

  test('retorno do checkout cancelado avisa que nada foi cobrado', async ({ page, api }) => {
    api.asUser(makeUser(), { assinatura: makeAssinaturaGratuita() })
    await open(page, '/planos?canceled=true')

    await expect(page.getByRole('status').filter({ hasText: t('planos.paymentCanceled') })).toBeVisible()
    await expect(page).toHaveURL(/\/planos$/)
  })
})
