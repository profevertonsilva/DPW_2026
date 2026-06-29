import type { Animal, FiltrosAnimal, CadastrarAnimalRequest } from '../../types/Animal';
import { animaisMock } from '../data/animais';

let nextId = animaisMock.length + 1;

export async function listar(filtros?: FiltrosAnimal): Promise<Animal[]> {
  await new Promise(r => setTimeout(r, 600));
  let resultado = animaisMock.filter(a => a.status === 'disponivel');
  if (filtros?.especie) {
    resultado = resultado.filter(a => a.especie?.toLowerCase() === filtros.especie!.toLowerCase());
  }
  if (filtros?.porte) {
    resultado = resultado.filter(a => a.porte === filtros.porte);
  }
  if (filtros?.sexo) {
    resultado = resultado.filter(a => a.sexo === filtros.sexo);
  }
  if (filtros?.busca) {
    const q = filtros.busca.toLowerCase();
    resultado = resultado.filter(a => a.nome.toLowerCase().includes(q));
  }
  return resultado;
}

export async function meus(): Promise<Animal[]> {
  await new Promise(r => setTimeout(r, 500));
  return animaisMock.filter(a => a.ong?.id === 1);
}

export async function buscarPorId(id: number): Promise<Animal> {
  await new Promise(r => setTimeout(r, 400));
  const animal = animaisMock.find(a => a.id === id);
  if (!animal) {
    const err: any = new Error('Animal não encontrado');
    err.response = { status: 404, data: { erro: 'Animal não encontrado' } };
    throw err;
  }
  return animal;
}

export async function cadastrar(req: CadastrarAnimalRequest): Promise<Animal> {
  await new Promise(r => setTimeout(r, 800));
  const novo: Animal = {
    id: nextId++,
    nome: req.nome,
    data_nascimento: req.data_nascimento ?? null,
    sexo: req.sexo,
    especie: null,
    porte: req.porte,
    localizacao: req.local_cidade ? `${req.local_cidade}, ${req.local_estado ?? ''}`.trim() : null,
    foto: req.fotos?.[0] ?? null,
    status: 'disponivel',
    raca: req.raca,
    cor: req.cor,
    descricao: req.descricao,
    historico_resgate: req.historico_resgate,
    fotos: req.fotos,
    alergias: req.alergias,
    castrado: req.castrado,
    data_castracao: req.data_castracao,
    obs_veterinarias: req.obs_veterinarias,
    local_cep: req.local_cep,
    local_logradouro: req.local_logradouro,
    local_numero: req.local_numero,
    local_bairro: req.local_bairro,
    local_cidade: req.local_cidade,
    local_estado: req.local_estado,
    ong: { id: 1, nome: 'Patinhas Felizes' },
  };
  animaisMock.push(novo);
  return novo;
}
