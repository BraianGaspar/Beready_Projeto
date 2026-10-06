<template>
  <!-- Rolagem horizontal (se a tabela passar da largura) contida no wrapper;
       o wrapper só entra na ordem do Tab quando de fato rola. -->
  <div
    ref="wrapRef"
    class="ui-table max-w-full overflow-x-auto overscroll-x-contain focus-visible:focus-ring-inset"
    :style="minWidth ? { '--ui-table-min': minWidth } : undefined"
    :role="scrollable ? 'region' : undefined"
    :aria-labelledby="scrollable && caption ? captionId : undefined"
    :tabindex="scrollable ? 0 : undefined"
  >
    <!-- < 768px: linhas como cards empilhados; >= 768px (md:): tabela tradicional -->
    <table class="ui-table__table block w-full border-collapse md:table md:min-w-table">
      <caption
        v-if="caption"
        :id="captionId"
        class="ui-table__caption px-4 pt-3 text-start text-sm font-semibold text-text"
        :class="captionHidden ? 'sr-only' : 'block md:table-caption'"
      >
        {{ caption }}
      </caption>
      <!-- cabeçalho só para leitores de tela no layout empilhado -->
      <thead
        class="ui-table__head clip-hidden absolute h-px w-px overflow-hidden whitespace-nowrap md:clip-auto md:static md:table-header-group md:h-auto md:w-auto md:overflow-visible"
      >
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            scope="col"
            class="ui-table__th border-0 border-b border-solid border-border bg-surface-muted px-4 py-3 align-middle text-xs font-semibold uppercase tracking-label text-text-muted whitespace-nowrap"
            :class="alignClasses[column.align ?? 'start']"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>
      <tbody class="ui-table__body flex flex-col gap-3 p-3 md:table-row-group md:p-0">
        <tr
          v-for="(row, index) in rows"
          :key="getRowKey(row, index)"
          class="ui-table__row group/row flex flex-col gap-2 rounded-lg border border-solid border-border bg-surface p-4 md:table-row md:rounded-none md:border-0 md:bg-transparent md:p-0 md:transition-colors md:hover:bg-surface-hover"
        >
          <td
            v-for="column in columns"
            :key="column.key"
            :class="[
              tdBase,
              alignClasses[column.align ?? 'start'],
              column.hideLabel ? 'justify-end before:content-none' : tdLabel,
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

// Alinhamento só no layout de tabela (uma classe por célula: text-* não tem ordem garantida entre si)
const alignClasses: Record<NonNullable<TableColumn['align']>, string> = {
  start: 'md:text-start',
  center: 'md:text-center',
  end: 'md:text-end',
}

const tdBase =
  'ui-table__td flex min-w-0 flex-wrap items-center gap-2 text-text wrap-anywhere ' +
  'md:table-cell md:border-0 md:border-b md:border-solid md:border-border md:px-4 md:py-3 md:align-middle ' +
  'md:break-normal md:group-last/row:border-b-0'
// Rótulo da coluna (::before com o data-label), só no layout empilhado
const tdLabel =
  'justify-between before:content-label before:text-xs before:font-semibold before:uppercase ' +
  'before:tracking-label before:text-text-muted md:before:content-none'

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
