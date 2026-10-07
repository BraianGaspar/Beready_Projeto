import type { Page } from '@playwright/test'
import { fail, type QuizMock } from './support/api'
import { makeFlashcards, makeQuestoes, makeQuiz, makeUser } from './support/data'
import { expect, open, test } from './support/fixtures'
import { t } from './support/i18n'

const quizComQuestoes = (id = 1): QuizMock => ({ ...makeQuiz(id, { titulo: 'Frutas e preposições' }), questoes: makeQuestoes(id) })
const quizVazio = (id = 2): QuizMock => ({ ...makeQuiz(id, { titulo: 'Quiz sem questões' }), questoes: [] })

/** Card de uma questão no editor ("Questão N"). */
const questaoCard = (page: Page, n: number) =>
  page.getByRole('article').filter({ has: page.getByRole('heading', { name: t('quizEditor.questaoN', { n }), exact: true }) })

test.describe('Quizes: lista e geração', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser()).withFlashcards(makeFlashcards(0))
  })

  test('lista mostra contagem de questões e "Sem questões"', async ({ page, api }) => {
    api.withQuizes([quizComQuestoes(1), quizVazio(2)])
    await open(page, '/quizes')

    const cards = page.getByRole('article')
    await expect(cards).toHaveCount(2)
    await expect(cards.filter({ hasText: 'Frutas e preposições' })).toContainText(`2 ${t('quizes.questoes')}`)
    await expect(cards.filter({ hasText: 'Quiz sem questões' })).toContainText(t('quizEditor.semQuestoes'))
  })

  test('"Gerar quiz dos meus flashcards" cria o quiz e abre o jogo', async ({ page, api }) => {
    api.withQuizes([quizComQuestoes(1)])
    await open(page, '/quizes')

    await page.getByRole('button', { name: t('quizGerar.botao') }).click()
    const dialog = page.getByRole('dialog', { name: t('quizGerar.titulo') })
    await expect(dialog).toBeVisible()
    await dialog.getByRole('spinbutton', { name: t('quizGerar.quantidade') }).fill('5')
    await dialog.getByRole('combobox', { name: t('quizes.nivel') }).selectOption('iniciante')

    const gerar = api.waitFor('POST', '/quizes/gerar')
    await dialog.getByRole('button', { name: t('quizGerar.gerar') }).click()

    expect((await gerar).postDataJSON()).toEqual({
      quantidade: 5,
      nivel: 'iniciante',
      titulo: t('quizGerar.tituloPadrao'),
    })
    await expect(page.getByRole('status').filter({ hasText: t('quizGerar.sucesso') })).toBeVisible()
    await expect(page).toHaveURL(/\/quizes\/\d+\/play$/)
    await expect(page.getByRole('heading', { level: 2, name: 'apple' })).toBeVisible()
  })

  test('gerar com poucos flashcards (422) explica o mínimo e leva aos flashcards', async ({ page, api }) => {
    api.on(
      'POST',
      '/quizes/gerar',
      fail(422, 'São necessários pelo menos 4 flashcards com respostas diferentes para gerar um quiz (você tem 3)', {
        flashcards: { minimo: 'Crie pelo menos 4 flashcards com respostas diferentes' },
      }),
    )
    await open(page, '/quizes')

    await page.getByRole('button', { name: t('quizGerar.botao') }).click()
    const dialog = page.getByRole('dialog', { name: t('quizGerar.titulo') })
    await dialog.getByRole('button', { name: t('quizGerar.gerar') }).click()

    await expect(dialog.getByText(t('quizGerar.erroMinimo', { n: 4 }))).toBeVisible()
    await expect(page).toHaveURL(/\/quizes$/)
    await dialog.getByRole('link', { name: t('quizGerar.irFlashcards') }).click()
    await expect(page).toHaveURL(/\/flashcards$/)
  })

  test('gerar com limite do plano atingido (403) mostra a mensagem', async ({ page, api }) => {
    api.on(
      'POST',
      '/quizes/gerar',
      fail(403, 'Você atingiu o limite de 10 quizzes do seu plano. Faça upgrade para criar mais.', {
        limite: { recurso: 'quizes', limite: 10, usados: 10 },
      }),
    )
    await open(page, '/quizes')

    await page.getByRole('button', { name: t('quizGerar.botao') }).click()
    const dialog = page.getByRole('dialog', { name: t('quizGerar.titulo') })
    await dialog.getByRole('button', { name: t('quizGerar.gerar') }).click()

    await expect(dialog.getByText(t('plan.limitReached', { recurso: t('common.quizes') }))).toBeVisible()
  })

  test('quantidade inválida é barrada no cliente', async ({ page, api }) => {
    await open(page, '/quizes')
    await page.getByRole('button', { name: t('quizGerar.botao') }).click()
    const dialog = page.getByRole('dialog', { name: t('quizGerar.titulo') })
    const quantidade = dialog.getByRole('spinbutton', { name: t('quizGerar.quantidade') })
    await quantidade.fill('80')
    await dialog.getByRole('button', { name: t('quizGerar.gerar') }).click()

    await expect(quantidade).toHaveAccessibleDescription(t('quizGerar.erroQuantidade', { max: 50 }))
    expect(api.calls('POST', '/quizes/gerar')).toHaveLength(0)
  })
})

