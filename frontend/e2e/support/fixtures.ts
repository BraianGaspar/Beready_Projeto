import { test as base, expect, type Page } from '@playwright/test'
import { ApiMock } from './api'

/**
 * `test` com o mock da API já instalado (`api`, começa como visitante) e todo acesso
 * externo controlado: o script do reCAPTCHA é trocado por um stub (sem rede), imagens e
 * fontes de terceiros respondem vazio e as páginas do Stripe viram uma página simples.
 */

// Stub do reCAPTCHA v3: o login com senha recebe um token falso (o backend real recusaria)
const RECAPTCHA_STUB = `window.grecaptcha = {
  ready: function (cb) { cb() },
  execute: function () { return Promise.resolve('e2e-recaptcha-token') }
};`

const TRANSPARENT_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>'

export const blockExternal = async (page: Page): Promise<void> => {
  await page.route(/https:\/\/www\.google\.com\/recaptcha\//, (route) =>
    route.fulfill({ status: 200, contentType: 'application/javascript', body: RECAPTCHA_STUB }),
  )
  await page.route(/https:\/\/(www\.gstatic\.com|fonts\.googleapis\.com|fonts\.gstatic\.com)\//, (route) =>
    route.fulfill({ status: 200, contentType: 'image/svg+xml', body: TRANSPARENT_SVG }),
  )
  await page.route(/https:\/\/(billing|checkout)\.stripe\.com\//, (route) =>
    route.fulfill({
      status: 200,
      contentType: 'text/html',
      body: '<!doctype html><html lang="pt-BR"><title>Stripe (mock)</title><h1>Stripe (mock)</h1></html>',
    }),
  )
}

export const test = base.extend<{ api: ApiMock }>({
  api: async ({ page }, use) => {
    await blockExternal(page)
    const api = new ApiMock(page)
    await api.install()
    await use(api)
  },
})

export { expect }

/** Espera o app montar (o boot-loader do index.html some quando o Vue monta). */
export const waitForApp = async (page: Page): Promise<void> => {
  await expect(page.locator('.boot-loader')).toHaveCount(0)
}

/** Navega e espera o app montar. */
export const open = async (page: Page, path: string): Promise<void> => {
  await page.goto(path)
  await waitForApp(page)
}
