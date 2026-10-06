<template>
  <div class="ui-tabs flex min-w-0 flex-col" :class="embedded ? 'gap-0' : 'gap-4'">
    <!-- Barra de abas: rolagem horizontal contida aqui em telas estreitas -->
    <div
      ref="listRef"
      class="ui-tabs__list flex gap-1 overflow-x-auto overscroll-x-contain bg-surface-muted scrollbar-thin"
      :class="
        embedded
          ? 'rounded-none border-0 border-b border-solid border-border p-2 md:p-3'
          : 'rounded-lg border border-solid border-border p-1'
      "
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
        :class="[tabBase, tab.id === active ? tabActive : tabIdle]"
        :aria-selected="tab.id === active ? 'true' : 'false'"
        :aria-controls="panelId(tab.id)"
        :tabindex="tab.id === active ? 0 : -1"
        :disabled="tab.disabled"
        @click="select(tab.id)"
      >
        <BaseIcon v-if="tab.icon" :name="tab.icon" class="ui-tabs__icon size-em-lg" />
        <span class="ui-tabs__label">{{ tab.label }}</span>
      </button>
    </div>

    <!-- Só o painel ativo é renderizado (mesmo comportamento de v-if por aba) -->
    <div
      v-if="activeTab"
      :id="panelId(activeTab.id)"
      :key="activeTab.id"
      class="ui-tabs__panel min-w-0 focus-visible:rounded-md"
      :class="embedded ? 'focus-visible:focus-ring-inset' : 'focus-visible:focus-ring'"
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

// Anel por dentro (focus-ring-inset): a lista tem overflow e cortaria o anel externo
const tabBase =
  'ui-tabs__tab inline-flex shrink-0 grow basis-auto items-center justify-center gap-2 min-h-control px-4 ' +
  'rounded-md border border-solid text-sm font-semibold leading-inherit whitespace-nowrap cursor-pointer ' +
  'transition-colors focus-visible:focus-ring-inset disabled:cursor-not-allowed disabled:opacity-disabled'
// Aba ativa: fundo + borda + sombra + barra inferior (não depende só de cor)
const tabActive = 'bg-surface border-border text-primary shadow-tab-active'
const tabIdle =
  'bg-transparent border-transparent text-text-muted enabled:hover:bg-surface-hover enabled:hover:text-text'

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
