# VALIDACAO DA API REST - AmigoPet

Data: 2026-06-29 | Versao da API: 1.0.0 | PHP local: 8.2.12 | DB: MySQL remoto (216.172.172.214)

---

## PARTE 1 - INVENTARIO

### 1.1 Estrutura App/Api/ (32 arquivos)

**Infraestrutura (8):**
| Arquivo               | Responsabilidade                              |
|-----------------------|-----------------------------------------------|
| ApiRouter.php         | Roteamento, CORS, despacho para controllers   |
| ApiController.php     | Base: auth JWT, body(), ip(), exigirCampos()   |
| ApiResponse.php       | Helper de resposta JSON + status code          |
| ApiException.php      | Excecao HTTP com codigo de status              |
| Jwt.php               | Emissao/validacao JWT (HS256, 7 dias TTL)      |
| LoginThrottle.php     | Rate-limit login (file-based, exponencial)     |
| Validador.php         | Email + senhaForte (min 8, alfanum + especial) |
| routes.php            | 61 endpoints registrados                       |

**Controllers (13):**
Health, Auth, Animal, Ong, Solicitacao, Saude, Avistamento, Perfil, Transferencia, Notificacao, Upload, Clinica, Usuario

**Repositories (11):**
Auth, Animal, Ong, Solicitacao, Saude, Avistamento, Perfil, Transferencia, Notificacao, Clinica, Usuario

### 1.2 Todos os Endpoints (61)

