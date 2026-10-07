import axios from 'axios'
import { useI18n } from 'vue-i18n'
import type { QuizQuestao, QuizQuestaoInput, QuizQuestaoTipo } from '@/core/types'

// Mesmas regras do backend (QuizQuestaoService)
export const MIN_ALTERNATIVAS = 2
export const MAX_ALTERNATIVAS = 6
export const MAX_QUESTOES = 100
const MAX_TEXTO = 2000
const MAX_RESPOSTA = 500

export interface AlternativaForm {
  key: number
  texto: string
  correta: boolean
}

export interface QuestaoForm {
  key: number
  tipo: QuizQuestaoTipo
  enunciado: string
  resposta_esperada: string
  explicacao: string
  alternativas: AlternativaForm[]
}

// Erros por campo, no formato das chaves do backend: "questoes.{i}.enunciado", "questoes.{i}.alternativas.{j}.texto"
export type QuestoesErrors = Record<string, string>

// Chave estável para o v-for (as questões/alternativas são reordenadas e removidas)
let sequencia = 0
const novaChave = () => ++sequencia

export const novaAlternativa = (correta = false): AlternativaForm => ({ key: novaChave(), texto: '', correta })

export const novaQuestao = (tipo: QuizQuestaoTipo = 'multipla_escolha'): QuestaoForm => ({
  key: novaChave(),
  tipo,
  enunciado: '',
  resposta_esperada: '',
  explicacao: '',
  alternativas:
    tipo === 'multipla_escolha'
      ? [novaAlternativa(true), novaAlternativa(), novaAlternativa(), novaAlternativa()]
      : [],
})

// Questões do editor (GET /quizes/{id}/questoes, com gabarito) -> formulário
export const questoesFromApi = (questoes: QuizQuestao[]): QuestaoForm[] =>
  questoes.map((questao) => ({
    key: novaChave(),
    tipo: questao.tipo,
    enunciado: questao.enunciado,
    resposta_esperada: questao.resposta_esperada ?? '',
    explicacao: questao.explicacao ?? '',
    alternativas: questao.alternativas.map((alternativa) => ({
      key: novaChave(),
      texto: alternativa.texto,
      correta: !!alternativa.correta,
    })),
  }))

// Formulário -> corpo da API
export const questoesToInput = (questoes: QuestaoForm[]): QuizQuestaoInput[] =>
  questoes.map((questao) => ({
    tipo: questao.tipo,
    enunciado: questao.enunciado.trim(),
    explicacao: questao.explicacao.trim() || null,
    ...(questao.tipo === 'completar'
      ? { resposta_esperada: questao.resposta_esperada.trim() }
      : {
          alternativas: questao.alternativas.map((alternativa) => ({
            texto: alternativa.texto.trim(),
            correta: alternativa.correta,
          })),
        }),
  }))

/**
 * Erros da API (422) no formato do CakePHP: { campo: { regra: mensagem } }.
 */
export const getApiErrors = (err: unknown): Record<string, Record<string, string>> => {
  if (axios.isAxiosError<{ errors?: Record<string, Record<string, string>> }>(err)) {
    return err.response?.data?.errors ?? {}
  }
  return {}
}

/**
 * Validação no cliente (mesmas regras do servidor) e tradução dos erros devolvidos pela API.
 */
export function useQuestoesValidacao() {
  const { t } = useI18n()

  const validar = (questoes: QuestaoForm[]): QuestoesErrors => {
    const erros: QuestoesErrors = {}

    if (questoes.length > MAX_QUESTOES) {
      erros.questoes = t('quizEditor.erroMaxQuestoes', { n: MAX_QUESTOES })
    }

    questoes.forEach((questao, i) => {
      const p = `questoes.${i}.`
      const enunciado = questao.enunciado.trim()

      if (!enunciado) erros[`${p}enunciado`] = t('quizEditor.erroEnunciado')
      else if (enunciado.length > MAX_TEXTO) erros[`${p}enunciado`] = t('quizEditor.erroMuitoLongo')

      if (questao.explicacao.trim().length > MAX_TEXTO) erros[`${p}explicacao`] = t('quizEditor.erroMuitoLongo')

      if (questao.tipo === 'completar') {
        const resposta = questao.resposta_esperada.trim()
        if (!resposta) erros[`${p}resposta_esperada`] = t('quizEditor.erroResposta')
        else if (resposta.length > MAX_RESPOSTA) erros[`${p}resposta_esperada`] = t('quizEditor.erroMuitoLongo')
        return
      }

      questao.alternativas.forEach((alternativa, j) => {
        const texto = alternativa.texto.trim()
        if (!texto) erros[`${p}alternativas.${j}.texto`] = t('quizEditor.erroAlternativaTexto')
        else if (texto.length > MAX_TEXTO) erros[`${p}alternativas.${j}.texto`] = t('quizEditor.erroMuitoLongo')
      })

      const total = questao.alternativas.length
      const corretas = questao.alternativas.filter((alternativa) => alternativa.correta).length
      if (total < MIN_ALTERNATIVAS) erros[`${p}alternativas`] = t('quizEditor.erroMinAlternativas', { n: MIN_ALTERNATIVAS })
      else if (total > MAX_ALTERNATIVAS) erros[`${p}alternativas`] = t('quizEditor.erroMaxAlternativas', { n: MAX_ALTERNATIVAS })
      else if (corretas !== 1) erros[`${p}alternativas`] = t('quizEditor.erroUmaCorreta')
    })

    return erros
  }

  // Regra do backend -> mensagem traduzida (o texto do servidor é só em português)
  const traduzirErrosApi = (apiErrors: Record<string, Record<string, string>>): QuestoesErrors => {
    const erros: QuestoesErrors = {}

    Object.entries(apiErrors).forEach(([campo, regras]) => {
      if (!campo.startsWith('questoes')) return
      const regra = Object.keys(regras ?? {})[0] ?? ''
      const nome = campo.split('.').filter((parte) => !/^\d+$/.test(parte)).pop() ?? ''

      let mensagem: string
      if (regra === 'maxLength') mensagem = t('quizEditor.erroMuitoLongo')
      else if (nome === 'enunciado') mensagem = t('quizEditor.erroEnunciado')
      else if (nome === 'resposta_esperada') mensagem = t('quizEditor.erroResposta')
      else if (nome === 'texto') mensagem = t('quizEditor.erroAlternativaTexto')
      else if (nome === 'tipo') mensagem = t('quizEditor.erroTipo')
      else if (nome === 'alternativas' && regra === 'minimo') mensagem = t('quizEditor.erroMinAlternativas', { n: MIN_ALTERNATIVAS })
      else if (nome === 'alternativas' && regra === 'maximo') mensagem = t('quizEditor.erroMaxAlternativas', { n: MAX_ALTERNATIVAS })
      else if (nome === 'alternativas') mensagem = t('quizEditor.erroUmaCorreta')
      else if (campo === 'questoes' && regra === 'maximo') mensagem = t('quizEditor.erroMaxQuestoes', { n: MAX_QUESTOES })
      else mensagem = t('quizEditor.erroRevisar')

      erros[campo] = mensagem
    })

    return erros
  }

  return { validar, traduzirErrosApi }
}
