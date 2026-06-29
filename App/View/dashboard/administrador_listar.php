<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Administradores</h1>
        <div>
            <a href="/dashboard/administrador/promover-usuario" class="btn btn-info me-2">
                <i class="fas fa-user-plus"></i> Promover Usuário
            </a>
            <a href="/dashboard/administrador/cadastro" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Administrador
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-administradores" class="table table-striped table-hover" width="100%">
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
                        <?php if (!empty($this->getView()->administradores)): ?>
                            <?php foreach ($this->getView()->administradores as $administrador): ?>
                                <tr>
                                    <td><?= htmlspecialchars($administrador->__get('adm_id')) ?></td>
                                    <td><?= htmlspecialchars($administrador->__get('adm_nome')) ?></td>
                                    <td><?= htmlspecialchars($administrador->__get('adm_cpf')) ?></td>
                                    <td><?= htmlspecialchars($administrador->__get('adm_tel1')) ?></td>
                                    <td><?= htmlspecialchars($administrador->__get('adm_cidade')) ?></td>
                                    <td><?= htmlspecialchars($administrador->__get('adm_estado')) ?></td>
                                    <td>
                                        <a href="/dashboard/administrador/editar/<?= $administrador->__get('adm_id') ?>"
                                           class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/administrador/excluir"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este administrador?');">
                                            <input type="hidden" name="id" value="<?= $administrador->__get('adm_id') ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum administrador cadastrado.</td>
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
    $('#tabela-administradores').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
        "pageLength": 10,
        "responsive": true
    });
});
</script>
