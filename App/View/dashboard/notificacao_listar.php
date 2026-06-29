<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Notificações</h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-notificacoes" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Mensagem</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($this->getView()->notificacoes)): ?>

                        <?php foreach ($this->getView()->notificacoes as $notificacao): ?>

                            <?php
                                $lida = $notificacao->__get('lida');
                                $status = $lida ? 'lida' : 'nao_lida';

                                $statusMap = [
                                    'nao_lida' => ['label' => 'Não lida', 'class' => 'danger'],
                                    'lida'     => ['label' => 'Lida',     'class' => 'success'],
                                ];

                                $info = $statusMap[$status];
                            ?>

                            <tr>
                                <td><?= htmlspecialchars($notificacao->__get('id')) ?></td>

                                <td><?= htmlspecialchars($notificacao->__get('titulo')) ?></td>

                                <td><?= htmlspecialchars($notificacao->__get('mensagem')) ?></td>

                                <td><?= htmlspecialchars($notificacao->__get('tipo')) ?></td>

                                <td>
                                    <span class="badge bg-<?= $info['class'] ?>">
                                        <?= $info['label'] ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($notificacao->__get('data_criacao')) ?></td>

                                <td>
                                    <?php if (!$lida): ?>
                                        <form method="POST" action="/dashboard/notificacao/marcar">
                                            <input type="hidden" name="id" value="<?= $notificacao->__get('id') ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Marcar como lida
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Sem ações</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Nenhuma notificação encontrada.
                            </td>
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
    $('#tabela-notificacoes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        pageLength: 10,
        responsive: true
    });
});
</script>