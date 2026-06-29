# Polimento Visual — Grupos 2, 3 e 4

> Aplica o mesmo nível de polimento do Grupo 1 (Login, EscolhaCadastro, AnimalDetalhe, Feed, AnimalCard) a todas as telas restantes.
> Apenas camada visual: cores, espaçamento, tipografia, sombras, bordas, estados vazios, badges.
> Nenhuma lógica, navegação, serviço ou tipo alterado.
> `npx tsc --noEmit` = 0 erros.

---

## Grupo 2 — Adoção (1 arquivo editado)

### SolicitacaoDetalheScreen.tsx
- `fontSize: 9` na timeline label substituído por `typography.fontSize.xs` (11)
- Adicionado elevation + shadow na `secao`: `elevation: 1, shadowOpacity: 0.06, shadowRadius: 3`

### Sem alteração necessária
- SolicitarAdocaoScreen, MinhasSolicitacoesScreen, TermoDigitalScreen, SolicitacaoCard

---

## Grupo 3 — ONG / Vet / Saúde / Perfil Edição (10 telas, 9 arquivos editados)

### MeusAnimaisScreen.tsx
- Empty state plain text substituído por `<EmptyState icon="🐾" title="Nenhum animal cadastrado" message="Cadastre seu primeiro pet para começar." />`
- Card shadow: elevation 1→2, shadowOffset height 1→2, shadowRadius 3→6
- Removidos styles `vazio` e `vazioTexto` (unused)

### AdocoesOngScreen.tsx
- Empty state plain text substituído por `<EmptyState icon="📋" ...>`
- Badge `fontSize: 10` → `typography.fontSize.xs`
- Removidos styles `vazio` e `vazioTexto`

### AtendimentosScreen.tsx
- Empty state plain text substituído por `<EmptyState icon="🩺" ...>`
- Removidos styles `vazio` e `vazioTexto`

### AvaliacaoAdotanteScreen.tsx
- Campos agrupados em `<Secao>`: "Perfil do Lar" e "Parecer e Resultado"
- Adicionada função local `Secao` + estilos `secao`/`secaoTitulo` (borderLeft accent)
- Aviso `borderRadius: 8` → `10`

### EditarPerfilOngScreen.tsx
- Campos agrupados em 3 `<Secao>`: "Dados da ONG", "Contato", "Endereço"
- Adicionada função local `Secao` + estilos
- CNPJ box `borderRadius: 4` → `8`

### EditarPerfilVeterinarioScreen.tsx
- Campos agrupados em 3 `<Secao>`: "Dados do Profissional", "Contato", "Endereço"
- Adicionada função local `Secao` + estilos

### EditarSaudeAnimalScreen.tsx
- Campos agrupados em `<Secao titulo="Condição de Saúde">`
- `semPermissao` plain text substituído por `<EmptyState icon="🔒" title="Sem permissão" ...>`
- Adicionada função local `Secao` + estilos
- Removidos styles `semPermissao` e `semPermissaoTexto`

### AdicionarVacinaScreen.tsx
- Campos agrupados em `<Secao titulo="Registro de Vacina">`
- Adicionada função local `Secao` + estilos
- Aviso `borderRadius: 8` → `10`

### AdicionarProcedimentoScreen.tsx
- Campos agrupados em `<Secao titulo="Registro de Procedimento">`
- Adicionada função local `Secao` + estilos
- Aviso `borderRadius: 8` → `10`

### Sem alteração necessária
- CadastrarAnimalScreen (já tem Secao)

---

## Grupo 4 — Resgates / ONGs / Perfil / Saúde / Auth (9 arquivos editados)

### FeedResgatesScreen.tsx
- Empty state substituído por `<EmptyState icon="🐾" title="Nenhum avistamento registrado" ...>`
- `statusLabel fontSize: 9` → `typography.fontSize.xs`
- Removidos styles `vazio`/`vazioTexto`

### RankingScreen.tsx
- Empty state substituído por `<EmptyState icon="🏆" title="Sem dados de ranking" ...>`
- Removidos styles `vazio`/`vazioTexto`

### AvistamentoDetalheScreen.tsx
- `root` backgroundColor `colors.bg` → `colors.bgMuted`
- `content` agora tem card styling: `backgroundColor: colors.bg, borderRadius: 12, margin: spacing.md, elevation: 2, shadow*`

### ListaOngsScreen.tsx
- Adicionado pattern de busca com icone: `searchInputWrap` (flexDirection row) + `searchIcon`
- Input agora `flex: 1` dentro do wrapper, sem backgroundColor proprio (usa o do wrapper)
- Import `Text` adicionado

### OngDetalheScreen.tsx
- `hero` agora com elevation + shadow: `elevation: 2, shadowOffset: {0,2}, shadowOpacity: 0.06, shadowRadius: 6`
- Apenas 1 propriedade de estilo adicionada. Screen já usava `Section` component + theme tokens.

### PerfilScreen.tsx
- `card` agora com elevation + shadow
- `menu` agora com elevation + shadow
- `adminBanner` borderRadius `8` → `10`, borderLeftColor `'#F2994A'` → `colors.accent`

### NotificacoesScreen.tsx
- `root` backgroundColor `colors.bg` → `colors.bgMuted`

### CarteiraIdentificacaoScreen.tsx
- Inline style `{ fontSize: 40 }` movido para `styles.fotoPlaceholderIcon`

### RecuperarSenhaScreen.tsx
- `successCard` agora com elevation + shadow: `elevation: 2, shadowOpacity: 0.06, shadowRadius: 6`

### Sem alteração necessária
- EditarPerfilScreen, HistoricoScreen, CadastroAdotante, CadastroOng, CadastroVeterinario, CadastroClinica

---

## Padroes aplicados (resumo)

| Padrao | Onde |
|--------|------|
| `EmptyState` component | 6 telas (MeusAnimais, AdocoesOng, Atendimentos, FeedResgates, Ranking, EditarSaude) |
| `Secao` local com borderLeft primary | 6 telas de formulario |
| `elevation: 2` + shadow 0.06 | AvistamentoDetalhe, OngDetalhe, PerfilScreen, RecuperarSenha, CarteiraIdentificacao |
| `bgMuted` background em root | AvistamentoDetalhe, Notificacoes |
| `typography.fontSize.xs` no lugar de hardcode | FeedResgates (9→11), SolicitacaoDetalhe (9→11), AdocoesOng (10→11) |
| borderRadius consistente (10-12) | Avisos, adminBanner, cnpjBox |
| Theme tokens no lugar de hardcode | PerfilScreen adminBanner borderLeftColor |
| Inline style → StyleSheet | CarteiraIdentificacao fotoPlaceholderIcon |
| Search bar com icone | ListaOngsScreen |

## Verificação

```
npx tsc --noEmit → 0 erros
```

Total de arquivos editados: **19 telas**
