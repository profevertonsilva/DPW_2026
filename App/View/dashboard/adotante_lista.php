<?php
/**
 * AmigoPet - Módulo de Adotantes (Listagem Geral)
 * Localização: ~/App/View/dashboard/adotante_listar.php
 */

// Captura a lista de adotantes enviada pelo Controller (garante array se vier nulo)
$adotantes = $this->getView()->adotantes ?? [];
?>

<style>
    /* Identidade Visual Obrigatória AmigoPet */
    .amigopet-wrapper {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        padding: 40px;
        margin-top: 40px;
        margin-left: 260px; /* Alinhamento correto com a Sidebar */
    }
    .amigopet-wrapper h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #4F4F4F;
    }
    .btn-main-success {
        background-color: #6FCF97 !important;
        color: white !important;
        font-weight: 600;
        border: none !important;
        transition: background-color 0.2s ease;
    }
    .btn-main-success:hover {
        background-color: #5bba84 !important;
    }
    /* Estilização da Tabela */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    .custom-table thead {
        background-color: #FAF9F6 !important;
        color: #4F4F4F;
        font-weight: 600;
    }
    .custom-table th, .custom-table td {
        padding: 16px 12px !important;
        vertical-align: middle !important;
    }
    /* Badge de Avaliação de Perfil */
    .badge-status {
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.85rem;
        text-transform: capitalize;
    }
    .badge-excelente { background-color: #D1FAE5; color: #065F46; }
    .badge-muito-bom { background-color: #DBEAFE; color: #1E40AF; }
    .badge-bom { background-color: #FEF3C7; color: #92400E; }
    .badge-regular { background-color: #F3F4F6; color: #374151; }
    .badge-pessimo { background-color: #FEE2E2; color: #991B1B; }
</style>

<div class="amigopet-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Lista de Adotantes 👥</h1>
            <p class="text-muted mb-0">Gerencie os tutores cadastrados e acompanhe o status da triagem de segurança.</p>
        </div>
        <a href="/dashboard/adotante/cadastrar" class="btn btn-main-success d-flex align-items-center px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i> Novo Adotante
        </a>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover mb-0" id="tabelaAdotantes">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Contato</th>
                            <th>Cidade/UF</th>
                            <th class="text-center">Avaliação</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($adotantes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fa-2x mb-3 d-block text-opacity-25"></i>
                                    Nenhum adotante localizado no banco de dados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($adotantes as $adotante): 
                                // Proteção nativa extra para garantir que o laço não quebre se houver registro corrompido
                                if (!is_object($adotante) || !method_exists($adotante, '__get')) continue;
                                
                                // Captura e trata o status para a estilização do badge
                                $status = strtolower($adotante->__get('adt_status') ?? 'regular');
                                $badgeClass = 'badge-regular';
                                if ($status === 'excelente')  $badgeClass = 'badge-excelente';
                                if ($status === 'muito bom')  $badgeClass = 'badge-muito-bom';
                                if ($status === 'bom')        $badgeClass = 'badge-bom';
                                if ($status === 'pessimo')    $badgeClass = 'badge-pessimo';
                            ?>
                                <tr>
                                    <td class="fw-medium text-dark">
                                        <?= htmlspecialchars($adotante->__get('adt_nome') ?? 'Não informado') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($adotante->__get('adt_cpf') ?? '---') ?>
                                    </td>
                                    <td>
                                        <div class="small text-dark"><?= htmlspecialchars($adotante->__get('adt_tel1') ?? '') ?></div>
                                        <?php if ($adotante->__get('adt_tel2')): ?>
                                            <div class="small text-muted"><?= htmlspecialchars($adotante->__get('adt_tel2')) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($adotante->__get('adt_cidade') ?? '---') ?> / <?= htmlspecialchars($adotante->__get('adt_estado') ?? '--') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status <?= $badgeClass ?>">
                                            <?= htmlspecialchars($adotante->__get('adt_status') ?? 'Regular') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="/dashboard/adotante/editar?id=<?= $adotante->__get('adt_id') ?>" class="btn btn-sm btn-light border" title="Editar Adotante" style="color: #4F4F4F;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="/dashboard/adotante/deletar?id=<?= $adotante->__get('adt_id') ?>" class="btn btn-sm btn-light border text-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja remover este adotante?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable && $('#tabelaAdotantes tbody tr').length > 1) {
        $('#tabelaAdotantes').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
            },
            "pageLength": 10,
            "responsive": true
        });
    }
});
</script>
