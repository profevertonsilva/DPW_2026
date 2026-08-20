<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Rastreadores</h1>
        <a href="/dashboard/rastreador/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Rastreador
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-rastreadores" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->rastreadores)): ?>
                            <?php foreach ($this->getView()->rastreadores as $rastreador): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_id')) ?></td>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_nome')) ?></td>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_cpf')) ?></td>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_tel1')) ?></td>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_cidade')) ?></td>
                                    <td><?= htmlspecialchars($rastreador->__get('rast_estado')) ?></td>
                                    <td>
                                        <a href="/dashboard/rastreador/editar/<?= $rastreador->__get('rast_id') ?>"
                                           class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/rastreador/excluir"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este rastreador?');">
                                            <input type="hidden" name="id" value="<?= $rastreador->__get('rast_id') ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum rastreador cadastrado.</td>
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
    $('#tabela-rastreadores').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
        "pageLength": 10,
        "responsive": true
    });
});
</script>
