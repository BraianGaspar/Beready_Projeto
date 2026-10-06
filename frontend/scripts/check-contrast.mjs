// Verificação de contraste WCAG dos tokens de cor nos 4 temas.
// Uso (na pasta frontend): node scripts/check-contrast.mjs [src/styles/tokens.css] [all|md]
// Sai com código 1 se algum par ficar abaixo do mínimo.
import fs from 'fs'
const file = process.argv[2] || 'src/styles/tokens.css'
const css = fs.readFileSync(file, 'utf8').replace(/\/\*[\s\S]*?\*\//g, '')

function block(selector) {
  const re = new RegExp(selector.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*\\{([^}]*)\\}')
  const m = css.match(re)
  if (!m) throw new Error('bloco não encontrado: ' + selector)
  const out = {}
  for (const d of m[1].split(';')) {
    const i = d.indexOf(':')
    if (i < 0) continue
    out[d.slice(0, i).trim()] = d.slice(i + 1).trim()
  }
  return out
}
const light = block(':root')
const dark = block('.dark-mode')
const daltL = block('.daltonico-mode')
const daltD = block('.dark-mode.daltonico-mode')
const themes = {
  claro: { ...light },
  escuro: { ...light, ...dark },
  'daltonico-claro': { ...light, ...daltL },
  'daltonico-escuro': { ...light, ...dark, ...daltL, ...daltD },
}
function resolve(t, v, depth = 0) {
  if (depth > 10) return v
  return v.replace(/var\((--[\w-]+)\)/g, (_, n) => resolve(t, t[n] ?? '??', depth + 1))
}
function hex(c) {
  c = c.trim()
  const m = c.match(/^#([0-9a-f]{3,8})$/i)
  if (!m) return null
  let h = m[1]
  if (h.length === 3) h = h.split('').map((x) => x + x).join('')
  return [0, 2, 4].map((i) => parseInt(h.slice(i, i + 2), 16))
}
function lum([r, g, b]) {
  const f = (v) => {
    v /= 255
    return v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4
  }
  return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b)
}
function ratio(a, b) {
  const [x, y] = [lum(a), lum(b)].sort((p, q) => q - p)
  return (x + 0.05) / (y + 0.05)
}
// [texto, fundo, mínimo]
const pairs = [
  ['--color-text', '--color-bg', 4.5],
  ['--color-text', '--color-surface', 4.5],
  ['--color-text', '--color-surface-muted', 4.5],
  ['--color-text-muted', '--color-bg', 4.5],
  ['--color-text-muted', '--color-surface', 4.5],
  ['--color-text-muted', '--color-surface-muted', 4.5],
  ['--color-text-subtle', '--color-surface', 4.5],
  ['--color-primary', '--color-surface', 4.5],
  ['--color-primary', '--color-bg', 4.5],
  ['--color-primary-contrast', '--color-primary', 4.5],
  ['--color-primary-contrast', '--color-primary-hover', 4.5],
  ['--color-primary-soft-text', '--color-primary-soft', 4.5],
  ['--color-hero-text', '--color-hero-from', 4.5],
  ['--color-hero-text', '--color-hero-to', 4.5],
  ['--color-danger-contrast', '--color-danger-hover', 4.5],
  ['--color-success-contrast', '--color-success-hover', 4.5],
  ['--color-border-strong', '--color-surface', 3],
  ['--color-focus-ring', '--color-surface', 3],
  ['--color-focus-ring', '--color-bg', 3],
]
for (const s of ['success', 'warning', 'danger', 'info']) {
  pairs.push([`--color-${s}`, '--color-surface', 4.5])
  pairs.push([`--color-${s}`, `--color-${s}-soft`, 4.5])
  pairs.push([`--color-${s}-contrast`, `--color-${s}`, 4.5])
}
const results = {}
let fails = 0
for (const [name, t] of Object.entries(themes)) {
  results[name] = []
  for (const [fg, bg, min] of pairs) {
    const a = hex(resolve(t, t[fg] ?? '??'))
    const b = hex(resolve(t, t[bg] ?? '??'))
    if (!a || !b) {
      results[name].push({ fg, bg, r: 'n/a', ok: false })
      fails++
      continue
    }
    const r = ratio(a, b)
    const ok = r >= min
    if (!ok) fails++
    results[name].push({ fg, bg, min, r: r.toFixed(2), ok })
  }
}
if (process.argv[3] === 'md') {
  const names = Object.keys(themes)
  console.log('| Texto / elemento | Fundo | Mín. | ' + names.join(' | ') + ' |')
  console.log('|---|---|---|' + names.map(() => '---').join('|') + '|')
  pairs.forEach(([fg, bg, min], i) => {
    console.log(`| \`${fg}\` | \`${bg}\` | ${min} | ` + names.map((n) => results[n][i].r + (results[n][i].ok ? '' : ' ❌')).join(' | ') + ' |')
  })
} else {
  for (const [n, rs] of Object.entries(results)) {
    console.log('== ' + n)
    for (const x of rs) if (!x.ok || process.argv[3] === 'all') console.log(`${x.ok ? 'ok ' : 'FAIL'} ${x.fg} on ${x.bg}: ${x.r}`)
  }
}
console.log(`falhas: ${fails}`)
if (fails > 0) process.exitCode = 1
