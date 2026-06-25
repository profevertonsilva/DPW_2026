<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    .amigopet-wrapper {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        padding: 40px;
        margin-top: 40px;
        margin-left: 260px;
    }
    .amigopet-wrapper h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #4F4F4F;
    }
    .btn-main-success {
        background-color: #6FCF97 !important;
        color: #ffffff !important;
        font-weight: 600;
        border: none !important;
    }
    .custom-card {
        background-color: #ffffff;
        border: none !important;
        border-radius: 12px !important;
    }
    .custom-table th {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        color: #A1887F !important; /* Cor decorativa */
        border-bottom: 2px solid #FAF9F6 !important;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .custom-table td {
        font-size: 0.95rem;
        color: #4F4F4F !important;
        border-bottom: 1px solid #FAF9F6 !important;
        padding: 14px 8px !important;
    }
</style>

<div class="amigopet-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Animais para Adoção</h1>
        <a href="/dashboard/animal/cadastro" class="btn btn-main-success d-flex align-items-center gap-2 px-3 py-2 shadow-sm">
            <i class="fas fa-plus"></i> Novo Pet
        </a>
    </div>

    <div class="card custom-card shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tabela-animais" class="table table-hover custom-table align-middle" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Idade</th>
                            <th>Espécie</th>
                            <th>Raça</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->animais)): ?>
                            <?php foreach ($this->getView()->animais as $animal): ?>
                                <tr>
                                    <td class="text-muted">#<?= htmlspecialchars($animal->__get('ani_id')) ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($animal->__get('ani_nome')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('ani_idade')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('ani_especie')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('ani_raca')) ?></td>
                                    <td class="text-end">
                                        <a href="/dashboard/animal/editar?id=<?= $animal->__get('ani_id') ?>" class="btn btn-sm btn-outline-secondary border-0 me-1" style="color: #4F4F4F;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="/dashboard/animal/excluir" style="display:inline-block;" onsubmit="return confirm('Deseja remover este pet?');">
                                            <input type="hidden" name="id" value="<?= $animal->__get('ani_id') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhum animal cadastrado no momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#tabela-animais').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
        "pageLength": 10,
        "responsive": true,
        "dom": '<"d-flex justify-content-between align-items-center mb-3"fl>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });
});
</script>