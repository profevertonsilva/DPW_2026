<?php
/**
 * AmigoPet - Conteúdo do Acompanhamento de Denúncias
 * Localização: ~/App/View/acompanhar_content.php
 */

// Simulação de dados do utilizador (Substituir por SELECT * FROM reportes WHERE usuario_id = :id)
$meusReportes = [
    [
        'protocolo' => 'REP-2024-001',
        'tipo' => 'Maus-tratos',
        'assunto' => 'Cachorro acorrentado sem água',
        'data' => '12/05/2024',
        'status' => 'Pendente',
        'status_classe' => 'bg-warning',
        'urgencia' => 'Alta'
    ],
    [
        'protocolo' => 'REP-2024-005',
        'tipo' => 'Animal Perdido',
        'assunto' => 'Gato Siamês desaparecido no Centro',
        'data' => '10/05/2024',
        'status' => 'Em Análise',
        'status_classe' => 'bg-info',
        'urgencia' => 'Média'
    ],
    [
        'protocolo' => 'REP-2024-009',
        'tipo' => 'Animal Abandonado',
        'assunto' => 'Ninhada de gatos abandonada em caixa',
        'data' => '05/05/2024',
        'status' => 'Resolvido',
        'status_classe' => 'bg-success',
        'urgencia' => 'Alta'
    ]
];
?>

<style>
    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
    }

    .report-list-card {
        background: white;
        border-radius: 20px;
        padding: 0;
        overflow: hidden;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }

    .table thead th {
        background-color: #fcfcfc;
        border-bottom: 2px solid #f0f0f0;
        padding: 18px 20px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        color: #ADB5BD;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f8f8f8;
        font-size: 0.9rem;
    }

    .protocol-text {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        color: var(--primary-green);
    }

    .urgency-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .urgency-alta { background-color: #EB5757; }
    .urgency-media { background-color: #F2994A; }
    .urgency-baixa { background-color: #6FCF97; }

    .btn-view-details {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: 0.2s;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Minhas Denúncias 📋</h1>
                <p class="text-muted">Acompanhe aqui o estado e a resolução dos casos que você reportou.</p>
            </div>
            <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
                <a href="reportar.php" class="btn btn-primary shadow-sm" style="background-color: var(--primary-green); border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600;">
                    <i data-lucide="plus-circle" class="me-2" style="width: 18px;"></i> Novo Reporte
                </a>
            </div>
        </div>

        <!-- Resumo Rápido -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card-amigopet p-3 text-center">
                    <h6 class="text-muted small text-uppercase mb-2">Total Enviado</h6>
                    <h3 class="mb-0 protocol-text"><?php echo count($meusReportes); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-amigopet p-3 text-center">
                    <h6 class="text-muted small text-uppercase mb-2">Em Resolução</h6>
                    <h3 class="mb-0 text-warning">1</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-amigopet p-3 text-center">
                    <h6 class="text-muted small text-uppercase mb-2">Casos Resolvidos</h6>
                    <h3 class="mb-0 text-success">1</h3>
                </div>
            </div>
        </div>

        <!-- Lista de Reportes -->
        <div class="report-list-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Protocolo</th>
                            <th>Tipo / Assunto</th>
                            <th>Data</th>
                            <th>Urgência</th>
                            <th>Estado</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meusReportes as $reporte): ?>
                        <tr>
                            <td><span class="protocol-text"><?php echo $reporte['protocolo']; ?></span></td>
                            <td>
                                <div class="fw-bold"><?php echo $reporte['tipo']; ?></div>
                                <small class="text-muted d-block" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo $reporte['assunto']; ?>
                                </small>
                            </td>
                            <td class="text-muted small"><?php echo $reporte['data']; ?></td>
                            <td>
                                <span class="urgency-indicator urgency-<?php echo strtolower($reporte['urgencia']); ?>"></span>
                                <small class="fw-bold"><?php echo $reporte['urgencia']; ?></small>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $reporte['status_classe']; ?>">
                                    <?php echo $reporte['status']; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-light border text-muted btn-view-details">
                                    <i data-lucide="eye" class="me-1" style="width: 14px;"></i> Detalhes
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-4 text-center text-muted small">
            <i data-lucide="info" class="me-1" style="width: 14px; vertical-align: middle;"></i> 
            Clique em "Detalhes" para ver o histórico de atualizações das equipas de campo.
        </p>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }
});
</script>