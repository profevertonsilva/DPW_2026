<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Promover Usuário a Administrador</h1>
        <a href="/dashboard/administrador/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (!empty($this->getView()->usuarios)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Selecione um usuário para promover ao cargo de administrador.
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Tipo de Usuário</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->getView()->usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td>
                                        <?php
                                        $tipoMap = [
                                            'adotante'    => 'Adotante',
                                            'rastreador'  => 'Rastreador',
                                            'veterinario' => 'Veterinário',
                                            'ong'         => 'ONG',
                                            'clinica'     => 'Clínica',
                                        ];
                                        echo htmlspecialchars($tipoMap[$usuario['tipo_usuario']] ?? ucfirst($usuario['tipo_usuario']));
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($usuario['status'] === 'ativo'): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="/dashboard/administrador/promover"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja promover este usuário a administrador?');">
                                            <input type="hidden" name="login_id" value="<?= $usuario['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-shield"></i> Promover
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Não há usuários disponíveis para promover.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
