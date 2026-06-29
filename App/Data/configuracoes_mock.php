<?php
/**
 * AmigoPet - Mock de Banco de Dados das Configurações
 */

// Configurações Globais do Sistema
$configGeralMock = array (
  'dashboard_titulo' => 'Bem vindo ao Painel de Adoção 🐾',
  'dashboard_subtitulo' => 'Bem-vindo ao painel de adoção do AmigoPet.',
);

// Mock de Publicações e Notícias
$publicacoesMock = array (
  0 => 
  array (
    'id' => 1,
    'titulo' => 'Max - Golden Retriever',
    'tipo' => 'Adoção',
    'autor' => 'ONG Vida Animal',
    'data' => '15/05/2026',
    'status' => 'Ativo',
    'descricao' => 'sei lah',
    'animal_id' => '20',
    'animal_nome' => 'Gato Preguiça',
    'animal_especie' => 'Gato',
    'animal_imagem' => '/resources/dashboard/images/animais/animal_20_gato_preguica_20260629_89e9b1.jpg',
  ),
  1 => 
  array (
    'id' => 2,
    'titulo' => 'Feira de Adoção',
    'tipo' => 'Notícia',
    'autor' => 'Admin',
    'data' => '14/05/2026',
    'status' => 'Ativo',
  ),
  2 => 
  array (
    'id' => 3,
    'titulo' => 'Luna - Gato Siamês',
    'tipo' => 'Adoção',
    'autor' => 'Gatinhos Felizes',
    'data' => '10/05/2026',
    'status' => 'Oculto',
  ),
);

// Mock do Carrossel de Animais
$carrosselMock = array (
  0 => 
  array (
    'id' => 101,
    'nome' => 'Max',
    'especie' => 'Cachorro',
    'imagem' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=100',
  ),
  1 => 
  array (
    'id' => 102,
    'nome' => 'Luna',
    'especie' => 'Gato',
    'imagem' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=100',
  ),
  2 => 
  array (
    'id' => 103,
    'nome' => 'Bolinha',
    'especie' => 'Cachorro',
    'imagem' => 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=100',
  ),
  3 => 
  array (
    'id' => 1782762816,
    'animal_id' => '28',
    'nome' => 'bola',
    'especie' => 'Cachorro',
    'imagem' => '/resources/dashboard/images/animais/animal_bola_20260629_0bd1a7.jpg',
  ),
  4 => 
  array (
    'id' => 1782762827,
    'animal_id' => '25',
    'nome' => 'Toquinho',
    'especie' => 'Cachorro',
    'imagem' => '/resources/dashboard/images/animais/animal_25_toquinho_20260629_1694a8.jpg',
  ),
  5 => 
  array (
    'id' => 1782762836,
    'animal_id' => '19',
    'nome' => 'Zorro',
    'especie' => 'Gato',
    'imagem' => '/resources/dashboard/images/animais/animal_19_zorro_20260629_9759ad.jpg',
  ),
  6 => 
  array (
    'id' => 1782763724,
    'animal_id' => '15',
    'nome' => 'Zézinho',
    'especie' => 'Cachorro',
    'imagem' => '/resources/dashboard/images/animais/animal_15_z_ezinho_20260613_731255.jpg',
  ),
);

// Mock de Logs de Auditoria
$logsMock = array (
  0 => 
  array (
    'data' => '2026-05-20 10:45:12',
    'user' => 'Admin (ID:1)',
    'acao' => 'Usuário criado: ID 45 - nome: Joana',
    'ip' => '192.168.1.10',
  ),
  1 => 
  array (
    'data' => '2026-05-19 15:20:03',
    'user' => 'Moderador (ID:3)',
    'acao' => 'Publicação removida: ID 122',
    'ip' => '172.16.0.5',
  ),
  2 => 
  array (
    'data' => '2026-05-18 09:10:45',
    'user' => 'Sistema',
    'acao' => 'Backup automático concluído',
    'ip' => '127.0.0.1',
  ),
  3 => 
  array (
    'data' => '2026-05-17 11:02:22',
    'user' => 'ONG Patinhas (ID:12)',
    'acao' => 'Animal colocado em adoção: ID 101 - Max',
    'ip' => '200.137.10.5',
  ),
  4 => 
  array (
    'data' => '2026-05-16 08:44:01',
    'user' => 'Veterinário (ID:7)',
    'acao' => 'Vacina registrada: ID 101 - Raiva',
    'ip' => '10.0.0.8',
  ),
);
?>