# Design system BeReady — guia

Este guia é **obrigatório** para qualquer tela nova ou alterada. Se algo não estiver coberto aqui, use um token
existente e um componente de `src/shared/components/ui` antes de criar algo novo.
[`design-system-audit.md`](./design-system-audit.md) é só histórico da migração.

**Estado atual: tudo em Tailwind.** Não há CSS por tela nem `<style>` em `.vue`: o visual de telas e componentes
é feito com utilitários do Tailwind no template, e o Tailwind só conhece valores que apontam para os tokens.

## 1. Arquitetura

| Arquivo | Papel |
|---|---|
| `src/styles/tokens.css` | **Fonte única**: tokens e os 4 temas (claro, escuro, daltônico, daltônico+escuro). Importado primeiro em `main.ts` |
| `tailwind.config.js` | Tema do Tailwind **substituído pelos tokens** + extensões e plugin do projeto ([seção 7](#7-tailwind)) |
| `src/styles/tailwind.css` | Camadas do Tailwind + **reset base** em `@layer base` (box-sizing, body, links, foco visível, `::selection`, scrollbar, gutter/navbar por breakpoint, movimento reduzido) + `.ui-icon` em `@layer components`. Importado em `main.ts` depois de `tokens.css` |
| `src/shared/components/ui/` | Componentes reutilizáveis (só utilitários, sem `<style>`) + `index.ts` + `icons.ts` + `control.ts` (classes comuns de input/textarea/select) + `transitions.ts` (classes de `<Transition>`) |
| `src/shared/composables/useTheme.ts` | Único lugar que escreve `.dark-mode` / `.daltonico-mode` em `<html>` e `<body>` |
| `src/shared/composables/useFocusTrap.ts` | Focus trap para modais/drawers |
| `scripts/check-contrast.mjs` | Verificação WCAG dos tokens (`node scripts/check-contrast.mjs`) |

Não existem outros arquivos `.css` em `src/`. Um sistema só: os tokens são a fonte; o Tailwind é a forma de
aplicá-los no template.

## 2. Temas

| Tema | Como é ativado | Observação |
|---|---|---|
| Claro | padrão (`:root`) | |
| Escuro | `.dark-mode` | preferência `tema = 'escuro'` |
| Daltônico | `.daltonico-mode` | preferência `modo_daltonico`; só troca as cores de status |
| Daltônico + escuro | `.dark-mode.daltonico-mode` | combinação das duas |

Regras de aplicação (`useTheme.ts`):

- **Rotas públicas** (`/`, `/login`, `/register`, `/forgot-password`, `/reset-password/*`, `/oauth-callback`) e qualquer
  rota **sem sessão**: seguem `prefers-color-scheme` do sistema (reagem à troca ao vivo), **sem** daltônico.
- **Logado**: preferências de `/preferencias/usuario/{id}`, buscadas uma vez por usuário. Sem preferência salva (404) → claro.
- `usePreferencias` aplica o tema ao salvar chamando `applyTheme()` — **nunca** manipule as classes diretamente.

Paleta daltônica (Okabe-Ito): sucesso = **azul**, perigo = **vermelhão/laranja**, aviso = **âmbar/amarelo**,
info = **púrpura**. Segura para deuteranopia, protanopia e tritanopia. Mesmo assim, **status nunca depende só de
cor**: sempre ícone e/ou texto (os componentes de status já fazem isso).

### Como fazer algo reagir ao escuro/daltônico

1. Use **apenas classes do tema** (`bg-surface`, `text-text-muted`, `shadow-sm`…), que apontam para tokens e
   mudam sozinhas por tema.
2. **Nunca** cor fixa (`#fff`, `rgb()`, `white`, nomes de cor, `bg-[#…]`).
3. **Nunca** `var(--x, #fallback)`. Fallback só é permitido para custom property *local* apontando para outro token
   (ex.: a cor `tag` = `var(--tag-color, var(--color-border-strong))`).
4. **Nunca** `dark:`/`daltonico:` para cor. Se um token não existir para o caso, crie-o em `tokens.css` nos 4 temas,
   exponha no `tailwind.config.js` e rode o script de contraste.
5. Status (sucesso/erro/aviso/info): `BaseBadge icon`, `BaseAlert`, `StatCard variant`, ou tokens
   `--color-{status}` + `-soft` **com** ícone/texto.

Exceções aceitas: cores de marca de terceiros em ícones (Google/Facebook/LinkedIn) e o loader de `index.html`
(roda antes do CSS; espelha `--color-bg`, `--color-primary-soft`, `--color-primary` — atualize junto).

## 3. Tokens

### Cores (valores claro / escuro)

| Token | Uso | Claro | Escuro |
|---|---|---|---|
| `--color-bg` | fundo da página | `#f8fafc` | `#0f172a` |
| `--color-surface` | cards, modais, navbar, inputs | `#ffffff` | `#1e293b` |
| `--color-surface-muted` | blocos secundários, rodapés de card, tabelas | `#f1f5f9` | `#273449` |
| `--color-surface-hover` | hover de itens neutros | `#e9eef5` | `#314158` |
| `--color-border` | bordas e divisórias | `#e2e8f0` | `#334155` |
| `--color-border-strong` | borda de controles (≥ 3:1) | `#7a8aa0` | `#7587a0` |
| `--color-text` | texto principal | `#1e293b` | `#f1f5f9` |
| `--color-text-muted` | texto secundário (qualquer superfície) | `#475569` | `#cbd5e1` |
| `--color-text-subtle` | placeholder, metadados (**só** sobre `--color-surface`) | `#64748b` | `#a3b1c4` |
| `--color-text-inverse` | texto sobre fundo invertido | `#ffffff` | `#0f172a` |
| `--color-primary` / `-hover` / `-active` | marca, links, botão primário | `#4f46e5` | `#818cf8` |
| `--color-primary-contrast` | texto sobre `--color-primary` | `#ffffff` | `#0f172a` |
| `--color-primary-soft` / `-soft-text` | fundo suave de marca / texto sobre ele | `#eef2ff` / `#4338ca` | `#2b2f63` / `#c7d2fe` |
| `--color-success` / `-hover` / `-contrast` / `-soft` | sucesso (daltônico: azul); `-hover` = hover do botão `success` | `#15803d` / `#166534` | `#4ade80` / `#86efac` |
| `--color-warning` / `-contrast` / `-soft` | aviso (daltônico: âmbar) | `#b45309` | `#fbbf24` |
| `--color-danger` / `-hover` / `-contrast` / `-soft` | erro/perigo (daltônico: vermelhão) | `#b91c1c` | `#f87171` |
| `--color-info` / `-contrast` / `-soft` | informação (daltônico: púrpura) | `#1d4ed8` | `#60a5fa` |
| `--color-focus-ring` | anel de foco | `#4f46e5` | `#a5b4fc` |
| `--color-overlay` | fundo atrás de modal/drawer | slate 55% | quase preto 70% |
| `--color-skeleton` | placeholders de carregamento | `#e2e8f0` | `#334155` |
| `--color-hero-from` / `-to` / `--gradient-brand` | gradiente da marca (heróis) | `#4f46e5→#6d28d9` | `#312e81→#4c1d95` |
| `--color-hero-text` / `-muted` | texto sobre o gradiente | branco / 88% | branco / 85% |

Uso dos status: texto colorido sobre `--color-surface` ou `-soft` → `var(--color-X)`; fundo sólido →
`background: var(--color-X); color: var(--color-X-contrast)`.

### Demais tokens

| Grupo | Tokens |
|---|---|
| Tipografia | `--font-family-base`, `--font-family-mono`, `--font-size-xs` (12) `-sm` (14) `-base` (16) `-lg` (18) `-xl` (20) `-2xl` (24) `-3xl` (30) `-4xl` (36) `-display` (clamp 32–56), `--font-weight-regular/medium/semibold/bold`, `--line-height-tight/base/relaxed` |
| Espaçamento (4px) | `--space-0` `-1` (4) `-2` (8) `-3` (12) `-4` (16) `-5` (20) `-6` (24) `-8` (32) `-10` (40) `-12` (48) `-16` (64) |
| Raios | `--radius-sm` (6) `-md` (8) `-lg` (12) `-xl` (16) `-2xl` (24) `-full` |
| Sombras | `--shadow-sm` `-md` `-lg` `-xl` (mais fortes no escuro) |
| Controles | `--control-height-sm` (36) `-md` (44, alvo de toque) `-lg` (52), `--border-width`, `--focus-ring-width`, `--focus-ring-offset` |
| Layout | `--container-sm` (640) `-md` (960) `-lg` (1200) `-xl` (1400), `--page-gutter` (16 → 24 ≥768 → 32 ≥1024, no reset base de `tailwind.css`), `--navbar-height` (64; 56 < 768), `--drawer-width` (min(20rem, 88vw)) |
| Camadas | `--z-dropdown` 100 · `--z-sticky` 200 (navbar) · `--z-drawer` 300 · `--z-modal` 400 · `--z-toast` 500 · `--z-tooltip` 600 |
| Movimento | `--duration-fast/base/slow` (120/200/320ms, **0 com prefers-reduced-motion**), `--easing-standard`, `--easing-emphasized`, `--transition-base`, `--duration-spin` (800ms) / `--duration-spin-fast` (700ms) — giro de spinner, **não** zera (a classe `.motion-safe` deixa mais lento), `--scale-enter` (0.98) / `--scale-enter-sm` (0.97) — escala inicial de modal/toast |
| Tamanhos complementares (só `:root`, sem variação por tema) | `--space-0-5` (2px) `-1-5` (6) `-11` (44) `-14` (56) `-48` (192) `-64` (256) `-72` (288, mínimo padrão de grade fluida), `--space-nudge` (0.1rem) / `--space-nudge-em` (0.1em) — ajuste óptico de ícone; `--icon-size-em` (1em) `-em-md` (1.1em) `-em-lg` (1.25em) `--icon-size-check` (0.85rem); `--font-size-icon-lg` (1.75rem) `-icon-xl` (2rem); `--letter-spacing-label` (0.04em); `--opacity-disabled` (0.55); `--z-raised` (1); `--modal-width-sm/md/lg/xl` (26/32/44/60rem); `--toast-width` (26rem); `--toast-progress-height` (3px); `--measure-sm` (32rem) |
| Tamanhos das telas (só `:root`) | `--space-4-5` (18px) `-7` (28) `-9` (36) `-13` (52) `-18` (72) `-20` (80) — ícones, avatares, medalhões; `--space-28` … `--space-112` (7–28rem: `28` `34` `36` `40` `44` `56` `60` `68` `70` `80` `88` `96` `112`) — mínimos de grade fluida, bases flex, larguras máximas; `--space-fluid-hero` / `--space-fluid-section` (clamp do respiro vertical da home); `--font-size-fluid-lg-2xl` / `-2xl-3xl` / `-2xl-4xl` / `-3xl-4xl` (clamp entre dois tamanhos da escala); `--flashcard-min-height` (clamp(16rem, 50vh, 22rem)); `--perspective-card` (1000px); `--aspect-photo` (4 / 3) |

### Contraste (WCAG AA) — resultado de `node scripts/check-contrast.mjs src/styles/tokens.css md`

| Texto / elemento | Fundo | Mín. | claro | escuro | daltônico claro | daltônico escuro |
|---|---|---|---|---|---|---|
| `--color-text` | `--color-bg` | 4.5 | 13.98 | 16.30 | 13.98 | 16.30 |
| `--color-text` | `--color-surface` | 4.5 | 14.63 | 13.35 | 14.63 | 13.35 |
| `--color-text` | `--color-surface-muted` | 4.5 | 13.35 | 11.45 | 13.35 | 11.45 |
| `--color-text-muted` | `--color-bg` | 4.5 | 7.24 | 12.02 | 7.24 | 12.02 |
| `--color-text-muted` | `--color-surface` | 4.5 | 7.58 | 9.85 | 7.58 | 9.85 |
| `--color-text-muted` | `--color-surface-muted` | 4.5 | 6.92 | 8.45 | 6.92 | 8.45 |
| `--color-text-subtle` | `--color-surface` | 4.5 | 4.76 | 6.72 | 4.76 | 6.72 |
| `--color-primary` | `--color-surface` | 4.5 | 6.29 | 4.90 | 6.29 | 4.90 |
| `--color-primary` | `--color-bg` | 4.5 | 6.01 | 5.98 | 6.01 | 5.98 |
| `--color-primary-contrast` | `--color-primary` | 4.5 | 6.29 | 5.98 | 6.29 | 5.98 |
| `--color-primary-contrast` | `--color-primary-hover` | 4.5 | 7.90 | 8.96 | 7.90 | 8.96 |
| `--color-primary-soft-text` | `--color-primary-soft` | 4.5 | 7.07 | 8.33 | 7.07 | 8.33 |
| `--color-hero-text` | `--color-hero-from` | 4.5 | 6.29 | 11.42 | 6.29 | 11.42 |
| `--color-hero-text` | `--color-hero-to` | 4.5 | 7.10 | 10.95 | 7.10 | 10.95 |
| `--color-danger-contrast` | `--color-danger-hover` | 4.5 | 8.31 | 9.74 | 8.22 | 10.33 |
| `--color-success-contrast` | `--color-success-hover` | 4.5 | 7.13 | 10.62 | 9.74 | 9.20 |
| `--color-border-strong` | `--color-surface` | 3 | 3.52 | 3.99 | 3.52 | 3.99 |
| `--color-focus-ring` | `--color-surface` | 3 | 6.29 | 7.34 | 6.29 | 7.34 |
| `--color-focus-ring` | `--color-bg` | 3 | 6.01 | 8.96 | 6.01 | 8.96 |
| `--color-success` | `--color-surface` | 4.5 | 5.02 | 8.40 | 7.14 | 6.34 |
| `--color-success` | `--color-success-soft` | 4.5 | 4.57 | 7.72 | 6.04 | 6.15 |
| `--color-success-contrast` | `--color-success` | 4.5 | 5.02 | 8.55 | 7.14 | 7.03 |
| `--color-warning` | `--color-surface` | 4.5 | 5.02 | 8.76 | 6.19 | 11.06 |
| `--color-warning` | `--color-warning-soft` | 4.5 | 4.51 | 8.30 | 5.44 | 9.86 |
| `--color-warning-contrast` | `--color-warning` | 4.5 | 5.02 | 9.98 | 6.19 | 11.54 |
| `--color-danger` | `--color-surface` | 4.5 | 6.47 | 5.29 | 6.18 | 7.18 |
| `--color-danger` | `--color-danger-soft` | 4.5 | 5.30 | 5.51 | 5.13 | 7.12 |
| `--color-danger-contrast` | `--color-danger` | 4.5 | 6.47 | 6.68 | 6.18 | 8.47 |
| `--color-info` | `--color-surface` | 4.5 | 6.70 | 5.75 | 6.96 | 7.22 |
| `--color-info` | `--color-info-soft` | 4.5 | 5.49 | 5.70 | 5.72 | 7.23 |
| `--color-info-contrast` | `--color-info` | 4.5 | 6.70 | 6.73 | 6.96 | 8.31 |

0 falhas. Alterou um token de cor? Rode o script de novo (sai com código 1 se algo ficar abaixo do mínimo).

## 4. Componentes (`import { … } from '@/shared/components/ui'`)

Todos: `<script setup lang="ts">`, props tipadas, só tokens, foco visível, propriedades lógicas (RTL), textos via i18n.
Componentes de campo passam atributos extras (`name`, `autocomplete`, `maxlength`, `inputmode`…) para o controle
nativo e `class`/`style` para o wrapper.

| Componente | Props principais | Slots / eventos |
|---|---|---|
| `BaseButton` | `variant` primary·secondary·ghost·danger·success·ghost-danger, `size` sm·md·lg, `type`, `loading`, `disabled`, `icon`, `iconEnd`, `block`, `wrap` (texto quebra, respiro lateral menor), `stacked` (ícone acima do texto, compacto), `to` (vira `<router-link>`) | default; `@click` nativo. Só ícone ⇒ passe `aria-label` |
| `BaseIcon` | `name` (ver `icons.ts`), `size`, `label` (sem label = decorativo) | — |
| `BaseInput` | `v-model` (aceita `.trim`/`.number`), `label`, `type`, `placeholder`, `hint`, `error`, `required`, `disabled`, `icon`, `id` | `@blur`. `type="password"` ganha botão mostrar/ocultar |
| `BaseTextarea` | `v-model`, `label`, `rows`, `maxlength`, `showCount`, `mono` (fonte monoespaçada), `hint`, `error`… | `@blur` |
| `BaseSelect` | `v-model`, `label`, `options: {value,label,disabled?}[]`, `placeholder`, `hint`, `error`… | default (alternativa: `<option>`s) |
| `BaseCheckbox` | `v-model` boolean, `label`, `hint`, `disabled` | default (label rico) |
| `BaseSwitch` | `v-model` boolean, `label`, `hint`, `disabled` (`role="switch"`, ícone de check) | default |
| `BaseField` | `fieldId`, `label`, `hint`, `error`, `required` | default — moldura para controles customizados |
| `BaseCard` | `as`, `title`, `subtitle`, `titleTag`, `padding` none·sm·md·lg, `interactive`, `muted`, `highlight` primary·success·warning·danger (borda + anel no tom; reforço visual, o significado vem de badge/texto) | default, `header`, `actions`, `footer` |
| `BaseModal` | `v-model`, `title`, `description`, `ariaLabel`, `size` sm·md·lg·xl, `closeOnOverlay`, `closeOnEsc`, `hideClose` | default, `header`, `footer` (`{ close }`); `@close`. Focus trap, Esc, foco devolvido, scroll travado |
| `ConfirmModal` | `v-model`, `title`, `message`, `confirmText`, `type` danger·warning·info, `itemName`, `warning` (2ª linha com ícone, ex. `$t('confirmModal.irreversible')`), `loading` | default (conteúdo extra abaixo da mensagem); `@confirm` (API antiga mantida) |
| `BaseBadge` | `variant` neutral·primary·success·warning·danger·info, `size` sm·md, `icon` (nome ou `true` = ícone do status), `solid`, `wrap` (texto longo quebra linha) | default |
| `BaseAlert` | `variant` success·warning·danger·info, `title` (padrão: rótulo do status), `message`, `dismissible` | default; `@close` |
| `BaseSpinner` | `size` sm·md·lg, `label`, `showLabel`, `center` | — (`role="status"`) |
| `EmptyState` | `title`, `description`, `icon` (padrão `inbox`), `titleTag`, `compact` | default = ações |
| `PageContainer` | `size` sm·md·lg·xl·full, `as` | default |
| `PageHeader` | `title` (é o `<h1>`), `subtitle`, `icon`, `backTo`, `backLabel`, `variant` hero·plain | default (abaixo do subtítulo), `actions` |
| `StatCard` | `label`, `value`, `icon`, `variant`, `hint`, `trend` up·down·flat, `trendLabel`, `to` | `hint` |
| `BaseProgress` | `value`, `min` (0), `max` (100), `label` (nome acessível), `valueText` (padrão: % no idioma), `variant` primary·success·warning·danger·info, `size` sm·md·lg, `showLabel`, `decorative` | `label`. `role="progressbar"` + `aria-valuemin/max/now/valuetext`; anima `inline-size` (0 com movimento reduzido; enche da direita em RTL) |
| `BaseTabs` | `v-model` (id da aba ativa), `tabs: TabItem[]` (`{ id, label, icon?, disabled? }`), `label` (nome do `tablist`), `embedded` (dentro de `BaseCard padding="none"`) | um slot por aba com o nome do `id` (`{ tab }`). ←/→ (invertidas em RTL), Home/End, roving tabindex, `aria-selected`/`aria-controls`; só o painel ativo é renderizado; a barra rola sozinha em telas estreitas |
| `BaseTable` | `columns: TableColumn[]` (`{ key, label, align?, hideLabel? }`), `rows`, `rowKey`, `caption`, `captionHidden`, `minWidth` (largura mínima ≥ 768px) | `cell-<key>` (`{ row, value, index }`; sem slot mostra `row[key]`). < 768px: cada linha vira card com rótulo por célula; ≥ 768px: `<table>`; rolagem contida no wrapper (que vira região focável só quando rola) |
| `AlertContainer` | — (montado uma vez em `App.vue`) | alimentado por `useAlert()` |

Notificações: continue usando `useAlert()` (`success/error/warning/info(msg, duração?)`); `error` vira variante `danger`.

Quando usar cada variante de `BaseButton`:

| Variante | Uso |
|---|---|
| `primary` / `secondary` / `ghost` | ação principal / secundária / terciária |
| `danger` | ação destrutiva principal (confirmar exclusão, "Errei" na autoavaliação) |
| `ghost-danger` | ação destrutiva secundária em listas/cards (lixeira, "Cancelar assinatura") — **não** pinte `ghost` de vermelho com CSS local |
| `success` | resposta positiva de status ("Acertei", "Fácil"); sempre com ícone + texto |

Outros utilitários do design system:

- Nível de dificuldade: `getNivelVariant(nivel)` (`@/shared/utils/nivelDificuldade`) → `'success' | 'warning' | 'danger'`
  para `BaseBadge`; o texto vem de `$t(getNivelLabelKey(nivel))`. Não crie mapas locais de nível → cor.
- Ícones adicionados nesta fase: `key` (senha/recuperação), `sun`/`moon` (tema), `trophy` (resultado/conquista).
- Ícones direcionais (`arrow-left`, `arrow-right`, `chevron-left`, `chevron-right`) são espelhados automaticamente pelo `BaseIcon` em RTL (árabe). Não adicione `scaleX(-1)` nas telas: isso inverteria duas vezes.

### Exemplos

```vue
<script setup lang="ts">
import {
  PageContainer, PageHeader, BaseButton, BaseCard, BaseInput, BaseSelect, BaseSwitch,
  BaseBadge, BaseSpinner, EmptyState, ConfirmModal, StatCard,
} from '@/shared/components/ui'
</script>

<template>
  <PageContainer>
    <PageHeader :title="$t('tags.title')" :subtitle="$t('tags.subtitle')" icon="tag" back-to="/dashboard">
      <template #actions>
        <BaseButton icon="plus" @click="openCreate">{{ $t('tags.nova') }}</BaseButton>
      </template>
    </PageHeader>

    <BaseSpinner v-if="loading" center size="lg" />

    <EmptyState v-else-if="!tags.length" :title="$t('tags.empty')" icon="tag">
      <BaseButton icon="plus" @click="openCreate">{{ $t('tags.criarPrimeira') }}</BaseButton>
    </EmptyState>

    <ul v-else class="grid list-none grid-cols-fill gap-6" role="list">
      <li v-for="tag in tags" :key="tag.id">
        <BaseCard as="article" :title="tag.nome" title-tag="h3" interactive>
          <BaseBadge variant="success" icon>{{ $t('common.ativado') }}</BaseBadge>
          <template #footer>
            <BaseButton variant="ghost" size="sm" icon="pencil" :aria-label="$t('common.editar')" />
            <BaseButton variant="danger" size="sm" icon="trash">{{ $t('common.excluir') }}</BaseButton>
          </template>
        </BaseCard>
      </li>
    </ul>
  </PageContainer>

  <ConfirmModal v-model="confirmOpen" :item-name="tagToDelete?.nome" :loading="deleting" @confirm="remove" />
</template>
```

```vue
<form class="flex flex-col gap-4" @submit.prevent="save">
  <BaseInput v-model.trim="form.nome" :label="$t('tags.nome')" :error="errors.nome" required />
  <BaseSelect v-model="form.cor" :label="$t('tags.cor')" :options="corOptions" />
  <BaseSwitch v-model="form.ativa" :label="$t('tags.ativa')" :hint="$t('tags.ativaHint')" />
  <BaseButton type="submit" :loading="saving" block>{{ $t('common.salvar') }}</BaseButton>
</form>

<StatCard :label="$t('dashboard.totalFlashcards')" :value="stats.total" icon="document" to="/flashcards" />
```

```vue
<!-- Confirmação com aviso extra -->
<ConfirmModal
  v-model="showDeleteModal"
  :title="$t('flashcards.confirmDelete')"
  :message="$t('flashcards.deleteConfirmMessage')"
  :item-name="item?.frente"
  :warning="$t('flashcards.deleteWarning')"
  :loading="deleting"
  @confirm="remove"
/>

<!-- Card realçado (o badge diz o porquê) -->
<BaseCard as="article" :highlight="isAtual ? 'success' : undefined">
  <BaseBadge v-if="isAtual" variant="success" solid icon>{{ $t('planos.currentPlan') }}</BaseBadge>
</BaseCard>

<!-- Progresso -->
<BaseProgress
  :value="current"
  :max="total"
  :label="$t('common.progresso')"
  :value-text="$t('quizPlay.progress', { current, total })"
/>

<!-- Abas (slot com o id de cada aba); para sincronizar com a URL use :model-value + @update:model-value -->
<BaseTabs v-model="activeTab" :tabs="tabs" :label="$t('prompts.detailsTitle')">
  <template #traducoes>…</template>
  <template #imagens>…</template>
</BaseTabs>

<!-- Tabela responsiva -->
<BaseTable :columns="columns" :rows="users" row-key="id" :caption="$t('admin.manageUsers')" caption-hidden min-width="44rem">
  <template #cell-status="{ row }">
    <BaseBadge :variant="row.status === 'ativo' ? 'success' : 'neutral'" icon>{{ row.status }}</BaseBadge>
  </template>
</BaseTable>
```

```ts
const tabs = computed<TabItem[]>(() => [
  { id: 'traducoes', icon: 'language', label: t('prompts.tabTraducoes', { count: traducoes.value.length }) },
  { id: 'imagens', icon: 'photo', label: t('prompts.tabImagens', { count: imagens.value.length }) },
])
const columns = computed<TableColumn[]>(() => [
  { key: 'nome', label: t('admin.user') },
  { key: 'status', label: t('admin.status') },
])
```

## 5. Responsividade

Breakpoints (mobile-first, sempre `min-width`; CSS não aceita `var()` em `@media`, use os números):

| Nome | Valor | Uso típico |
|---|---|---|
| base | < 480px | 1 coluna, botões de ação em largura total, gutter 16px |
| sm | 480px | 2 colunas em grades pequenas |
| md | 768px | gutter 24px, layouts lado a lado, títulos maiores |
| lg | 1024px | gutter 32px, sidebars |
| xl | 1280px | navbar com menu inline (abaixo disso: drawer) |
| 2xl | 1536px | navbar mostra ícones e nome/e-mail |

Regras:

1. Toda tela começa com `<PageContainer>` (o `<main>` já vem do `AuthenticatedLayout`; não use outro `<main>`).
2. Testar em **360px**: nada de rolagem horizontal (`document.documentElement.scrollWidth === clientWidth`).
3. Proibido `width`/`min-width` fixos > 320px; use `max-width` + `width: 100%`, `min()`, `clamp()`.
4. Grades fluidas: `grid grid-cols-fill-64 gap-6` (auto-fill) / `grid-cols-fit-64` (auto-fit; colunas esticam), mínimo da coluna pela escala de espaçamento; ou por breakpoint (`grid-cols-1 md:grid-cols-2`).
5. Linhas de ações: `flex flex-wrap items-center gap-3`; pilhas: `flex flex-col gap-4`. Botões em largura total só no celular: `*:grow *:basis-full sm:*:grow-0 sm:*:shrink-0 sm:*:basis-auto` no contêiner.
6. Tabelas: use `BaseTable` (cards empilhados < 768px, `<table>` ≥ 768px, rolagem contida no wrapper). Abas: `BaseTabs`
   (a barra rola sozinha). Não reimplemente esses padrões com CSS local (`data-label`, `role="tablist"`).
7. Texto longo: `wrap-anywhere` / `truncate min-w-0` no item flex.
8. Alvos de toque ≥ 44px (`--control-height-md`); `font-size` de inputs ≥ 16px (já no componente).
9. RTL (árabe): só propriedades lógicas — `margin-inline-start`, `padding-inline`, `inset-inline-end`,
   `border-inline-start`, `text-align: start/end`. Nada de `left/right` físico.

## 6. Convenção de estilo

- **Tudo é utilitário do Tailwind no template**, em telas e em componentes. Não existe `<style>` em `.vue` nem CSS
  por tela; não crie arquivos `.css` (os únicos são `tokens.css` e `tailwind.css`).
- **Componentes de `ui/`**: mapas de classe tipados no `<script setup>` ([seção 7.4](#74-componentes-de-ui-com-tailwind));
  cada elemento mantém a classe BEM `ui-…` (sem CSS) como gancho/legibilidade.
- **Telas**: classes BEM (`.tag-list__grid`) são opcionais e servem só de gancho/legibilidade (não têm CSS). Muitas
  classes repetidas no mesmo arquivo → constante no `<script setup>` (ex. `linkBase`/`linkActive` da `AppNavbar`).
- Só classes do tema; sem `!`; sem `dark:`/`daltonico:` para cor; sem estilos inline (`style=""`) exceto custom
  properties vindas de dado (ex. `--tag-color` da tag) ou de duração dinâmica.
- Para mudar algo de um componente filho a partir da tela, use **props** (`BaseButton wrap/stacked`, `BaseBadge wrap`,
  `BaseTextarea mono`, `BaseCard muted/highlight/padding`…). Sem `:deep`. Se faltar, crie a prop no componente.
- Ícones: `<BaseIcon name="…" />`; nunca emoji como ícone nem `<svg>` colado. Ícone novo → `ui/icons.ts`.
- Animações respeitam `prefers-reduced-motion` automaticamente via tokens de duração (e o reset base); animação
  contínua necessária (spinner) recebe a classe `motion-safe`.
- Acessibilidade: `sr-only` do Tailwind (e `focus:not-sr-only` para o link "pular para o conteúdo", ver
  `AuthenticatedLayout`).

## 7. Tailwind

Tailwind 3.4, configurado em `tailwind.config.js` com o **tema substituído** pelos tokens: só existem classes que
apontam para `var(--…)`, então tudo que sai do Tailwind troca sozinho nos 4 temas. Preflight e `container` estão
desligados (o reset é o `@layer base` de `src/styles/tailwind.css`; a largura da página é do `PageContainer`).

### O que o tema expõe

| Grupo | Classes | Token |
|---|---|---|
| Cores (`bg-`, `text-`, `border-`, `ring-`…) | `bg`, `surface` (`-muted`, `-hover`), `border` (`-strong`), `text` (`-muted`, `-subtle`, `-inverse`), `primary` (`-hover`, `-active`, `-contrast`, `-soft`, `-soft-text`), `success`/`danger` (`-hover`, `-contrast`, `-soft`), `warning`/`info` (`-contrast`, `-soft`), `focus`, `overlay`, `skeleton`, `hero-from/-to/-text/-text-muted`, `transparent`, `current`, `inherit` | `--color-*` |
| Espaçamento (`p-`, `m-`, `gap-`, `w-`, `h-`, `inset-`…) | `0`, `px`, `1`, `2`, `3`, `4`, `5`, `6`, `8`, `10`, `12`, `16` (+ `auto`, `full`, frações do Tailwind) | `--space-*` |
| Raios | `rounded-none/sm/md/lg/xl/2xl/full` (`rounded` = `md`) | `--radius-*` |
| Sombras | `shadow-none/sm/md/lg/xl` (`shadow` = `md`) | `--shadow-*` |
| Tipografia | `font-sans/mono`, `text-xs/sm/base/lg/xl/2xl/3xl/4xl/display`, `font-regular/medium/semibold/bold`, `leading-none/tight/base/relaxed` | `--font-*`, `--line-height-*` |
| Camadas | `z-base/dropdown/sticky/drawer/modal/toast/tooltip` | `--z-*` |
| Duração | `duration-fast/base/slow` | `--duration-*` |
| Larguras | `max-w-container-sm/md/lg/xl` | `--container-*` |
| Alturas de controle | `min-h-control`, `min-h-control-sm`, `min-h-control-lg` (só `min-height`) | `--control-height-*` |
| Gradiente | `bg-brand` | `--gradient-brand` |
| Breakpoints | `sm:` 480 · `md:` 768 · `lg:` 1024 · `xl:` 1280 · `2xl:` 1536 (mobile-first) | seção 5 |
| Variantes | `dark:`, `daltonico:`, `rtl:`, `ltr:` | classes de tema / `dir` |

### Extensões (`theme.extend` + plugin) usadas pelos componentes — disponíveis para as telas

Tudo aponta para tokens (`tokens.css`); nenhuma exige valor arbitrário.

| Grupo | Classes | Valor |
|---|---|---|
| Espaçamento extra (vale para `p-`/`m-`/`gap-`/`w-`/`h-`/`size-`/`min-*`/`max-*`/`inset-`/`translate-`/`basis-`) | `0.5` · `1.5` · `11` · `14` · `48` · `64` · `72` | `--space-0-5` … `--space-72` |
| | `nudge`, `nudge-em` (ex. `mt-nudge` alinha ícone à 1ª linha) | `--space-nudge(-em)` |
| | `gutter` (`px-gutter`) | `--page-gutter` |
| | `control`, `control-sm`, `control-lg` (`h-control`, `w-control`, `size-control-sm`, `pe-control`…) | `--control-height-*` |
| | `control-inner` (botão dentro do campo), `control-icon` (recuo p/ ícone no campo), `control-chevron` (recuo p/ seta do select), `check-indent` (dica do checkbox) | `calc()` de tokens |
| | `em`, `em-md`, `em-lg` (ícone relativo à fonte: `size-em-lg`), `check` | `--icon-size-*` |
| `max-w-` | `page-sm/md/lg/xl` (container + 2×gutter), `modal-sm/md/lg/xl`, `measure` | `--container-*`, `--modal-width-*`, `--measure-sm` |
| `max-h-` / `min-h-` / `min-w-` | `max-h-modal` (100dvh − 2rem), `min-h-textarea` (2× controle), `min-w-table` (`--ui-table-min`, padrão 100%) | tokens |
| `w-` / `h-` | `w-toast` (min(26rem, 100vw − 2rem)), `h-toast-progress` | `--toast-*` |
| Tipografia | `text-icon-lg` (1.75rem), `text-icon-xl` (2rem), `leading-inherit`, `tracking-label` | `--font-size-icon-*`, `--letter-spacing-label` |
| Estado | `opacity-disabled` (0.55), `z-raised` (1) | `--opacity-disabled`, `--z-raised` |
| Borda | `border` = `--border-width`; `border-3` (3px) | — |
| Anel | `ring` = `--focus-ring-width` (**sempre** com cor: `focus:ring focus:ring-primary-soft`; a cor padrão não segue o tema); `ring-1 ring-inset ring-border` p/ contorno interno | `--focus-ring-width` |
| Sombra | `shadow-tab-active` (barra inferior primária + `--shadow-sm`) | tokens |
| Transição | `transition` usa por padrão `--duration-base` + `--easing-standard`; `ease-standard`, `ease-emphasized`; propriedades `transition-control` (cor, fundo, borda, sombra), `transition-card` (sombra, borda, transform), `transition-enter` (opacidade, transform), `transition-size` (`inline-size`) | `--easing-*` |
| Animação | `animate-spinner` (`--duration-spin`), `animate-spinner-fast`, `animate-toast-progress` (duração via `style="animation-duration"`; anima `inline-size`, logo encolhe para o início também em RTL); `scale-enter`, `scale-enter-sm` | `--duration-spin*`, `--scale-enter*` |
| Conteúdo | `before:content-label` (`attr(data-label)`) | — |
| Variante `aria-invalid:` | `[aria-invalid="true"]` (as demais `aria-*` são nativas do Tailwind 3.4) | — |
| **Utilitários do plugin** | `focus-ring` (anel de foco padrão: `focus-visible:focus-ring`), `focus-ring-inset` (anel por dentro, p/ elementos em contêiner com overflow), `wrap-anywhere` (`overflow-wrap: anywhere`), `scrollbar-thin`, `clip-hidden` / `clip-auto` (esconder visualmente com breakpoint, sem mexer em largura/posição como o `sr-only`) | tokens |
| **Grade fluida** | `grid-cols-fit` / `grid-cols-fill` (auto-fit / auto-fill, mínimo `--u-min`, padrão `--space-72` = 18rem) e `grid-cols-fit-{n}` / `grid-cols-fill-{n}` com `n` da escala de espaçamento (`grid-cols-fit-48` = 12rem, `-64` = 16rem). Ex.: `class="grid grid-cols-fill gap-6" style="--u-min: 16rem"` | `--space-*` |
| **Variantes do plugin** | `t-active:` / `t-from:` (estado de `<Transition>` — ver 7.4), `peer-checked-deep:` / `peer-focus-visible-deep:` (descendente de um irmão do `peer`), `can-hover:` (`@media (hover: hover)` + `:hover`), `only-text:` (filho único que não é `button`/`a`) | — |
| Nativas úteis | `max-sm:` (< 480px) e demais `max-*:`, `*:` (filhos diretos), `group/<nome>` + `group-last/<nome>:`, `peer-checked:`, `enabled:hover:`, `motion-reduce:`, `rtl:`, `file:` (botão do `<input type="file">`), `before:`/`after:`, `last:`, `accent-primary` | — |

### Extensões das telas (etapa 2 da conversão)

| Grupo | Classes | Valor |
|---|---|---|
| Espaçamento extra (mesmas escalas acima) | `4.5` (18px) · `7` · `9` · `13` · `18` · `20` (ícones, avatares, medalhões: `size-9`, `size-18`); `28` · `34` · `36` · `40` · `44` · `56` · `60` · `68` · `70` · `80` · `88` · `96` · `112` (grades `grid-cols-fit-56`, bases `basis-64`, `max-w-96`, `min-h-56`) | `--space-*` |
| | `navbar` (`h-navbar`), `drawer` (`w-drawer`), `fluid-hero` / `fluid-section` (`py-fluid-hero`) | `--navbar-height`, `--drawer-width`, `--space-fluid-*` |
| Tipografia fluida | `text-fluid-lg-2xl`, `text-fluid-2xl-3xl`, `text-fluid-2xl-4xl`, `text-fluid-3xl-4xl` | `--font-size-fluid-*` (clamp) |
| Cor de dado | `border-s-tag`, `bg-tag` = `var(--tag-color, --color-border-strong)` (cor da tag via `style="--tag-color: …"`) | — |
| Outros | `rounded-inherit`, `aspect-photo` (4/3), `min-h-flashcard`, `duration-flip` (2 × slow), `transition-reveal` (opacidade, transform, visibility), `shadow-nav-active` (sublinhado do link ativo), `shadow-drawer-active` / `rtl:shadow-drawer-active-rtl` (barra no início), `content-empty` (`before:content-empty`), `content-colon` (`after:content-colon`) | tokens |
| Utilitários do plugin | `min-h-viewport` (100vh + 100dvh), `perspective-card`, `preserve-3d`, `backface-hidden`, `rotate-y-180` (virada 3D do flashcard), `outline-hidden` (`outline: none` de verdade) | tokens |
| Componente do plugin | `font-inherit` (`font: inherit` para `<button>` nativo; na camada `components`, então `text-sm`/`font-medium` no mesmo elemento vencem) | — |

Atenção ao nome das cores de texto: o grupo se chama `text`, então a classe é **`text-text-muted`** (não
`text-muted`), `text-text-subtle`, `text-text`; e `bg-text-…`/`border-text-…` também existem (evite).

### Onde fica cada coisa

| Use | Para |
|---|---|
| **Componente `ui/`** | qualquer controle, card, badge, modal, tabela, abas, estado vazio… (o visual é dele; não refaça com utilitários) |
| **Utilitário no template** | `flex`/`grid`/`grid-cols-*`, `gap-*`, `p-*`/`m-*`, `items-*`/`justify-*`, `w-full`, `min-w-0`, `h-full`, `col-span-full`, `max-w-container-*`, breakpoints (`md:`), `text-center`/`text-start`, `truncate`, `line-clamp-*`, `sr-only`; cor/tipografia quando é só aplicar um token (`text-sm font-semibold text-text-muted`, `bg-surface-muted rounded-lg`) |
| **Também utilitário** | estados (`hover:`, `focus-visible:focus-ring`, `disabled:`, `aria-selected:`, `aria-invalid:`), transições (`transition-control`, `<Transition v-bind="overlayTransition">`), pseudo-elementos simples (`before:content-label`), filhos diretos (`*:w-full`), bordas (`border border-solid border-border`; borda de um lado só: `border-0 border-t border-solid` — sem preflight a largura inicial é `medium`), `wrap-anywhere`, grades fluidas (`grid-cols-fit-48`), tamanhos de controle (`size-control`, `basis-48`) |
| **Extensão no config** | o que o tema não tem: token novo em `tokens.css` + entrada em `tailwind.config.js` (ou utilitário no plugin, se for uma propriedade que o Tailwind não cobre). Nunca valor arbitrário |
| **Reset base** (`tailwind.css`, `@layer base`) | só regras globais por elemento (body, `a`, foco, scrollbar, movimento reduzido). Não acrescente regras de tela |

Estados, pseudo-elementos e descendentes também são utilitários: `group` + `group-hover:`, `before:content-empty`,
`file:`, `last:`, `*:` (filhos diretos). Um seletor de descendente (`.x a`) vira classe direto no elemento.

### Regras

1. **Sem valores arbitrários**: nada de `p-[13px]`, `w-[350px]`, `text-[14px]`, `bg-[#fff]`, `grid-cols-[…]`.
   Se o tema não tiver o valor, crie o token em `tokens.css` (cor nos 4 temas) e a entrada no `tailwind.config.js`.
2. **RTL**: só utilitários lógicos — `ms-*`/`me-*`, `ps-*`/`pe-*`, `start-*`/`end-*`, `text-start`/`text-end`,
   `border-s`/`border-e`, `rounded-s-*`/`rounded-e-*`. Proibido `ml-`/`mr-`/`pl-`/`pr-`/`left-`/`right-`/
   `text-left`/`text-right`. (`mt-`/`mb-`/`pt-`/`pb-` são de bloco e não mudam em RTL.)
3. **Sem opacidade de cor** (`bg-primary/50`, `text-text/80`): as cores são `var()` e o modificador não funciona.
   Para fundo suave use `-soft`.
4. **Sem `dark:`/`daltonico:` para cor**: os tokens já trocam. As variantes existem só para algo estrutural de tema.
5. **Mobile-first**: classe sem prefixo = celular; `md:`/`lg:` acrescentam. Grades: `grid grid-cols-1
   md:grid-cols-2 xl:grid-cols-3` ou `grid grid-cols-fill-64 gap-6`. Nada de rolagem horizontal em 360px.
6. **Especificidade**: na raiz de um componente `ui/` (feita de utilitários), a tela só passa utilitário para o que
   o componente **não** define (ex.: `flex-1`, `h-full`, `me-auto`, `col-span-full`, `mt-4`, `absolute end-2`).
   Dois utilitários da mesma propriedade no mesmo elemento (`p-4` da tela + `p-6` do `BaseCard`) **empatam** e quem
   vence é a ordem do CSS gerado, não a ordem no atributo — não faça isso; para mudar algo do componente, use props.
   Exceções seguras: o tamanho do `BaseIcon` (padrão 1em, numa camada mais baixa: `size-5`/`size-em-lg` da tela
   sempre vencem) e "abreviação antes do lado" (`border-s-4 border-s-tag` sobre o `border border-border` do `BaseCard`).
7. Dentro de `src/shared/components/ui/**` vale tudo acima **e** as regras da [seção 7.4](#74-componentes-de-ui-com-tailwind).
8. Não use `.container` (desligado) nem crie classes globais próprias.

### Exemplos

```vue
<!-- Linha de card: ícone + texto que encolhe + ações -->
<div class="tag-list__row flex items-start gap-3">
  <span class="tag-list__swatch mt-1 h-6 w-6 shrink-0 rounded-full" aria-hidden="true"></span>
  <div class="min-w-0 flex-1">
    <h2 class="tag-list__name text-lg font-semibold text-text">{{ tag.nome }}</h2>
    <p class="mt-1 text-sm text-text-muted">{{ tag.descricao }}</p>
  </div>
  <div class="flex shrink-0 gap-1">…</div>
</div>

<!-- Grade fluida (auto-fill, coluna mínima de 16rem) -->
<ul class="grid list-none grid-cols-fill-64 gap-4" role="list">…</ul>

<!-- Grade por breakpoint -->
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">…</div>

<!-- Ação destrutiva separada (lógico: vai para o outro lado em RTL) -->
<BaseButton variant="danger" icon="trash" class="sm:me-auto">…</BaseButton>
```

```vue
<!-- Borda de um lado (lógica), estado e pseudo-elemento: tudo utilitário -->
<div class="border-0 border-s-4 border-solid border-info rounded-lg bg-surface-muted p-4">…</div>
<button class="border-0 bg-transparent font-inherit cursor-pointer hover:bg-surface-hover focus-visible:focus-ring-inset">…</button>
<p class="flex items-center gap-4 before:flex-1 before:border-0 before:border-b before:border-solid before:border-border before:content-empty">…</p>

<!-- Virada 3D: perspectiva no botão, preserve-3d no miolo, faces empilhadas na mesma célula -->
<button class="group perspective-card …">
  <span class="grid min-h-flashcard preserve-3d transition-transform duration-flip ease-emphasized" :class="{ 'rotate-y-180': virado }">
    <span class="col-start-1 row-start-1 backface-hidden group-hover:border-primary …">frente</span>
    <span class="col-start-1 row-start-1 backface-hidden rotate-y-180 …">verso</span>
  </span>
</button>
```

### 7.4 Componentes de `ui/` com Tailwind

Os componentes de `src/shared/components/ui` não têm `<style>`: todo o visual está em utilitários. Padrão:

```vue
<template>
  <button :class="classes"><slot /></button>
</template>

<script setup lang="ts">
type Variant = 'primary' | 'secondary'
type Size = 'sm' | 'md'

// 1. Base: o que não muda (classe BEM primeiro, como gancho)
const base = 'ui-thing inline-flex items-center gap-2 border border-solid rounded-md transition-control focus-visible:focus-ring'
// 2. Um mapa tipado por eixo de variação: cada mapa define propriedades que os outros não definem
const variantClasses: Record<Variant, string> = {
  primary: 'bg-primary text-primary-contrast border-transparent',
  secondary: 'bg-surface text-text border-border-strong',
}
const sizeClasses: Record<Size, string> = { sm: 'min-h-control-sm px-3 text-xs', md: 'min-h-control px-5 text-sm' }
// 3. Estado combinado no computed (não confie na ordem de duas classes da mesma propriedade)
const classes = computed(() => [base, variantClasses[props.variant], sizeClasses[props.size], props.disabled ? 'opacity-disabled' : 'hover:bg-primary-hover'])
</script>
```

Regras para componente novo (ou alteração):

1. **Zero `<style>`** em `ui/`. Sem valor arbitrário (`[...]`), sem `!`, sem `ml-/mr-/pl-/pr-/left-/right-/text-left/
   text-right` (use `ms-/me-/ps-/pe-/start-/end-/text-start/text-end`), sem `dark:`/`daltonico:` para cor.
2. **Nunca duas classes da mesma propriedade** no mesmo elemento esperando que a "última" vença (`p-0` + `px-3`,
   `text-center` + `text-start`, `min-h-control` + `min-h-textarea`): escolha uma no mapa/computed. Exceções
   seguras, porque a ordem do Tailwind é fixa: abreviação antes do lado (`border` → `border-s-4`, `border-0` →
   `border-t`, `px-3` → `ps-control-icon`, `border-border` → `border-s-info`/`border-t-primary`) e qualquer
   variante (`hover:`, `md:`…) depois da classe sem variante.
3. **Estados**: `hover:` / `enabled:hover:` (não pinta hover em desabilitado), `focus-visible:focus-ring` (ou
   `focus-ring-inset` dentro de contêiner com overflow), `disabled:`, `aria-*:`, `peer-*:` / `peer-*-deep:`,
   `group/<nome>` + `group-*/<nome>:`. Em `<a>`/`router-link`, repita `hover:text-…`: `a:hover` do reset base vence um
   `text-…` sem variante. Em `<button disabled>` use `disabled:cursor-…` (o reset base tem `button:disabled`).
4. **Transições Vue**: `<Transition v-bind="overlayTransition">` / `<TransitionGroup v-bind="toastTransition">`
   (`ui/transitions.ts`). A raiz recebe `ui-t-active`/`ui-t-from`; um filho anima junto com
   `t-active:transition-transform t-from:translate-y-3 t-from:scale-enter`. Drawer que entra pelo lado "fim":
   `t-active:transition-transform t-active:duration-slow t-from:translate-x-full rtl:t-from:-translate-x-full`.
5. **Movimento reduzido**: durações vêm dos tokens (zeram sozinhas). Animação contínua (spinner) usa
   `animate-spinner` + a classe `motion-safe` (continua girando, mais devagar). `motion-reduce:` só se precisar de
   algo além disso.
6. **RTL**: utilitários lógicos; espelhar só com `rtl:` (ex. `rtl:-scale-x-100` no `BaseIcon` dos ícones
   direcionais; `rtl:peer-checked-deep:-translate-x-5` no thumb do `BaseSwitch`).
7. Tamanho que não existe no tema → token novo em `tokens.css` (`:root`; cor nos 4 temas) + entrada em
   `theme.extend` do `tailwind.config.js` + linha na tabela acima. Valores derivados (`calc()` de tokens) podem ir
   direto no config.
8. CSS que o Tailwind realmente não expressa vai em `src/styles/tailwind.css` dentro de `@layer components`,
   comentado. Hoje só existe um: `.ui-icon` (tamanho padrão do `BaseIcon` em camada baixa, para utilitários de
   tamanho passados por quem usa vencerem).
9. Confira se nenhuma classe é "fantasma": gere o CSS (`npx tailwindcss -c tailwind.config.js -i
   src/styles/tailwind.css -o <tmp>.css`) e procure cada classe nova nele. Classes montadas por concatenação
   (`` `bg-${tom}` ``) **não** são geradas — escreva a classe inteira no mapa.

Não há ganchos de tela em componentes (nenhum `:deep`). As classes BEM `ui-…` dos
elementos continuam no DOM, mas as de modificador (`ui-button--primary` etc.) deixaram de existir.

## 8. Histórico

A migração terminou: os arquivos legados (`legacy-aliases.css`, `legacy-overrides.css`, `themes.css`, `main.css`),
o CSS por tela (`src/styles/views/**`, `src/styles/components/**`) e os utilitários próprios (`.u-*`, `.skip-link`,
`.sr-only` com `!important`, transição `fade`) foram removidos. Os nomes antigos de variável (`--bg-*`,
`--text-primary`, `--card-*`, `--modal-*`, `--plano-*`…) e de classe (`.stat-card`, `.hero-title`, `.form-input`…)
**não existem mais** — não os reintroduza. Registro da auditoria: [`design-system-audit.md`](./design-system-audit.md).

## 9. Checklist "pronto" para uma tela

- [ ] Envolta em `PageContainer`; cabeçalho com `PageHeader` (um `<h1>` por página).
- [ ] Botões, campos, cards, badges, modais, spinners e estados vazios usam componentes de `ui/`.
- [ ] Zero `<style>` no `.vue` e nenhum `.css` novo; tudo em utilitários do tema.
- [ ] Tailwind só com o tema: `grep -nE "\b[a-z-]+-\[|\b(ml|mr|pl|pr|left|right)-|text-(left|right)\b|/[0-9]{2}\b"`
      no template retorna vazio (sem valor arbitrário, sem físico esquerda/direita, sem opacidade); nada de `!`
      nem `dark:`/`daltonico:` para cor.
- [ ] Classes passadas a componentes de `ui/` não repetem um grupo que o componente já define (use props).
- [ ] Nenhuma classe "fantasma" (gere o CSS e confira, seção 7.4 item 9).
- [ ] Testado em 360px, 768px, 1280px sem rolagem horizontal.
- [ ] Testado nos 4 temas (no console: `document.documentElement.classList.toggle('dark-mode')` / `'daltonico-mode'`).
- [ ] Status com ícone/texto além da cor; ícones via `BaseIcon`, sem emoji.
- [ ] Teclado: tudo alcançável com Tab, foco visível, Esc fecha modais; botões só-ícone com `aria-label`; campos com label.
- [ ] RTL: só utilitários lógicos; conferir com o idioma árabe.
- [ ] Texto novo com chave nos **14** locales (traduções reais, mesmo conjunto de chaves).
- [ ] `npx vue-tsc --build --force`, `npx eslint src` e `npx vite build` sem erros.