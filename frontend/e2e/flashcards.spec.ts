import { fail } from './support/api'
import { makeFlashcards, makeUser } from './support/data'
import { expect, open, test } from './support/fixtures'
import { t } from './support/i18n'

test.describe('Flashcards', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser())
  })

  test('lista mostra os cards, a próxima revisão e o atalho da revisão do dia', async ({ page, api }) => {
    api.withFlashcards(makeFlashcards(1))
    await open(page, '/flashcards')

    await expect(page.getByRole('heading', { level: 1, name: t('flashcards.title') })).toBeVisible()
    const cards = page.getByRole('article')
    await expect(cards).toHaveCount(3)
    await expect(cards.filter({ hasText: 'apple' })).toContainText(t('revisao.devidoAgora'))
    await expect(cards.filter({ hasText: 'book' })).toContainText(t('revisao.proximaEm', { quando: '' }).trim())

    await expect(page.getByText(t('revisao.paraRevisarHoje', { n: 1 }))).toBeVisible()
    await page.getByRole('link', { name: t('revisao.revisarAgora') }).click()
    await expect(page).toHaveURL(/\/flashcards\/revisao$/)
  })

  test('sem permissão de ver flashcards mostra acesso negado', async ({ page, api }) => {
    api.asUser(makeUser(), { permissions: ['quizes.view'] }).withFlashcards(makeFlashcards(0))
    await open(page, '/flashcards')

    await expect(page.getByRole('heading', { name: t('common.acessoNegado') })).toBeVisible()
    await expect(page.getByRole('article')).toHaveCount(0)
  })

  test('com "ver" mas sem "criar" o botão fica desabilitado', async ({ page, api }) => {
    api.asUser(makeUser(), { permissions: ['flashcards.view'] }).withFlashcards(makeFlashcards(0))
    await open(page, '/flashcards')

    await expect(page.getByRole('article')).toHaveCount(3)
    await expect(page.getByRole('button', { name: t('flashcards.newFlashcard') })).toBeDisabled()
    await expect(page.getByText(t('common.semPermissao'))).toBeVisible()
    // Sem editar/excluir: só o "Estudar" no rodapé dos cards
    await expect(page.getByRole('button', { name: t('common.editar') })).toHaveCount(0)
    await expect(page.getByRole('button', { name: t('common.excluir') })).toHaveCount(0)
  })

  test('lista vazia oferece criar o primeiro card', async ({ page }) => {
    await open(page, '/flashcards')
    await expect(page.getByRole('heading', { name: t('flashcards.emptyTitle') })).toBeVisible()
    await expect(page.getByRole('button', { name: t('flashcards.createFirst') })).toBeEnabled()
  })

  test('cria um flashcard pelo modal', async ({ page, api }) => {
    api.withFlashcards(makeFlashcards(0))
    await open(page, '/flashcards')

    await page.getByRole('button', { name: t('flashcards.newFlashcard') }).click()
    const dialog = page.getByRole('dialog', { name: t('flashcards.newFlashcard') })
    await expect(dialog).toBeVisible()

    await dialog.getByRole('textbox', { name: t('flashcards.pergunta') }).fill('dog')
    await dialog.getByRole('textbox', { name: t('flashcards.resposta') }).fill('cachorro')
    await dialog.getByRole('combobox', { name: t('flashcards.dificuldade') }).selectOption('avancado')

    const create = api.waitFor('POST', '/flashcards')
    await dialog.getByRole('button', { name: t('common.criar') }).click()

    expect((await create).postDataJSON()).toMatchObject({
      usuario_id: 1,
      frente: 'dog',
      verso: 'cachorro',
      nivel_dificuldade: 'avancado',
    })
    await expect(dialog).toBeHidden()
    await expect(page.getByRole('status').filter({ hasText: t('flashcards.successCreate') })).toBeVisible()
    await expect(page.getByRole('article')).toHaveCount(4)
    await expect(page.getByRole('article').filter({ hasText: 'cachorro' })).toBeVisible()
  })

  test('limite do plano (403 com errors.limite) mostra a mensagem da API', async ({ page, api }) => {
    const mensagem = 'Você atingiu o limite de 50 flashcards do seu plano. Faça upgrade para criar mais.'
    api.withFlashcards(makeFlashcards(0))
    api.on('POST', '/flashcards', fail(403, mensagem, { limite: { recurso: 'flashcards', limite: 50, usados: 50 } }))
    await open(page, '/flashcards')

    await page.getByRole('button', { name: t('flashcards.newFlashcard') }).click()
    const dialog = page.getByRole('dialog', { name: t('flashcards.newFlashcard') })
    await dialog.getByRole('textbox', { name: t('flashcards.pergunta') }).fill('dog')
    await dialog.getByRole('textbox', { name: t('flashcards.resposta') }).fill('cachorro')
    await dialog.getByRole('button', { name: t('common.criar') }).click()

    await expect(page.getByRole('alert').filter({ hasText: mensagem })).toBeVisible()
    // Modal continua aberto com o que foi digitado
    await expect(dialog.getByRole('textbox', { name: t('flashcards.pergunta') })).toHaveValue('dog')
    await expect(page.getByRole('article')).toHaveCount(3)
  })

  test('limite do plano no front desabilita o "Novo flashcard"', async ({ page, api }) => {
    api.withFlashcards(makeFlashcards(0))
    // Plano com limite 3 e o usuário já tem 3 cards
    const assinatura = api.state.assinatura
    if (assinatura?.plano) assinatura.plano.limites = { flashcards: 3, quizes: 10, prompts: 10 }
    api.state.planos[0]!.limites = { flashcards: 3, quizes: 10, prompts: 10 }
    await open(page, '/flashcards')

    await expect(page.getByRole('button', { name: t('flashcards.newFlashcard') })).toBeDisabled()
    await expect(page.getByText(t('common.limiteAtingido'))).toBeVisible()
  })
})

