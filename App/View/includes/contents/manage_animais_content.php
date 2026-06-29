<?php
/**
 * AmigoPet - Conteúdo Gerenciar Animais
 * Localização: ~/App/View/includes/contents/manage_animais_content.php
 * Página única com comportamento adaptado por cargo (role)
 */

if (session_status() === PHP_SESSION_NONE) session_start();

// Pega o tipo de usuário real da sessão
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

// Mapeia o tipo_usuario do banco para os roles do sistema
$roleMap = [
    'administrador' => 'admin',
    'ong' => 'ong',
    'veterinario' => 'vet',
    'moderador' => 'campo',
    'adotante' => 'usuario'
];

$role = $roleMap[$tipoUsuario] ?? 'usuario';

// Acesso restrito: apenas cargos que não sejam 'usuario' podem acessar
$allowedRoles = ['admin','ong','vet','campo','moderador'];
if (!in_array($role, $allowedRoles)) {
    echo "<div class=\"container p-4\"><div class=\"alert alert-danger\">Acesso negado: recurso disponível apenas para funcionários, ONGs e administradores.</div></div>";
    return;
}

// Carrega DAOs necessários
use App\DAO\AnimalDAO;
use App\DAO\EspecieDAO;
use App\DAO\RacaDAO;
use App\DAO\AnimalRacaDAO;
use App\DAO\SolicitacaoAdocaoDAO;

$animalDao = new AnimalDAO();
$especieDao = new EspecieDAO();
$racaDao = new RacaDAO();
$animalRacaDao = new AnimalRacaDAO();
$solDao = new SolicitacaoAdocaoDAO();

$animalModels = $animalDao->listar();

// Contagem de solicitações por animal
$solCounts = [];
try {
    $allSols = $solDao->listar();
    foreach ($allSols as $s) {
        $aid = $s->__get('fk_animal_id');
        if (!isset($solCounts[$aid])) $solCounts[$aid] = 0;
        $solCounts[$aid]++;
    }
} catch (\Throwable $ex) {
    // ignore
}

// Mapear modelos para arrays que a UI espera (mantendo parte da lógica antiga de vacinas/logs em sessão)
$animals = [];
foreach ($animalModels as $m) {
    $animals[] = [
        'id' => $m->__get('id'),
        'nome' => $m->__get('nome'),
        'especie' => $m->__get('especie_nome'),
        'raca' => $m->__get('racas'),
        'idade' => $m->__get('idade_meses') !== null ? (intdiv((int)$m->__get('idade_meses'),12) . 'a ' . ((int)$m->__get('idade_meses')%12) . 'm') : '',
        'sexo' => $m->__get('sexo'),
        'porte' => $m->__get('porte'),
        'imagem' => $m->__get('foto'),
        'descricao' => $m->__get('descricao'),
        'em_adocao' => ($m->__get('status') === 'disponivel') ? true : false,
        'castrado' => $m->__get('castrado') ? true : false,
        'vacinas' => $_SESSION['vacinas'][$m->__get('id')] ?? []
    ];
}

// Inicializa logs em sessão
if (!isset($_SESSION['action_logs'])) $_SESSION['action_logs'] = [];
$logs =& $_SESSION['action_logs'];

// Helper para gravar log
function pushLog($role, $action, $details = '') {
    // Persistir log em auditoria (DB)
    try {
        $audDao = new \App\DAO\AuditoriaDAO();
        $audDao->registrar($role, $action, $details);
    } catch (\Throwable $ex) {
        // Fallback para session caso DAO falhe
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['action_logs'])) $_SESSION['action_logs'] = [];
        $_SESSION['action_logs'][] = [
            'time' => date('Y-m-d H:i:s'),
            'role' => $role,
            'action' => $action,
            'details' => $details
        ];
    }
}

