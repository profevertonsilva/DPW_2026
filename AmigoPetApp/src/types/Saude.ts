export interface Vacina {
  id: number;
  nome: string;
  data_aplicacao: string;
  data_reforco: string | null;
  veterinario_nome: string | null;
  clinica_nome: string | null;
  fk_animal_id: number;
  criado_em: string;
  criado_por: string;
}

export interface AdicionarVacinaRequest {
  nome: string;
  data_aplicacao: string;
  data_reforco?: string | null;
  veterinario_nome?: string;
  clinica_nome?: string;
  fk_animal_id: number;
}

export type TipoProcedimento = 'consulta' | 'cirurgia' | 'exame' | 'castracao' | 'outro';

export interface Procedimento {
  id: number;
  nome: string;
  tipo: TipoProcedimento;
  data: string;
  veterinario_nome: string | null;
  observacoes: string | null;
  anexo_url: string | null;
  fk_animal_id: number;
  criado_em: string;
  criado_por: string;
}

export interface AdicionarProcedimentoRequest {
  nome: string;
  tipo: TipoProcedimento;
  data: string;
  veterinario_nome?: string;
  observacoes?: string;
  anexo_url?: string;
  fk_animal_id: number;
}

export interface SaudeAnimal {
  fk_animal_id: number;
  apto_para_adocao: boolean;
  temperamento: string | null;
  necessidades_especiais: string | null;
  condicao_geral: string | null;
}

export interface CarteiraIdentificacao {
  animal_id: number;
  nome: string;
  especie: string | null;
  raca: string | null;
  data_nascimento: string | null;
  castrado: boolean;
  alergias: string | null;
  foto: string | null;
  qr_code_url: string | null;
  pdf_url: string | null;
}

export type TipoAuditoria =
  | 'registro_inicial'
  | 'atualizacao_pos_tratamento'
  | 'vacinacao'
  | 'procedimento'
  | 'adocao'
  | 'resgate'
  | 'outros';

export interface AuditoriaEntry {
  id: number;
  tipo: TipoAuditoria;
  descricao: string;
  data: string;
  autor_nome: string;
  fk_animal_id: number;
}

export interface AtendimentoResumo {
  animal_id: number;
  animal_nome: string;
  animal_especie: string | null;
  animal_foto: string | null;
  ultima_vacina: string | null;
  ultimo_procedimento: string | null;
}