test.describe('Quizes: editor de questões', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser()).withQuizes([quizVazio(2)])
  })

  test('adiciona múltipla escolha e completar e salva no formato da API', async ({ page, api }) => {
    await open(page, '/quizes/edit/2')

    await expect(page.getByRole('heading', { name: t('quizEditor.emptyTitle') })).toBeVisible()
    await page.getByRole('button', { name: t('quizEditor.adicionarMultipla') }).click()

    const q1 = questaoCard(page, 1)
    await expect(q1.getByRole('textbox', { name: t('quizEditor.enunciado') })).toBeFocused()
    await q1.getByRole('textbox', { name: t('quizEditor.enunciado') }).fill('Qual é a cor do céu?')
    for (const [letra, texto] of [['A', 'red'], ['B', 'green'], ['C', 'blue'], ['D', 'yellow']] as const) {
      await q1.getByRole('textbox', { name: t('quizEditor.alternativaN', { letra }) }).fill(texto)
    }
    // A primeira começa marcada; troca para a C
    await expect(q1.getByRole('radio', { name: t('quizEditor.marcarCorreta', { letra: 'A' }) })).toBeChecked()
    await q1.getByRole('radio', { name: t('quizEditor.marcarCorreta', { letra: 'C' }) }).check()
    await expect(q1.getByRole('radio', { name: t('quizEditor.marcarCorreta', { letra: 'A' }) })).not.toBeChecked()
    // Remove a D (mínimo de 2 continua respeitado)
    await q1.getByRole('button', { name: t('quizEditor.removerAlternativa', { letra: 'D' }) }).click()
    await expect(q1.getByRole('textbox', { name: /^Alternativa [A-Z]$/ })).toHaveCount(3)

    await page.getByRole('button', { name: t('quizEditor.adicionarCompletar') }).click()
    const q2 = questaoCard(page, 2)
    await expect(q2.getByRole('combobox', { name: t('quizEditor.tipo') })).toHaveValue('completar')
    await q2.getByRole('textbox', { name: t('quizEditor.enunciado') }).fill('I ___ a student.')
    await q2.getByRole('textbox', { name: t('quizEditor.respostaEsperada') }).fill('am')
    await q2.getByRole('textbox', { name: t('quizEditor.explicacao') }).fill('Verbo to be')

    await expect(page.getByText(t('quizEditor.contagem', { n: 2 }))).toBeVisible()

    const salvar = api.waitFor('PUT', '/quizes/2/questoes')
    const atualizar = api.waitFor('PUT', '/quizes/2')
    await page.getByRole('button', { name: t('quizes.saveChanges') }).click()

    expect((await salvar).postDataJSON()).toEqual({
      questoes: [
        {
          tipo: 'multipla_escolha',
          enunciado: 'Qual é a cor do céu?',
          explicacao: null,
          alternativas: [
            { texto: 'red', correta: false },
            { texto: 'green', correta: false },
            { texto: 'blue', correta: true },
          ],
        },
        { tipo: 'completar', enunciado: 'I ___ a student.', explicacao: 'Verbo to be', resposta_esperada: 'am' },
      ],
    })
    await atualizar
    await expect(page).toHaveURL(/\/quizes\/2$/)
  })

  test('validação no cliente marca os campos sem chamar a API', async ({ page, api }) => {
    await open(page, '/quizes/edit/2')
    await page.getByRole('button', { name: t('quizEditor.adicionarCompletar') }).click()
    await page.getByRole('button', { name: t('quizes.saveChanges') }).click()

    const q1 = questaoCard(page, 1)
    await expect(q1.getByRole('textbox', { name: t('quizEditor.enunciado') })).toHaveAccessibleDescription(
      t('quizEditor.erroEnunciado'),
    )
    await expect(q1.getByRole('textbox', { name: t('quizEditor.respostaEsperada') })).toHaveAccessibleDescription(
      t('quizEditor.erroResposta'),
    )
    await expect(page.getByText(t('quizEditor.erroRevisar'))).toBeVisible()
    expect(api.calls('PUT', '/quizes/2/questoes')).toHaveLength(0)
  })

  test('erro 422 da API aparece no campo da questão', async ({ page, api }) => {
    api.on(
      'PUT',
      '/quizes/:id/questoes',
      fail(422, 'Dados inválidos', {
        'questoes.0.enunciado': { maxLength: 'O enunciado pode ter no máximo 2000 caracteres' },
        'questoes.0.alternativas': { umaCorreta: 'Marque exatamente uma alternativa correta' },
      }),
    )
    await open(page, '/quizes/edit/2')
    await page.getByRole('button', { name: t('quizEditor.adicionarMultipla') }).click()
    const q1 = questaoCard(page, 1)
    await q1.getByRole('textbox', { name: t('quizEditor.enunciado') }).fill('Pergunta')
    await q1.getByRole('textbox', { name: t('quizEditor.alternativaN', { letra: 'A' }) }).fill('um')
    await q1.getByRole('textbox', { name: t('quizEditor.alternativaN', { letra: 'B' }) }).fill('dois')
    await q1.getByRole('button', { name: t('quizEditor.removerAlternativa', { letra: 'D' }) }).click()
    await q1.getByRole('button', { name: t('quizEditor.removerAlternativa', { letra: 'C' }) }).click()

    await page.getByRole('button', { name: t('quizes.saveChanges') }).click()

    await expect(q1.getByRole('textbox', { name: t('quizEditor.enunciado') })).toHaveAccessibleDescription(
      t('quizEditor.erroMuitoLongo'),
    )
    await expect(q1.getByRole('group', { name: t('quizEditor.alternativas') })).toHaveAccessibleDescription(
      t('quizEditor.erroUmaCorreta'),
    )
    await expect(page.getByRole('alert').filter({ hasText: t('quizEditor.erroSalvar') })).toBeVisible()
    // Questões recusadas: os dados do quiz não são gravados e a tela continua no editor
    expect(api.calls('PUT', '/quizes/2')).toHaveLength(0)
    await expect(page).toHaveURL(/\/quizes\/edit\/2$/)
  })
})