// Sincroniza alterações via POST (simulado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ma_action'])) {
    $act = $_POST['ma_action'];

    if ($act === 'create' && in_array($role, ['admin','ong'])) {
        // Persistir no banco com suporte a criação de espécie/raça e upload de imagem
        try {
            $uploadError = false;
            $nome = trim($_POST['nome'] ?? 'Sem nome');
            $descricao = trim($_POST['descricao'] ?? '');
            $sexo = trim($_POST['sexo'] ?? 'n/a');
            $porte = trim($_POST['porte'] ?? 'medio');
            $data_nascimento = trim($_POST['data_nascimento'] ?? '') ?: null;
            $cor = trim($_POST['cor'] ?? '');
            $localizacao = trim($_POST['localizacao'] ?? '');
            $castrado = !empty($_POST['castrado']) ? 1 : 0;

            // Especie: usa fk_especie_id ou cria nova espécie se informado
            $fk_especie_id = null;
            if (!empty($_POST['fk_especie_id'])) {
                $fk_especie_id = (int) $_POST['fk_especie_id'];
            } elseif (!empty($_POST['new_especie'])) {
                $e = new \App\Model\EspecieModel();
                $e->__set('nome', trim($_POST['new_especie']));
                $fk_especie_id = $especieDao->inserir($e);
            }

            // Raça: may provide existing fk_raca_id or new_raca name
            $fk_raca_id = null;
            if (!empty($_POST['fk_raca_id'])) {
                $fk_raca_id = (int) $_POST['fk_raca_id'];
            } elseif (!empty($_POST['new_raca'])) {
                $newRacaName = trim($_POST['new_raca']);
                $targetEspecieId = null;
                if (!empty($_POST['new_raca_especie_id'])) $targetEspecieId = (int)$_POST['new_raca_especie_id'];
                elseif (!empty($fk_especie_id)) $targetEspecieId = $fk_especie_id;

                // Try to find existing race with same name for the target species
                $existing = false;
                if ($targetEspecieId) {
                    $existing = $racaDao->buscarPorNomeEEspecie($newRacaName, $targetEspecieId);
                }

                if ($existing) {
                    $fk_raca_id = (int) $existing->__get('id');
                } else {
                    $r = new \App\Model\RacaModel();
                    $r->__set('nome', $newRacaName);
                    $r->__set('fk_especie_id', $targetEspecieId ?: null);
                    $fk_raca_id = $racaDao->inserir($r);
                }
            }

            // Se o usuário escolheu uma raça existente mas não escolheu espécie,
            // infere a espécie a partir da raça selecionada.
            if (empty($fk_especie_id) && !empty($fk_raca_id)) {
                $raceModel = $racaDao->buscarPorId($fk_raca_id);
                if ($raceModel) {
                    $fk_especie_id = (int) $raceModel->__get('fk_especie_id');
                }
            }

            // Processar as 5 imagens
            $imagens = [];
            $uploadDir = realpath(__DIR__ . '/../../../../resources/dashboard/images/animais/');
            if (!$uploadDir) {
                $uploadDir = __DIR__ . '/../../../../resources/dashboard/images/animais/';
            }
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $uploadDir = rtrim($uploadDir, '\\/') . DIRECTORY_SEPARATOR;

            // Helper para gerar nome de arquivo descritivo (igual ao AnimalController)
            $gerarNomeArquivo = function($animalNome, $animalId, $extensao) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', $animalNome ?: 'animal')));
                $slug = preg_replace('/_+/', '_', trim($slug, '_'));
                $date = date('Ymd');
                $random = substr(bin2hex(random_bytes(3)), 0, 6);
                $idPart = $animalId ? $animalId . '_' : '';
                return sprintf('animal_%s%s_%s.%s', $idPart, $slug, $date . '_' . $random, $extensao);
            };

            for ($i = 0; $i < 5; $i++) {
                // Verificar se há imagem recortada (base64)
                if (!empty($_POST['cropped_imagem_' . $i])) {
                    $base64Image = $_POST['cropped_imagem_' . $i];
                    
                    // Remove prefixo data:image/...;base64, se presente
                    if (strpos($base64Image, ',') !== false) {
                        $base64Image = explode(',', $base64Image)[1];
                    }
                    
                    $imageData = base64_decode($base64Image);
                    if ($imageData) {
                        $filename = $gerarNomeArquivo($nome, null, 'jpg');
                        $target = $uploadDir . $filename;
                        if (file_put_contents($target, $imageData)) {
                            $imagens[$i] = '/resources/dashboard/images/animais/' . $filename;
                        }
                    }
                }
                // Se não houver imagem recortada, verificar upload normal
                elseif (!empty($_FILES['imagem_' . $i]) && is_uploaded_file($_FILES['imagem_' . $i]['tmp_name'])) {
                    $ext = pathinfo($_FILES['imagem_' . $i]['name'], PATHINFO_EXTENSION);
                    $filename = $gerarNomeArquivo($nome, null, ($ext ?: 'jpg'));
                    $target = $uploadDir . $filename;
                    if (move_uploaded_file($_FILES['imagem_' . $i]['tmp_name'], $target)) {
                        $imagens[$i] = '/resources/dashboard/images/animais/' . $filename;
                    }
                }
            }

            // Reordenar imagens baseado na seleção de principal
            $principalIndex = isset($_POST['imagem_principal']) ? (int) $_POST['imagem_principal'] : 0;
            if ($principalIndex > 0 && isset($imagens[$principalIndex])) {
                $principal = $imagens[$principalIndex];
                unset($imagens[$principalIndex]);
                array_splice($imagens, 0, 0, [$principal]);
                $imagens = array_values($imagens);
            }

            $fotoUrl = $imagens[0] ?? '';

            $model = new \App\Model\AnimalModel();
            $model->__set('nome', $nome);
            $model->__set('descricao', $descricao);
            $model->__set('sexo', $sexo);
            $model->__set('porte', $porte);
            $model->__set('foto', $fotoUrl);
            $model->__set('fk_especie_id', $fk_especie_id);
            $model->__set('cor', $cor);
            $model->__set('localizacao', $localizacao);
            $model->__set('data_nascimento', $data_nascimento);
            $model->__set('castrado', $castrado);
            $model->__set('status', isset($_POST['em_adocao']) ? 'reservado' : 'disponivel');

            $newId = $animalDao->inserir($model);

            // Salvar as 5 imagens no banco
            if ($newId && !empty($imagens)) {
                $animalDao->salvarImagens((int)$newId, $imagens);
            }

            // Vincular raça se foi selecionada/ criada
            if ($newId && $fk_raca_id) {
                $animalRacaDao->vincular((int)$newId, (int)$fk_raca_id);
            }

            pushLog($role, 'create_animal', json_encode(['id' => $newId, 'nome' => $model->__get('nome')]));
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!isset($_SESSION['flash_message']) || $_SESSION['flash_message']['type'] !== 'danger') {
                $_SESSION['flash_message'] = ['type'=>'success','text'=>'Animal cadastrado com sucesso.'];
            }
        } catch (\Throwable $ex) {
            $_SESSION['flash_message'] = ['type'=>'danger','text'=>'Falha ao cadastrar animal.'];
        }
    }

    if ($act === 'toggle_adocao' && in_array($role, ['admin','ong','moderador'])) {
        $id = $_POST['pet_id'] ?? null;
        $model = $animalDao->buscarPorId((int)$id);
        if ($model) {
            $current = $model->__get('status');
            $newStatus = ($current === 'disponivel') ? 'reservado' : 'disponivel';
            $model->__set('status', $newStatus);
            $animalDao->alterar($model);
            pushLog($role, 'toggle_adocao', json_encode(['id'=>$id,'status'=>$newStatus]));
            // Mensagem flash para o usuário
            if (session_status() === PHP_SESSION_NONE) session_start();
            $petName = $model->__get('nome') ?: 'Animal';
            if ($newStatus === 'reservado') {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => "{$petName} foi marcado para adoção."];
            } else {
                $_SESSION['flash_message'] = ['type' => 'info', 'text' => "{$petName} foi removido da adoção."];
            }
        }
    }

    if ($act === 'add_vaccine' && $role === 'vet') {
        $id = $_POST['pet_id'] ?? null;
        $vac = trim($_POST['vacina'] ?? '');
        if ($id) {
            // Persistir em historico_animal
            $hist = new \App\Model\HistoricoAnimalModel();
            $hist->__set('hist_descr', $vac);
            $hist->__set('hist_data', date('Y-m-d H:i:s'));
            $hist->__set('hist_tipo', 'vacinacao');
            $hist->__set('fk_animal_id', (int)$id);
            $histDao = new \App\DAO\HistoricoAnimalDAO();
            $histDao->inserir($hist);
            pushLog($role, 'add_vaccine', json_encode(['id'=>$id,'vacina'=>$vac]));
        }
    }

    if ($act === 'castrar' && in_array($role, ['admin','vet','ong'])) {
        $id = $_POST['pet_id'] ?? null;
        $model = $animalDao->buscarPorId((int)$id);
        if ($model) {
            $model->__set('castrado', 1);
            $animalDao->alterar($model);
            pushLog($role, 'castrar', json_encode(['id'=>$id]));
        }
    }

    if ($act === 'approve_adoption' && in_array($role, ['admin','ong'])) {
        $id = $_POST['pet_id'] ?? null;
        $reason = trim($_POST['reason'] ?? '');
        pushLog($role, 'approve_adoption', json_encode(['id'=>$id,'reason'=>$reason]));
    }

    if ($act === 'deny_adoption' && in_array($role, ['admin','ong'])) {
        $id = $_POST['pet_id'] ?? null;
        $reason = trim($_POST['reason'] ?? '');
        pushLog($role, 'deny_adoption', json_encode(['id'=>$id,'reason'=>$reason]));
    }

    if ($act === 'report_found' && $role === 'campo') {
        $id = $_POST['pet_id'] ?? null;
        $loc = trim($_POST['local'] ?? '');
        $desc = trim($_POST['descricao'] ?? '');
        pushLog($role, 'report_found', json_encode(['id'=>$id,'local'=>$loc,'descricao'=>$desc]));
    }

    // After POST actions, request a client-side reload — set flag and continue rendering
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['should_reload_manage_animais'] = true;
}

