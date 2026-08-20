<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Raças</h1>
        <a href="/dashboard/raca/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Raça
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-racas" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Espécie</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->racas)): ?>
                            <?php foreach ($this->getView()->racas as $raca): ?>
                                <tr>
                                    <td><?= htmlspecialchars($raca->__get('id')) ?></td>
                                    <td><?= htmlspecialchars($raca->__get('nome')) ?></td>
                                    <td><?= htmlspecialchars($raca->__get('especie_nome') ?? '—') ?></td>
                                    <td>
                                        <a href="/dashboard/raca/editar/<?= $raca->__get('id') ?>"
                                           class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/raca/excluir"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta raça?');">
                                            <input type="hidden" name="id" value="<?= $raca->__get('id') ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tabela-racas').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' },
            pageLength: 10,
            responsive: true
        });
    });
</script>
