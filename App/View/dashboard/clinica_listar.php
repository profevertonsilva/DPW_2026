<?php
/**
 * AmigoPet - Módulo de Clínicas (Listagem Geral)
 * Localização: ~/App/View/dashboard/clinica_listar.php
 */
$clinicas = $this->getView()->lista_clinicas ?? [];
?>

<style>
    /* FIXAÇÃO E ALINHAMENTO DA LISTAGEM (PADRÃO SIDEBAR 260px) */
    .amigopet-wrapper-listar-fix {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        
        position: absolute !important;
        top: 70px !important;
        left: 260px !important;
        width: calc(100vw - 290px) !important; 
        min-height: calc(100vh - 70px) !important;
        
        padding: 30px !important;
        box-sizing: border-box !important;
        background-color: #f8f9fa !important;
        z-index: 5 !important;
    }
    
    .amigopet-wrapper-listar-fix h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #4F4F4F;
    }

    .btn-main-success {
        background-color: #6FCF97 !important;
        color: white !important;
        font-weight: 600;
        border: none !important;
    }
    .btn-main-success:hover { background-color: #5bba84 !important; }
    
    .table-responsive {
        border-radius: 12px;
        overflow-x: auto !important;
        background-color: #ffffff;
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
</style>

<div class="amigopet-wrapper-listar-fix">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Clínicas Cadastradas 🏥</h1>
            <p class="text-muted mb-0">Gerencie as clínicas veterinárias parceiras registradas no sistema.</p>
        </div>
        <a href="/dashboard/clinica/cadastro" class="btn btn-main-success d-flex align-items-center px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i> Nova Clínica
        </a>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover mb-0" id="tabelaClinicas">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>Cidade/UF</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clinicas)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fa-2x mb-3 d-block text-opacity-25"></i>
                                    Nenhuma clínica cadastrada no banco de dados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clinicas as $clinica): 
                                if (!is_object($clinica) || !method_exists($clinica, '__get')) continue;
                            ?>
                                <tr>
                                    <td class="fw-medium text-dark">
                                        <?= htmlspecialchars($clinica->__get('cln_nome') ?? 'Sem nome') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($clinica->__get('cln_cnpj') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($clinica->__get('cln_tel1') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(($clinica->__get('cln_cidade') ?? '---') . '/' . ($clinica->__get('cln_estado') ?? '---')) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="/dashboard/clinica/editar?id=<?= $clinica->__get('cln_id') ?>" class="btn btn-sm btn-light border" title="Editar Clínica" style="color: #4F4F4F;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="/dashboard/clinica/excluir?id=<?= $clinica->__get('cln_id') ?>" class="btn btn-sm btn-light border text-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja remover esta clínica permanentemente?');">
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
    // Ativa o DataTables se houver registros cadastrados na tabela
    if ($.fn.DataTable && $('#tabelaClinicas tbody tr').length > 1) {
        $('#tabelaClinicas').DataTable({
            "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json" },
            "pageLength": 10,
            "responsive": true
        });
    }
});
</script>