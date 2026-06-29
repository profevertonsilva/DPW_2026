import { client } from '../client';
import { ENDPOINTS } from '../endpoints';
import { USE_MOCKS } from '../../config';
import * as mock from '../../mocks/handlers/animal';
import type { Animal, Especie, FiltrosAnimal, HistoricoAnimal, CadastrarAnimalRequest } from '../../types/Animal';

export const animalService = {
  async listar(filtros?: FiltrosAnimal): Promise<Animal[]> {
    if (USE_MOCKS) return mock.listar(filtros);
    const { data } = await client.get<Animal[]>(ENDPOINTS.animais.listar, { params: filtros });
    return data;
  },

  async meus(): Promise<Animal[]> {
    if (USE_MOCKS) return mock.meus();
    const { data } = await client.get<Animal[]>(ENDPOINTS.animais.meus);
    return data;
  },

  async buscarPorId(id: number): Promise<Animal> {
    if (USE_MOCKS) return mock.buscarPorId(id);
    const { data } = await client.get<Animal>(ENDPOINTS.animais.buscarPorId(id));
    return data;
  },

  async cadastrar(req: CadastrarAnimalRequest): Promise<Animal> {
    if (USE_MOCKS) return mock.cadastrar(req);
    const { data } = await client.post<Animal>(ENDPOINTS.animais.cadastrar, req);
    return data;
  },

  async especies(): Promise<Especie[]> {
    if (USE_MOCKS) return [
      { id: 1, nome: 'Cachorro' }, { id: 2, nome: 'Gato' }, { id: 3, nome: 'Ave' },
      { id: 4, nome: 'Coelho' }, { id: 5, nome: 'Hamster' }, { id: 6, nome: 'Outro' },
    ];
    const { data } = await client.get<Especie[]>(ENDPOINTS.especies.listar);
    return data;
  },

  async historico(id: number): Promise<HistoricoAnimal[]> {
    if (USE_MOCKS) { await new Promise(r => setTimeout(r, 400)); return []; }
    const { data } = await client.get<HistoricoAnimal[]>(ENDPOINTS.animais.historico(id));
    return data;
  },
};
