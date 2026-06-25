<?php
/**
 * AmigoPet - Módulo de ONGs (Listagem Geral Atualizada)
 * Localização: ~/App/View/dashboard/ong_listar.php
 */
$ongs = $this->getView()->lista_ongs ?? [];
?>

<style>
    /* FIXAÇÃO E ALINHAMENTO DEFINITIVO DA LISTAGEM */
    .amigopet-wrapper-listar-fix {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        
        /* Força o recuo para o lado direito da sidebar fixa de 260px */
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
            <h1 class="h3 mb-1">ONGs Cadastradas 🏢</h1>
            <p class="text-muted mb-0">Gerencie as instituições parceiras registradas no sistema.</p>
        </div>
        <a href="cadastro" class="btn btn-main-success d-flex align-items-center px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i> Nova ONG
        </a>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover mb-0" id="tabelaOngs">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>Cidade/UF</th>
                            <th class="text-center">Qtd Animais</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ongs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fa-2x mb-3 d-block text-opacity-25"></i>
                                    Nenhuma ONG cadastrada no banco de dados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ongs as $ong): 
                                if (!is_object($ong) || !method_exists($ong, '__get')) continue;
                            ?>
                                <tr>
                                    <td class="fw-medium text-dark">
                                        <?= htmlspecialchars($ong->__get('ong_nome') ?? 'Sem nome') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($ong->__get('ong_cnpj') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($ong->__get('ong_tel1') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(($ong->__get('ong_cidade') ?? '---') . '/' . ($ong->__get('ong_estado') ?? '---')) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($ong->__get('ong_qnt_animais') ?? '0') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="editar?id=<?= $ong->__get('ong_id') ?>" class="btn btn-sm btn-light border" title="Editar ONG" style="color: #4F4F4F;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="excluir?id=<?= $ong->__get('ong_id') ?>" class="btn btn-sm btn-light border text-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja remover esta ONG permanentemente?');">
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
    if ($.fn.DataTable && $('#tabelaOngs tbody tr').length > 1) {
        $('#tabelaOngs').DataTable({
            "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json" },
            "pageLength": 10,
            "responsive": true
        });
    }
});
</script>