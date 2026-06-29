<?php
/**
 * Conteúdo da página Verificar Denúncias
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../Data/denuncias_mock.php';
$mockFile = __DIR__ . '/../../../Data/denuncias_mock.php';

// Inicializa denúncias a partir do mock e sincroniza com sessão
$denuncias = $denunciasMock ?? [];
if (!isset($_SESSION['denuncias']) || !is_array($_SESSION['denuncias']) || empty($_SESSION['denuncias'])) {
    $_SESSION['denuncias'] = $denuncias;
} else {
    $denuncias = $_SESSION['denuncias'];
}

// Lidar com ações POST: responder ou marcar resolvido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'respond_report' && !empty($_POST['report_id'])) {
        $rid = $_POST['report_id'];
        $resposta = trim($_POST['resposta'] ?? '');
        foreach ($denuncias as &$d) {
            if ((string)$d['id'] === (string)$rid) {
                if (!isset($d['respostas']) || !is_array($d['respostas'])) $d['respostas'] = [];
                $tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';
                $roleMap = [
                    'administrador' => 'admin',
                    'ong' => 'ong',
                    'veterinario' => 'vet',
                    'moderador' => 'campo',
                    'adotante' => 'usuario'
                ];
                $role = $roleMap[$tipoUsuario] ?? 'usuario';

                $d['respostas'][] = [
                    'texto' => $resposta,
                    'por' => $_SESSION['nome'] ?? 'Sistema',
                    'cargo' => $role,
                    'data' => date('d/m/Y H:i')
                ];
            }
        }
        unset($d);
    }

    if ($action === 'toggle_resolved' && !empty($_POST['report_id'])) {
        $rid = $_POST['report_id'];
        foreach ($denuncias as &$d) {
            if ((string)$d['id'] === (string)$rid) {
                $d['status'] = ($d['status'] === 'Resolvido') ? 'Pendente' : 'Resolvido';
            }
        }
        unset($d);
    }

    // Persistir alterações no mock file e na sessão, evitar reenvio de formulário.
    $_SESSION['denuncias'] = $denuncias;
    $export = "<?php\n\$denunciasMock = " . var_export($denuncias, true) . ";\n?>\n";
    @file_put_contents($mockFile, $export);
    header('Location: verificar_denuncias.php');
    exit;
}

?>
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-0 fw-bold">Denúncias Recebidas</h4>
                <small class="text-muted">Todas as ocorrências reportadas pela comunidade</small>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <?php if (empty($denuncias)): ?>
                    <p class="text-muted">Ainda não há denúncias registradas. Usuários poderão reportar casos através do formulário de denúncia.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Urgência</th>
                                    <th>Assunto</th>
                                    <th>Local</th>
                                    <th>Enviado</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($denuncias as $r): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo htmlspecialchars($r['id']); ?></td>
                                        <td><?php echo htmlspecialchars($r['tipo'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($r['urgencia'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($r['assunto'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($r['localizacao'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($r['criado_em'] ?? '-'); ?></td>
                                        <td>
                                            <?php if (($r['status'] ?? '') === 'Resolvido'): ?>
                                                <span class="badge bg-success">Resolvido</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-light border me-1" data-bs-toggle="modal" data-bs-target="#viewReportModal" onclick="openReportModal(this)"
                                                data-id="<?php echo htmlspecialchars($r['id']); ?>"
                                                data-tipo="<?php echo htmlspecialchars($r['tipo'] ?? ''); ?>"
                                                data-urgencia="<?php echo htmlspecialchars($r['urgencia'] ?? ''); ?>"
                                                data-assunto="<?php echo htmlspecialchars($r['assunto'] ?? ''); ?>"
                                                data-localizacao="<?php echo htmlspecialchars($r['localizacao'] ?? ''); ?>"
                                                data-descricao="<?php echo htmlspecialchars($r['descricao'] ?? ''); ?>"
                                                data-fotos="<?php echo htmlspecialchars(json_encode($r['fotos'] ?? []), ENT_QUOTES); ?>"
                                            >
                                                Visualizar
                                            </button>

                                            <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#createChamadoModal" onclick="openCreateChamadoModal(this)"
                                                data-id="<?php echo htmlspecialchars($r['id']); ?>"
                                                data-tipo="<?php echo htmlspecialchars($r['tipo'] ?? ''); ?>"
                                                data-urgencia="<?php echo htmlspecialchars($r['urgencia'] ?? ''); ?>"
                                                data-assunto="<?php echo htmlspecialchars($r['assunto'] ?? ''); ?>"
                                                data-localizacao="<?php echo htmlspecialchars($r['localizacao'] ?? ''); ?>"
                                                data-descricao="<?php echo htmlspecialchars($r['descricao'] ?? ''); ?>"
                                                data-reporter-name="<?php echo htmlspecialchars($r['reporter_name'] ?? ''); ?>"
                                            >
                                                <i data-lucide="phone-call" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                                Criar Chamado
                                            </button>

                                            <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#respondModal" onclick="openRespondModal(this)" data-id="<?php echo htmlspecialchars($r['id']); ?>">Responder</button>

                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_resolved">
                                                <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($r['id']); ?>">
                                                <button class="btn btn-sm btn-outline-secondary">Marcar/Desmarcar Resolvido</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal: Visualizar denúncia -->
        <div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Detalhes da Denúncia</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Tipo:</strong> <span id="modalTipo"></span></p>
                        <p><strong>Urgência:</strong> <span id="modalUrgencia"></span></p>
                        <p><strong>Assunto:</strong> <span id="modalAssunto"></span></p>
                        <p><strong>Localização:</strong> <span id="modalLocal"></span></p>
                        <hr>
                        <div id="modalDescricao" style="white-space:pre-wrap;"></div>
                        <div id="modalFotos" class="row g-2 mt-3"></div>
                        <hr>
                        <div id="modalRespostas"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Responder -->
        <div class="modal fade" id="respondModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h6 class="modal-title">Responder Denúncia</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="respond_report">
                            <input type="hidden" name="report_id" id="respondReportId">
                            <div class="mb-3">
                                <label class="form-label">Resposta</label>
                                <textarea class="form-control" name="resposta" id="respostaText" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Criar Chamado -->
        <div class="modal fade" id="createChamadoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h6 class="modal-title">
                            <i data-lucide="phone-call" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;"></i>
                            Criar Chamado de Resgate
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createChamadoForm">
                            <input type="hidden" name="fk_denuncia_id" id="chamadoDenunciaId">
                            <input type="hidden" name="origem" value="denuncia">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Chamado *</label>
                                <select class="form-select" name="tipo" id="chamadoTipo" required>
                                    <option value="resgate">Resgate</option>
                                    <option value="abandono">Abandono</option>
                                    <option value="maus_tratos">Maus Tratos</option>
                                    <option value="perdido">Perdido</option>
                                    <option value="encontrado">Encontrado</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Urgência *</label>
                                <select class="form-select" name="urgencia" id="chamadoUrgencia" required>
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="critica">Crítica</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assunto *</label>
                                <input type="text" class="form-control" name="assunto" id="chamadoAssunto" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Localização *</label>
                                <input type="text" class="form-control" name="localizacao" id="chamadoLocalizacao" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição *</label>
                                <textarea class="form-control" name="descricao" id="chamadoDescricao" rows="4" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome do Contato</label>
                                <input type="text" class="form-control" name="contato_nome" id="chamadoContatoNome">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Telefone do Contato</label>
                                <input type="tel" class="form-control" name="contato_telefone" id="chamadoContatoTelefone" placeholder="(00) 00000-0000">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="criarChamado()">
                            <i data-lucide="phone-call" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                            Criar Chamado
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function openReportModal(btn) {
    const id = btn.getAttribute('data-id');
    const tipo = btn.getAttribute('data-tipo');
    const urg = btn.getAttribute('data-urgencia');
    const assunto = btn.getAttribute('data-assunto');
    const local = btn.getAttribute('data-localizacao');
    const descricao = btn.getAttribute('data-descricao');
    const fotosJson = btn.getAttribute('data-fotos');

    document.getElementById('modalTipo').innerText = tipo || '-';
    document.getElementById('modalUrgencia').innerText = urg || '-';
    document.getElementById('modalAssunto').innerText = assunto || '-';
    document.getElementById('modalLocal').innerText = local || '-';
    document.getElementById('modalDescricao').innerText = descricao || '';

    const fotos = fotosJson ? JSON.parse(fotosJson) : [];
    const fotosContainer = document.getElementById('modalFotos');
    fotosContainer.innerHTML = '';
    fotos.forEach(src => {
        const col = document.createElement('div'); col.className = 'col-4 mb-2';
        col.innerHTML = `<img src="${src}" class="img-fluid rounded" style="height:120px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/120'">`;
        fotosContainer.appendChild(col);
    });

    // Carregar respostas existentes via lookup no DOM (render server-side) - we'll read from a global map if needed
    // For simplicity, build responses from dataset if provided (not present here). Leave modalRespostas empty; server-rendered responses appear below the table if desired.
    const respostasDiv = document.getElementById('modalRespostas');
    respostasDiv.innerHTML = '';

    // Optionally, fetch responses from a JS object by id inlined server-side (omitted for brevity)
}

function openRespondModal(btn) {
    const id = btn.getAttribute('data-id');
    document.getElementById('respondReportId').value = id;
    document.getElementById('respostaText').value = '';
}

function openCreateChamadoModal(btn) {
    const id = btn.getAttribute('data-id');
    const tipo = btn.getAttribute('data-tipo');
    const urg = btn.getAttribute('data-urgencia');
    const assunto = btn.getAttribute('data-assunto');
    const local = btn.getAttribute('data-localizacao');
    const descricao = btn.getAttribute('data-descricao');
    const reporterName = btn.getAttribute('data-reporter-name');

    document.getElementById('chamadoDenunciaId').value = id;
    document.getElementById('chamadoTipo').value = tipo || 'resgate';
    document.getElementById('chamadoUrgencia').value = urg || 'media';
    document.getElementById('chamadoAssunto').value = assunto || '';
    document.getElementById('chamadoLocalizacao').value = local || '';
    document.getElementById('chamadoDescricao').value = descricao || '';
    document.getElementById('chamadoContatoNome').value = reporterName || '';
}

function criarChamado() {
    const form = document.getElementById('createChamadoForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch('/chamados-criar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Chamado criado com sucesso!');
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('createChamadoModal')).hide();
        } else {
            alert('Erro ao criar chamado: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao criar chamado. Tente novamente.');
    });
}
</script>