test.describe('Quizes: jogar', () => {
  test.beforeEach(({ api }) => {
    api.asUser(makeUser()).withQuizes([quizComQuestoes(1), quizVazio(2)])
  })

  test('uma questão por vez, feedback do servidor e resultado com revisão das erradas', async ({ page, api }) => {
    await open(page, '/quizes/1/play')

    await expect(page.getByText(t('quizPlay.progress', { current: 1, total: 2 }))).toBeVisible()
    await expect(page.getByRole('heading', { level: 2, name: 'Como se diz "maçã" em inglês?' })).toBeVisible()
    // Só a questão atual aparece
    await expect(page.getByRole('heading', { name: 'The book is ___ the table.' })).toHaveCount(0)

    const responder = page.getByRole('button', { name: t('quizPlay.submitAnswer') })
    await expect(responder).toBeDisabled()

    // Q1: resposta errada
    await page.getByRole('radio', { name: 'grape' }).check()
    let verificar = api.waitFor('POST', '/quizes/1/questoes/101/verificar')
    await responder.click()
    expect((await verificar).postDataJSON()).toEqual({ alternativa_id: 1002 })

    const feedbackErrado = page.getByRole('alert').filter({ hasText: t('quizPlay.feedbackWrong') })
    await expect(feedbackErrado).toContainText(t('quizPlay.correctWas', { resposta: 'apple' }))
    await expect(feedbackErrado).toContainText('Apple é maçã.')
    await expect(page.getByRole('radio', { name: 'apple' })).toBeDisabled()

    const proxima = page.getByRole('button', { name: t('quizPlay.next') })
    await expect(proxima).toBeFocused()
    await proxima.click()

    // Q2: completar, resposta certa (maiúsculas/espaços ignorados pelo servidor)
    await expect(page.getByText(t('quizPlay.progress', { current: 2, total: 2 }))).toBeVisible()
    await page.getByRole('textbox', { name: t('quizPlay.typeAnswer') }).fill('  ON ')
    verificar = api.waitFor('POST', '/quizes/1/questoes/102/verificar')
    await page.getByRole('button', { name: t('quizPlay.submitAnswer') }).click()
    expect((await verificar).postDataJSON()).toEqual({ resposta: 'ON' })
    await expect(page.getByRole('status').filter({ hasText: t('quizPlay.feedbackCorrect') })).toBeVisible()

    const finalizar = api.waitFor('POST', '/quizes/1/finalizar')
    await page.getByRole('button', { name: t('quizPlay.seeResult') }).click()
    expect((await finalizar).postDataJSON()).toEqual({
      respostas: [
        { questao_id: 101, alternativa_id: 1002 },
        { questao_id: 102, resposta: 'ON' },
      ],
    })

    await expect(page.getByRole('heading', { name: t('quizPlay.finished') })).toBeFocused()
    await expect(page.getByText(t('quizPlay.result', { acertos: 1, total: 2 }))).toBeVisible()
    const revisao = page.getByRole('region', { name: t('quizPlay.reviewWrong') }).or(
      page.locator('section').filter({ has: page.getByRole('heading', { name: t('quizPlay.reviewWrong') }) }),
    )
    const itens = revisao.first().getByRole('listitem')
    await expect(itens).toHaveCount(1)
    await expect(itens.first()).toContainText('Como se diz "maçã" em inglês?')
    await expect(itens.first()).toContainText('grape')
    await expect(itens.first()).toContainText('apple')

    // Jogar novamente reinicia na primeira questão
    await page.getByRole('button', { name: t('quizPlay.playAgain') }).click()
    await expect(page.getByText(t('quizPlay.progress', { current: 1, total: 2 }))).toBeVisible()
  })

  test('acertando tudo mostra "Gabaritou!"', async ({ page }) => {
    await open(page, '/quizes/1/play')
    await page.getByRole('radio', { name: 'apple' }).check()
    await page.getByRole('button', { name: t('quizPlay.submitAnswer') }).click()
    await page.getByRole('button', { name: t('quizPlay.next') }).click()
    await page.getByRole('textbox', { name: t('quizPlay.typeAnswer') }).fill('on')
    // Enter envia a resposta e, com o foco em "Ver resultado", Enter finaliza
    await page.keyboard.press('Enter')
    await expect(page.getByRole('button', { name: t('quizPlay.seeResult') })).toBeFocused()
    await page.keyboard.press('Enter')

    await expect(page.getByText(t('quizPlay.result', { acertos: 2, total: 2 }))).toBeVisible()
    await expect(page.getByText(t('quizPlay.perfectTitle'))).toBeVisible()
  })

  test('quiz sem questões mostra o estado vazio com atalho para o editor', async ({ page }) => {
    await open(page, '/quizes/2/play')

    await expect(page.getByRole('heading', { name: t('quizPlay.emptyTitle') })).toBeVisible()
    await expect(page.getByText(t('quizPlay.emptyDescriptionOwner'))).toBeVisible()
    await page.getByRole('link', { name: t('quizPlay.addQuestions') }).click()
    await expect(page).toHaveURL(/\/quizes\/edit\/2$/)
  })
})
