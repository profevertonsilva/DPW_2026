# Especificação da API REST — AmigoPet
:**Documento definitivo para construção do back-end** · Versão 1.2 · 2026-06-23 (atualizado em 2026-06-29)
**Para:** Lucas e Adryel (back-end) · **Banco:** `eswdev14_dsw_2026` (MySQL, 34 tabelas)
**Complementos:** `D:\API_CONTRATO_ESPERADO.md` (contrato detalhado), `D:\SOBREPOSICOES_BANCO.md` (decisões pendentes do grupo)

> Este documento é autossuficiente: traz os endpoints, os shapes, o de-para com as colunas
> REAIS do banco e as regras de negócio. Onde houver dúvida, ver a seção F (pontos em aberto).
>
> **🆕 v1.1 (2026-06-27):** seção **H. STATUS DE IMPLEMENTAÇÃO** adicionada pelo back-end,
> documentando o que já foi construído (Auth, Animais, ONGs), a convenção de resposta efetiva
> e os desvios/decisões em relação a este contrato. Marcadores **✅** indicam endpoints prontos.
> **🆕 v1.2 (2026-06-29):** módulos restantes implementados — **C.8 Transferência**, **C.9 Notificações**,
> **C.10 Upload**, **C.11 Perfis**, **C.12 Clínicas**, **C.13 Busca**. Migration `DB/Migracao_API_v1.2.sql`
> cria as tabelas/colunas novas (idempotente — executar no remoto). Ver H.1/H.3 atualizados.

---

## A. INTRODUÇÃO

- **O que é:** API REST que serve o app mobile AmigoPet (React Native/Expo) e o painel web.
- **Base URL:** `https://SEU_DOMINIO/api` (configurável; em dev `http://localhost:8000/api`).
  > ⚠️ Em dev a porta 8000 da máquina do back-end está ocupada por outro serviço; o servidor
  > PHP de desenvolvimento sobe em `http://127.0.0.1:8090` (`php -S 127.0.0.1:8090 index.php`).
- **Formato:** JSON em todas as requisições/respostas (`Content-Type: application/json`),
  exceto upload (`multipart/form-data`).
- **Datas:** ISO 8601 (`2026-06-23T10:00:00`). Datas puras: `YYYY-MM-DD`.
- **Autenticação:** JWT (`firebase/php-jwt`), header `Authorization: Bearer {token}`.
- **Padrão de erro:** `{ "erro": "mensagem legível em português" }`.
- **Padrão de sucesso:** **valor nu, SEM envelope** — listas retornam array puro (`[ {...}, {...} ]`)
  e detalhes retornam o objeto puro (`{ ... }`). Não há embrulho do tipo `{ "animal": {...} }`. (Ver H.2.)
- **Status codes:** 200 OK · 201 Created · 400 Bad Request · 401 Unauthorized ·
  403 Forbidden · 404 Not Found · 409 Conflict · 422 Unprocessable Entity · 429 Too Many Requests · 500 Server Error.
- **Charset:** usar **utf8mb4** na conexão (há enum com acento — `solicitacao_adocao.status`).

---

## B. AUTENTICAÇÃO E REGRAS DE JWT

**Login** (`POST /auth/login`) retorna `{ token, usuario }`. O token é um JWT assinado com `JWT_SECRET` (do `.env`).

**Payload sugerido do JWT:**
```json
{ "sub": <login.id>, "email": "...", "tipo_usuario": "adotante|ong|veterinario|administrador|moderador",
  "status": "a|i", "iat": <ts>, "exp": <ts> }
```
- **Expiry:** 7 dias (app não tem refresh token).
- **`tipo_usuario`** vem de `login.tipo_usuario` (enum migrado — ver seção E). NÃO existe mais `rastreador`: reportar animal de rua é capability de QUALQUER usuário logado.

**Como a API descobre o perfil (adotante/ong/vet) a partir do `login`:**
- **Adotante:** `SELECT * FROM adotante WHERE fk_login_id = {login.id}`.
- **Veterinário:** `SELECT * FROM veterinario WHERE fk_login_id = {login.id}`.
- **ONG:** `SELECT * FROM ong WHERE fk_login_id = {login.id}`.
  > 🔧 **Desvio do contrato original:** a v1.0 sugeria `login.fk_ong_id`, mas essa coluna **não existe**
  > no banco. O grupo decidiu usar `ong.fk_login_id` (simétrico a adotante/veterinário). Ver H.3.
- **Administrador:** tabela `administrador` (não tem fk_login_id ainda — ver seção F).

**E-mail e senha** ficam SEMPRE em `login` (não nas tabelas de perfil). ONG/vet não têm coluna `email` própria — o e-mail é o do `login`.

---

## C. ENDPOINTS POR MÓDULO

> Convenção: "🔒" = requer JWT. Campos de body marcados `?` são opcionais/nullable.
> "**✅**" = implementado e validado no back-end (ver H).

