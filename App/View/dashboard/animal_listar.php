<?php
/**
 * AmigoPet - Módulo de Animais (Listagem Geral)
 * Localização: ~/App/View/dashboard/animal_listar.php
 */

// Captura a lista de animais enviada pelo Controller (garante array vazio se vier nulo)
$animais = $this->getView()->animais ?? [];
?>

<style>
    /* FIXAÇÃO E ALINHAMENTO DEFINITIVO DA LISTAGEM */
    .amigopet-wrapper-listar-fix {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        
        /* Força o recuo definitivo para o lado direito da sidebar fixa de 260px */
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
    
    /* Cores das Badges de Status do Pet */
    .badge-status {
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.85rem;
        text-transform: capitalize;
    }
    .badge-disponivel { background-color: #D1FAE5; color: #065F46; }
    .badge-triagem { background-color: #FEF3C7; color: #92400E; }
    .badge-adotado { background-color: #DBEAFE; color: #1E40AF; }
</style>

<div class="amigopet-wrapper-listar-fix">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Prontuário de Animais 🐾</h1>
            <p class="text-muted mb-0">Gerencie os pets cadastrados na plataforma e altere seus status de adoção.</p>
        </div>
        <a href="/dashboard/animal/cadastrar" class="btn btn-main-success d-flex align-items-center px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i> Novo Pet
        </a>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table table-hover mb-0" id="tabelaAnimais">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Sexo</th>
                            <th>Porte</th>
                            <th>Cor</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($animais)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fa-2x mb-3 d-block text-opacity-25"></i>
                                    Nenhum pet cadastrado no banco de dados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($animais as $animal): 
                                if (!is_object($animal) || !method_exists($animal, '__get')) continue;
                                
                                $status = strtolower($animal->__get('status') ?? 'disponível');
                                $badgeClass = 'badge-disponivel';
                                if ($status === 'triagem') $badgeClass = 'badge-triagem';
                                if ($status === 'adotado') $badgeClass = 'badge-adotado';
                            ?>
                                <tr>
                                    <td class="fw-medium text-dark">
                                        <?= htmlspecialchars($animal->__get('nome') ?? 'Sem nome') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($animal->__get('sexo') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($animal->__get('porte') ?? '---') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($animal->__get('cor') ?? '---') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status <?= $badgeClass ?>">
                                            <?= htmlspecialchars($animal->__get('status') ?? 'Disponível') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="/dashboard/animal/editar?id=<?= $animal->__get('id') ?>" class="btn btn-sm btn-light border" title="Editar Pet" style="color: #4F4F4F;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="/dashboard/animal/excluir?id=<?= $animal->__get('id') ?>" class="btn btn-sm btn-light border text-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja remover este pet permanentemente?');">
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
    // Liga o DataTables com tradução para pt-BR
    if ($.fn.DataTable && $('#tabelaAnimais tbody tr').length > 1) {
        $('#tabelaAnimais').DataTable({
            "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json" },
            "pageLength": 10,
            "responsive": true
        });
    }
});
</script>