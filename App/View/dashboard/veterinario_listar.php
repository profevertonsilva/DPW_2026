<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Veterinários</h1>
        <a href="/dashboard/veterinario/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Veterinário
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-veterinarios" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>CRMV</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->veterinarios)): ?>
                            <?php foreach ($this->getView()->veterinarios as $veterinario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_id')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_nome')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_cpf')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_crmv')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_tel1')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_cidade')) ?></td>
                                    <td><?= htmlspecialchars($veterinario->__get('vet_estado')) ?></td>
                                    <td>
                                        <a href="/dashboard/veterinario/editar/<?= $veterinario->__get('vet_id') ?>"
                                           class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/veterinario/excluir"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este veterinário?');">
                                            <input type="hidden" name="id" value="<?= $veterinario->__get('vet_id') ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Nenhum veterinário cadastrado.</td>
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
    $('#tabela-veterinarios').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
        "pageLength": 10,
        "responsive": true
    });
});
</script>
