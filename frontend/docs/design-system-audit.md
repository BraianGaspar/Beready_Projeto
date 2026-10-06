# Auditoria do design system — frontend BeReady

> Fase 1 (fundação) concluída em 05/10/2026. Este documento orienta a **fase 2** (migração das telas).
> Regras e componentes: [`design-system.md`](./design-system.md).
> Métricas geradas por varredura de `src/**/*.vue` e `src/styles/**/*.css` (contagem de ocorrências).

## Legenda

- **hex/rgb**: cores fixas fora dos tokens.
- **fallback**: `var(--x, #valor)` (proibido no padrão novo).
- **!imp**: `!important` (guerra de especificidade).
- **dark**: regras `.dark-mode` escritas à mão (sinal de que a tela não usa tokens).
- **Prioridade**: **Alta** = quebra no escuro/daltônico, em 360px ou em acessibilidade · **Média** = funciona via aliases/overrides legados, mas fora do padrão · **Baixa** = ajuste fino.

## Problemas transversais (valem para quase todas as telas)

| # | Problema | Onde | Como resolver na fase 2 |
|---|---|---|---|
| T1 | ~1.500 cores hex/rgb fixas; 3 paletas concorrentes (`#667eea/#764ba2`, `#7c3aed`, `#3b82f6`) | todos os CSS de `styles/views` e `styles/components` | só tokens `--color-*`; marca = `--color-primary` / `--gradient-brand` |
| T2 | ~80 nomes de variáveis divergentes (`--bg-primary`, `--bg-card-dark`, `--premium-bg`, `--plano-text`…) quase sempre com fallback fixo | 20 arquivos | hoje funcionam por **aliases temporários** (`styles/legacy-aliases.css`); trocar pelos tokens |
| T3 | Tema escuro feito com regras `.dark-mode X {…}` à mão + overrides globais com `!important` em nomes genéricos (`.stat-card`, `.hero-title`, `.form-input`…) | `themes.css` antigo (agora `legacy-overrides.css`), `prompt-detail.css` (187 `!important`) | usar tokens (reagem sozinhos); apagar regras `.dark-mode` da tela |
| T4 | Daltônico antigo apenas "acinzentava" botões/badges; status em vermelho/verde sem ícone/texto | `FlashcardStudy`, `QuizPlay`, `progresso`, `dashboard` | `BaseBadge icon`, `BaseAlert`, tokens `--color-success/danger` (paleta Okabe-Ito no modo daltônico) |
| T5 | Hero (faixa com gradiente + botão voltar) reimplementado em cada tela (`.xxx-hero`, `.hero-back-btn`) | flashcards, quizes, prompts, tags, progresso, preferências, profile-edit, frases/imagens/traduções | `PageHeader` (`back-to`, `icon`, slot `actions`) |
| T6 | Botões recriados por tela (`.btn-primary`, `.btn-save`, `.btn-cancel`, `.btn-danger`, `.hero-btn`, `.btn-study`, `.btn-submit`, `.btn-action-*`) | todas | `BaseButton` (variant/size/loading/icon/to) |
| T7 | Inputs/selects/textarea recriados (`.form-input`, `.form-select`, `.form-textarea`), labels sem `for`, erros sem `aria-describedby` | auth, quizes (add/edit), preferências, admin, prompts | `BaseInput`, `BaseSelect`, `BaseTextarea`, `BaseCheckbox`, `BaseSwitch` |
| T8 | Loading (`.loading-state .spinner`), estado vazio (`.empty-state`), modais próprios (UserProfile, QuizEdit, quizes, AdminPanel) | listas e detalhes | `BaseSpinner center`, `EmptyState`, `BaseModal` / `ConfirmModal` |
| T9 | ~20 telas colam `<svg>` Heroicons com classes Tailwind mortas (`h-5 w-5`, Tailwind não era carregado) e emoji como ícone (❌ ✨ ♾️) | ver tabelas | `<BaseIcon name="…">` (registro em `ui/icons.ts`) |
| T10 | Grades com `minmax(350–380px, 1fr)` e sem `min()` | flashcards, prompts, quizes, tags, frases, traduções, prompt-detail | `.u-grid-auto` ou `minmax(min(100%, 18rem), 1fr)` |
| T11 | Propriedades físicas (`left/right`, `margin-left`…) quebram em árabe (`dir="rtl"`) | ~90 ocorrências | propriedades lógicas (`inset-inline-*`, `margin-inline-*`, `text-align: start/end`) |
| T12 | Media queries só `max-width` com breakpoints soltos (380/640/1366/1920…) | vários | mobile-first `min-width` com 480/768/1024/1280 |

