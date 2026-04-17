<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Adotantes</h1>
        <a href="/dashboard/adotante/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Adotante
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-adotantes" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->adotantes)): ?>
                            <?php foreach ($this->getView()->adotantes as $adotante): ?>
                                <tr>
                                    <td><?= htmlspecialchars($adotante->__get('adt_id')) ?></td>
                                    <td><?= htmlspecialchars($adotante->__get('adt_nome')) ?></td>
                                    <td><?= htmlspecialchars($adotante->__get('adt_cpf')) ?></td>
                                    <td><?= htmlspecialchars($adotante->__get('adt_tel1')) ?></td>
                                    <td><?= htmlspecialchars($adotante->__get('adt_cidade')) ?></td>
                                    <td><?= htmlspecialchars($adotante->__get('adt_estado')) ?></td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'pessimo'   => ['label' => 'Péssimo',   'class' => 'danger'],
                                            'regular'   => ['label' => 'Regular',   'class' => 'warning'],
                                            'bom'       => ['label' => 'Bom',       'class' => 'success'],
                                            'muito bom' => ['label' => 'Muito Bom', 'class' => 'primary'],
                                            'excelente' => ['label' => 'Excelente', 'class' => 'info'],
                                        ];
                                        $st = strtolower($adotante->__get('adt_status') ?? '');
                                        $info = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $info['class'] ?>"><?= $info['label'] ?></span>
                                    </td>
                                    <td>
                                        <a href="/dashboard/adotante/editar/<?= $adotante->__get('adt_id') ?>"
                                           class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/adotante/excluir"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este adotante?');">
                                            <input type="hidden" name="id" value="<?= $adotante->__get('adt_id') ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Nenhum adotante cadastrado.</td>
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
    $('#tabela-adotantes').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json" },
        "pageLength": 10,
        "responsive": true
    });
});
</script>