### C.1 Auth — ✅ MÓDULO COMPLETO
- **✅ POST /auth/login** — público. Body `{ email, senha }`. 200 `{ token, usuario:{ id, nome, email, tipo_usuario, status } }`. 401 credenciais inválidas. 429 se exceder tentativas (ver RF#02 na seção E).
- **✅ POST /auth/logout** 🔒 — 200 `{ mensagem }`. (JWT stateless; o cliente descarta o token.)
- **✅ POST /auth/cadastrar/adotante** — público. Body: `nome, cpf, data_nascimento, cep, numero, bairro, cidade, estado, complemento?, logradouro, telefone_1, telefone_2?, email, senha`. 201 `{ token, usuario }`. 400 CPF já cadastrado · 422 e-mail em uso / senha fraca.
- **✅ POST /auth/cadastrar/ong** — público. Body: `nome, cnpj, email, senha, telefone_1, telefone_2?, cep, logradouro, numero, bairro, cidade, estado, complemento?`. 201 `{ token, usuario }`. 400 CNPJ já cadastrado · 422 e-mail em uso.
- **✅ POST /auth/cadastrar/veterinario** — público. Body: `nome, cpf, crmv, email, senha, data_nascimento, telefone_1, telefone_2?, cep, logradouro, numero, bairro, cidade, estado, complemento?`. 201 `{ token, usuario }`. 400 CRMV já cadastrado · 422 e-mail em uso. (de-para: `telefone_1` → `veterinario.telefone`.)
- **✅ POST /auth/alterar-senha** 🔒 — Body `{ senha_atual, senha_nova, senha_confirmacao }`. 200/401/422.
- **✅ POST /auth/recuperar-senha** — público. Body `{ email }`. **Sempre 200** `{ mensagem genérica }` (não revelar se o e-mail existe — anti-enumeração). ⚠️ Disparo de e-mail (RNF#09) é **STUB** (MAIL_* vazios no `.env`) — só registra em log. Ver H.3.

### C.2 Animais — ✅ MÓDULO COMPLETO
- **✅ GET /animais** — query `especie?, porte?, sexo?, localizacao?, busca?`. 200 **array** de **Animal**. (Sem filtro de status, retorna só `disponivel`.)
- **✅ GET /animais/{id}** — 200 Animal · 404.
- **✅ POST /animais** 🔒 (ong) — Body de Animal (ver shape). 201 Animal · 403.
- **✅ PUT /animais/{id}** 🔒 (ong dona) — Body parcial (PATCH semântico). 200 · 403 · 404.
- **✅ GET /animais/meus** 🔒 (ong) — 200 array de Animal da ONG logada.
- **✅ GET /animais/{id}/historico** — 200 array (auditoria; shape de C.5).
- > ⚠️ **Auth efetiva:** o contrato marcava todos estes como 🔒. Na implementação atual os **GET de leitura
  > foram deixados PÚBLICOS** (browsing sem login). Não quebra o app (um endpoint público aceita token).
  > **Decisão pendente:** confirmar se a navegação de animais deve exigir login. Ver H.3.

**Shape Animal (resposta) — IMPLEMENTADO exatamente assim:**
```json
{ "id":n, "nome":"", "data_nascimento":"YYYY-MM-DD|null", "sexo":"m|f|n/a",
  "especie":"|null", "raca":"|null", "cor":"|null", "porte":"pequeno|medio|grande|gigante|null",
  "castrado":bool, "data_castracao":"YYYY-MM-DD|null", "descricao":"|null",
  "historico_resgate":"|null", "alergias":"|null", "localizacao":"|null",
  "status":"disponivel|adotado|em_tratamento|reservado",
  "foto":"URL|null", "fotos":["URL"], "idade_anos":n|null,
  "local_cep":"|null","local_logradouro":"|null","local_numero":"|null",
  "local_bairro":"|null","local_cidade":"|null","local_estado":"|null",
  "ong":{ "id":n, "nome":"" }|null }
```
- `especie` e `raca` são **strings** (nomes), não objetos. `raca` = 1ª raça do animal (a relação é N:N — ver H.3).
- `foto` é a capa única (`animal.foto`); `fotos[]` são as imagens extras (`animal_imagens`). Campos separados.
- `idade_anos` é calculado a partir de `data_nascimento`.

### C.3 ONGs — ✅ MÓDULO COMPLETO
- **✅ GET /ongs** — 200 array de ONG.
- **✅ GET /ongs/{id}** — 200 ONG · 404.
- **✅ GET /ongs/{id}/animais** — 200 array de Animal · 404.
- > (Mesma observação de auth pública dos GET — ver C.2 / H.3.)

**Shape ONG (resposta) — IMPLEMENTADO:**
```json
{ "id":n, "nome":"", "cnpj":"|null", "email":"|null",
  "telefone_1":"|null","telefone_2":"|null",
  "cep":"|null","logradouro":"|null","numero":"|null","bairro":"|null","cidade":"|null","estado":"|null","complemento":"|null",
  "foto":"URL|null", "descricao":"|null", "quantidade_animais":n, "status":"a|i" }
```
- de-para: `foto` ← `ong.avatar`, `descricao` ← `ong.bio`, `email`/`status` ← `login` (via `ong.fk_login_id`).
- `quantidade_animais` é **contado ao vivo** em `ong_animal` (não usa a coluna `ong.quantidade_animais`, que pode estar defasada).

### C.4 Solicitações de Adoção — ✅ MÓDULO COMPLETO
- **✅ GET /solicitacoes/minhas** 🔒 (adotante) — 200 array de Solicitacao (com `animal`).
- **✅ POST /solicitacoes** 🔒 (adotante) — Body `{ fk_animal_id, motivo, aceite_termo:true, timestamp_aceite? }`. 201 · 400 já existe ativa · 422 animal indisponível / perfil incompleto (RF#08) / sem aceite.
- **✅ GET /solicitacoes/{id}** 🔒 — 200 · 403 · 404. (Acesso: adotante dono OU ONG dona do animal; a ONG recebe `adotante_nome/email`.)
- **✅ GET /solicitacoes/recebidas** 🔒 (ong) — 200 array (com `adotante_nome, adotante_email, animal, termo_assinado, pdf_termo_url`).
- **✅ PATCH /solicitacoes/{id}/status** 🔒 (ong dona) — Body `{ status, motivo_recusa? }`. 200 · 403 · 422 (status inválido / recusa sem motivo). Ao `Concluído` → `animal.status='adotado'`.
- **✅ POST /solicitacoes/{id}/avaliacao** 🔒 (ong dona) — Body do checklist (ver C.7). 201 · 409 se já avaliada (append-only).
- **✅ GET /solicitacoes/{id}/termo** 🔒 (adotante dono) — 200 `{ solicitacao_id, pdf_url, pdf_assinado_url, conteudo_texto, assinado, data_assinatura }`. (Cria a linha de termo + `conteudo_texto` na 1ª chamada.)
- **✅ POST /solicitacoes/{id}/termo/assinar** 🔒 (adotante dono) — Body `{ aceite:true, timestamp? }`. **IP e user_agent capturados server-side** (Lei 14.063/2020). 200 · 400 já assinado · 422 status != Aprovado / sem aceite. Conclui a adoção (status → Concluído, `animal → adotado`).
- > ⚠️ **PDF é STUB:** `pdf_url`/`pdf_assinado_url` retornam `null` (igual ao stub de e-mail). O `conteudo_texto`
  > do termo é gerado e a assinatura é registrada de verdade; falta só plugar a geração via TCPDF. Ver H.3.

**Status canônicos:** `Pendente`, `Em Análise`, `Aprovado`, `Concluído`, `Recusado` (com acento exato).

### C.5 Saúde do Animal — ✅ MÓDULO COMPLETO (carteira QR/PDF = stub)
- **✅ GET /animais/{id}/vacinas** 🔒 — 200 array de Vacina (mais recentes primeiro). 404 se animal não existe.
- **✅ POST /animais/{id}/vacinas** 🔒 (vet/ong) — **append-only**. Body `{ nome, data_aplicacao, data_reforco?, veterinario_nome?, clinica_nome? }`. 201 · 403 · 404. Registra automaticamente na auditoria (tipo `vacinacao`).
- **✅ GET /animais/{id}/procedimentos** 🔒 — 200 array de Procedimento.
- **✅ POST /animais/{id}/procedimentos** 🔒 (vet/ong) — **append-only**. Body `{ nome, tipo, data, veterinario_nome?, observacoes?, anexo_url? }`. `tipo` ∈ `consulta|cirurgia|exame|castracao|outro`. 201 · 422 tipo inválido. Registra na auditoria (tipo `procedimento`).
- **✅ GET /animais/{id}/saude** 🔒 — 200 `{ fk_animal_id, apto_para_adocao, temperamento, necessidades_especiais, condicao_geral }`. Sem registro → defaults (`apto_para_adocao=true`, demais null).
- **✅ PUT /animais/{id}/saude** 🔒 (vet/ong) — upsert (atualiza só os campos enviados). 200.
- **✅ GET /animais/{id}/carteira** 🔒 — 200 `{ animal_id, nome, especie, raca, data_nascimento, castrado, alergias, foto, qr_code_url, pdf_url }`. ⚠️ **`qr_code_url`/`pdf_url` são STUB (null)** — geração via API pendente (ver H.3).
- **✅ GET /animais/{id}/auditoria** 🔒 — **append-only/imutável**. 200 array `{ id, tipo, descricao, data, autor_nome, fk_animal_id }`. Mesmo dado de `historico_animal`; alimentado pelos POSTs de vacina/procedimento. (Também exposto como `GET /animais/{id}/historico`, porém público.)
- **✅ GET /vet/atendimentos** 🔒 (vet) — 200 array `{ animal_id, animal_nome, animal_especie, animal_foto, ultima_vacina, ultimo_procedimento }`. Liga os animais pelo `fk_login_id` em vacina/procedimento.

### C.6 Avistamentos (animal de rua / rastreador) + Ranking — ✅ MÓDULO COMPLETO
- **✅ POST /avistamentos** 🔒 (qualquer logado) — Body `{ fotos[], data, especie, condicao, acoes_tomadas, lat?, lng?, endereco_texto?, local_cep?, local_logradouro?, local_numero?, local_bairro?, local_cidade?, local_estado? }`. Localização = GPS (lat+lng) OU endereço (endereco_texto/local_cidade). 201 Avistamento · 422 sem localização. Grava `pontos=10`.
- **✅ GET /avistamentos** 🔒 — query `status?, especie?`. 200 array (status no filtro aceita o formato do app).
- **✅ GET /avistamentos/{id}** 🔒 — 200 · 404.
- **✅ PATCH /avistamentos/{id}/status** 🔒 (ong/admin) — Body `{ status }`. 200 · 403 · 404 · 422 status inválido.
- **✅ GET /ranking/rastreadores** 🔒 — 200 array `{ posicao, usuario_id, nome, foto, total_reports, pontos }`. 10 pts/avistamento; `nome`/`foto` resolvidos pelo perfil do reporter.
- > 🔧 **Schema:** `publicacao_encontrado.fk_animal_id` foi tornado **NULL** (era NOT NULL) — avistamento é standalone (sem animal). **Replicar no remoto.** Ver H.3.
- > ℹ️ Simplificação: a regra E#10 ("ONG que assume acolhimento não pontua") não é aplicada — o ranking conta por reporter (`fk_login_id`); ONG só pontua se ela mesma reportou.

**Status avistamento (app):** `aguardando_acolhimento`, `em_acolhimento`, `resgatado`, `encerrado`. ⚠️ No banco ficam com **espaço** (`aguardando acolhimento`); a API converte underscore↔espaço nos dois sentidos.

### C.7 Adoção — Termo e Avaliação — ✅ (junto com C.4)
- **✅ POST /solicitacoes/{id}/avaliacao** 🔒 (ong) — Body `{ tipo_moradia, experiencia_previa:bool, tem_criancas:bool, tem_outros_animais:bool, parecer, resultado }`. `tipo_moradia` ∈ `casa_com_quintal|casa_sem_quintal|apartamento|outro`; `resultado` ∈ `aprovado|reprovado|pendente_informacoes`. 201 · 409 se já avaliada.
- **✅ Termo:** ver C.4 (`/termo` e `/termo/assinar`). (Geração de PDF pendente — stub.)

### C.8 Transferência de Responsabilidade — ✅ MÓDULO COMPLETO
- **✅ POST /animais/{id}/transferencias** 🔒 (ONG dona do animal) — **append-only**. Body `{ para_usuario_id, para_usuario_nome?, motivo? }`. 201. `de_usuario` = ONG logada; valida existência do login destino.
- **✅ GET /animais/{id}/transferencias** 🔒 (ONG dona do animal) — 200 array (histórico imutável de tutores).

### C.9 Notificações — ✅ MÓDULO COMPLETO
- **✅ GET /notificacoes** 🔒 — 200 array `{ id, titulo, mensagem, data, lida, tipo, destino? }`.
- **✅ PATCH /notificacoes/{id}/marcar-lida** 🔒 — 200 · 404.
- **Tipos:** `solicitacao, sistema, adocao_concluida, status_adocao, avistamento_atualizado, alerta_vacina, promocao_papel`.
- **`destino`** (opcional): `{ tab, tela, params }` para deep-link no app — só montado se `destino_tab`/`destino_tela` preenchidos.

### C.10 Upload — ✅ MÓDULO COMPLETO
- **✅ POST /upload** 🔒 — `multipart/form-data` campo `arquivo`. Imagem JPG/PNG/GIF/WebP ≤5MB **ou** PDF ≤10MB. MIME real via `finfo`. 200 `{ url }` (URL absoluta). 400. Armazena em `resources/uploads/`.

### C.11 Perfis — ✅ MÓDULO COMPLETO
- **✅ GET /adotante/perfil** 🔒 · **✅ PUT /adotante/perfil** 🔒 — inclui `ranking` e `status` SEPARADOS (`status` ← login). PUT é PATCH semântico; **não** altera `ranking` nem `login.status`.
- **✅ GET /ong/perfil** 🔒 · **✅ PUT /ong/perfil** 🔒 — `email` vem do login; `descricao` → `ong.bio`.
- **✅ GET /veterinario/perfil** 🔒 · **✅ PUT /veterinario/perfil** 🔒 — `telefone_1` → `veterinario.telefone`.

### C.12 Clínicas (RF#13) — ✅ MÓDULO COMPLETO
- **✅ GET /clinicas** 🔒 · **✅ GET /clinicas/{id}** 🔒 · **✅ POST /clinicas** 🔒 (vet).
- **✅ GET /veterinario/clinicas** 🔒 (vet) — clínicas associadas (via `vet_clinica`).
- **✅ POST /veterinario/clinicas/{clinicaId}** 🔒 (vet) — associa (idempotente) · **✅ DELETE** idem — desassocia.

### C.13 Busca de usuários (para transferência) — ✅ MÓDULO COMPLETO
- **✅ GET /usuarios/busca?q={query}** 🔒 — q ≥ 2 chars (422 se curto). 200 array `{ id, nome, tipo_usuario, email }`. Não retorna admin/moderador. LIKE %q%, limite 10.

### C.14 Auxiliares — ✅
- **✅ GET /especies** — 200 `[{ id, nome }]`. (Implementado público — ver C.2 / H.3.)
- **✅ GET /racas?especie_id=** — 200 `[{ id, nome, fk_especie_id }]`. (Implementado público.)

---

## D. MAPEAMENTO ENDPOINT → TABELA → COLUNAS REAIS

> ⚠️ A API usa nomes que o app espera; o de-para abaixo evita devolver nomes errados.
> Regra geral de foto: o app pede **`foto`**; o banco guarda **`avatar`** (em adotante/ong/veterinario/clinica) — expor `avatar` como `foto`.

### Auth / Perfis
| Campo JSON (app) | Tabela.coluna real |
|---|---|
| login: email, senha, tipo_usuario, status | `login.email`, `login.senha`, `login.tipo_usuario`, `login.status` |
| adotante.foto | `adotante.avatar` |
| adotante.ranking | **`adotante.ranking`** (enum) — ⚠️ ver H.3: no banco local o enum é `pessimo,regular,bom,muito bom,excelente` (`regular`, não `ruim`) |
| adotante.status (conta) | **`login.status`** (`a|i`) — NÃO use `adotante.status` (legado de ranking) |
| ong.email | `login.email` (join via **`ong.fk_login_id`** — ver B/H.3) — `ong` NÃO tem coluna email |
| ong.foto | `ong.avatar` · ong.descricao → `ong.bio` |
| veterinario.email | `login.email` (join via `veterinario.fk_login_id`) |
| veterinario.telefone_1 | `veterinario.telefone` (nome difere) · vet.foto → `veterinario.avatar` |
| clinica.foto | `clinica.avatar` · clinica.email → `clinica.email` |

### Animais
| Campo JSON | Tabela.coluna real |
|---|---|
| Animal.* base | `animal.*` (nome, data_nascimento, sexo, cor, castrado, descricao, porte, localizacao, status) |
| Animal.foto | `animal.foto` (capa única) |
| Animal.fotos[] | **`animal_imagens`** (`caminho_imagem` ORDER BY `ordem`) — NÃO há coluna `fotos` em animal |
| Animal.alergias / historico_resgate / data_castracao | `animal.alergias` / `animal.historico_resgate` / `animal.data_castracao` |
| Animal.local_* | `animal.local_cep/logradouro/numero/bairro/cidade/estado` |
| Animal.especie | `especie.nome` via `animal.fk_especie_id` (retornar STRING) |
| Animal.raca | `raca.nome` via `animal_raca` (N:N) — retornar STRING da 1ª raça (ORDER BY animal_raca.id) |
| Animal.ong | `ong` via `ong_animal` (N:N) — retornar a 1ª (ORDER BY ong_animal.id LIMIT 1) |
| Animal.idade_anos | calcular de `animal.data_nascimento` |

### Solicitações / Adoção
| Campo JSON | Tabela.coluna real |
|---|---|
| Solicitacao.* | `solicitacao_adocao.*` |
| Solicitacao.status | `solicitacao_adocao.status` (enum textual com acento) |
| motivo_recusa, termo_assinado, pdf_termo_url, aceite_termo, timestamp_aceite | colunas homônimas em `solicitacao_adocao` |
| adotante_nome/email | join `adotante`/`login` |
| Termo.* | **`termo_adocao`** (pdf_url, pdf_assinado_url, conteudo_texto, assinado, data_assinatura, ip_assinatura, user_agent) |
| Avaliacao.* | **`avaliacao_adotante`** (tipo_moradia, experiencia_previa, tem_criancas, tem_outros_animais, parecer, resultado, criado_em, criado_por) |

### Saúde
| Campo JSON | Tabela.coluna real |
|---|---|
| Vacina.* | **`vacina`** (append-only) |
| Procedimento.* | **`procedimento`** (append-only) |
| SaudeAnimal.* | **`saude_animal`** (apto_para_adocao, temperamento, necessidades_especiais, condicao_geral) |
| Auditoria.* | **`historico_animal`** — `id, tipo, descricao, data, autor_nome, fk_animal_id` |
| Carteira qr_code_url/pdf_url | gerados pela API (não há coluna; gerar on-the-fly) |

### Avistamentos
| Campo JSON (Avistamento) | `publicacao_encontrado`.coluna |
|---|---|
| data | `data_encontro` · condicao → `condicao_fisica` · acoes_tomadas → `acoes_realizadas` |
| endereco_texto | `localizacao` · usuario.id → `fk_login_id` |
| lat/lng | `latitude`/`longitude` · especie → `especie` · pontos → `pontos` |
| fotos/foto | `fotos` (JSON) / `foto` · local_* → `local_*` |
| status | `status` VARCHAR — real `'aguardando acolhimento'` (com espaço); normalizar p/ `aguardando_acolhimento` |

### Notificações / Transferência
| Campo JSON | Tabela.coluna real |
|---|---|
| Notificacao.* | **`notificacao`** (fk_login_id, titulo, mensagem, data, lida, tipo, destino_tab/destino_tela/destino_params) |
| destino | montar de `destino_tab/destino_tela/destino_params(JSON)` |
| Transferencia.* | **`transferencia`** (fk_animal_id, de_usuario_id/nome, para_usuario_id/nome, motivo, data) |

---

## E. REGRAS DE NEGÓCIO QUE A API DEVE IMPLEMENTAR

1. **Senha (RF#01):** mín. 8 chars, ≥1 alfanumérico, ≥1 especial. Revalidar no back-end (422 `{ erro }`). — ✅ implementado (`Validador::senhaForte`).
2. **Bloqueio progressivo de login (RF#02):** após 3 tentativas falhas, bloquear ~10s, **dobrando** a cada nova falha (10→20→40…). Por e-mail/IP. Retornar 429 com tempo restante. Resetar no sucesso. — ✅ implementado (`LoginThrottle`, arquivo em `storage/throttle/`, não toca o schema).
3. **Status da solicitação:** exatamente `Pendente`, `Em Análise`, `Aprovado`, `Concluído`, `Recusado` (acentos/caixa exatos). Fluxo: Pendente→Em Análise→Aprovado→Concluído, ou →Recusado. Ao Concluído: `animal.status='adotado'` e sai das listagens de disponíveis. — 🔜 (C.4).
4. **Tabelas APPEND-ONLY (RNF#08):** `vacina`, `procedimento`, `transferencia`, `avaliacao_adotante`, `termo_adocao` (após assinatura) e a auditoria (`historico_animal`). A API **NÃO** expõe UPDATE/DELETE nesses recursos. *Recomendação:* reforçar no banco com triggers `BEFORE UPDATE/DELETE` que lançam erro, ou revogar privilégios — se a hospedagem permitir.
5. **Assinatura do termo (RF#25, Lei 14.063/2020):** o app envia só `{ aceite:true, timestamp }`. A API captura o **IP server-side** (`X-Forwarded-For` ou `REMOTE_ADDR`) e o `user_agent`, e grava em `termo_adocao` + avança status para Concluído. Nunca confiar em IP enviado pelo cliente. — 🔜 (C.4). (Helper de IP server-side já existe em `ApiController::ip()`.)
6. **Perfil completo antes de adotar (RF#08):** ao `POST /solicitacoes`, validar que o adotante tem `nome, cpf, email, telefone_1, endereço (cep/cidade/estado), data_nascimento`. Faltando algo → 422 `{ erro: "Complete seu perfil para adotar." }`. — 🔜 (C.4).
7. **Upload (RNF#10):** imagem JPG/PNG ≤5MB, PDF ≤10MB. Armazenar fora do banco (filesystem/`/resources/...` ou bucket) e retornar **URL absoluta**. Validar MIME real, não só extensão. — 🔜 (C.10).
8. **PDF e QR Code:** responsabilidade da API. Termo (`/termo`) e carteira (`/carteira`) retornam URLs (`pdf_url`, `qr_code_url`). O app só exibe/baixa — nunca gera. — 🔜.
9. **Notificações por e-mail (RNF#09):** disparar em: cadastro, recuperação de senha, mudança de status de adoção, alerta de vacina próxima do vencimento. Em paralelo, gravar `notificacao` in-app. — ⚠️ **STUB** (SMTP não configurado; MAIL_* vazios no `.env`). Ver H.3.
10. **Ranking de rastreadores (RF#19):** contar avistamentos por `publicacao_encontrado.fk_login_id`; pontos sugeridos = 10/avistamento (campo `pontos`). ONG que assume acolhimento não pontua como rastreador. — 🔜 (C.6).
11. **tipo_usuario:** enum final `('administrador','ong','adotante','veterinario','moderador')`. `rastreador` não existe mais (qualquer logado reporta animal de rua). Admin/moderador não têm telas completas no app (só leitura/aviso).

---

## F. PONTOS EM ABERTO / DECISÕES PENDENTES DO GRUPO

1. **Sobreposições de tabela** — ver `D:\SOBREPOSICOES_BANCO.md`:
   - `visita_adocao` vs `avaliacao_adotante` (qual usar / unificar).
   - `caso_veterinario` vs `procedimento`/`vacina` (triagem vs histórico).
   - `resgate` vs `transferencia` (finalidades distintas — provável manter ambas).
   - Padronização de fotos: `animal_imagens` (tabela) vs `publicacao_encontrado.fotos` (JSON).
   - Se "reportar animal de rua" (app→`publicacao_encontrado`) deve abrir um `chamado` para a ONG.
2. **`administrador` sem `fk_login_id`** — não há vínculo direto admin↔login. Definir como autenticar admin (hoje só via `login.tipo_usuario='administrador'`).
3. **`veterinario.telefone`** mantido (não renomeado para `telefone_1`) para não quebrar INSERTs do time — a API faz o de-para. Confirmar se o grupo quer padronizar depois.
4. **`adotante.status` legado** (enum de ranking) — mantido por compatibilidade, mas a avaliação oficial é `adotante.ranking`. Definir quando remover o legado.
5. **Avistamento status** com espaço (`'aguardando acolhimento'`) no dado existente vs `aguardando_acolhimento` esperado pelo app — a API normaliza; avaliar padronizar no banco.
6. **Sem foreign keys físicas:** tabelas core são MyISAM (sem FK). Integridade é responsabilidade da aplicação — a API deve validar existência dos `fk_*` antes de inserir.

---

## G. PRIORIDADE DE IMPLEMENTAÇÃO (sugestão)

1. **Auth** (login, cadastros adotante/ong/vet, alterar/recuperar senha) — destrava todo o resto. — ✅ **CONCLUÍDO**
2. **Animais** (GET listar/detalhe, especies/racas, animal_imagens) — tela principal do app. — ✅ **CONCLUÍDO**
   - *(+ ONGs / C.3, feito junto — leitura de ONGs e seus animais.)* — ✅ **CONCLUÍDO**
3. **Solicitações + Termo** (criar, status, recebidas, termo/assinar) — fluxo central de adoção. — ✅ **CONCLUÍDO** (PDF do termo é stub)
4. **Saúde** (vacinas, procedimentos, saude, carteira, auditoria) — ✅ **CONCLUÍDO** (carteira QR/PDF stub) · **Perfis** (adotante/ong/vet) — ✅ **CONCLUÍDO** (C.11).
5. **Avistamentos + Ranking** (C.6) — ✅ **CONCLUÍDO**. **Módulos finais (C.8–C.13) — ✅ CONCLUÍDO**: Transferência, Notificações, Upload, Clínicas, Busca.

---

## H. STATUS DE IMPLEMENTAÇÃO (back-end) — atualizado 2026-06-29

> Seção mantida pelo back-end (Lucas/Adryel). Resume o que está pronto, a arquitetura efetiva
> e os **desvios/decisões** em relação ao contrato v1.0, com justificativa.

### H.1 Módulos prontos e validados
| Módulo | Endpoints | Situação |
|---|---|---|
| Base/diagnóstico | `GET /ping`, `GET /health`, `GET /me` 🔒 | ✅ |
| C.1 Auth | login, logout, cadastrar/{adotante,ong,veterinario}, alterar-senha, recuperar-senha | ✅ (recuperar-senha sem envio real de e-mail) |
| C.2 Animais | listar, {id}, meus, POST, PUT, {id}/historico | ✅ |
| C.3 ONGs | listar, {id}, {id}/animais | ✅ |
| C.4 Solicitações + C.7 Termo/Avaliação | minhas, recebidas, POST, {id}, status (PATCH), avaliacao, termo, termo/assinar | ✅ (PDF stub) |
| C.5 Saúde | vacinas, procedimentos, saude, carteira, auditoria, /vet/atendimentos | ✅ (carteira QR/PDF stub) |
| C.6 Avistamentos + Ranking | POST/GET avistamentos, {id}, status (PATCH), /ranking/rastreadores | ✅ |
| C.14 Auxiliares | especies, racas | ✅ |
| C.11 Perfis | GET/PUT adotante/ong/veterinario | ✅ |
| C.8 Transferência | POST/GET /animais/{id}/transferencias (append-only) | ✅ |
| C.9 Notificações | GET /notificacoes, PATCH marcar-lida | ✅ (gatilhos de emissão pendentes) |
| C.10 Upload | POST /upload (imagem/PDF) | ✅ |
| C.12 Clínicas | GET/POST /clinicas, /veterinario/clinicas (+associa/desassocia) | ✅ |
| C.13 Busca | GET /usuarios/busca | ✅ |

### H.2 Arquitetura efetiva
- Camada de API **isolada** da stack web (sessão/HTML via `FW\Controller\Action` + tabela `routes`).
  O `index.php` intercepta `^/api(/|$)` e delega ao `App\Api\ApiRouter` (stateless, sem `session_start`).
- **Rotas declaradas em código** (`App/Api/routes.php`), não na tabela `routes` (que é só da web).
- Estrutura: `App/Api/` → `ApiRouter`, `ApiController` (base), `Jwt`, `ApiResponse`, `ApiException`,
  `Validador`, `LoginThrottle`; `Controller/` (Health, Auth, Animal, Ong); `Repository/` (Auth, Animal, Ong).
- **Convenção de resposta:** valores nus (sem envelope). Listas = array puro; detalhe = objeto puro;
  erro = `{ "erro": "..." }`. CORS liberado (`*`), preflight OPTIONS → 204.
- **Compatibilidade PHP 7.0** em todo `App/Api/` (host de produção é stack antiga): sem typed properties,
  `: void`/`: object`, `?Tipo`, `private const`, union types.
- `.htaccess`: regra extra para passar o header `Authorization` no Apache (`E=HTTP_AUTHORIZATION`).
- `vendor/FW/DB/Connection.php`: charset `utf8` → `utf8mb4`.

### H.3 Desvios e decisões em relação ao contrato (IMPORTANTE)
1. **Vínculo ONG↔login = `ong.fk_login_id`** (não `login.fk_ong_id`, que não existe no banco). Simétrico a
   adotante/veterinário. **Ações:** (a) **replicar a coluna `ong.fk_login_id` no banco REMOTO** (já existe
   só no local); (b) **backfill** das ONGs já cadastradas (a ONG id 1, por ex., está com `fk_login_id` nulo,
   então `email`/`status` vêm nulos/legado). Novos cadastros via API já preenchem o vínculo.
2. **GET de leitura públicos vs 🔒:** `GET /animais*`, `GET /ongs*`, `GET /especies`, `GET /racas` foram deixados
   **públicos**, embora o contrato marque 🔒. Motivo: navegação de catálogo costuma ser pública e isso não quebra
   o app (endpoint público aceita requisição com token). **Confirmar com o mobile** se o browsing deve exigir login;
   se sim, é só marcar as rotas como protegidas.
3. **`raca` como string única:** o shape pede `raca:"string"`, mas a relação `animal_raca` é N:N. A API retorna
   a **1ª raça** (ORDER BY `animal_raca.id`). Se o app precisar de todas as raças, avisar que mudamos para lista.
4. **Recuperação de senha / e-mails (RNF#09):** o contrato de `recuperar-senha` está cumprido (sempre 200,
   anti-enumeração), mas **o envio real de e-mail é um STUB** — `MAIL_*` estão vazios no `.env`. Quando o SMTP
   for configurado, plugar PHPMailer (já está no `vendor/`) + token de redefinição.
5. **`adotante.ranking`:** no banco local o enum é `('pessimo','regular','bom','muito bom','excelente')`
   (`regular`, e não `ruim` como o contrato cita). Confirmar o valor correto antes do módulo de Perfis (C.11).
   No cadastro de adotante, hoje gravamos `adotante.status='bom'` (legado); o `ranking` oficial será tratado em C.11.
6. **URLs de foto relativas:** `animal.foto`/`animal_imagens.caminho_imagem`/`ong.avatar` são caminhos
   **relativos** (`/resources/...`) no banco — a API os devolve como estão. Se o app precisar de URL absoluta,
   definimos um `APP_URL` base e prefixamos. **Confirmar com o mobile.**
7. **Throttle de login:** estado em arquivo (`storage/throttle/`, gitignored) — não cria tabela nova.
9. **`publicacao_encontrado.fk_animal_id` → NULL** (era NOT NULL; decisão 2026-06-28): o avistamento do app é um
   report standalone (sem animal cadastrado). `ALTER TABLE publicacao_encontrado MODIFY fk_animal_id INT NULL;`
   aplicado no LOCAL — **replicar no remoto** (compatível com os inserts atuais do web, que sempre mandam animal).
   Status de avistamento ficam com **espaço** no banco; a API converte underscore↔espaço.
8. **PDF do termo (C.4) = STUB** (decisão de 2026-06-27): todo o lifecycle de adoção está implementado e
   testado (criar → status → avaliação → assinatura com IP/user_agent server-side → status Concluído →
   `animal=adotado`), e o `termo_adocao.conteudo_texto` é gerado de verdade. Falta apenas **gerar o arquivo PDF
   via TCPDF** e preencher `pdf_url`/`pdf_assinado_url` (hoje `null`). TCPDF já está no vendor e versionado.
   **Pendência de infra junto:** definir diretório público p/ os PDFs (ex.: `resources/termos/`) e a URL.
10. **C.8 Transferência (2026-06-29):** append-only em `transferencia`. Autorização = **ONG dona do animal** (o "responsável atual" é a ONG via `ong_animal`); `de_usuario` = ONG logada. Valida existência do login destino (`para_usuario_id`) antes de inserir (sem FK física — F#6). Resolve `para_usuario_nome` via COALESCE dos perfis se o app não enviar.
11. **C.9 Notificações (2026-06-29):** tabela `notificacao`. `destino` (`{tab,tela,params}`) só é montado se `destino_tab`/`destino_tela` preenchidos (senão `null`). O repositório expõe `criar()` para outros módulos dispararem in-app (ex.: mudança de status de adoção) — **ligar nos gatilhos é pendência**.
12. **C.10 Upload (2026-06-29):** armazena em `resources/uploads/`, nome seguro (`bin2hex(random_bytes(16))`), valida **MIME real via `finfo`** (não extensão); JPG/PNG/GIF/WebP ≤5MB, PDF ≤10MB. `.htaccess` do diretório bloqueia execução de PHP. URL absoluta via `BASE_URL` (.env) ou montada de `HTTP_HOST`.
13. **C.11 Perfis (2026-06-29):** GET+PUT para adotante/ong/veterinario. PUT é **PATCH semântico** (só campos enviados); **não** altera `ranking` (definido pela avaliação da ONG) nem `login.status`. `email` validado (formato + unicidade) quando alterado. `ranking` lido com fallback para o legado `adotante.status`.
14. **C.12 Clínicas (2026-06-29):** leitura protegida (qualquer autenticado); criação/associação restritas a **vet**. Associação (`vet_clinica`) é **idempotente**. De-para `foto`↔`clinica.avatar`, `email`↔`clinica.email` (colunas adicionadas na migration, tratadas defensivamente).
15. **C.13 Busca (2026-06-29):** `q` ≥ 2 chars (422 se curto); exclui `administrador`/`moderador`; `LIKE %q%` em nome (COALESCE dos perfis) ou e-mail; limite 10.
16. **Migration v1.2:** `DB/Migracao_API_v1.2.sql` cria `transferencia` e `notificacao` e adiciona (idempotente) `clinica.avatar`/`clinica.email` e `adotante.ranking`. **Executar no remoto** (e em qualquer ambiente que ainda não tem).

### H.4 Como rodar/testar (dev)
- Banco local em Docker (MySQL 5.7, espelho do remoto). App PHP: `php -S 127.0.0.1:8090 index.php`.
- **Aplicar a migration** `DB/Migracao_API_v1.2.sql` (idempotente) antes de testar os módulos C.8/C.9 e os perfis de Clínicas/Adotante.
- Healthcheck: `GET http://127.0.0.1:8090/api/health` (deve acusar banco conectado).
- Gerar token de teste:
  `php -r 'require "vendor/autoload.php"; Dotenv\Dotenv::createImmutable(__DIR__)->load(); echo App\Api\Jwt::emitir(["sub"=>1,"tipo_usuario"=>"ong","status"=>"a"]);'`

### H.5 Pendências priorizadas
- **Módulos de API: 100% implementados** (C.1–C.14). Restam apenas pendências de **infra/integração** (abaixo).
- Itens de infra a resolver com o grupo: replicar no remoto `ong.fk_login_id` (+ backfill) e
  `publicacao_encontrado.fk_animal_id` NULL; configurar SMTP; **gerar PDFs/QR via API** — termo (C.4) e
  carteira (C.5) via TCPDF + definir dir/URL; decidir auth pública vs protegida dos GET; padronizar URLs de foto.
- **Perfis vazios no banco:** `veterinario` e `ong` não têm linhas para vários logins (precisam backfill/cadastro),
  o que afeta `/vet/atendimentos`, `/animais/meus`, autoria de saúde, etc.

---

_Contrato gerado por Claude Code (claude-opus-4-8) em 2026-06-23. Seção H e marcadores de status
adicionados pelo back-end em 2026-06-27 (v1.1) e 2026-06-29 (v1.2 — módulos C.8–C.13), alinhados ao banco real e à implementação atual da API._
