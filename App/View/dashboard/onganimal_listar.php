<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">Relacionamento ONG x Animal</h1>

        <a href="/dashboard/onganimal/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Relacionamento
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table id="tabela-onganimal"
                    class="table table-striped table-hover"
                    width="100%">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>ID ONG</th>
                            <th>ID Animal</th>
                            <th>Ações</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($this->getView()->ongAnimais)): ?>

                            <?php foreach ($this->getView()->ongAnimais as $ongAnimal): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($ongAnimal->__get('onganl_id')) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($ongAnimal->__get('fk_ong_id')) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($ongAnimal->__get('fk_animal_id')) ?>
                                    </td>

                                    <td>

                                        <a href="/dashboard/onganimal/editar/<?= $ongAnimal->__get('onganl_id') ?>"
                                            class="btn btn-warning btn-sm me-1">

                                            <i class="fas fa-edit"></i> Editar

                                        </a>

                                        <form method="POST"
                                            action="/dashboard/onganimal/excluir"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este relacionamento?');">

                                            <input type="hidden"
                                                name="id"
                                                value="<?= $ongAnimal->__get('onganl_id') ?>">

                                            <button type="submit"
                                                class="btn btn-danger btn-sm">

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

        $('#tabela-onganimal').DataTable({

            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },

            "pageLength": 10,
            "responsive": true
        });

    });
</script>