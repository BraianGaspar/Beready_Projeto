# Design system BeReady — guia

Este guia é **obrigatório** para qualquer tela nova ou migrada. Se algo não estiver coberto aqui, use um token
existente e um componente de `src/shared/components/ui` antes de criar CSS novo.
Auditoria do que falta migrar: [`design-system-audit.md`](./design-system-audit.md).

## 1. Arquitetura

| Arquivo | Papel |
|---|---|
| `src/styles/tokens.css` | **Fonte única**: tokens e os 4 temas (claro, escuro, daltônico, daltônico+escuro) |
| `src/styles/themes.css` | Entrada importada em `main.ts`; importa tokens + arquivos legados |
| `src/styles/main.css` | Reset, tipografia base, foco visível, scrollbar, movimento reduzido, utilitários |
| `src/styles/legacy-aliases.css` | **Temporário**: nomes antigos de variável → tokens |
| `src/styles/legacy-overrides.css` | **Temporário**: regras globais antigas com `!important` (já em tokens) |
| `src/shared/components/ui/` | Componentes reutilizáveis + `index.ts` + `icons.ts` |
| `src/shared/composables/useTheme.ts` | Único lugar que escreve `.dark-mode` / `.daltonico-mode` em `<html>` e `<body>` |
| `src/shared/composables/useFocusTrap.ts` | Focus trap para modais/drawers |
| `scripts/check-contrast.mjs` | Verificação WCAG dos tokens (`node scripts/check-contrast.mjs`) |

Tailwind foi **removido** (não era carregado: nenhum arquivo com `@tailwind` era importado; as classes `h-5 w-5`
nos templates eram código morto). Não reintroduzir: um sistema só (tokens + componentes + CSS por tela).

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

1. Use **apenas tokens** (`var(--color-*)`, `var(--shadow-*)`…). Eles mudam sozinhos por tema.
2. **Nunca** cor fixa (`#fff`, `rgb()`, `white`, nomes de cor) em CSS de tela ou componente.
3. **Nunca** `var(--x, #fallback)`. Fallback só é permitido para custom property *local* de componente apontando
   para outro token/valor neutro (ex.: `var(--u-gap, var(--space-4))`).
4. **Nunca** escreva regras `.dark-mode …` / `.daltonico-mode …` em telas. Se um token não existir para o caso,
   crie o token em `tokens.css` nos 4 temas e rode o script de contraste.
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
| Layout | `--container-sm` (640) `-md` (960) `-lg` (1200) `-xl` (1400), `--page-gutter` (16 → 24 ≥768 → 32 ≥1024), `--navbar-height` (64; 56 < 768) |
| Camadas | `--z-dropdown` 100 · `--z-sticky` 200 (navbar) · `--z-drawer` 300 · `--z-modal` 400 · `--z-toast` 500 · `--z-tooltip` 600 |
| Movimento | `--duration-fast/base/slow` (120/200/320ms, **0 com prefers-reduced-motion**), `--easing-standard`, `--easing-emphasized`, `--transition-base` |

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
| `BaseButton` | `variant` primary·secondary·ghost·danger·success·ghost-danger, `size` sm·md·lg, `type`, `loading`, `disabled`, `icon`, `iconEnd`, `block`, `to` (vira `<router-link>`) | default; `@click` nativo. Só ícone ⇒ passe `aria-label` |
| `BaseIcon` | `name` (ver `icons.ts`), `size`, `label` (sem label = decorativo) | — |
| `BaseInput` | `v-model` (aceita `.trim`/`.number`), `label`, `type`, `placeholder`, `hint`, `error`, `required`, `disabled`, `icon`, `id` | `@blur`. `type="password"` ganha botão mostrar/ocultar |
| `BaseTextarea` | `v-model`, `label`, `rows`, `maxlength`, `showCount`, `hint`, `error`… | `@blur` |
| `BaseSelect` | `v-model`, `label`, `options: {value,label,disabled?}[]`, `placeholder`, `hint`, `error`… | default (alternativa: `<option>`s) |
| `BaseCheckbox` | `v-model` boolean, `label`, `hint`, `disabled` | default (label rico) |
| `BaseSwitch` | `v-model` boolean, `label`, `hint`, `disabled` (`role="switch"`, ícone de check) | default |
| `BaseField` | `fieldId`, `label`, `hint`, `error`, `required` | default — moldura para controles customizados |
| `BaseCard` | `as`, `title`, `subtitle`, `titleTag`, `padding` none·sm·md·lg, `interactive`, `muted`, `highlight` primary·success·warning·danger (borda + anel no tom; reforço visual, o significado vem de badge/texto) | default, `header`, `actions`, `footer` |
| `BaseModal` | `v-model`, `title`, `description`, `ariaLabel`, `size` sm·md·lg·xl, `closeOnOverlay`, `closeOnEsc`, `hideClose` | default, `header`, `footer` (`{ close }`); `@close`. Focus trap, Esc, foco devolvido, scroll travado |
| `ConfirmModal` | `v-model`, `title`, `message`, `confirmText`, `type` danger·warning·info, `itemName`, `warning` (2ª linha com ícone, ex. `$t('confirmModal.irreversible')`), `loading` | default (conteúdo extra abaixo da mensagem); `@confirm` (API antiga mantida) |
| `BaseBadge` | `variant` neutral·primary·success·warning·danger·info, `size` sm·md, `icon` (nome ou `true` = ícone do status), `solid` | default |
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

    <ul v-else class="tag-list__grid u-grid-auto">
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
<form class="tag-form u-stack" @submit.prevent="save">
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
4. Grades: `.u-grid-auto` (ajuste com `style="--u-min: 16rem"`) ou `repeat(auto-fill, minmax(min(100%, X), 1fr))`.
5. Linhas de ações: `.u-cluster` (quebra linha); pilhas: `.u-stack`. Ajuste o espaçamento com `--u-gap`.
6. Tabelas: use `BaseTable` (cards empilhados < 768px, `<table>` ≥ 768px, rolagem contida no wrapper). Abas: `BaseTabs`
   (a barra rola sozinha). Não reimplemente esses padrões com CSS local (`data-label`, `role="tablist"`).
