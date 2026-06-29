export interface Clinica {
  id: number;
  nome: string;
  cnpj: string;
  email: string;
  telefone_1: string;
  telefone_2?: string;
  cep: string;
  logradouro: string;
  numero: number;
  bairro: string;
  cidade: string;
  estado: string;
  complemento?: string;
  foto?: string | null;
}

export interface CadastrarClinicaRequest {
  nome: string;
  cnpj: string;
  email: string;
  telefone_1: string;
  telefone_2?: string;
  cep: string;
  logradouro: string;
  numero: number;
  bairro: string;
  cidade: string;
  estado: string;
  complemento?: string;
}
