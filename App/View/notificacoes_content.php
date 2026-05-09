<?php
/**
 * AmigoPet - Conteúdo das Notificações
 * Localização: ~/App/View/notificacoes_content.php
 */

// Simulação de notificações (No backend, buscar do banco com filtro de data)
// Datas baseadas em 2026 (Ano atual do sistema)
$notificacoes = [
    [
        'id' => 1,
        'titulo' => 'Entrevista Marcada!',
        'mensagem' => 'Sua entrevista para adotar o Max foi agendada para o dia 15/05.',
        'data' => '2026-05-08 10:30:00',
        'tipo' => 'adocao',
        'lida' => false
    ],
    [
        'id' => 2,
        'titulo' => 'Novo Reporte Próximo',
        'mensagem' => 'Um novo caso de animal abandonado foi reportado a 2km de sua localização.',
        'data' => '2026-05-07 14:20:00',
        'tipo' => 'reporte',
        'lida' => true
    ],
    [
        'id' => 3,
        'titulo' => 'Perfil Atualizado',
        'mensagem' => 'As alterações no seu perfil foram salvas com sucesso.',
        'data' => '2026-04-20 09:00:00',
        'tipo' => 'sistema',
        'lida' => true
    ],
    [
        'id' => 4,
        'titulo' => 'Campanha de Vacinação 2025',
        'mensagem' => 'Recordamos que a campanha de vacinação do ano passado foi um sucesso!',
        'data' => '2025-06-12 08:00:00',
        'tipo' => 'sistema',
        'lida' => true
    ],
    [
        'id' => 5,
        'titulo' => 'Início da Jornada AmigoPet',
        'mensagem' => 'Bem-vindo à nossa plataforma! Sua conta foi criada em 2024.',
        'data' => '2024-01-15 11:00:00',
        'tipo' => 'sistema',
        'lida' => true
    ],
    [
        'id' => 6,
        'titulo' => 'Evento Histórico 2023',
        'mensagem' => 'Relatório de impacto da feira de adoção de 3 anos atrás.',
        'data' => '2023-09-10 16:45:00',
        'tipo' => 'sistema',
        'lida' => true
    ]
];
?>

<style>
    .notif-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #f0f0f0;
        padding: 18px;
        margin-bottom: 12px;
        transition: 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        position: relative;
    }

    .notif-card:hover {
        border-color: var(--primary-green);
        background-color: #fafafa;
    }

    .notif-card.unseen {
        border-left: 4px solid var(--primary-green);
        background-color: rgba(111, 207, 151, 0.02);
    }

    .notif-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-adocao { background: rgba(111, 207, 151, 0.1); color: var(--primary-green); }
    .icon-reporte { background: rgba(242, 153, 74, 0.1); color: var(--secondary-orange); }
    .icon-sistema { background: rgba(86, 204, 242, 0.1); color: var(--info-blue); }

    .notif-time {
        font-size: 0.75rem;
        color: #ADB5BD;
        margin-top: 5px;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid #f0f0f0;
        margin-bottom: 25px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        display: none;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Notificações 🔔</h1>
                <p class="text-muted">Fique por dentro de tudo o que acontece no seu AmigoPet.</p>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="filter-section shadow-sm">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i data-lucide="search" style="width: 18px;"></i>
                        </span>
                        <input type="text" id="notifSearch" class="form-control border-start-0 ps-0" placeholder="Pesquisar por assunto ou mensagem...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="periodFilter" class="form-select">
                        <option value="all">Todo o período</option>
                        <option value="7d">Últimos 7 dias</option>
                        <option value="30d">Último mês</option>
                        <option value="1y">Último ano</option>
                        <option value="3y">Últimos 3 anos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="markAllRead">
                        <small class="fw-bold">Lidas</small>
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Notificações -->
        <div id="notifList">
            <?php foreach ($notificacoes as $n): 
                $icon = 'bell';
                $class = 'icon-sistema';
                if($n['tipo'] == 'adocao') { $icon = 'heart'; $class = 'icon-adocao'; }
                if($n['tipo'] == 'reporte') { $icon = 'alert-triangle'; $class = 'icon-reporte'; }
            ?>
            <div class="notif-card <?php echo $n['lida'] ? '' : 'unseen'; ?>" 
                 data-date="<?php echo $n['data']; ?>"
                 data-text="<?php echo strtolower($n['titulo'] . ' ' . $n['mensagem']); ?>">
                
                <div class="notif-icon <?php echo $class; ?>">
                    <i data-lucide="<?php echo $icon; ?>"></i>
                </div>
                
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1" style="font-weight: 700;"><?php echo $n['titulo']; ?></h6>
                        <?php if(!$n['lida']): ?>
                            <span class="badge bg-success rounded-pill" style="font-size: 0.6rem;">Nova</span>
                        <?php endif; ?>
                    </div>
                    <p class="mb-0 text-muted small"><?php echo $n['mensagem']; ?></p>
                    <div class="notif-time">
                        <i data-lucide="clock" class="me-1" style="width: 12px; height: 12px;"></i>
                        <?php echo date('d/m/Y H:i', strtotime($n['data'])); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Mensagem de lista vazia -->
        <div id="emptyNotif" class="empty-state">
            <i data-lucide="bell-off" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
            <h5 class="text-muted">Nenhuma notificação encontrada.</h5>
            <p class="small text-secondary">Tente mudar os filtros de período ou termo de busca.</p>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    const searchInput = document.getElementById('notifSearch');
    const periodFilter = document.getElementById('periodFilter');
    const cards = document.querySelectorAll('.notif-card');
    const emptyMsg = document.getElementById('emptyNotif');

    function filterNotifications() {
        const searchTerm = searchInput.value.toLowerCase();
        const period = periodFilter.value;
        const now = new Date();
        let visibleCount = 0;

        cards.forEach(card => {
            const cardDate = new Date(card.getAttribute('data-date'));
            const cardText = card.getAttribute('data-text');
            
            // Lógica de tempo
            const diffTime = Math.abs(now - cardDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const diffYears = now.getFullYear() - cardDate.getFullYear();

            let timeMatch = true;
            if (period === '7d') timeMatch = diffDays <= 7;
            else if (period === '30d') timeMatch = diffDays <= 30;
            else if (period === '1y') timeMatch = diffYears <= 1;
            else if (period === '3y') timeMatch = diffYears <= 3;

            // Lógica de texto
            const textMatch = cardText.includes(searchTerm);

            if (timeMatch && textMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterNotifications);
    periodFilter.addEventListener('change', filterNotifications);

    document.getElementById('markAllRead').onclick = () => {
        alert('Todas as notificações marcadas como lidas.');
        cards.forEach(c => c.classList.remove('unseen'));
        document.querySelectorAll('.badge.bg-success').forEach(b => b.remove());
    };
});
</script>