test.describe('Estudo de flashcards', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser()).withFlashcards(makeFlashcards(0))
  })

  test('vira o card por clique e por teclado (Enter/Espaço)', async ({ page }) => {
    await open(page, '/flashcards/1/study')

    const card = page.getByRole('button', { pressed: false }).filter({ hasText: 'apple' })
    await expect(card).toBeVisible()
    // Avaliação escondida até virar
    await expect(page.getByRole('button', { name: t('flashcardStudy.rateGood') })).toBeHidden()

    await card.click()
    const flipped = page.getByRole('button', { pressed: true }).filter({ hasText: 'maçã' })
    await expect(flipped).toBeVisible()
    await expect(page.getByRole('button', { name: t('flashcardStudy.rateGood') })).toBeVisible()

    await flipped.focus()
    await page.keyboard.press('Enter')
    await expect(page.getByRole('button', { pressed: false }).filter({ hasText: 'apple' })).toBeVisible()
    await expect(page.getByRole('button', { name: t('flashcardStudy.rateGood') })).toBeHidden()

    await page.keyboard.press('Space')
    await expect(page.getByRole('button', { pressed: true }).filter({ hasText: 'maçã' })).toBeVisible()
  })

  test('avaliar Fácil/Errei/Bom envia a nota certa e conclui o estudo', async ({ page, api }) => {
    await open(page, '/flashcards/1/study')

    const avaliar = async (id: number, frente: string, botao: string, nota: string) => {
      const card = page.getByRole('button', { pressed: false }).filter({ hasText: frente })
      await expect(card).toBeVisible()
      await card.click()
      const revisao = api.waitFor('POST', `/flashcards/${id}/revisao`)
      await page.getByRole('button', { name: botao, exact: true }).click()
      expect((await revisao).postDataJSON()).toEqual({ nota })
    }

    await avaliar(1, 'apple', t('flashcardStudy.rateEasy'), 'facil')
    await expect(page).toHaveURL(/\/flashcards\/2\/study$/)
    await avaliar(2, 'book', t('flashcardStudy.rateHard'), 'errei')
    await expect(page).toHaveURL(/\/flashcards\/3\/study$/)
    await avaliar(3, 'house', t('flashcardStudy.rateGood'), 'bom')

    const modal = page.getByRole('dialog', { name: t('flashcardStudy.completedTitle') })
    await expect(modal).toBeVisible()
    await expect(modal.getByText(t('flashcardStudy.wrongCount')).locator('..')).toContainText('1')
    await expect(modal.getByText(t('flashcardStudy.correctCount')).locator('..')).toContainText('2')
  })

  test('falha ao salvar a avaliação mantém o card para tentar de novo', async ({ page, api }) => {
    api.on('POST', '/flashcards/:id/revisao', fail(500, 'Erro interno'))
    await open(page, '/flashcards/1/study')

    await page.getByRole('button', { pressed: false }).filter({ hasText: 'apple' }).click()
    await page.getByRole('button', { name: t('flashcardStudy.rateGood') }).click()

    await expect(page.getByRole('alert').filter({ hasText: 'Erro interno' })).toBeVisible()
    await expect(page).toHaveURL(/\/flashcards\/1\/study$/)
    await expect(page.getByRole('button', { name: t('flashcardStudy.rateGood') })).toBeEnabled()
  })
})

test.describe('Revisar agora (repetição espaçada)', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser())
  })

  test('fila dos vencidos mostra "Faltam N" e grava cada avaliação', async ({ page, api }) => {
    api.withFlashcards(makeFlashcards(2))

    const fila = api.waitFor('GET', '/flashcards/revisao')
    await open(page, '/flashcards/revisao')
    // A fila completa vem sem `limite`
    expect(new URL((await fila).url()).searchParams.get('limite')).toBeNull()

    await expect(page.getByRole('heading', { level: 1, name: t('revisao.titulo') })).toBeVisible()
    await expect(page.getByText(t('revisao.faltam', { n: 2 }))).toBeVisible()
    await expect(page.getByText(t('revisao.posicao', { current: 1, total: 2 }))).toBeVisible()

    await page.getByRole('button', { pressed: false }).filter({ hasText: 'apple' }).click()
    let revisao = api.waitFor('POST', '/flashcards/1/revisao')
    await page.getByRole('button', { name: t('flashcardStudy.rateHard') }).click()
    expect((await revisao).postDataJSON()).toEqual({ nota: 'errei' })

    await expect(page.getByText(t('revisao.faltam', { n: 1 }))).toBeVisible()
    await page.getByRole('button', { pressed: false }).filter({ hasText: 'book' }).click()
    revisao = api.waitFor('POST', '/flashcards/2/revisao')
    await page.getByRole('button', { name: t('flashcardStudy.rateEasy') }).click()
    expect((await revisao).postDataJSON()).toEqual({ nota: 'facil' })

    await expect(page.getByRole('dialog', { name: t('revisao.completedTitle') })).toBeVisible()
  })

  test('sem cards vencidos mostra "Tudo em dia"', async ({ page, api }) => {
    api.withFlashcards(makeFlashcards(0))
    await open(page, '/flashcards/revisao')

    await expect(page.getByRole('heading', { name: t('revisao.emptyTitle') })).toBeVisible()
    await page.getByRole('button', { name: t('flashcardStudy.backToDecks') }).click()
    await expect(page).toHaveURL(/\/flashcards$/)
  })
})
