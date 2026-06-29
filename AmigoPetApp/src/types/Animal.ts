export type AnimalPorte = 'pequeno' | 'medio' | 'grande' | 'gigante';
export type AnimalSexo = 'm' | 'f' | 'n/a';
export type AnimalStatus = 'disponivel' | 'adotado' | 'em_tratamento' | 'reservado';

export interface Animal {
  id: number;
  nome: string;
  data_nascimento: string | null;
  sexo: AnimalSexo;
  especie: string | null;
  porte: AnimalPorte | null;
  localizacao: string | null;
  foto: string | null;
  status: AnimalStatus;
  raca?: string;
  idade_anos?: number;
  ong?: {
    id: number;
    nome: string;
  };
  // Campos detalhados (retornados pelo GET /animais/{id} e cadastro)
  cor?: string;
  descricao?: string;
  historico_resgate?: string;
  fotos?: string[];
  alergias?: string;
  castrado?: boolean;
  data_castracao?: string | null;
  obs_veterinarias?: string;
  local_cep?: string;
  local_logradouro?: string;
  local_numero?: string;
  local_bairro?: string;
  local_cidade?: string;
  local_estado?: string;
}

export interface HistoricoAnimal {
  id: number;
  descricao: string;
  data: string;
  tipo: string;
  fk_animal_id: number;
  autor_nome?: string;
  fk_ong_id?: number | null;
  fk_veterinario_id?: number | null;
}

export interface FiltrosAnimal {
  especie?: string;
  porte?: AnimalPorte;
  sexo?: AnimalSexo;
  localizacao?: string;
  busca?: string;
}

export interface Especie {
  id: number;
  nome: string;
}

export interface CadastrarAnimalRequest {
  nome: string;
  fk_especie_id: number;
  raca?: string;
  cor?: string;
  sexo: AnimalSexo;
  data_nascimento?: string | null;
  idade_estimada_anos?: number | null;
  porte: AnimalPorte;
  descricao?: string;
  historico_resgate?: string;
  fotos?: string[];
  local_cep?: string;
  local_logradouro?: string;
  local_numero?: string;
  local_bairro?: string;
  local_cidade?: string;
  local_estado?: string;
  alergias?: string;
  castrado: boolean;
  data_castracao?: string | null;
  obs_veterinarias?: string;
}