// UI
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <?php if (isset($_SESSION['flash_message'])): $f = $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
            <div id="flash-message" class="alert <?php echo ($f['type']==='success')? 'alert-success':'alert-info'; ?>" role="alert" style="position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:2000;min-width:300px;max-width:80%;text-align:center;box-shadow:0 8px 20px rgba(0,0,0,0.08);">
                <?php echo htmlspecialchars($f['text']); ?>
            </div>
            <script>
                (function(){
                    var el = document.getElementById('flash-message');
                    if (!el) return;
                    setTimeout(function(){
                        el.style.transition = 'opacity 400ms ease';
                        el.style.opacity = '0';
                        setTimeout(function(){ el.remove(); }, 500);
                    }, 3500);
                })();
            </script>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Animais</h2>
            <div>Perfil atual: <strong><?php echo htmlspecialchars(strtoupper($role)); ?></strong></div>
        </div>

        <?php if (in_array($role, ['admin','ong'])): ?>
        <div class="mb-3">
            <button class="btn btn-lg btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#newAnimalCollapse" aria-expanded="false" aria-controls="newAnimalCollapse">
                Cadastrar novo animal
            </button>
        </div>

        <div class="collapse mb-4" id="newAnimalCollapse">
            <div class="card card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="ma_action" value="create">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Nome</label>
                            <input class="form-control" name="nome" placeholder="Nome" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Espécie</label>
                            <div class="input-group">
                                <select class="form-select" name="fk_especie_id" id="fk_especie_id">
                                    <option value="">-- selecione --</option>
                                    <?php try { $esps = $especieDao->listar(); foreach($esps as $esp): ?>
                                        <option value="<?php echo (int)$esp->__get('id'); ?>"><?php echo htmlspecialchars($esp->__get('nome')); ?></option>
                                    <?php endforeach; } catch(Throwable $e) {} ?>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="btnNewEspecie">+</button>
                            </div>
                            <input class="form-control mt-2 d-none" name="new_especie" id="new_especie" placeholder="Nova espécie">
                            <button class="btn btn-sm btn-primary mt-2 d-none" type="button" id="btnAddEspecie">Adicionar</button>
                            <div class="mt-1 small text-muted">Sugestões: <a href="#" class="quick-especie">Cachorro</a>, <a href="#" class="quick-especie">Gato</a></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Raça</label>
                            <div class="input-group">
                                <select class="form-select" name="fk_raca_id" id="fk_raca_id">
                                    <option value="">-- selecione --</option>
                                    <?php try { $racas = $racaDao->listar(); foreach($racas as $rc): ?>
                                        <option value="<?php echo (int)$rc->__get('id'); ?>" data-especie="<?php echo (int)$rc->__get('fk_especie_id'); ?>"><?php echo htmlspecialchars($rc->__get('nome')); ?></option>
                                    <?php endforeach; } catch(Throwable $e) {} ?>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="btnNewRaca">+</button>
                            </div>
                            <input class="form-control mt-2 d-none" name="new_raca" id="new_raca" placeholder="Nova raça">
                            <select class="form-select mt-2 d-none" name="new_raca_especie_id" id="new_raca_especie_id">
                                <option value="">-- espécie da raça --</option>
                                <?php try { $esps2 = $especieDao->listar(); foreach($esps2 as $esp2): ?>
                                    <option value="<?php echo (int)$esp2->__get('id'); ?>"><?php echo htmlspecialchars($esp2->__get('nome')); ?></option>
                                <?php endforeach; } catch(Throwable $e) {} ?>
                            </select>
                            <button class="btn btn-sm btn-primary mt-2 d-none" type="button" id="btnAddRaca">Adicionar</button>
                            <div class="form-text">Clique no + para adicionar uma nova raça e selecione a espécie associada.</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Porte</label>
                            <div class="input-group">
                                <select class="form-select" name="porte" id="porte_select">
                                    <option value="pequeno">Pequeno</option>
                                    <option value="medio" selected>Médio</option>
                                    <option value="grande">Grande</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="btnNewPorte">+</button>
                            </div>
                            <input class="form-control mt-2 d-none" id="new_porte" placeholder="Novo porte (opcional)">
                            <button class="btn btn-sm btn-primary mt-2 d-none" type="button" id="btnAddPorte">Adicionar</button>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sexo</label>
                            <select class="form-select" name="sexo">
                                <option value="m">Macho</option>
                                <option value="f">Fêmea</option>
                                <option value="n/a">N/A</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" name="data_nascimento">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Cor</label>
                            <input class="form-control" name="cor" placeholder="Cor">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Localização</label>
                            <input class="form-control" name="localizacao" placeholder="Cidade / Bairro">
                        </div>

                        <div class="col-md-3 d-flex align-items-center">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="castrado_chk" name="castrado">
                                <label class="form-check-label" for="castrado_chk">Castrado</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Imagens do animal (5 imagens, selecione a principal)</label>
                            <div class="row g-3">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <?php $previewSrc = 'https://via.placeholder.com/300?text=Upload'; ?>
                                    <div class="col-6 col-xl-3">
                                        <div class="border rounded p-3 text-center" style="aspect-ratio: 1 / 1; display: flex; flex-direction: column; justify-content: space-between;">
                                            <div>
                                                <small class="text-muted">Imagem <?= $i + 1 ?></small>
                                                <img id="preview-<?= $i ?>" src="<?= $previewSrc ?>" alt="Preview <?= $i + 1 ?>" class="img-fluid rounded mb-2" style="width:100%; height:120px; object-fit:cover;">
                                                <input class="form-control form-control-sm" type="file" id="imagem_<?= $i ?>" name="imagem_<?= $i ?>" accept="image/*" data-preview-target="preview-<?= $i ?>" data-cropped-target="cropped_imagem_<?= $i ?>">
                                                <input type="hidden" id="cropped_imagem_<?= $i ?>" name="cropped_imagem_<?= $i ?>" value="">
                                            </div>
                                            <div class="form-check mt-2 text-start">
                                                <input class="form-check-input imagem-principal-radio" type="radio" id="imagem_principal_<?= $i ?>" name="imagem_principal" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="imagem_principal_<?= $i ?>">Principal</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="form-text">Envie até 5 imagens do animal. A primeira é definida como principal por padrão. Selecione outra para alterar a principal em tempo real.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <input class="form-control" name="descricao" placeholder="Descrição curta">
                        </div>

                        <div class="col-12 text-end mt-2"><button class="btn btn-primary">Cadastrar</button></div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card-amigopet p-3 mb-3">
                    <h5 class="mb-3">Lista de Animais</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Foto</th><th>Nome</th><th>Espécie</th><th>Sexo</th><th>Status</th><th>Ações</th></tr></thead>
                            <tbody>
                                <?php foreach ($animals as $a): ?>
                                    <tr>
                                        <td style="width:80px;"><img src="<?php echo htmlspecialchars($a['imagem']); ?>" style="width:70px;height:50px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="openAnimalProfile(<?php echo htmlspecialchars($a['id']); ?>)"></td>
                                        <td><span style="color:#000;cursor:pointer;" onclick="openAnimalProfile(<?php echo htmlspecialchars($a['id']); ?>)"><?php echo htmlspecialchars($a['nome']); ?></span></td>
                                        <td><?php echo htmlspecialchars($a['especie']); ?></td>
                                        <td><?php echo ($a['sexo']==='m')? 'Macho' : (($a['sexo']==='f')? 'Fêmea' : 'N/A'); ?></td>
                                        <td>
                                            <?php echo !empty($a['em_adocao']) ? '<span class="badge bg-success">Em Adoção</span>' : '<span class="badge bg-secondary">Não em adoção</span>'; ?>
                                            <?php if (!empty($a['castrado'])): ?> <small class="text-muted"> • Castrado</small><?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Ações por role -->
                                            <?php if (in_array($role, ['admin','ong','vet','moderador'])): ?>
                                                <a href="/dashboard/animal/editar/<?php echo htmlspecialchars($a['id']); ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                            <?php endif; ?>

                                            <?php if (in_array($role, ['admin','ong','moderador'])): ?>
                                                <form style="display:inline-block" method="post">
                                                    <input type="hidden" name="ma_action" value="toggle_adocao">
                                                    <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                    <button class="btn btn-sm btn-outline-primary" type="submit"><?php echo empty($a['em_adocao']) ? 'Colocar em adoção' : 'Remover da adoção'; ?></button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($role === 'vet'): ?>
                                                <button class="btn btn-sm btn-outline-success" onclick="document.getElementById('vacina-<?php echo $a['id']; ?>').classList.toggle('d-none')">Vacinar</button>
                                                <div id="vacina-<?php echo $a['id']; ?>" class="d-none mt-2">
                                                    <form method="post">
                                                        <input type="hidden" name="ma_action" value="add_vaccine">
                                                        <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="vacina" class="form-control" placeholder="Nome da vacina">
                                                            <button class="btn btn-sm btn-primary" type="submit">Adicionar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (in_array($role, ['admin','vet','ong'])): ?>
                                                <form style="display:inline-block" method="post">
                                                    <input type="hidden" name="ma_action" value="castrar">
                                                    <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                    <button class="btn btn-sm btn-outline-warning" type="submit">Marcar castrado</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if (in_array($role, ['admin','ong'])): ?>
                                                <?php $solCount = $solCounts[$a['id']] ?? 0; ?>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('adopt-forms-<?php echo $a['id']; ?>').classList.toggle('d-none')">Solicitações <span class="badge bg-secondary ms-1"><?php echo $solCount; ?></span></button>
                                                <div id="adopt-forms-<?php echo $a['id']; ?>" class="d-none mt-2">
                                                    <form method="post" style="margin-bottom:6px;">
                                                        <input type="hidden" name="ma_action" value="approve_adoption">
                                                        <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="Motivo (opcional)">
                                                        <div class="mt-2 text-end"><button class="btn btn-sm btn-success">Aprovar</button></div>
                                                    </form>
                                                    <form method="post">
                                                        <input type="hidden" name="ma_action" value="deny_adoption">
                                                        <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="Motivo da recusa">
                                                        <div class="mt-2 text-end"><button class="btn btn-sm btn-danger">Negar</button></div>
                                                    </form>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($role === 'campo'): ?>
                                                <button class="btn btn-sm btn-outline-info" onclick="document.getElementById('found-<?php echo $a['id']; ?>').classList.toggle('d-none')">Reportar encontrado</button>
                                                <div id="found-<?php echo $a['id']; ?>" class="d-none mt-2">
                                                    <form method="post">
                                                        <input type="hidden" name="ma_action" value="report_found">
                                                        <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                                                        <input type="text" name="local" class="form-control form-control-sm" placeholder="Local encontrado">
                                                        <textarea name="descricao" class="form-control form-control-sm mt-1" placeholder="Descrição"></textarea>
                                                        <div class="mt-2 text-end"><button class="btn btn-sm btn-primary">Enviar relatório</button></div>
                                                    </form>
                                                </div>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulário de cadastro movido para o topo (collapse) -->

            </div>

            <!-- Logs agora centralizados na aba de Auditoria (Configurações do Admin) -->
        </div>

    </div>
