<style>
    .page-header-animal {
        margin-bottom: 28px;
    }
    .page-header-animal h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.6rem;
        color: #2D2D2D;
    }
    .page-header-animal p {
        color: #9B9B9B;
        font-size: 0.88rem;
        margin-bottom: 0;
    }
    .btn-novo-animal {
        background-color: #6FCF97;
        border: none;
        color: white;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 20px;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .btn-novo-animal:hover {
        background-color: #5BBF87;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(111, 207, 151, 0.35);
    }
    .card-tabela {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-tabela .card-body {
        padding: 24px;
    }
    #tabela-animais thead th {
        background-color: #F8F9FA;
        color: #6B7280;
        font-family: 'Poppins', sans-serif;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #F0F0F0;
        padding: 14px 16px;
        white-space: nowrap;
    }
    #tabela-animais tbody td {
        font-size: 0.875rem;
        color: #4F4F4F;
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #F5F5F5;
    }
    #tabela-animais tbody tr:hover {
        background-color: #FAFFFE;
    }
    #tabela-animais tbody tr:last-child td {
        border-bottom: none;
    }
    .animal-nome {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #2D2D2D;
    }
    .badge-especie {
        background-color: rgba(111, 207, 151, 0.12);
        color: #27AE60;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .badge-racas {
        background-color: rgba(242, 153, 74, 0.12);
        color: #F2994A;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-castrado-sim {
        background-color: rgba(111, 207, 151, 0.15);
        color: #27AE60;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-castrado-nao {
        background-color: rgba(0,0,0,0.05);
        color: #9B9B9B;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-status {
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge-disponivel   { background: rgba(111,207,151,0.15); color: #27AE60; }
    .badge-adotado      { background: rgba(47,128,237,0.12);  color: #2F80ED; }
    .badge-em_tratamento{ background: rgba(242,201,76,0.18);  color: #B8860B; }
    .badge-reservado    { background: rgba(155,89,182,0.12);  color: #8E44AD; }
    .sexo-icon { font-size: 0.85rem; font-weight: 600; }
    .sexo-m { color: #2F80ED; }
    .sexo-f { color: #F2994A; }
    .btn-editar-animal {
        background: rgba(242,153,74,0.1);
        border: none;
        color: #F2994A;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 10px;
        transition: all 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-editar-animal:hover {
        background: #F2994A;
        color: white;
    }
    .btn-excluir-animal {
        background: rgba(235,87,87,0.08);
        border: none;
        color: #EB5757;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 10px;
        transition: all 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-excluir-animal:hover {
        background: #EB5757;
        color: white;
    }
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #9B9B9B;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        display: block;
        opacity: 0.3;
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">

    <!-- Header -->
    <div class="page-header-animal d-flex justify-content-between align-items-center">
        <div>
            <h1>Animais</h1>
        </div>
        <a href="/dashboard/animal/cadastro" class="btn btn-novo-animal">
            <i class="fas fa-plus me-2"></i> Novo Animal
        </a>
    </div>

    <!-- Tabela -->
    <div class="card card-tabela">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabela-animais" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Animal</th>
                            <th>Espécie</th>
                            <th>Raças</th>
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
                                    <td class="text-muted" style="font-size:0.8rem;"><?= htmlspecialchars($animal->__get('id')) ?></td>
                                    <td>
                                        <span class="animal-nome"><?= htmlspecialchars($animal->__get('nome')) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-especie"><?= htmlspecialchars($animal->__get('especie_nome') ?? '—') ?></span>
                                    </td>
                                    <td>
                                        <?php $racas = $animal->__get('racas'); ?>
                                        <?php if ($racas): ?>
                                            <span class="badge-racas"><?= htmlspecialchars($racas) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($animal->__get('sexo') === 'm'): ?>
                                            <span class="sexo-icon sexo-m">Macho</span>
                                        <?php else: ?>
                                            <span class="sexo-icon sexo-f">Fêmea</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(ucfirst($animal->__get('porte') ?? '—')) ?></td>
                                    <td><?= htmlspecialchars($animal->__get('localizacao') ?? '—') ?></td>
                                    <td>
                                        <?php if ($animal->__get('castrado')): ?>
                                            <span class="badge-castrado-sim">Sim</span>
                                        <?php else: ?>
                                            <span class="badge-castrado-nao">Não</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusMap = [
                                            'disponivel'    => ['label' => 'Disponível',    'class' => 'badge-disponivel'],
                                            'adotado'       => ['label' => 'Adotado',       'class' => 'badge-adotado'],
                                            'em_tratamento' => ['label' => 'Em Tratamento', 'class' => 'badge-em_tratamento'],
                                            'reservado'     => ['label' => 'Reservado',     'class' => 'badge-reservado'],
                                        ];
                                        $st   = $animal->__get('status') ?? '';
                                        $info = $statusMap[$st] ?? ['label' => ucfirst($st), 'class' => 'badge-disponivel'];
                                        ?>
                                        <span class="badge-status <?= $info['class'] ?>"><?= $info['label'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="/dashboard/animal/editar/<?= $animal->__get('id') ?>" class="btn btn-editar-animal">
                                                <i class="fas fa-pen me-1"></i> Editar
                                            </a>
                                            <form method="POST" action="/dashboard/animal/excluir"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este animal?');">
                                                <input type="hidden" name="id" value="<?= $animal->__get('id') ?>">
                                                <button type="submit" class="btn btn-excluir-animal">
                                                    <i class="fas fa-trash me-1"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="fas fa-paw"></i>
                                        <p class="mb-0">Nenhum animal cadastrado ainda.</p>
                                    </div>
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
        $('#tabela-animais').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' },
            pageLength: 10,
            responsive: true,
            columnDefs: [{ orderable: false, targets: 9 }]
        });
    });
</script>
