<?php
/**
 * AmigoPet - Sidebar Menu Dinâmico
 * Localização: ~/App/View/Includes/dashboard/menu.php
 */

// Garantir que a sessão está ativa para ler o cargo do usuário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pega o tipo de usuário real da sessão
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';

// Mapeia o tipo_usuario do banco para os roles do menu
$roleMap = [
    'administrador' => 'admin',
    'ong' => 'ong',
    'veterinario' => 'vet',
    'rastreador' => 'campo',
    'adotante' => 'usuario'
];

$role = $roleMap[$tipoUsuario] ?? 'usuario';

// Helper para identificar a página atual e aplicar a classe 'active'
$currentPage = basename($_SERVER['PHP_SELF']);
// Normaliza o path atual (suporta '/dashboard/animal/editar/2' e '/App/View/dashboard.php')
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$baseName = basename($currentPath); // ex: 'dashboard.php' ou 'dashboard'
$normalized = preg_replace('/\.php$/', '', $baseName);

$isDashboard = ($currentPage === 'dashboard.php' || $normalized === 'dashboard' || strpos($currentPath, 'dashboard/') === 0);
$isAdocao = ($currentPage === 'adocao.php' || $normalized === 'adocao');
$isReportar = ($currentPage === 'reportar.php' || $normalized === 'reportar');
$isAcompanharDenuncia = ($currentPage === 'acompanhar.php' || $normalized === 'acompanhar');
$isAcompanharAdocao = ($currentPage === 'acompanhar_adocao.php' || $normalized === 'acompanhar_adocao');
$isPerfil = ($currentPage === 'perfil.php' || $normalized === 'perfil');
?>