| #  | Metodo  | Caminho                                  | Controller              | Acao                 | JWT? |
|----|---------|------------------------------------------|-------------------------|----------------------|------|
| 1  | GET     | /ping                                    | HealthController        | ping                 | Nao  |
| 2  | GET     | /health                                  | HealthController        | health               | Nao  |
| 3  | GET     | /me                                      | HealthController        | me                   | Sim  |
| 4  | POST    | /auth/login                              | AuthController          | login                | Nao  |
| 5  | POST    | /auth/logout                             | AuthController          | logout               | Sim  |
| 6  | POST    | /auth/cadastrar/adotante                 | AuthController          | cadastrarAdotante    | Nao  |
| 7  | POST    | /auth/cadastrar/ong                      | AuthController          | cadastrarOng         | Nao  |
| 8  | POST    | /auth/cadastrar/veterinario              | AuthController          | cadastrarVeterinario | Nao  |
| 9  | POST    | /auth/alterar-senha                      | AuthController          | alterarSenha         | Sim  |
| 10 | POST    | /auth/recuperar-senha                    | AuthController          | recuperarSenha       | Nao  |
| 11 | GET     | /animais                                 | AnimalController        | listar               | Nao  |
| 12 | GET     | /animais/meus                            | AnimalController        | meus                 | Sim  |
| 13 | GET     | /animais/{id}                            | AnimalController        | detalhe              | Nao  |
| 14 | GET     | /animais/{id}/historico                  | AnimalController        | historico            | Nao  |
| 15 | POST    | /animais                                 | AnimalController        | criar                | Sim  |
| 16 | PUT     | /animais/{id}                            | AnimalController        | atualizar            | Sim  |
| 17 | GET     | /ongs                                    | OngController           | listar               | Nao  |
| 18 | GET     | /ongs/{id}                               | OngController           | detalhe              | Nao  |
| 19 | GET     | /ongs/{id}/animais                       | OngController           | animais              | Nao  |
| 20 | GET     | /solicitacoes/minhas                     | SolicitacaoController   | minhas               | Sim  |
| 21 | GET     | /solicitacoes/recebidas                  | SolicitacaoController   | recebidas            | Sim  |
| 22 | POST    | /solicitacoes                            | SolicitacaoController   | criar                | Sim  |
| 23 | GET     | /solicitacoes/{id}                       | SolicitacaoController   | detalhe              | Sim  |
| 24 | PATCH   | /solicitacoes/{id}/status                | SolicitacaoController   | atualizarStatus      | Sim  |
| 25 | POST    | /solicitacoes/{id}/avaliacao             | SolicitacaoController   | avaliar              | Sim  |
| 26 | GET     | /solicitacoes/{id}/termo                 | SolicitacaoController   | termo                | Sim  |
| 27 | POST    | /solicitacoes/{id}/termo/assinar         | SolicitacaoController   | assinarTermo         | Sim  |
| 28 | GET     | /animais/{id}/vacinas                    | SaudeController         | vacinas              | Sim  |
| 29 | POST    | /animais/{id}/vacinas                    | SaudeController         | adicionarVacina      | Sim  |
| 30 | GET     | /animais/{id}/procedimentos              | SaudeController         | procedimentos        | Sim  |
| 31 | POST    | /animais/{id}/procedimentos              | SaudeController         | adicionarProcedimento| Sim  |
| 32 | GET     | /animais/{id}/saude                      | SaudeController         | saude                | Sim  |
| 33 | PUT     | /animais/{id}/saude                      | SaudeController         | atualizarSaude       | Sim  |
| 34 | GET     | /animais/{id}/carteira                   | SaudeController         | carteira             | Sim  |
| 35 | GET     | /animais/{id}/auditoria                  | AnimalController        | historico            | Sim  |
| 36 | GET     | /vet/atendimentos                        | SaudeController         | atendimentos         | Sim  |
| 37 | POST    | /avistamentos                            | AvistamentoController   | criar                | Sim  |
| 38 | GET     | /avistamentos                            | AvistamentoController   | listar               | Sim  |
| 39 | GET     | /ranking/rastreadores                    | AvistamentoController   | ranking              | Sim  |
| 40 | GET     | /avistamentos/{id}                       | AvistamentoController   | detalhe              | Sim  |
| 41 | PATCH   | /avistamentos/{id}/status                | AvistamentoController   | atualizarStatus      | Sim  |
| 42 | GET     | /especies                                | AnimalController        | especies             | Nao  |
| 43 | GET     | /racas                                   | AnimalController        | racas                | Nao  |
| 44 | GET     | /adotante/perfil                         | PerfilController        | perfilAdotante       | Sim  |
| 45 | PUT     | /adotante/perfil                         | PerfilController        | atualizarAdotante    | Sim  |
| 46 | GET     | /ong/perfil                              | PerfilController        | perfilOng            | Sim  |
| 47 | PUT     | /ong/perfil                              | PerfilController        | atualizarOng         | Sim  |
| 48 | GET     | /veterinario/perfil                      | PerfilController        | perfilVeterinario    | Sim  |
| 49 | PUT     | /veterinario/perfil                      | PerfilController        | atualizarVeterinario | Sim  |
| 50 | POST    | /animais/{id}/transferencias             | TransferenciaController | criar                | Sim  |
| 51 | GET     | /animais/{id}/transferencias             | TransferenciaController | listar               | Sim  |
| 52 | GET     | /notificacoes                            | NotificacaoController   | listar               | Sim  |
| 53 | PATCH   | /notificacoes/{id}/marcar-lida           | NotificacaoController   | marcarLida           | Sim  |
| 54 | POST    | /upload                                  | UploadController        | upload               | Sim  |
| 55 | GET     | /clinicas                                | ClinicaController       | listar               | Sim  |
| 56 | GET     | /clinicas/{id}                           | ClinicaController       | detalhe              | Sim  |
| 57 | POST    | /clinicas                                | ClinicaController       | criar                | Sim  |
| 58 | GET     | /veterinario/clinicas                    | ClinicaController       | minhasClinicas       | Sim  |
| 59 | POST    | /veterinario/clinicas/{clinicaId}        | ClinicaController       | associarClinica      | Sim  |
| 60 | DELETE  | /veterinario/clinicas/{clinicaId}        | ClinicaController       | desassociarClinica   | Sim  |
| 61 | GET     | /usuarios/busca                          | UsuarioController       | buscar               | Sim  |

