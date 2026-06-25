<?php
/**
 * AmigoPet - Módulo de Veterinários (Listagem Geral)
 * Localização: ~/App/View/dashboard/veterinario_listar.php
 */
// Captura a lista de veterinários enviada pelo VeterinarioController
$lista_veterinarios = $this->getView()->lista_veterinarios ?? [];
?>

<style>
    /* Alinhamento padrão AmigoPet para o menu lateral fixo */
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
        background: #ffffff;
        border-radius: 12px;
        padding: 10px;
    }

    .action-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        text-decoration: none;
        font-weight: 500;
    }
    .badge-edit { background-color: #E0F2FE; color: #0369A1; }
    .badge-edit:hover { background-color: #BAE6FD; }
    .badge-delete { background-color: #FEE2E2; color: #B91C1C; }
    .badge-delete:hover { background-color: #FECACA; }
</style>

<div class="amigopet-wrapper-listar-fix">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Médicos Veterinários 🩺</h1>
            <p class="text-muted mb-0">Gerencie os profissionais cadastrados que realizam os atendimentos dos animais.</p>
        </div>
        <a href="/dashboard/veterinario/cadastro" class="btn btn-main-success px-4 py-2 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Novo Veterinário
        </a>
    </div>

    <div class="table-responsive shadow-sm border-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-secondary fw-semibold py-3" style="border-radius: 8px 0 0 8px;">Nome do Profissional</th>
                    <th scope="col" class="text-secondary fw-semibold py-3">CRMV / UF</th>
                    <th scope="col" class="text-secondary fw-semibold py-3">CPF</th>
                    <th scope="col" class="text-secondary fw-semibold py-3">Telefone</th>
                    <th scope="col" class="text-secondary fw-semibold py-3">Cidade / UF</th>
                    <th scope="col" class="text-secondary fw-semibold py-3 text-center" style="border-radius: 0 8px 8px 0; width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lista_veterinarios)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-user-md-slash d-block fs-2 mb-3 opacity-50"></i>
                            Nenhum médico veterinário cadastrado até o momento.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lista_veterinarios as $vet): ?>
                        <tr>
                            <td class="fw-semibold text-dark py-3">
                                <?= htmlspecialchars($vet->__get('vet_nome')) ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary opacity-75"><?= htmlspecialchars($vet->__get('vet_crmv')) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($vet->__get('vet_cpf')) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($vet->__get('vet_tel1')) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($vet->__get('vet_cidade')) ?> / <?= htmlspecialchars($vet->__get('vet_estado')) ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="/dashboard/veterinario/editar?id=<?= $vet->__get('vet_id') ?>" class="action-badge badge-edit">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                    </a>
                                    <a href="/dashboard/veterinario/excluir?id=<?= $vet->__get('vet_id') ?>" 
                                       class="action-badge badge-delete"
                                       onclick="return confirm('Deseja realmente remover o(a) Dr(a). <?= htmlspecialchars($vet->__get('vet_nome')) ?> permanentemente?');">
                                        <i class="fa-solid fa-trash me-1"></i> Excluir
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