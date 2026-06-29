<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Listar Publicação de Animais Encontrados</h1>
        <a href="/dashboard/publicacao/cadastro" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Publicação de Animal Encontrado
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-publicacao_encontrado" class="table table-striped table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Animal</th>
                            <th>Usuário</th>
                            <th>Data Encontro</th>
                            <th>Condição Fisica</th>
                            <th>Ações Realizadas</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                       <?php if (!empty($this->getView()->publicacoes)): ?>                                             
                        <?php foreach ($this->getView()->publicacoes as $publicacao): ?>                           
                            <tr>
                                <td><?= htmlspecialchars($publicacao->__get('id')) ?></td>
                                <td><?= htmlspecialchars($publicacao->__get('animal_nome')) ?></td>
                                <td><?= htmlspecialchars($publicacao->__get('usuario_nome')) ?></td>
                                <td><?= htmlspecialchars($publicacao->__get('data_encontro')) ?></td>
                                <td><?= htmlspecialchars($publicacao->__get('condicao_fisica')) ?></td>
                                <td><?= htmlspecialchars($publicacao->__get('acoes_realizadas')) ?></td>
                                <td>
                                    <?php
                                    $statusMap = [
                                        'aguardando acolhimento'   => ['label' => 'Aguardando Acolhimento',   'class' => 'info'],
                                        'acolhido'   => ['label' => 'Acolhido',   'class' => 'success'],
                                        'em análise'   => ['label' => 'Em Análise',   'class' => 'warning'],
                                        
                                    ];
                                    $st = strtolower($publicacao->__get('status') ?? '');
                                    $info = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'secondary'];
                                    ?>
                                    <span class="badge bg-<?= $info['class'] ?>"><?= $info['label'] ?></span>
                                </td>
                                <td>
                                    <a href="/dashboard/publicacao/editar/<?= $publicacao->__get('id') ?>"
                                        class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" action="/dashboard/publicacao/excluir"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta publicação?');">
                                        <input type="hidden" name="id" value="<?= $publicacao->__get('id') ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                         <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Nenhuma publicação cadastrada.</td>
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
    $('#tabela-publicacao_encontrado').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        pageLength: 10,
        responsive: true
    });
});
</script>