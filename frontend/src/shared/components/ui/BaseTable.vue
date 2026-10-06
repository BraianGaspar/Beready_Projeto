<template>
  <!-- Rolagem horizontal (se a tabela passar da largura) contida no wrapper;
       o wrapper só entra na ordem do Tab quando de fato rola. -->
  <div
    ref="wrapRef"
    class="ui-table"
    :style="minWidth ? { '--ui-table-min': minWidth } : undefined"
    :role="scrollable ? 'region' : undefined"
    :aria-labelledby="scrollable && caption ? captionId : undefined"
    :tabindex="scrollable ? 0 : undefined"
  >
    <table class="ui-table__table">
      <caption v-if="caption" :id="captionId" class="ui-table__caption" :class="{ 'sr-only': captionHidden }">
        {{ caption }}
      </caption>
      <thead class="ui-table__head">
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            scope="col"
            class="ui-table__th"
            :class="column.align && `ui-table__cell--${column.align}`"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>
      <tbody class="ui-table__body">
        <tr v-for="(row, index) in rows" :key="getRowKey(row, index)" class="ui-table__row">
          <td
            v-for="column in columns"
            :key="column.key"
            class="ui-table__td"
            :class="[
              column.align && `ui-table__cell--${column.align}`,
              { 'ui-table__td--no-label': column.hideLabel },
            ]"
            :data-label="column.hideLabel ? undefined : column.label"
          >
            <slot :name="`cell-${column.key}`" :row="row" :value="getValue(row, column.key)" :index="index">
              {{ getValue(row, column.key) }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
export interface TableColumn {
  /** Campo da linha (valor padrão da célula) e nome do slot `cell-<key>` */
  key: string
  /** Cabeçalho da coluna e rótulo da célula no layout empilhado (< 768px) */
  label: string
  align?: 'start' | 'center' | 'end'
  /** Esconde o rótulo no layout empilhado (ex.: coluna de ações) */
  hideLabel?: boolean
}
</script>

<script setup lang="ts" generic="T extends object">
// Tabela responsiva: >= 768px renderiza <table> tradicional; abaixo disso cada linha
// vira um card empilhado e cada célula mostra o rótulo da coluna (data-label).
// A semântica de tabela (caption, th scope, linhas/células) é mantida nos dois layouts.
import { onBeforeUnmount, onMounted, ref, useId } from 'vue'

const props = withDefaults(
  defineProps<{
    columns: TableColumn[]
    rows: T[]
    /** Campo usado como :key das linhas (padrão: índice) */
    rowKey?: string
    /** Legenda da tabela (nome acessível). Use captionHidden se já há título visível */
    caption?: string
    captionHidden?: boolean
    /** Largura mínima da tabela no layout >= 768px (ex.: '44rem'); acima disso rola no wrapper */
    minWidth?: string
  }>(),
  { rowKey: undefined, caption: '', captionHidden: false, minWidth: undefined },
)

defineSlots<
  Record<string, (props: { row: T; value: unknown; index: number }) => unknown>
>()

const captionId = `ui-table-${useId()}-caption`

const getValue = (row: T, key: string): unknown => (row as Record<string, unknown>)[key]

const getRowKey = (row: T, index: number): PropertyKey => {
  if (!props.rowKey) return index
  const value = getValue(row, props.rowKey)
  return typeof value === 'string' || typeof value === 'number' ? value : index
}

// Detecta se o wrapper rola (para torná-lo focável/rotulado só quando necessário)
const wrapRef = ref<HTMLElement | null>(null)
const scrollable = ref(false)
let observer: ResizeObserver | undefined

const measure = () => {
  const el = wrapRef.value
  scrollable.value = !!el && el.scrollWidth > el.clientWidth + 1
}

onMounted(() => {
  measure()
  if (typeof ResizeObserver !== 'undefined' && wrapRef.value) {
    observer = new ResizeObserver(measure)
    observer.observe(wrapRef.value)
    const table = wrapRef.value.querySelector('table')
    if (table) observer.observe(table)
  }
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<style scoped>
.ui-table {
  max-inline-size: 100%;
  overflow-x: auto;
  overscroll-behavior-x: contain;
}

.ui-table:focus-visible {
  outline: var(--focus-ring-width) solid var(--color-focus-ring);
  outline-offset: calc(var(--focus-ring-width) * -1);
}

.ui-table__caption {
  padding: var(--space-3) var(--space-4) 0;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
  text-align: start;
}

/* ---------- Base (< 768px): linhas como cards empilhados ---------- */
.ui-table__table {
  display: block;
  inline-size: 100%;
  border-collapse: collapse;
}

.ui-table__caption:not(.sr-only) {
  display: block;
}

/* cabeçalho só para leitores de tela no layout empilhado */
.ui-table__head {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
}

.ui-table__body {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-3);
}

.ui-table__row {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  padding: var(--space-4);
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.ui-table__td {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-2);
  min-width: 0;
  color: var(--color-text);
  overflow-wrap: anywhere;
}

.ui-table__td::before {
  content: attr(data-label);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.ui-table__td--no-label::before {
  content: none;
}

.ui-table__td--no-label {
  justify-content: flex-end;
}

/* ---------- >= 768px: tabela tradicional ---------- */
@media (min-width: 768px) {
  .ui-table__table {
    display: table;
    min-inline-size: var(--ui-table-min, 100%);
  }

  .ui-table__caption:not(.sr-only) {
    display: table-caption;
  }

  .ui-table__head {
    position: static;
    display: table-header-group;
    width: auto;
    height: auto;
    overflow: visible;
    clip: auto;
  }

  .ui-table__body {
    display: table-row-group;
    padding: 0;
  }

  .ui-table__row {
    display: table-row;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    transition: background-color var(--transition-base);
  }

  .ui-table__row:hover {
    background: var(--color-surface-hover);
  }

  .ui-table__th,
  .ui-table__td {
    display: table-cell;
    padding: var(--space-3) var(--space-4);
    border-block-end: var(--border-width) solid var(--color-border);
    text-align: start;
    vertical-align: middle;
    overflow-wrap: normal;
  }

  .ui-table__th {
    background: var(--color-surface-muted);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
  }

  .ui-table__row:last-child .ui-table__td {
    border-block-end: 0;
  }

  .ui-table__td::before {
    content: none;
  }

  .ui-table__cell--center {
    text-align: center;
  }

  .ui-table__cell--end {
    text-align: end;
  }
}
</style>
