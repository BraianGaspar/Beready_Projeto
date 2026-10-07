# Testes E2E (Playwright)

Testes de ponta a ponta do frontend. **Não usam backend, banco nem reCAPTCHA**: toda chamada
para a API é interceptada e respondida com fixtures no formato real (`{ success, message, data }`).

## Como rodar

```bash
cd frontend
npx playwright install chromium   # uma vez (só o Chromium)
npm run test:e2e                  # todos os testes (desktop 1280px + 360px)
npm run test:e2e:ui               # modo interativo
npm run test:e2e:report           # abre o último relatório HTML
npx playwright test e2e/quizes.spec.ts --project=chromium-desktop   # um arquivo/projeto
```

O `webServer` do `playwright.config.ts` gera um build de teste (`vite build` em
`node_modules/.cache/e2e-dist`, com variáveis fictícias que têm prioridade sobre o `.env`) e o
serve com `vite preview` na porta 4317. Fora do CI, um servidor já aberto nessa porta é reaproveitado.
Workers: 2 (`E2E_WORKERS=1` em máquinas com pouca memória).

Projetos:

- `chromium-desktop` (1280 x 800): todos os specs, menos `responsive.spec.ts`;
- `chromium-360` (360 x 740): só `responsive.spec.ts` (rolagem horizontal e menu em gaveta).

## Como funcionam os mocks

- `support/fixtures.ts`: exporta `test`/`expect`. Todo teste recebe `api` (um `ApiMock` já
  instalado, começando como **visitante**) e as chamadas externas ficam bloqueadas: o script do
  reCAPTCHA vira um stub que devolve um token falso, imagens/fontes de terceiros respondem vazio e
  as páginas do Stripe (checkout/portal) viram uma página simples.
- `support/api.ts`: `ApiMock` intercepta `E2E_API_URL` (padrão `http://localhost:8765`) com
  `page.route`, responde CORS (inclusive preflight) e mantém um **estado** (`api.state`): criar
  flashcard aparece na lista, avaliar um card reagenda a revisão, cancelar a assinatura muda o
  `GET /user/assinatura`, as questões do quiz guardam o gabarito e `verificar`/`finalizar`
  corrigem de verdade.
  - Cenários: `api.asAnonymous()` (`POST /auth/refresh` -> 401) e
    `api.asUser(user, { permissions, assinatura, preferencias })` (refresh -> 200 com
    `access_token` + `user`); dados com `withFlashcards`, `withQuizes`, `withPreferencias`.
  - Sobrescrever uma rota no teste: `api.on('POST', '/flashcards', fail(403, 'msg', { limite: {...} }))`
    (a sobrescrita mais recente vence; `ok(data)` e `fail(status, message, errors)` montam o envelope).
  - Conferir chamadas: `await api.waitFor('POST', '/flashcards/1/revisao')` (devolve o `Request`)
    ou `api.calls(method, path)`. Rotas sem mock respondem 404 e ficam em `api.unhandled`.
- `support/data.ts`: fixtures (usuário, planos, assinaturas Gratuito/Premium recorrente,
  flashcards com/sem revisão vencida, quizes e questões).
- `support/i18n.ts`: `t('chave')` lê `src/locales/pt-BR.ts`, então os seletores
  (`getByRole`/`getByText`) usam os mesmos textos do app.

O login com senha não pode resolver o reCAPTCHA real: os testes validam a tela e, para o fluxo
logado, simulam a sessão pelo mock do `POST /auth/refresh` (ou do `POST /auth/login` com o token
do stub).
