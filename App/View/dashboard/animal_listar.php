<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Animais</h1>
        <a href="/dashboard/animal/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Animal
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-animais" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Espécie</th>
                            <th>Sexo</th>
                            <th>Porte</th>
                            <th>Localização</th>
                            <th>Castrado</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($this->getView()->animais)): ?>
                            <?php foreach ($this->getView()->animais as $animal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($animal->__get('id')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('nome')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('especie_nome') ?? '—') ?></td>
                                    <td>
                                        <?= $animal->__get('sexo') === 'm' ? 'Macho' : 'Fêmea' ?>
                                    </td>
                                    <td><?= htmlspecialchars(ucfirst($animal->__get('porte') ?? '—')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('localizacao') ?? '—') ?></td>
                                    <td>
                                        <?php if ($animal->__get('castrado')): ?>
                                            <span class="badge bg-success">Sim</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Não</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'disponivel'    => ['label' => 'Disponível',    'class' => 'success'],
                                            'adotado'       => ['label' => 'Adotado',       'class' => 'primary'],
                                            'em_tratamento' => ['label' => 'Em Tratamento', 'class' => 'warning'],
                                            'reservado'     => ['label' => 'Reservado',     'class' => 'info'],
                                        ];
                                        $st   = $animal->__get('status') ?? '';
                                        $info = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $info['class'] ?>"><?= $info['label'] ?></span>
                                    </td>
                                    <td>
                                        <a href="/dashboard/animal/editar/<?= $animal->__get('id') ?>"
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="/dashboard/animal/excluir"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este animal?');">
                                            <input type="hidden" name="id" value="<?= $animal->__get('id') ?>">
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
    $(document).ready(function() {
        $('#tabela-animais').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },
            "pageLength": 10,
            "responsive": true
        });
    });
</script>