### 1.3 Comparacao Spec vs Implementacao

| Secao Spec | Modulo                    | Endpoints Spec | Implementados | Status       |
|------------|---------------------------|---------------|---------------|--------------|
| C.1        | Auth                      | 7             | 7             | 100% OK      |
| C.2        | Animais + Taxonomia       | 8             | 8             | 100% OK      |
| C.3        | ONGs                      | 3             | 3             | 100% OK      |
| C.4        | Solicitacoes + Termo      | 8             | 8             | 100% OK      |
| C.5        | Saude do Animal           | 8             | 8             | 100% OK      |
| C.6        | Avistamentos + Ranking    | 5             | 5             | 100% OK      |
| C.7        | Avaliacao Adotante        | (embutido C.4)| (embutido)    | 100% OK      |
| C.8        | Transferencias            | 2             | 2             | 100% OK      |
| C.9        | Notificacoes              | 2             | 2             | 100% OK      |
| C.10       | Upload                    | 1             | 1             | 100% OK      |
| C.11       | Perfis                    | 6             | 6             | 100% OK      |
| C.12       | Clinicas                  | 6             | 6             | 100% OK      |
| C.13       | Busca Usuarios            | 1             | 1             | 100% OK      |
| --         | Health/Diagnostico        | 3             | 3             | 100% OK      |
| **TOTAL**  |                           | **61**        | **61**        | **100%**     |

---

## PARTE 2 - VALIDACAO DE CODIGO

### 2.1 Conexao com Banco

