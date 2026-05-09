<?php
/**
 * AmigoPet - Conteúdo de Gerenciamento de Usuários
 * Localização: ~/App/View/usuarios_content.php
 */

// Simulação de base de dados de utilizadores
$usuariosLista = [
    [
        'id' => 1,
        'nome' => 'João Silva',
        'email' => 'joao.silva@email.com',
        'telefone' => '(11) 91234-5678',
        'status' => 'Ativo',
        'status_classe' => 'bg-success',
        'adocoes' => 3,
        'denuncias' => 0,
        'alertas' => 1,
        'data_registro' => '10/02/2024'
    ],
    [
        'id' => 2,
        'nome' => 'Ricardo Santos',
        'email' => 'ricardo.s@email.com',
        'telefone' => '(11) 98888-7777',
        'status' => 'Notificado',
        'status_classe' => 'bg-warning',
        'adocoes' => 1,
        'denuncias' => 2,
        'alertas' => 3,
        'data_registro' => '05/03/2024'
    ],
    [
        'id' => 3,
        'nome' => 'Maria Souza',
        'email' => 'maria.souza@email.com',
        'telefone' => '(11) 97777-6666',
        'status' => 'Banido',
        'status_classe' => 'bg-danger',
        'adocoes' => 0,
        'denuncias' => 5,
        'alertas' => 0,
        'data_registro' => '20/01/2024'
    ],
    [
        'id' => 4,
        'nome' => 'Carla Pires',
        'email' => 'carla.p@email.com',
        'telefone' => '(11) 96666-5555',
        'status' => 'Ativo',
        'status_classe' => 'bg-success',
        'adocoes' => 5,
        'denuncias' => 0,
        'alertas' => 0,
        'data_registro' => '15/04/2024'
    ]
];
?>

<style>
    .user-table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 25px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .table thead th {
        background: #F9F9F9;
        font-family: 'Poppins', sans-serif;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #828282;
        padding: 15px 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E0E0E0;
        background: white;
        color: #828282;
        transition: 0.2s;
        margin-left: 4px;
    }

    .action-btn:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .btn-ban:hover { background: #EB5757; border-color: #EB5757; }
    .btn-notify:hover { background: #F2994A; border-color: #F2994A; }
    .btn-message:hover { background: #56CCF2; border-color: #56CCF2; }

    .stat-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .status-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Gestão de Usuários 👥</h1>
                <p class="text-muted">Administre perfis, acompanhe condutas e processos de adoção.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex gap-2 justify-content-md-end">
                    <input type="text" id="userSearch" class="form-control" placeholder="Buscar por nome ou email..." style="max-width: 250px; border-radius: 10px;">
                    <button class="btn btn-primary" style="background: var(--primary-green); border: none; border-radius: 10px;">
                        <i data-lucide="filter" style="width: 18px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="user-table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Contato</th>
                            <th class="text-center">Adoções</th>
                            <th class="text-center">Denúncias</th>
                            <th class="text-center">Alertas</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php foreach ($usuariosLista as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold; color: var(--primary-green);">
                                        <?php echo substr($u['nome'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo $u['nome']; ?></div>
                                        <small class="text-muted">ID: #<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="d-block"><?php echo $u['email']; ?></small>
                                <small class="text-muted"><?php echo $u['telefone']; ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-success border"><?php echo $u['adocoes']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light <?php echo $u['denuncias'] > 0 ? 'text-danger border-danger' : 'text-muted'; ?> border">
                                    <?php echo $u['denuncias']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-warning border"><?php echo $u['alertas']; ?></span>
                            </td>
                            <td>
                                <span class="status-badge-dot <?php echo $u['status_classe']; ?>"></span>
                                <small class="fw-bold"><?php echo $u['status']; ?></small>
                            </td>
                            <td class="text-end">
                                <button class="action-btn btn-message" title="Enviar Mensagem" onclick="alert('Abrindo chat com <?php echo $u['nome']; ?>')">
                                    <i data-lucide="message-square" style="width: 14px;"></i>
                                </button>
                                <button class="action-btn btn-notify" title="Notificar Usuário" onclick="alert('Enviando notificação administrativa para <?php echo $u['nome']; ?>')">
                                    <i data-lucide="bell" style="width: 14px;"></i>
                                </button>
                                <button class="action-btn btn-ban" title="Banir Usuário" onclick="confirm('Deseja realmente banir este usuário?')">
                                    <i data-lucide="slash" style="width: 14px;"></i>
                                </button>
                                <button class="action-btn" title="Ver Detalhes/Bloquear" onclick="alert('Visualizando histórico completo de <?php echo $u['nome']; ?>')">
                                    <i data-lucide="more-horizontal" style="width: 14px;"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    const searchInput = document.getElementById('userSearch');
    const tableRows = document.querySelectorAll('#userTableBody tr');

    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
});
</script>