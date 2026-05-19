<?php
/**
 * View: animal_listar.php
 * Layout: dashboard
 * Dados esperados: $this->animais (array de AnimalModel)
 */
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Animais</h4>
        <a href="/dashboard/animal/cadastro" class="btn btn-primary btn-sm">+ Novo Animal</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Sexo</th>
                        <th>Porte</th>
                        <th>Status</th>
                        <th>Localização</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($animais)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhum animal cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($animais as $animal): ?>
                        <tr>
                            <td><?= $animal->__get('id') ?></td>
                            <td><?= htmlspecialchars($animal->__get('nome')) ?></td>
                            <td><?= htmlspecialchars($animal->__get('especie_nome') ?? '—') ?></td>
                            <td><?= $animal->__get('sexo') === 'm' ? 'Macho' : ($animal->__get('sexo') === 'f' ? 'Fêmea' : '—') ?></td>
                            <td><?= htmlspecialchars(ucfirst($animal->__get('porte') ?? '—')) ?></td>
                            <td>
                                <?php
                                    $status = $animal->__get('status');
                                    $badge = match($status) {
                                        'disponivel'    => 'success',
                                        'adotado'       => 'primary',
                                        'reservado'     => 'warning',
                                        'em_tratamento' => 'danger',
                                        default         => 'secondary'
                                    };
                                    $label = match($status) {
                                        'disponivel'    => 'Disponível',
                                        'adotado'       => 'Adotado',
                                        'reservado'     => 'Reservado',
                                        'em_tratamento' => 'Em Tratamento',
                                        default         => $status
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                            </td>
                            <td><?= htmlspecialchars($animal->__get('localizacao') ?? '—') ?></td>
                            <td>
                                <a href="/dashboard/animal/editar/<?= $animal->__get('id') ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <form method="POST" action="/dashboard/animal/excluir" class="d-inline"
                                      onsubmit="return confirm('Excluir <?= addslashes(htmlspecialchars($animal->__get('nome'))) ?>?')">
                                    <input type="hidden" name="id" value="<?= $animal->__get('id') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
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