## Resumo por módulo (estado atual, após a fase 1)

| Módulo | hex/rgb | fallback | !imp | dark à mão | Emoji | Prioridade geral |
|---|---|---|---|---|---|---|
| auth (login, register, forgot/reset, profile, profile-edit, oauth) | 111 / 39 | 14 | 4 | 36 | 0 | Média (telas públicas já com cores em tokens) |
| flashcards | 218 / 39 | 32 | 29 | 10 | 4 | **Alta** |
| quizes | 199 / 43 | 93 | 31 | 34 | 1 | **Alta** |
| prompts + traduções + imagens + frases | 274 / 72 | 37 | 220 | 39 | 0 | **Alta** |
| tags | 51 / 19 | 26 | 0 | 4 | 0 | Média |
| progresso | 42 / 12 | 18 | 18 | 19 | 0 | Média |
| preferências | 33 / 11 | 0 | 0 | 0 | 0 | **Alta** |
| admin (AdminPanel, PlanoManager, RoleManager) | 326 / 22 | 40 | 0 | 106 | 3 | Média |
| globais (home, dashboard, planos) | 132 / 14 | 77 | 1 | 35 | 0 | Média |

## auth

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `UserLogin.vue` + `users/login.css` | **Já usa** `BaseCard/BaseInput/BaseButton` e tokens (fase 1). Falta: botões sociais como componente (hoje `.btn-social`), `font-size` em px | Baixa |
| `UserRegister.vue` + `users/register.css` | Cores do modo claro já em tokens; bloco `.dark-mode` antigo com 19 hex fixos; inputs próprios (`.form-input`, `.input-error`) sem `aria-describedby`; barra de força da senha só por cor; `px` | Média |
| `ForgotPassword.vue` + `users/forgot-password.css` | Cores em tokens (fase 1); inputs/botão/flash-message próprios → `BaseInput`, `BaseButton`, `BaseAlert`; ícone de 1em (classe Tailwind morta) | Média |
| `ResetPassword.vue` + `users/reset-password.css` | Igual ao forgot + barra de força só por cor (adicionar texto/ícone) | Média |
| `OAuthCallback.vue` + `auth/oauth-callback.css` | Cores em tokens; spinner e botão próprios → `BaseSpinner`, `BaseButton`; `min-width: 300px` | Baixa |
| `UserProfile.vue` + `users/profile.css` | 31 hex, 9 fallbacks, **nenhuma regra dark própria** (depende de overrides legados `.profile-*`); modal de exclusão próprio (`.modal-icon danger`) → `ConfirmModal`; badge de status (`.profile-status-badge`) → `BaseBadge icon` | **Alta** |
| `ProfileEdit.vue` + `users/profile-edit.css` | 61 hex, 22 rgb, 11 gradientes, 23 regras dark à mão; formulário próprio (9 inputs) → `BaseInput`; hero próprio → `PageHeader` | Média |

## flashcards

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `FlashcardList.vue` + `flashcards/flashcards.css` | 95 hex, 32 fallbacks, 29 `!important`; hero, filtros, cards, badges, loading, vazio, botões próprios; grade `minmax(380px)` | **Alta** |
| `FlashcardStudy.vue` (CSS no próprio SFC, 43 hex) + `flashcards/flashcard-study.css` | **Sem dark**; acerto/erro em verde/vermelho com emoji ❌ ✨ (`.text-red/.text-green`) → `BaseBadge`/`StatCard` com ícone; card de virar sem foco de teclado visível | **Alta** |
| `FlashcardView.vue` + `flashcards/flashcard-view.css` | 33 hex, **sem dark**; já usa `ConfirmModal` (novo) | **Alta** |

## quizes

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `QuizList.vue` + `quizes/quizes.css` | 107 hex, 30 rgb, 66 fallbacks, 31 `!important`; modal próprio com vars `--modal-*` → `BaseModal`; grade `minmax(350px)` | **Alta** |
| `QuizAdd.vue` / `QuizEdit.vue` + `quizes/quiz-form.css` | Formulário próprio (6 inputs) → `BaseInput/BaseSelect/BaseTextarea`; modal de exclusão próprio (QuizEdit) → `ConfirmModal`; 13 fallbacks | Média |
| `QuizView.vue` + `quizes/quiz-view.css` | 45 hex, 14 fallbacks, regras dark/daltônico à mão | Média |
| `QuizPlay.vue` (CSS no SFC, 11 hex) | Botões certo/errado (`.btn-action-wrong`…) só por cor, **sem dark**, emoji | **Alta** |