<style>
    /* ==========================================================================
       Correções Locais da Sidebar (Garante o funcionamento do Botão e Animações)
       ========================================================================== */
    .sidebar-amigopet {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        z-index: 1020 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .sidebar-nav {
        overflow-x: hidden; /* Corta apenas os textos internamente ao encolher */
        height: 100%;
    }

    /* Estilo do Botão Flutuante (Hambúrguer |||) */
    .sidebar-toggle-btn-inner {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: -36px; /* Fica 36px para fora da barra */
        width: 36px;
        height: 50px;
        background-color: #ffffff;
        border: 1px solid rgba(0,0,0,0.08);
        border-left: none;
        border-radius: 0 12px 12px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 4px 2px 10px rgba(0,0,0,0.05);
        color: var(--primary-green);
        z-index: 1050; /* O botão continua ACIMA da navbar e da sidebar */
        transition: all 0.2s ease;
    }

    .sidebar-toggle-btn-inner:hover {
        background-color: #fcfcfc;
        color: var(--secondary-orange);
        width: 42px; /* Pequeno efeito de esticar ao passar o rato */
        right: -42px;
    }

    /* ==========================================================================
       Lógica do Mini Menu (Aplica-se independentemente do style.css externo)
       ========================================================================== */
    
    /* Computador: Alterna entre Menu Completo (260px) e Mini Menu (80px) */
    @media (min-width: 992px) {
        body.sidebar-toggled .sidebar-amigopet {
            width: 80px !important;
        }
        body.sidebar-toggled .main-content {
            margin-left: 80px !important;
        }
        body.sidebar-toggled .sidebar-heading,
        body.sidebar-toggled .nav-item-amigopet span {
            display: none !important;
        }
        body.sidebar-toggled .nav-item-amigopet {
            justify-content: center;
            padding: 0.8rem 0;
        }
    }

    /* Telemóvel: Alterna entre Fora do Ecrã e Menu Completo */
    @media (max-width: 991px) {
        .sidebar-amigopet {
            left: -260px !important;
            width: 260px !important;
        }
        /* No telemóvel, o botão fica visível à direita da barra escondida */
        body.sidebar-toggled .sidebar-amigopet {
            left: 0 !important;
            box-shadow: 5px 0 25px rgba(0,0,0,0.15);
        }
    }
</style>

<aside class="sidebar-amigopet">
    
    <!-- Botão de Abas para Esconder/Mostrar Menu (Ícone fixo de menu) -->
    <div id="sidebarToggleMenu" class="sidebar-toggle-btn-inner" title="Recolher/Mostrar Menu">
        <i data-lucide="menu"></i>
    </div>

    <div class="sidebar-nav">

        <?php if ($role == 'admin'): ?>
            <!-- MENU: ADMINISTRADOR -->
            <div class="sidebar-heading">Administrador</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/usuarios" class="nav-item-amigopet">
                <i data-lucide="users"></i> <span>Gerenciar Usuários</span>
            </a>
            <a href="/App/View/configuracoes.php" class="nav-item-amigopet">
                <i data-lucide="settings"></i> <span>Configurações</span>
            </a>
            <a href="/App/View/relatorios.php" class="nav-item-amigopet">
                <i data-lucide="file-text"></i> <span>Relatórios Gerais</span>
            </a>
            <!--<a href="analytics.php" class="nav-item-amigopet">
                <i data-lucide="bar-chart"></i> <span>Analytics</span>
            </a> -->
            <a href="/App/View/verificar_denuncias.php" class="nav-item-amigopet">
                <i data-lucide="shield-check"></i> <span>Verificar Denuncias</span>
            </a>
            <a href="/App/View/manage_animais.php" class="nav-item-amigopet">
                <i class="fa-solid fa-paw"></i> <span>Gerenciar Animais</span>
            </a>
            <a href="/ongs" class="nav-item-amigopet">
                <i data-lucide="building-2"></i> <span>Gerenciar ONGs</span>
            </a>
            <a href="/clinicas" class="nav-item-amigopet">
                <i data-lucide="hospital"></i> <span>Gerenciar Clínicas</span>
            </a>

        <?php elseif ($role == 'ong'): ?>
            <!-- MENU: ONG -->
            <div class="sidebar-heading">ONG</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/App/View/manage_animais.php" class="nav-item-amigopet">
                <i class="fa-solid fa-paw"></i> <span>Gerenciar Animais</span>
            </a>
            <a href="/coordenar-adocoes" class="nav-item-amigopet">
                <i data-lucide="clipboard-list"></i> <span>Coordenar Adoções</span>
            </a>
            <a href="/doacoes" class="nav-item-amigopet">
                <i data-lucide="coins"></i> <span>Recursos e Doações</span>
            </a>
            <a href="/voluntarios" class="nav-item-amigopet">
                <i data-lucide="users-2"></i> <span>Voluntários</span>
            </a>
            <a href="/enviar-caso-veterinario" class="nav-item-amigopet">
                <i data-lucide="stethoscope"></i> <span>Enviar Caso Veterinário</span>
            </a>
            <!-- <a href="parcerias.php" class="nav-item-amigopet">
                <i data-lucide="briefcase"></i> <span>Parcerias</span>
            </a> -->

        <?php elseif ($role == 'moderador'): ?>
            <!-- MENU: EQUIPE MODERADORA -->
            <div class="sidebar-heading">Equipe Moderadora</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/usuarios" class="nav-item-amigopet">
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
            <a href="/App/View/manage_animais.php" class="nav-item-amigopet">
                <i class="fa-solid fa-paw"></i> <span>Gerenciar Animais</span>
            </a>

        <?php elseif ($role == 'campo'): ?>
            <!-- MENU: EQUIPE DE CAMPO -->
            <div class="sidebar-heading">Equipe de Campo</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/chamados" class="nav-item-amigopet">
                <i data-lucide="phone-incoming"></i> <span>Receber Chamados</span>
            </a>
            <a href="/resgates" class="nav-item-amigopet">
                <i data-lucide="map-pin"></i> <span>Status de Resgate</span>
            </a>
            <!-- <a href="localizacao.php" class="nav-item-amigopet">
                <i data-lucide="navigation"></i> <span>Registrar Localização</span>
            </a> -->
            <a href="/comunicacao" class="nav-item-amigopet">
                <i data-lucide="messages-square"></i> <span>Comunicação Interna</span>
            </a>
            <!-- <a href="campo-relatorios.php" class="nav-item-amigopet">
                <i data-lucide="clipboard-edit"></i> <span>Relatórios de Campo</span>
            </a> -->
            <a href="/App/View/manage_animais.php" class="nav-item-amigopet">
                <i class="fa-solid fa-paw"></i> <span>Gerenciar Animais</span>
            </a>

        <?php elseif ($role == 'vet'): ?>
            <!-- MENU: VETERINÁRIOS -->
            <div class="sidebar-heading">Veterinários</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/casos-veterinario" class="nav-item-amigopet">
                <i data-lucide="stethoscope"></i> <span>Avaliar Casos</span>
            </a>
            <a href="/historico-medico" class="nav-item-amigopet">
                <i data-lucide="history"></i> <span>Histórico Médico</span>
            </a>
            
            <a href="/procedimentos" class="nav-item-amigopet">
                <i data-lucide="calendar"></i> <span>Agendar Procedimentos</span>
            </a>
            <a href="/App/View/manage_animais.php" class="nav-item-amigopet">
                <i class="fa-solid fa-paw"></i> <span>Gerenciar Animais</span>
            </a>

        <?php else: ?>
            <!-- MENU: USUÁRIO PADRÃO -->
            <div class="sidebar-heading">Principal</div>
            <a href="/dashboard" class="nav-item-amigopet <?php echo $isDashboard ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> <span>Painel inicial</span>
            </a>
            <a href="/App/View/adocao.php" class="nav-item-amigopet <?php echo $isAdocao ? 'active' : ''; ?>">
                <i data-lucide="paw-print"></i> <span>Visualizar Animais</span>
            </a>
            <a href="/App/View/acompanhar_adocao.php" class="nav-item-amigopet <?php echo $isAcompanharAdocao ? 'active' : ''; ?>">
                <i data-lucide="clipboard-check"></i> <span>Acompanhar adoção</span>
            </a>
            <a href="/App/View/reportar.php" class="nav-item-amigopet <?php echo $isReportar ? 'active' : ''; ?>">
                <i data-lucide="megaphone"></i> <span>Reportar Casos</span>
            </a>
            <a href="/App/View/acompanhar.php" class="nav-item-amigopet <?php echo $isAcompanharDenuncia ? 'active' : ''; ?>">
                <i data-lucide="activity"></i> <span>Acompanhar Denúncias</span>
            </a>
            <div class="sidebar-heading">Configurações</div>
            <a href="/perfil" class="nav-item-amigopet <?php echo $isPerfil ? 'active' : ''; ?>">
                <i data-lucide="user"></i> <span>Perfil</span>
            </a>
            <a href="/App/View/notificacoes.php" class="nav-item-amigopet">
                <i data-lucide="bell"></i> <span>Notificações</span>
            </a>
        <?php endif; ?>

    </div>
</aside>

<!-- Inicialização de Ícones Lucide e Lógica do Toggle -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleMenuBtn = document.getElementById('sidebarToggleMenu');

    // Inicializa o estado com base na preferência salva no navegador do usuário
    const savedState = localStorage.getItem('sidebar-collapsed') === 'true';
    if (savedState) {
        document.body.classList.add('sidebar-toggled');
    }

    // Evento de clique para ocultar/mostrar a barra lateral
    if (toggleMenuBtn) {
        toggleMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Alterna a classe no body
            document.body.classList.toggle('sidebar-toggled');
            
            // Salva a nova preferência
            const isCurrentlyCollapsed = document.body.classList.contains('sidebar-toggled');
            localStorage.setItem('sidebar-collapsed', isCurrentlyCollapsed);
        });
    }

    // Inicialização geral dos ícones Lucide do Menu
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>