</div>

<style>
.card-amigopet { background:white; border-radius:10px; padding:12px; border:1px solid #f0f0f0; }
</style>

<?php if (isset($_SESSION['should_reload_manage_animais']) && $_SESSION['should_reload_manage_animais']): ?>
    <script>
        // Remove flag on next request
        try { fetch('/?clear_reload=1', { method: 'POST', credentials: 'same-origin' }); } catch(e){}
        // Reload the page after the full layout is rendered
        setTimeout(function(){ window.location.href = window.location.pathname; }, 100);
    </script>
    <?php unset($_SESSION['should_reload_manage_animais']); endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btnNewEspecie = document.getElementById('btnNewEspecie');
    var newEspecie = document.getElementById('new_especie');
    var btnAddEspecie = document.getElementById('btnAddEspecie');
    var especieSelect = document.getElementById('fk_especie_id');

    if (btnNewEspecie && newEspecie) btnNewEspecie.addEventListener('click', function(){
        newEspecie.classList.toggle('d-none');
        if (btnAddEspecie) btnAddEspecie.classList.toggle('d-none');
    });

    if (btnAddEspecie && newEspecie && especieSelect) btnAddEspecie.addEventListener('click', function(){
        var val = newEspecie.value.trim();
        if (!val) return;
        var opt = document.createElement('option');
        opt.value = val;
        opt.text = val;
        opt.selected = true;
        especieSelect.appendChild(opt);
        newEspecie.value = '';
        newEspecie.classList.add('d-none');
        btnAddEspecie.classList.add('d-none');
    });

    var btnNewRaca = document.getElementById('btnNewRaca');
    var newRaca = document.getElementById('new_raca');
    var newRacaEsp = document.getElementById('new_raca_especie_id');
    var btnAddRaca = document.getElementById('btnAddRaca');
    var raceSelect = document.getElementById('fk_raca_id');

    if (btnNewRaca && newRaca) btnNewRaca.addEventListener('click', function(){
        newRaca.classList.toggle('d-none');
        if (newRacaEsp) newRacaEsp.classList.toggle('d-none');
        if (btnAddRaca) btnAddRaca.classList.toggle('d-none');
        if (newRacaEsp && especieSelect && especieSelect.value) newRacaEsp.value = especieSelect.value;
    });

    if (btnAddRaca && newRaca && newRacaEsp && raceSelect) btnAddRaca.addEventListener('click', function(){
        var val = newRaca.value.trim();
        var espId = newRacaEsp.value;
        if (!val || !espId) return;
        var opt = document.createElement('option');
        opt.value = val;
        opt.text = val;
        opt.setAttribute('data-especie', espId);
        opt.selected = true;
        raceSelect.appendChild(opt);
        newRaca.value = '';
        newRaca.classList.add('d-none');
        newRacaEsp.classList.add('d-none');
        btnAddRaca.classList.add('d-none');
        filterRacasByEspecie();
    });

    var btnNewPorte = document.getElementById('btnNewPorte');
    var newPorte = document.getElementById('new_porte');
    var btnAddPorte = document.getElementById('btnAddPorte');
    var porteSelect = document.getElementById('porte_select');

    if (btnNewPorte && newPorte) btnNewPorte.addEventListener('click', function(){
        newPorte.classList.toggle('d-none');
        if (btnAddPorte) btnAddPorte.classList.toggle('d-none');
        if (!newPorte.classList.contains('d-none')) newPorte.focus();
        else newPorte.value = '';
    });

    if (btnAddPorte && newPorte && porteSelect) btnAddPorte.addEventListener('click', function(){
        var val = newPorte.value.trim();
        if (!val) return;
        var opt = document.createElement('option');
        opt.value = val;
        opt.text = val;
        opt.selected = true;
        porteSelect.appendChild(opt);
        newPorte.value = '';
        newPorte.classList.add('d-none');
        btnAddPorte.classList.add('d-none');
    });

    var quicks = document.querySelectorAll('.quick-especie');
    quicks.forEach(function(el){ el.addEventListener('click', function(e){ e.preventDefault(); var v = this.textContent.trim(); var input = document.getElementById('new_especie'); if (input){ input.classList.remove('d-none'); input.value = v; if (btnAddEspecie) btnAddEspecie.classList.remove('d-none'); } }); });

    // Filter races by selected species
    function filterRacasByEspecie() {
        var especieVal = especieSelect ? especieSelect.value : '';
        if (!raceSelect) return;
        var options = raceSelect.querySelectorAll('option[data-especie]');
        options.forEach(function(opt){
            var e = opt.getAttribute('data-especie') || '';
            if (!especieVal) { opt.style.display = ''; } else {
                if (String(e) === String(especieVal)) opt.style.display = '';
                else opt.style.display = 'none';
            }
        });
        if (raceSelect.value) {
            var cur = raceSelect.selectedOptions[0];
            if (cur && cur.style.display === 'none') raceSelect.value = '';
        }
    }

    if (especieSelect) especieSelect.addEventListener('change', function(){
        if (newRacaEsp && especieSelect.value) newRacaEsp.value = especieSelect.value;
        filterRacasByEspecie();
    });

    filterRacasByEspecie();
});
</script>

