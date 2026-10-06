<template>
  <div class="ui-tabs" :class="{ 'ui-tabs--embedded': embedded }">
    <!-- Barra de abas: rolagem horizontal contida aqui em telas estreitas -->
    <div
      ref="listRef"
      class="ui-tabs__list"
      role="tablist"
      :aria-label="label || undefined"
      aria-orientation="horizontal"
      @keydown="onKeydown"
    >
      <button
        v-for="tab in tabs"
        :id="tabId(tab.id)"
        :key="tab.id"
        type="button"
        role="tab"
        class="ui-tabs__tab"
        :class="{ 'ui-tabs__tab--active': tab.id === active }"
        :aria-selected="tab.id === active ? 'true' : 'false'"
        :aria-controls="panelId(tab.id)"
        :tabindex="tab.id === active ? 0 : -1"
        :disabled="tab.disabled"
        @click="select(tab.id)"
      >
        <BaseIcon v-if="tab.icon" :name="tab.icon" class="ui-tabs__icon" />
        <span class="ui-tabs__label">{{ tab.label }}</span>
      </button>
    </div>

    <!-- Só o painel ativo é renderizado (mesmo comportamento de v-if por aba) -->
    <div
      v-if="activeTab"
      :id="panelId(activeTab.id)"
      :key="activeTab.id"
      class="ui-tabs__panel"
      role="tabpanel"
      :aria-labelledby="tabId(activeTab.id)"
      tabindex="0"
    >
      <slot :name="activeTab.id" :tab="activeTab" />
    </div>
  </div>
</template>

<script lang="ts">
import type { IconName } from './icons'

export interface TabItem {
  /** Identificador (também é o nome do slot do painel) */
  id: string
  label: string
  icon?: IconName
  disabled?: boolean
}
</script>

<script setup lang="ts" generic="K extends string = string">
// Abas acessíveis (padrão WAI-ARIA tablist/tab/tabpanel):
// - v-model = id da aba ativa; um slot por aba (`<template #users>…</template>`)
// - roving tabindex: só a aba ativa entra na ordem do Tab; ←/→ trocam de aba
//   (invertidas em RTL), Home/End vão à primeira/última; ativação automática
import { computed, nextTick, ref, useId } from 'vue'
import BaseIcon from './BaseIcon.vue'

const props = withDefaults(
  defineProps<{
    tabs: (TabItem & { id: K })[]
    /** Nome acessível da lista de abas */
    label?: string
    /** Dentro de um BaseCard (padding="none"): barra encostada no topo do card */
    embedded?: boolean
  }>(),
  { label: '', embedded: false },
)

const active = defineModel<K>({ required: true })

const uid = useId()
const tabId = (id: string) => `ui-tabs-${uid}-tab-${id}`
const panelId = (id: string) => `ui-tabs-${uid}-panel-${id}`

const listRef = ref<HTMLElement | null>(null)

const activeTab = computed(() => props.tabs.find((tab) => tab.id === active.value))

const select = (id: K) => {
  if (id !== active.value) active.value = id
}

const focusTab = async (id: K) => {
  await nextTick()
  const el = listRef.value?.querySelector<HTMLElement>(`#${CSS.escape(tabId(id))}`)
  el?.focus()
  el?.scrollIntoView({ block: 'nearest', inline: 'nearest' })
}

const onKeydown = (event: KeyboardEvent) => {
  const enabled = props.tabs.filter((tab) => !tab.disabled)
  if (!enabled.length) return

  const current = enabled.findIndex((tab) => tab.id === active.value)
  const isRtl = listRef.value ? getComputedStyle(listRef.value).direction === 'rtl' : false
  const next = isRtl ? 'ArrowLeft' : 'ArrowRight'
  const prev = isRtl ? 'ArrowRight' : 'ArrowLeft'

  let index: number
  if (event.key === next) index = (current + 1) % enabled.length
  else if (event.key === prev) index = (current - 1 + enabled.length) % enabled.length
  else if (event.key === 'Home') index = 0
  else if (event.key === 'End') index = enabled.length - 1
  else return

  event.preventDefault()
  const target = enabled[index]
  if (!target) return
  select(target.id)
  focusTab(target.id)
}
</script>

<style scoped>
.ui-tabs {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  min-width: 0;
}

.ui-tabs__list {
  display: flex;
  gap: var(--space-1);
  padding: var(--space-1);
  overflow-x: auto;
  overscroll-behavior-x: contain;
  scrollbar-width: thin;
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface-muted);
}

.ui-tabs__tab {
  display: inline-flex;
  flex: 1 0 auto;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  min-height: var(--control-height-md);
  padding-inline: var(--space-4);
  border: var(--border-width) solid transparent;
  border-radius: var(--radius-md);
  background: transparent;
  color: var(--color-text-muted);
  font: inherit;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  white-space: nowrap;
  cursor: pointer;
  transition:
    background-color var(--transition-base),
    border-color var(--transition-base),
    color var(--transition-base);
}

.ui-tabs__icon {
  width: 1.25em;
  height: 1.25em;
}

.ui-tabs__tab:hover:not(:disabled) {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

/* Anel por dentro: a lista tem overflow e cortaria o anel externo */
.ui-tabs__tab:focus-visible {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: calc(var(--focus-ring-width) * -1);
}

.ui-tabs__tab:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

/* Aba ativa: fundo + borda + sombra + barra inferior (não depende só de cor) */
.ui-tabs__tab--active,
.ui-tabs__tab--active:hover:not(:disabled) {
  background: var(--color-surface);
  border-color: var(--color-border);
  color: var(--color-primary);
  box-shadow:
    inset 0 calc(var(--focus-ring-width) * -1) 0 var(--color-primary),
    var(--shadow-sm);
}

.ui-tabs__panel {
  min-width: 0;
}

.ui-tabs__panel:focus-visible {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: var(--focus-ring-offset);
  border-radius: var(--radius-md);
}

/* Variante dentro de card */
.ui-tabs--embedded {
  gap: 0;
}

.ui-tabs--embedded .ui-tabs__list {
  padding: var(--space-2);
  border: 0;
  border-block-end: var(--border-width) solid var(--color-border);
  border-radius: 0;
}

.ui-tabs--embedded .ui-tabs__panel:focus-visible {
  outline-offset: calc(var(--focus-ring-width) * -1);
}

@media (min-width: 768px) {
  .ui-tabs--embedded .ui-tabs__list {
    padding: var(--space-3);
  }
}
</style>
