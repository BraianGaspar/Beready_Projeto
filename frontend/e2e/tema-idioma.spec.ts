import { makeUser } from './support/data'
import { expect, open, test } from './support/fixtures'
import { t } from './support/i18n'
import ar from '../src/locales/ar'
import enUS from '../src/locales/en-US'

test.describe('Temas pelas preferências do usuário', () => {
  test('tema escuro e modo daltônico aplicam as classes em <html> e <body>', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias({ tema: 'escuro', modo_daltonico: true })

    const prefs = api.waitFor('GET', '/preferencias/usuario/1')
    await open(page, '/dashboard')
    await prefs

    const html = page.locator('html')
    await expect(html).toHaveClass(/(^|\s)dark-mode(\s|$)/)
    await expect(html).toHaveClass(/(^|\s)daltonico-mode(\s|$)/)
    await expect(page.locator('body')).toHaveClass(/dark-mode/)
  })

  test('tema claro sem daltônico não aplica as classes', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias({ tema: 'claro', modo_daltonico: false })
    await open(page, '/dashboard')
    await expect.poll(() => api.calls('GET', '/preferencias/usuario/1').length).toBe(1)

    await expect(page.locator('html')).not.toHaveClass(/dark-mode|daltonico-mode/)
  })

  test('usuário sem preferências salvas (404) fica no tema claro', async ({ page, api }) => {
    api.asUser(makeUser())
    await page.emulateMedia({ colorScheme: 'dark' })
    await open(page, '/dashboard')
    await expect.poll(() => api.calls('GET', '/preferencias/usuario/1').length).toBe(1)

    await expect(page.locator('html')).not.toHaveClass(/dark-mode/)
  })

  test('páginas públicas seguem o tema do sistema', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias({ tema: 'claro', modo_daltonico: true })
    await page.emulateMedia({ colorScheme: 'dark' })
    await open(page, '/login')

    // Logado em "/login" vai para o dashboard; voltar a uma pública reaplica o sistema
    await expect(page).toHaveURL(/\/dashboard$/)
    await expect(page.locator('html')).toHaveClass(/daltonico-mode/)
    await expect(page.locator('html')).not.toHaveClass(/dark-mode/)

    await page.getByRole('button', { name: t('common.sair') }).click()
    await expect(page).toHaveURL(/\/login$/)
    await expect(page.locator('html')).toHaveClass(/dark-mode/)
    await expect(page.locator('html')).not.toHaveClass(/daltonico-mode/)
  })

  test('salvar preferências aplica o tema na hora', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias({ tema: 'claro' })
    await open(page, '/preferencias')

    await page.getByRole('button', { name: t('preferencias.escuro') }).click()
    await expect(page.getByRole('button', { name: t('preferencias.escuro') })).toHaveAttribute('aria-pressed', 'true')
    await page.getByRole('switch', { name: t('preferencias.modoDaltonico') }).check()

    const salvar = api.waitFor('POST', '/preferencias')
    await page.getByRole('button', { name: t('preferencias.salvar') }).click()
    expect((await salvar).postDataJSON()).toMatchObject({ usuario_id: 1, tema: 'escuro', modo_daltonico: true })

    await expect(page.locator('html')).toHaveClass(/dark-mode/)
    await expect(page.locator('html')).toHaveClass(/daltonico-mode/)
  })
})

test.describe('Idioma do usuário', () => {
  test('idioma_preferido "ar" aplica dir="rtl" e lang="ar"', async ({ page, api }) => {
    api.asUser(makeUser({ idioma_preferido: 'ar' }))
    await open(page, '/dashboard')

    const html = page.locator('html')
    await expect(html).toHaveAttribute('lang', 'ar')
    await expect(html).toHaveAttribute('dir', 'rtl')
    // Textos em árabe vindos do arquivo de mensagens
    await expect(page.getByRole('button', { name: ar.common.sair })).toBeVisible()
  })

  test('idioma em português mantém dir="ltr"', async ({ page, api }) => {
    api.asUser(makeUser({ idioma_preferido: 'pt-BR' }))
    await open(page, '/dashboard')

    await expect(page.locator('html')).toHaveAttribute('lang', 'pt')
    await expect(page.locator('html')).toHaveAttribute('dir', 'ltr')
  })

  test('trocar o idioma nas preferências grava idioma_preferido no usuário', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias()
    await open(page, '/preferencias')

    const update = api.waitFor('PUT', '/users/update/1')
    await page.getByRole('combobox', { name: t('preferencias.idioma') }).selectOption('ar')
    expect((await update).postDataJSON()).toEqual({ idioma_preferido: 'ar' })

    await expect(page.locator('html')).toHaveAttribute('dir', 'rtl')
    await expect(page.locator('html')).toHaveAttribute('lang', 'ar')

    // Voltar para português grava "pt-BR" (formato de users.idioma_preferido)
    const volta = api.waitFor('PUT', '/users/update/1')
    await page.getByRole('combobox', { name: ar.preferencias.idioma }).selectOption('pt')
    expect((await volta).postDataJSON()).toEqual({ idioma_preferido: 'pt-BR' })
    await expect(page.locator('html')).toHaveAttribute('dir', 'ltr')
  })

  test('falha ao gravar o idioma avisa mas mantém a troca local', async ({ page, api }) => {
    api.asUser(makeUser()).withPreferencias()
    api.on('PUT', '/users/update/:id', { status: 500, body: { success: false, message: 'Erro' } })
    await open(page, '/preferencias')

    await page.getByRole('combobox', { name: t('preferencias.idioma') }).selectOption('en')
    await expect(page.locator('html')).toHaveAttribute('lang', 'en')
    await expect(page.getByRole('alert').filter({ hasText: enUS.preferencias.idiomaNaoSalvo })).toBeVisible()
  })
})