<!-- Modal de Perfil do Animal -->
<div class="modal fade" id="animalProfileModal" tabindex="-1" aria-labelledby="animalProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="animalProfileModalLabel">Perfil do Animal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="animalProfileContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="animalProfileFullLink" href="#" class="btn btn-primary d-none">Ver Perfil Completo</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
function openAnimalProfile(animalId) {
    var modal = new bootstrap.Modal(document.getElementById('animalProfileModal'));
    var content = document.getElementById('animalProfileContent');
    var fullLink = document.getElementById('animalProfileFullLink');

    content.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
    fullLink.classList.add('d-none');

    fetch('/api/animal.php?id=' + animalId)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = '<div class="alert alert-danger">Erro ao carregar perfil: ' + data.error + '</div>';
                return;
            }

            var animal = data.animal;
            var imagens = data.imagens || [];
            var fotoPrincipal = imagens.length > 0 ? imagens[0] : (animal.foto || 'https://via.placeholder.com/300');

            content.innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="${fotoPrincipal}" class="img-fluid rounded mb-3" style="max-height:200px;object-fit:cover;">
                    </div>
                    <div class="col-md-8">
                        <h4>${animal.nome || 'Sem nome'}</h4>
                        <p><strong>Espécie:</strong> ${animal.especie_nome || 'N/A'}</p>
                        <p><strong>Raça:</strong> ${animal.racas || 'N/A'}</p>
                        <p><strong>Sexo:</strong> ${animal.sexo === 'm' ? 'Macho' : (animal.sexo === 'f' ? 'Fêmea' : 'N/A')}</p>
                        <p><strong>Porte:</strong> ${animal.porte || 'N/A'}</p>
                        <p><strong>Status:</strong> ${animal.status === 'disponivel' ? 'Disponível' : 'Reservado'}</p>
                        <p><strong>Descrição:</strong> ${animal.descricao || 'Sem descrição'}</p>
                    </div>
                </div>
            `;

            fullLink.href = '/animal/perfil/' + animalId;
            fullLink.classList.remove('d-none');

            modal.show();
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Erro ao carregar perfil: ' + error.message + '</div>';
            modal.show();
        });
}
</script>

<!-- Modal de recorte simples -->
<div id="cropModal" class="modal fade" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Recorte de imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center">
                <p>Arraste/zoom e ajuste a seleção quadrada. Confirme para aplicar o recorte.</p>
                <div class="crop-preview position-relative mx-auto" style="max-width: 700px;">
                    <img id="cropPreviewImage" src="" alt="Preview de recorte" class="img-fluid rounded" style="max-width:100%; display:block; margin:0 auto;">
                </div>
                <div id="cropStatus" class="mt-3 small text-muted">Aguardando imagem...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="cropConfirmBtn" class="btn btn-primary">Confirmar recorte</button>
            </div>
        </div>
    </div>
</div>

<!-- Cropper.js -->
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css">
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cropPreviewImage = document.getElementById('cropPreviewImage');
    var currentHiddenInput = null;

    function getCropModal() {
        var cropModalElement = document.getElementById('cropModal');
        if (!cropModalElement || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return null;
        }
        return new bootstrap.Modal(cropModalElement, { backdrop: 'static', keyboard: false });
    }

    var cropModal = getCropModal();
    var cropper = null;
    var currentInput = null;
    var currentPreview = null;

    // Handler para os 5 campos de imagem
    document.querySelectorAll('input[name^="imagem_"]').forEach(function(input) {
        input.addEventListener('change', function() {
            if (!this.files || !this.files[0]) {
                return;
            }
            var targetId = this.dataset.previewTarget;
            var preview = targetId ? document.getElementById(targetId) : null;
            if (!preview) {
                return;
            }
            currentInput = this;
            currentPreview = preview;
            var croppedTargetId = this.dataset.croppedTarget;
            currentHiddenInput = croppedTargetId ? document.getElementById(croppedTargetId) : null;
            if (currentHiddenInput) {
                currentHiddenInput.value = '';
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                if (cropPreviewImage) {
                    cropPreviewImage.src = e.target.result;
                }
                if (cropModal) {
                    cropModal.show();
                }
            };
            reader.readAsDataURL(this.files[0]);
        });
    });

    // Inicializa/destrói Cropper ao mostrar/ocultar modal
    var cropModalElement = document.getElementById('cropModal');
    if (cropModalElement) {
        cropModalElement.addEventListener('shown.bs.modal', function () {
            if (!cropPreviewImage) return;
            var status = document.getElementById('cropStatus');
            if (status) {
                status.textContent = 'Inicializando recorte...';
            }

            function initCropper() {
                if (typeof Cropper === 'undefined') {
                    if (status) {
                        status.textContent = 'Cropper ainda não carregado. Tentando novamente...';
                    }
                    return false;
                }
                if (cropper) {
                    try { cropper.destroy(); } catch (e) {}
                    cropper = null;
                }
                cropper = new Cropper(cropPreviewImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    background: false,
                    autoCropArea: 0.9,
                    responsive: true,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                });
                if (status) {
                    status.textContent = 'Arraste/zoom para ajustar o recorte e clique em Confirmar recorte.';
                }
                return true;
            }

            if (cropPreviewImage.complete && cropPreviewImage.naturalWidth !== 0) {
                if (!initCropper()) {
                    setTimeout(initCropper, 150);
                }
            } else {
                cropPreviewImage.onload = function () {
                    if (!initCropper()) {
                        setTimeout(initCropper, 150);
                    }
                    cropPreviewImage.onload = null;
                };
            }
        });

        cropModalElement.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (currentInput && !currentHiddenInput.value) {
                currentInput.value = '';
                if (currentPreview) {
                    var originalSrc = currentPreview.dataset.originalSrc;
                    if (originalSrc) {
                        currentPreview.src = originalSrc;
                    }
                }
            }
            currentInput = null;
            currentPreview = null;
            currentHiddenInput = null;
        });
    }

    // Salvar preview original antes de alterar
    document.querySelectorAll('input[name^="imagem_"]').forEach(function(input) {
        var targetId = input.dataset.previewTarget;
        var preview = targetId ? document.getElementById(targetId) : null;
        if (preview) {
            preview.dataset.originalSrc = preview.src;
        }
    });

    // Handler para seleção de imagem principal em tempo real
    document.querySelectorAll('.imagem-principal-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                console.log('Imagem principal selecionada: ' + this.value);
            }
        });
    });

    // Confirmar recorte
    var cropConfirmBtn = document.getElementById('cropConfirmBtn');
    if (cropConfirmBtn) {
        cropConfirmBtn.addEventListener('click', function () {
            var status = document.getElementById('cropStatus');
            if (!cropper || !currentInput) {
                if (status) {
                    status.textContent = 'Recorte não inicializado.';
                }
                return;
            }
            var canvas = cropper.getCroppedCanvas({ width: 800, height: 800, imageSmoothingQuality: 'high' });
            if (!canvas) {
                if (status) {
                    status.textContent = 'Não foi possível gerar o canvas de recorte.';
                }
                return;
            }
            canvas.toBlob(function (blob) {
                if (!blob) {
                    if (status) {
                        status.textContent = 'Falha ao gerar blob de imagem recortada.';
                    }
                    return;
                }
                var filename = 'gallery_' + Date.now() + '.jpg';
                try {
                    var file = new File([blob], filename, { type: 'image/jpeg' });
                } catch (e) {
                    var file = blob;
                    file.name = filename;
                }

                var dt = new DataTransfer();
                dt.items.add(file);
                currentInput.files = dt.files;

                var objectUrl = URL.createObjectURL(blob);
                if (currentPreview) {
                    currentPreview.src = objectUrl;
                }

                if (currentHiddenInput) {
                    currentHiddenInput.value = canvas.toDataURL('image/jpeg', 0.9);
                }

                if (status) {
                    status.textContent = 'Imagem recortada e pronta para enviar.';
                }

                if (cropModal) {
                    cropModal.hide();
                }
            }, 'image/jpeg', 0.9);
        });
    }
});
</script>