7. Texto longo: `overflow-wrap: anywhere` / `.u-truncate` com `min-width: 0` no item flex.
8. Alvos de toque ≥ 44px (`--control-height-md`); `font-size` de inputs ≥ 16px (já no componente).
9. RTL (árabe): só propriedades lógicas — `margin-inline-start`, `padding-inline`, `inset-inline-end`,
   `border-inline-start`, `text-align: start/end`. Nada de `left/right` físico.

## 6. Convenção de CSS

- **Componentes de `ui/`**: estilo dentro do próprio SFC (`<style scoped>`), classes prefixadas `ui-`.
- **Telas**: CSS em `src/styles/views/<modulo>/<tela>.css`, importado no SFC com
  `<style scoped>@import '@/styles/views/<modulo>/<tela>.css';</style>` (padrão atual mantido).
  Nada de CSS no SFC da tela além desse import.
- **Nomes BEM com o bloco = nome da tela**: `.tag-list`, `.tag-list__grid`, `.tag-list__item--inactive`.
  Não reutilize nomes genéricos legados (`.stat-card`, `.hero-title`, `.card-title`, `.form-input`, `.form-label`,
  `.empty-state`, `.loading-state`, `.btn-primary`, `.btn-cancel`, `.modal-*`, `.badge`, `.profile-*`,
  `.login-*`, `.register-*`, `.prompt-*`, `.quiz-card*`, `.flashcard-*`): eles são alvo de
  `legacy-overrides.css` com `!important`.
- Só tokens; sem `!important` (exceto `.sr-only`); sem regras `.dark-mode`/`.daltonico-mode`; sem estilos inline
  (`style=""`) exceto custom properties de layout (`--u-min`, `--u-gap`).
- Para estilizar um componente filho a partir da tela, prefira props; em último caso `:deep(.ui-…)` com comentário.
  Cor/tom de botão, realce de card, barra de progresso, abas e tabela responsiva já são props/componentes —
  seletores compostos de alta especificidade para isso não são aceitos.
- Ícones: `<BaseIcon name="…" />`; nunca emoji como ícone nem `<svg>` colado. Ícone novo → `ui/icons.ts`.
- Animações respeitam `prefers-reduced-motion` automaticamente via tokens de duração; animação contínua
  necessária (spinner) recebe a classe `motion-safe`.
- Utilitários globais disponíveis (não crie outros): `.sr-only`, `.skip-link`, `.u-truncate`, `.u-stack`,
  `.u-cluster`, `.u-grid-auto`, transição Vue `fade`.

## 7. Legado temporário

`legacy-aliases.css` mantém (apontando para tokens) os nomes antigos:
`--bg-primary(-dark)`, `--bg-card(-dark)`, `--bg-secondary(-dark)`, `--bg-muted`, `--bg-hover`, `--bg-table-header`,
`--input-bg-dark`, `--text-primary(-dark)`, `--text-secondary(-dark)`, `--text-muted(-dark)`, `--card-text-*`,
`--empty-*`, `--loading-*`, `--border-color(-dark)`, `--border-light`, `--border-hover`, `--footer-*`,
`--card-shadow(-hover)`, `--shadow-card(-dark)`, `--accent-color/-hover`, `--hero-bg(-dark)`, `--hero-text(-secondary)`,
`--card-badge-*`, `--modal-*`, `--btn-cancel-*-dark`, variáveis da PlanosPage (`--badge-*`, `--premium-*`, `--free-*`,
`--limite-*`, `--economia-*`, `--check-*`, `--danger-color`, `--trial-*`, `--btn-*`) e do admin (`--plano-*`, `--role-*`).

- Agentes da fase 2 **não editam** `legacy-aliases.css` nem `legacy-overrides.css`; ao migrar uma tela, apenas
  param de usar esses nomes. Os dois arquivos serão apagados na limpeza final, quando
  `grep -rE "var\(--(bg-|text-primary|text-secondary|border-color|hero-|card-|modal-|plano-|role-)" src` não achar nada.

## 8. Checklist "pronto" para uma tela migrada

- [ ] Envolta em `PageContainer`; cabeçalho com `PageHeader` (um `<h1>` por página).
- [ ] Botões, campos, cards, badges, modais, spinners e estados vazios usam componentes de `ui/`.
- [ ] CSS da tela só com tokens: `grep -E "#[0-9a-fA-F]{3,8}|rgba?\(|var\(--[a-z-]+,|!important|\.dark-mode|\.daltonico-mode"` no CSS da tela retorna vazio.
- [ ] Nenhum nome de variável/classe legado (seção 6/7).
- [ ] Testado em 360px, 768px, 1280px sem rolagem horizontal.
- [ ] Testado nos 4 temas (no console: `document.documentElement.classList.toggle('dark-mode')` / `'daltonico-mode'`).
- [ ] Status com ícone/texto além da cor; ícones via `BaseIcon`, sem emoji.
- [ ] Teclado: tudo alcançável com Tab, foco visível, Esc fecha modais; botões só-ícone com `aria-label`; campos com label.
- [ ] RTL: sem `left/right`/`margin-left` etc.; conferir com o idioma árabe.
- [ ] Texto novo com chave nos **14** locales (traduções reais, mesmo conjunto de chaves).
- [ ] `npx vue-tsc --build --force` e `npx eslint src` sem erros.
