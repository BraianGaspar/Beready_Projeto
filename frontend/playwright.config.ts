import { defineConfig, devices } from '@playwright/test'

/**
 * Testes E2E do frontend (Playwright).
 *
 * - O app roda a partir de um build de produção (vite build + vite preview): é mais leve e
 *   estável que o servidor de dev e não depende de HMR/file watching.
 * - Nenhum teste fala com o backend real: toda chamada para E2E_API_URL é interceptada
 *   por `e2e/support/api.ts` (fixtures no formato { success, message, data }).
 * - Só Chromium e poucos workers (máquinas com pouca memória).
 */
const PORT = Number(process.env.E2E_PORT ?? 4317)
const BASE_URL = `http://localhost:${PORT}`
// URL "falsa" da API embutida no build de teste; o mock intercepta tudo que começa com ela
const API_URL = process.env.E2E_API_URL ?? 'http://localhost:8765'
// Build separado do `dist/` de produção (gerado com as variáveis abaixo)
const OUT_DIR = 'node_modules/.cache/e2e-dist'

process.env.E2E_API_URL = API_URL

export default defineConfig({
  testDir: './e2e',
  outputDir: './test-results',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  workers: process.env.CI ? 2 : Number(process.env.E2E_WORKERS ?? 2),
  timeout: 30_000,
  expect: { timeout: 7_000 },
  reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report' }]],

  use: {
    baseURL: BASE_URL,
    locale: 'pt-BR',
    timezoneId: 'America/Sao_Paulo',
    colorScheme: 'light',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'off',
  },

  projects: [
    {
      name: 'chromium-desktop',
      testIgnore: /responsive\.spec\.ts/,
      use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } },
    },
    {
      name: 'chromium-360',
      testMatch: /responsive\.spec\.ts/,
      use: {
        ...devices['Desktop Chrome'],
        viewport: { width: 360, height: 740 },
        isMobile: false,
        hasTouch: true,
      },
    },
  ],

  webServer: {
    command: `npx vite build --outDir ${OUT_DIR} --emptyOutDir && npx vite preview --outDir ${OUT_DIR} --port ${PORT} --strictPort`,
    url: BASE_URL,
    reuseExistingServer: !process.env.CI,
    timeout: 180_000,
    stdout: 'ignore',
    stderr: 'pipe',
    // Variáveis do build de teste (têm prioridade sobre o .env; nenhum segredo real)
    env: {
      VITE_API_URL: API_URL,
      VITE_APP_NAME: 'BeReady',
      VITE_APP_ENV: 'test',
      VITE_RECAPTCHA_SITE_KEY: 'e2e-recaptcha-site-key',
      VITE_RECAPTCHA_JS_URL: 'https://www.google.com/recaptcha/api.js',
      VITE_STRIPE_PUBLIC_KEY: 'pk_test_e2e',
    },
  },
})
