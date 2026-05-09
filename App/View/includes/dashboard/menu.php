<?php
/**
 * AmigoPet - Sidebar Menu Dinâmico
 * Localização: ~/App/View/Includes/dashboard/menu.php
 */

// Garantir que a sessão está ativa para ler o cargo simulado
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Pega o cargo atual da sessão (definido no simulador da navbar/header)
$role = $_SESSION['sim_user_role'] ?? 'usuario';

// Helper para identificar a página atual e aplicar a classe 'active'
$currentPage = basename($_SERVER['PHP_SELF']);
$isDashboard = ($currentPage == 'dashboard.php');
$isAdocao = ($currentPage == 'adocao.php');
$isReportar = ($currentPage == 'reportar.php');
$isAcompanharDenuncia = ($currentPage == 'acompanhar.php');
$isAcompanharAdocao = ($currentPage == 'acompanhar_adocao.php');
$isPerfil = ($currentPage == 'perfil.php');
?>

<aside class="sidebar-amigopet">
    <div class="sidebar-nav">

        <?php if ($role == 'admin'): ?>
            <!-- MENU: ADMINISTRADOR -->
            <div class="sidebar-heading">Administrador</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="usuarios.php" class="nav-item-amigopet">
                <i data-lucide="users"></i> <span>Gerenciar Usuários</span>
            </a>
            <a href="configuracoes.php" class="nav-item-amigopet">
                <i data-lucide="settings"></i> <span>Configurações</span>
            </a>
            <a href="relatorios.php" class="nav-item-amigopet">
                <i data-lucide="file-text"></i> <span>Relatórios Gerais</span>
            </a>
            <a href="analytics.php" class="nav-item-amigopet">
                <i data-lucide="bar-chart"></i> <span>Analytics</span>
            </a>
            <a href="permissoes.php" class="nav-item-amigopet">
                <i data-lucide="shield-check"></i> <span>Permissões</span>
            </a>

        <?php elseif ($role == 'ong'): ?>
            <!-- MENU: ONG -->
            <div class="sidebar-heading">ONG</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="resgatados.php" class="nav-item-amigopet">
                <i data-lucide="heart-handshake"></i> <span>Gerenciar Resgatados</span>
            </a>
            <a href="coordenar.php" class="nav-item-amigopet">
                <i data-lucide="clipboard-list"></i> <span>Coordenar Adoções</span>
            </a>
            <a href="doacoes.php" class="nav-item-amigopet">
                <i data-lucide="coins"></i> <span>Recursos e Doações</span>
            </a>
            <a href="voluntarios.php" class="nav-item-amigopet">
                <i data-lucide="users-2"></i> <span>Voluntários</span>
            </a>
            <a href="parcerias.php" class="nav-item-amigopet">
                <i data-lucide="briefcase"></i> <span>Parcerias</span>
            </a>

        <?php elseif ($role == 'moderador'): ?>
            <!-- MENU: EQUIPE MODERADORA -->
            <div class="sidebar-heading">Equipe Moderadora</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="usuarios.php" class="nav-item-amigopet">
                <i data-lucide="users"></i> <span>Gerenciar Usuários</span>
            </a>
            <a href="denuncias.php" class="nav-item-amigopet">
                <i data-lucide="alert-triangle"></i> <span>Validar Denúncias</span>
            </a>
            <a href="conteudo.php" class="nav-item-amigopet">
                <i data-lucide="eye"></i> <span>Moderar Conteúdo</span>
            </a>
            <a href="cadastros.php" class="nav-item-amigopet">
                <i data-lucide="user-plus"></i> <span>Aprovar Cadastros</span>
            </a>
            <a href="conflitos.php" class="nav-item-amigopet">
                <i data-lucide="scale"></i> <span>Gerenciar Conflitos</span>
            </a>
            <a href="auditoria.php" class="nav-item-amigopet">
                <i data-lucide="search"></i> <span>Auditoria</span>
            </a>

        <?php elseif ($role == 'campo'): ?>
            <!-- MENU: EQUIPE DE CAMPO -->
            <div class="sidebar-heading">Equipe de Campo</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="chamados.php" class="nav-item-amigopet">
                <i data-lucide="phone-incoming"></i> <span>Receber Chamados</span>
            </a>
            <a href="resgates.php" class="nav-item-amigopet">
                <i data-lucide="map-pin"></i> <span>Status de Resgate</span>
            </a>
            <a href="localizacao.php" class="nav-item-amigopet">
                <i data-lucide="navigation"></i> <span>Registrar Localização</span>
            </a>
            <a href="comunicacao.php" class="nav-item-amigopet">
                <i data-lucide="messages-square"></i> <span>Comunicação Interna</span>
            </a>
            <a href="campo-relatorios.php" class="nav-item-amigopet">
                <i data-lucide="clipboard-edit"></i> <span>Relatórios de Campo</span>
            </a>

        <?php elseif ($role == 'vet'): ?>
            <!-- MENU: VETERINÁRIOS -->
            <div class="sidebar-heading">Veterinários</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="avaliar.php" class="nav-item-amigopet">
                <i data-lucide="stethoscope"></i> <span>Avaliar Casos</span>
            </a>
            <a href="atendimentos.php" class="nav-item-amigopet">
                <i data-lucide="heart-pulse"></i> <span>Registrar Atendimentos</span>
            </a>
            <a href="historico.php" class="nav-item-amigopet">
                <i data-lucide="history"></i> <span>Histórico Médico</span>
            </a>
            <a href="prescricoes.php" class="nav-item-amigopet">
                <i data-lucide="pill"></i> <span>Prescrições</span>
            </a>
            <a href="procedimentos.php" class="nav-item-amigopet">
                <i data-lucide="calendar"></i> <span>Agendar Procedimentos</span>
            </a>

        <?php else: ?>
            <!-- MENU: USUÁRIO PADRÃO -->
            <div class="sidebar-heading">Principal</div>
            <a href="dashboard.php" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="adocao.php" class="nav-item-amigopet <?php echo $isAdocao ? 'active' : ''; ?>">
                <i data-lucide="paw-print"></i> <span>Visualizar Animais</span>
            </a>
            <a href="acompanhar_adocao.php" class="nav-item-amigopet <?php echo $isAcompanharAdocao ? 'active' : ''; ?>">
                <i data-lucide="clipboard-check"></i> <span>Acompanhar adoção</span>
            </a>
            <a href="reportar.php" class="nav-item-amigopet <?php echo $isReportar ? 'active' : ''; ?>">
                <i data-lucide="megaphone"></i> <span>Reportar Casos</span>
            </a>
            <a href="acompanhar.php" class="nav-item-amigopet <?php echo $isAcompanharDenuncia ? 'active' : ''; ?>">
                <i data-lucide="activity"></i> <span>Acompanhar Denúncias</span>
            </a>
            <div class="sidebar-heading">Configurações</div>
            <a href="perfil.php" class="nav-item-amigopet <?php echo $isPerfil ? 'active' : ''; ?>">
                <i data-lucide="user"></i> <span>Perfil</span>
            </a>
            <a href="notificacoes.php" class="nav-item-amigopet">
                <i data-lucide="bell"></i> <span>Notificações</span>
            </a>
        <?php endif; ?>

    </div>
</aside>

<!-- Inicialização de Ícones Lucide -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>