## prompts + traduções + imagens + frases

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `PromptList.vue` + `prompts/prompts.css` | 89 hex, 37 fallbacks, 33 `!important`; grade `minmax(380px)`; cards/badges/vazio próprios | **Alta** |
| `PromptDetail.vue` + `prompts/prompt-detail.css` | **187 `!important`**, 26 regras dark; grade `minmax(350px) !important` | **Alta** |
| `TraducoesPrompt.vue` + `traducoes/traducoes.css` | 37 hex, 7 gradientes, **sem dark** | **Alta** |
| `ImagensPrompt.vue` + `imagens/imagens.css` | 43 hex, 17 rgb, **sem dark** | **Alta** |
| `FrasesPrompt.vue` + `frases/frases.css` | 52 hex, 11 gradientes, **sem dark** | **Alta** |

## tags

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `TagList.vue` + `tags/tags.css` | 51 hex, 26 fallbacks; cards e formulário próprios; cor da tag como único indicador; grade `minmax(350px)` | Média |

## progresso

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `ProgressoDashboard.vue` + `progresso/progresso.css` | Cards de estatística próprios com classes estilo Tailwind (`bg-blue-100`…) definidas localmente → `StatCard`; 18 `!important`, 18 fallbacks; tendência só por cor | Média |

## preferências

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `UserPreferences.vue` + `preferencias/preferencias.css` | **A tela que liga o tema não tem tema escuro próprio** (0 regras, 33 hex); toggles `.toggle-switch` sem texto associado (o `<label>` do grupo não aponta para o input) → `BaseSwitch`; botões de tema/dificuldade sem `aria-pressed`; espaços em branco onde havia emoji | **Alta** |

## admin

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `AdminPanel.vue` + `admin/admin-panel.css` | 114 hex, 22 fallbacks, 31 regras dark; tabela sem rolagem horizontal controlada; badges de status (`.status-badge`) só por cor; emoji | Média |
| `components/admin/PlanoManager.vue` + `components/PlanoManager.css` | 121 hex, 43 regras dark, emoji ♾️ como "ilimitado"; já usa `ConfirmModal` (novo) | Média |
| `components/admin/RoleManager.vue` + `components/RoleManager.css` | 91 hex, 32 regras dark; botões de ícone sem `aria-label` | Média |

## views globais

| Tela / arquivo | O que foge do padrão | Prioridade |
|---|---|---|
| `views/_global/HomePage.vue` + `views/home.css` | **Já em tokens** e com `BaseButton` (fase 1); só `font-size`/`padding` fixos | Baixa |
| `views/_global/DashboardPage.vue` + `views/dashboard.css` | 28 hex, **sem dark próprio** (depende de overrides legados); stat cards e feature cards próprios → `StatCard`/`BaseCard interactive`; `<main>` interno trocado por `<div>` (o layout já tem `<main>`) | Média |
| `views/PlanosPage.vue` + `views/PlanosPage.css` | 104 hex, **77 fallbacks**, 35 dark + 18 daltônico à mão; badges/botões próprios | Média |

## Já resolvido na fase 1 (não migrar de novo)

`AppNavbar` (+ `Navbar.ts`, `navbar.css`), `AuthenticatedLayout`, toasts (`useAlert` + `ui/AlertContainer` + `BaseAlert`), `ConfirmModal` (agora sobre `BaseModal`), loader de boot (`index.html`), base global (`styles/main.css`), `useTheme` e `usePreferencias` (aplicação do tema), `UserLogin` e `HomePage` (componentes), cores das telas públicas (login, register, forgot, reset, oauth, home).

## Pendências que dependem do usuário

Arquivos sem uso que **não puderam ser apagados** nesta fase (remoção bloqueada pela política de permissões) — apagar manualmente:

- `src/shared/components/common/` (inteira: `AlertContainer.vue`, `AppAlert.vue/.ts`, `AppButton.vue`, `AppCard.vue`, `AppInput.vue/.ts`, `ConfirmModal.vue`)
- `src/styles/components/alert-container.css`, `alert.css`, `button.css`, `input.css`, `confirm-modal.css`
- `src/assets/main.css`, `src/style.css`, `tailwind.config.js` e depois `npm uninstall tailwindcss`