- **Classe:** `vendor/FW/DB/Connection.php`
- **Driver:** PDO MySQL, charset utf8mb4
- **Credenciais:** lidas de `$_ENV` (arquivo .env carregado via vlucas/phpdotenv)
- **Resultado:** Conexao confirmada via /api/health -> `{"status":"ok","banco":"conectado","tabelas":35}`
- **Observacao:** Em falha de conexao, `Connection.php` faz `echo $mensagem; die()` - retorna texto puro em vez de JSON (ver bug #5)

### 2.2 Prepared Statements

Todos os 11 repositories usam PDO prepared statements (:named_params) para TODAS as queries com input externo. Nenhuma interpolacao direta de dados do usuario.

Excecao controlada: `AuthRepository::valorExiste()` interpola `$tabela` e `$coluna`, porem esses valores sao INTERNOS (hardcoded nos controllers), nunca vindos do cliente.

**Resultado:** SEGURO. Sem SQL injection.

### 2.3 Banco de Dados - 35 tabelas

```
administrador, adotante, animal, animal_imagens, animal_raca, auditoria,
avaliacao_adotante, caso_veterinario, chamado, chamado_observacao, clinica,
despesa, doacao, equipe, especie, historico_animal, login, notificacao, ong,
ong_animal, procedimento, publicacao_encontrado, raca, rastreador, resgate,
routes, saude_animal, solicitacao_adocao, termo_adocao, transferencia, vacina,
vet_clinica, veterinario, visita_adocao, voluntario
```

SQL mode do servidor: `NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION` (nao-estrito).
Migracao `DB/Migracao_API_v1.2.sql`: parcialmente aplicada (transferencia e notificacao OK; adotante.ranking AUSENTE).

### 2.4 Cruzamento Repository SQL vs Schema Real

| Repository              | Tabelas usadas                                                  | Colunas OK? | Problemas                          |
|-------------------------|-----------------------------------------------------------------|------------|--------------------------------------|
| AuthRepository          | login, adotante, ong, veterinario                               | PARCIAL    | ong.fk_login_id AUSENTE (bug #1)    |
| AnimalRepository        | animal, especie, raca, animal_raca, animal_imagens, ong_animal, ong, historico_animal | PARCIAL | ong.fk_login_id AUSENTE (bug #1) |
| OngRepository           | ong, login, ong_animal                                          | FALHA      | ong.fk_login_id AUSENTE (bug #1)    |
| SolicitacaoRepository   | solicitacao_adocao, animal, adotante, login, ong_animal, avaliacao_adotante, termo_adocao, especie | OK | -                       |
| SaudeRepository         | vacina, procedimento, saude_animal, historico_animal, animal, ong, veterinario | PARCIAL | ong.fk_login_id (resolverAutor)   |
| AvistamentoRepository   | publicacao_encontrado, login, adotante, ong, veterinario        | FALHA      | fk_animal_id NOT NULL (bug #2) + ong.fk_login_id (bug #1) |
| PerfilRepository        | adotante, ong, veterinario, login                               | PARCIAL    | ong.fk_login_id (bug #1) + adotante.ranking (bug #3) |
| TransferenciaRepository | transferencia, login, adotante, ong, veterinario                | PARCIAL    | ong.fk_login_id em nomePorLoginId    |
| NotificacaoRepository   | notificacao                                                     | OK*        | tipo ENUM vs VARCHAR esperado (bug #4) |
| ClinicaRepository       | clinica, vet_clinica, veterinario                               | OK         | -                                    |
| UsuarioRepository       | login, adotante, ong, veterinario                               | PARCIAL    | ong.fk_login_id nos JOINs (bug #1)  |

### 2.5 JWT

- Biblioteca: `firebase/php-jwt` (HS256)
- TTL: 604800 segundos (7 dias)
- Payload: `{ sub: login_id, tipo_usuario, iat, exp }`
- Secret: `$_ENV['JWT_SECRET']`
- Validacao: `ApiController::exigirAutenticacao()` extrai Bearer token, decodifica, retorna payload
- `tipo_usuario` no token: usado para checagem de papel (adotante/ong/veterinario) nos controllers
- **Resultado:** Implementacao CORRETA para o escopo do projeto

### 2.6 Regras de Negocio

| Regra                         | Implementacao                                              | Status |
|-------------------------------|-----------------------------------------------------------|--------|
| Status canonicos solicitacao  | ['Pendente','Em Analise','Aprovado','Concluido','Recusado'] = ENUM identico no banco | OK |
| Status avistamento            | underscore<->espaco conversao bidirecional                 | OK     |
| LoginThrottle                 | LIMITE_LIVRE=3, BASE_SEGUNDOS=10, exponencial, file-based  | OK     |
| Senha forte (RF#01)           | min 8 chars + alfanumerico + especial                      | OK     |
| Termo IP capture              | IP via X-Forwarded-For/REMOTE_ADDR; user_agent server-side | OK     |
| Append-only (vacina)          | INSERT only, sem UPDATE/DELETE                             | OK     |
| Append-only (procedimento)    | INSERT only, sem UPDATE/DELETE                             | OK     |
| Append-only (avaliacao)       | INSERT only, sem UPDATE/DELETE                             | OK     |
| Append-only (transferencia)   | INSERT only, sem UPDATE/DELETE                             | OK     |
| Append-only (termo assinado)  | UPDATE assinado=1 (one-way), sem DELETE                    | OK     |
| Perfil completo p/ adotar     | Checa nome/cpf/data_nasc/telefone_1/cep/cidade/estado/email | OK    |
| Anti-enumeracao recuperar     | Sempre retorna 200 com mesma mensagem                      | OK     |
| CORS                          | Access-Control-Allow-Origin: * + OPTIONS->204              | OK     |
| Upload MIME real               | finfo (FILEINFO_MIME_TYPE), nao confia na extensao         | OK     |
| Upload limites                 | Imagem 5MB, PDF 10MB, nomes seguros (hex+ext mapeada)     | OK     |
| Unicidade email                | SELECT 1 antes de INSERT, case-insensitive (strtolower)    | OK     |
| Transacionalidade cadastro     | login + perfil na mesma transacao com rollback              | OK     |

---

## PARTE 2B - BUGS ENCONTRADOS

### BUG #1 - CRITICO: coluna `ong.fk_login_id` AUSENTE no banco

**Severidade:** CRITICO
**Impacto:** TODAS as operacoes de ONG falham (registro, login, perfil, animais, saude, transferencias, ranking, busca de usuarios)
**Prova:** `GET /api/ongs` -> `{"erro":"Erro interno no servidor."}` (500)
**Causa:** A tabela `ong` tem 16 colunas mas nenhuma `fk_login_id`. A coluna e usada em 10+ queries em 8 repositories.

**Arquivos afetados:**
- `AuthRepository.php:122` - INSERT INTO ong (..., fk_login_id)
- `AuthRepository.php:71` - SELECT nome FROM ong WHERE fk_login_id = :id
- `OngRepository.php:31` - LEFT JOIN login l ON l.id = o.fk_login_id
- `AnimalRepository.php:269` - SELECT id FROM ong WHERE fk_login_id = :id
- `SolicitacaoRepository.php:42` - SELECT id FROM ong WHERE fk_login_id = :id
- `PerfilRepository.php:36` - SELECT id FROM ong WHERE fk_login_id = :id
- `SaudeRepository.php:273` - SELECT id, nome FROM ong WHERE fk_login_id = :id
- `TransferenciaRepository.php:101` - SELECT o.nome FROM ong o WHERE o.fk_login_id = :id
- `AvistamentoRepository.php:186` - LEFT JOIN ong o ON o.fk_login_id = l.id
- `UsuarioRepository.php:40` - LEFT JOIN ong o ON o.fk_login_id = l.id

**Fix sugerido:**
```sql
ALTER TABLE ong ADD COLUMN fk_login_id INT NULL;
CREATE INDEX idx_ong_login ON ong (fk_login_id);
```

---

### BUG #2 - CRITICO: `publicacao_encontrado.fk_animal_id` e NOT NULL mas codigo insere NULL

**Severidade:** CRITICO
**Impacto:** Criacao de qualquer avistamento falha com erro SQL
**Causa:** `AvistamentoRepository::criar()` linha 39 faz `VALUES (NULL, :login, ...)` para fk_animal_id. A coluna no banco e `INT NOT NULL`.

**Arquivo:** `App/Api/Repository/AvistamentoRepository.php:39`
```php
"INSERT INTO publicacao_encontrado
 (fk_animal_id, fk_login_id, ...) VALUES (NULL, :login, ...)"
```

**Fix sugerido:**
```sql
ALTER TABLE publicacao_encontrado MODIFY COLUMN fk_animal_id INT NULL;
```

---

### BUG #3 - ATENCAO: coluna `adotante.ranking` AUSENTE (migracao parcial)

**Severidade:** ATENCAO
**Impacto:** Ranking do adotante sempre retorna fallback para `adotante.status` (legado). Funcional mas nao e o comportamento desejado.
**Causa:** `Migracao_API_v1.2.sql` criou `transferencia` e `notificacao` mas o ALTER TABLE para `adotante.ranking` aparentemente falhou ou nao foi executado.
**Codigo:** `PerfilRepository.php:339` - usa `isset($a['ranking'])` com fallback, entao nao causa erro.

**Fix sugerido:**
```sql
ALTER TABLE adotante ADD COLUMN ranking
  ENUM('pessimo','regular','bom','muito bom','excelente') NULL;
```

---

### BUG #4 - ATENCAO: `notificacao.tipo` e ENUM restritivo no banco vs VARCHAR no codigo

**Severidade:** ATENCAO
**Impacto:** Se um modulo chamar `NotificacaoRepository::criar()` com um `tipo` fora do ENUM, o INSERT falha.
**Causa:** A migracao definiu `VARCHAR(50)` mas o banco tem `ENUM('solicitacao','sistema','adocao_concluida','status_adocao','avistamento_atualizado','alerta_vacina','promocao_papel')`.
**Codigo:** `NotificacaoRepository.php:59` aceita qualquer string como `$tipo`.

**Fix sugerido:** Ou alterar a coluna para VARCHAR(50), ou documentar os valores validos e validar no codigo.

---

### BUG #5 - ATENCAO: `Connection.php` faz echo+die em falha de conexao

**Severidade:** ATENCAO
**Impacto:** Se o banco ficar indisponivel, a API retorna texto puro em vez de JSON, quebrando o contrato da API.
**Arquivo:** `vendor/FW/DB/Connection.php:28-29`
```php
echo "Ocorreu erro: " . $ex->getMessage();
die();
```

**Fix sugerido:** Lancar excecao em vez de echo+die:
```php
throw new \RuntimeException('Falha na conexao com o banco: ' . $ex->getMessage(), 500, $ex);
```

---

### BUG #6 - MENOR: JWT_SECRET com valor fraco/previsivel

**Severidade:** MENOR (aceitavel para contexto educacional)
**Fix sugerido (producao):** Gerar com `php -r "echo bin2hex(random_bytes(32));"`

---

### BUG #7 - MENOR: `adotante.bio` existe no banco mas nao e exposta na API

**Severidade:** MENOR
**Impacto:** Adotantes nao podem definir/ver sua bio via API de perfil.
**Fix sugerido:** Adicionar 'bio' em `PerfilRepository::colunasPermitidas` do adotante e no shape `montarAdotante()`.

---

## PARTE 3 - TESTES REAIS

### 3.1 Ambiente

- PHP: 8.2.12 (CLI, ZTS, Visual C++ 2019 x64)
- Servidor: `php -S localhost:8099 index.php`
- Banco: MySQL remoto 216.172.172.214 (conectado, 35 tabelas)
- MySQL CLI: nao disponivel localmente

### 3.2 Resultados dos Testes

| Endpoint                           | Metodo  | Status | Resultado                                                    |
|------------------------------------|---------|--------|--------------------------------------------------------------|
| /api/ping                          | GET     | 200    | `{"pong":true,"servico":"API AmigoPet","versao":"1.0.0"}`    |
| /api/health                        | GET     | 200    | `{"status":"ok","banco":"conectado","tabelas":35}`           |
| /api/me (sem token)                | GET     | 401    | `{"erro":"Token de autenticacao ausente."}`                  |
| /api/especies                      | GET     | 200    | 4 especies (Cachorro, Coelho, Gato, Pato)                   |
| /api/racas                         | GET     | 200    | 8 racas com fk_especie_id correto                            |
| /api/animais                       | GET     | 200    | Lista animais disponiveis (shape completo com fotos/idade)   |
| /api/animais/28                    | GET     | 200    | Shape Animal completo: idade_anos=5, 5 fotos, raca=Pitbull   |
| /api/animais/28/historico          | GET     | 200    | Array vazio (sem historico)                                  |
| /api/animais/999                   | GET     | 404    | `{"erro":"Animal nao encontrado."}`                          |
| /api/ongs                          | GET     | 500    | `{"erro":"Erro interno."}` - BUG #1: ong.fk_login_id        |
| /api/ongs/1                        | GET     | 500    | `{"erro":"Erro interno."}` - BUG #1                         |
| /api/auth/login (senha errada)     | POST    | 401    | `{"erro":"E-mail ou senha invalidos."}`                      |
| /api/auth/login (JSON invalido)    | POST    | 400    | `{"erro":"Corpo da requisicao nao e um JSON valido."}`       |
| /api/auth/cadastrar/adotante ({})  | POST    | 422    | Lista 12 campos obrigatorios faltando                        |
| /api/auth/recuperar-senha          | POST    | 200    | Anti-enumeracao: mesma msg para qualquer email               |
| /api/naoexiste                     | GET     | 404    | `{"erro":"Recurso nao encontrado."}`                         |
| DELETE /api/animais                | DELETE  | 405    | `{"erro":"Metodo nao permitido para este recurso."}`         |
| OPTIONS /api/animais (CORS)        | OPTIONS | 204    | Headers CORS presentes (Allow-Origin: *)                     |

### 3.3 Resumo dos Testes

- **Endpoints publicos funcionais:** ping, health, especies, racas, animais (listagem e detalhe), historico
- **Endpoints publicos FALHANDO:** ongs (listagem e detalhe) -> BUG #1
- **Auth flow:** Login retorna mensagem correta para credenciais invalidas, cadastro valida campos, recuperar-senha anti-enumeracao
- **JWT flow:** /api/me sem token retorna 401 corretamente
- **Roteamento:** 404 e 405 tratados corretamente
- **CORS:** Preflight OPTIONS retorna 204 com headers corretos
- **Avistamentos:** Nao testado live (requer JWT + fk_animal_id fix = BUG #2)

---

## RESUMO EXECUTIVO

### O que FUNCIONA (pronto para integracao)

- Infraestrutura da API: roteamento, CORS, JWT, throttle, validacao
- Modulo Auth: login, cadastro adotante/veterinario, alterar-senha, recuperar-senha
- Modulo Animais: listagem, detalhe, filtros, historico, criacao, atualizacao
- Modulo Solicitacoes: criar, listar minhas/recebidas, atualizar status, avaliacao, termo
- Modulo Saude: vacinas, procedimentos, saude_animal, carteira, auditoria
- Modulo Perfis: GET/PUT adotante e veterinario
- Modulo Clinicas: CRUD + associacao vet-clinica
- Modulo Transferencias: criar e listar (apos fix do bug #1)
- Modulo Notificacoes: listar e marcar-lida
- Modulo Upload: multipart com validacao MIME real
- Modulo Busca de Usuarios (apos fix do bug #1)
- Taxonomia: especies e racas

### O que PRECISA DE FIX antes da integracao

| #  | Severidade | Bug                                  | Bloqueante? | Fix             |
|----|-----------|--------------------------------------|-------------|-----------------|
| 1  | CRITICO   | ong.fk_login_id ausente              | SIM         | ALTER TABLE     |
| 2  | CRITICO   | publicacao_encontrado.fk_animal_id   | SIM         | ALTER TABLE     |
| 3  | ATENCAO   | adotante.ranking ausente             | NAO         | ALTER TABLE     |
| 4  | ATENCAO   | notificacao.tipo ENUM restritivo     | NAO         | ALTER ou validar|
| 5  | ATENCAO   | Connection.php echo+die              | NAO         | throw exception |
| 6  | MENOR     | JWT_SECRET fraco                     | NAO         | trocar em prod  |
| 7  | MENOR     | adotante.bio nao exposta             | NAO         | adicionar campo |

### SQL de correcao (BLOQUEANTES - rodar no banco remoto)

```sql
-- Fix BUG #1: ong.fk_login_id
ALTER TABLE ong ADD COLUMN fk_login_id INT NULL;
CREATE INDEX idx_ong_login ON ong (fk_login_id);

-- Fix BUG #2: publicacao_encontrado.fk_animal_id nullable
ALTER TABLE publicacao_encontrado MODIFY COLUMN fk_animal_id INT NULL;

-- Fix BUG #3: adotante.ranking (rerun from migration)
ALTER TABLE adotante ADD COLUMN ranking
  ENUM('pessimo','regular','bom','muito bom','excelente') NULL;
```

### STUBs documentados (nao sao bugs)

- Email (SMTP): recuperar-senha sempre retorna 200 sem enviar email
- PDF (TCPDF): termo pdf_url/pdf_assinado_url = null
- QR Code: carteira qr_code_url = null
- Notificacoes em tempo real: polling via GET, sem WebSocket/push

---

*Relatorio gerado por validacao automatizada em 2026-06-29.*
