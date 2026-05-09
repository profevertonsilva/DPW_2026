<?php
/**
 * AmigoPet - Navbar com Simulador de Cargos
 * Localização: ~/App/View/Includes/dashboard/navbar.php
 */

// Inicia a sessão para o simulador funcionar (Se não houver cargo, padrão é 'usuario')
if (session_status() === PHP_SESSION_NONE) { session_start(); }

/** * LÓGICA DE SIMULAÇÃO 
 * Para o Backend: Substituir estas linhas pela verificação real de autenticação/sessão do usuário.
 * Ex: $userRole = $_SESSION['user']['role'];
 */
if (isset($_GET['sim_role'])) {
    $_SESSION['sim_user_role'] = $_GET['sim_role'];
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // Limpa a URL
    exit;
}

$userRole = $_SESSION['sim_user_role'] ?? 'usuario';
?>

<nav class="navbar navbar-expand-lg navbar-amigopet">
    <div class="container-fluid">
        <!-- Logo e Nome -->
        <a class="navbar-brand" href="dashboard.php">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 10C13.1046 10 14 9.10457 14 8C14 6.89543 13.1046 6 12 6C10.8954 6 10 6.89543 10 8C10 9.10457 10.8954 10 12 10Z" fill="#F2994A"/>
                <path d="M7 9C8.10457 9 9 8.10457 9 7C9 5.89543 8.10457 5 7 5C5.89543 5 5 5.89543 5 7C5 8.10457 5.89543 9 7 9Z" fill="#6FCF97"/>
                <path d="M17 9C18.1046 9 19 8.10457 19 7C19 5.89543 18.1046 5 17 5C15.8954 5 15 5.89543 15 7C15 8.10457 15.8954 9 17 9Z" fill="#6FCF97"/>
                <path d="M12 21C15.3137 21 18 18.3137 18 15C18 11.6863 15.3137 9 12 9C8.68629 9 6 11.6863 6 15C6 18.3137 8.68629 21 12 21Z" fill="#F2994A"/>
            </svg>
            <span><span class="brand-text-amigo">Amigo</span><span class="brand-text-pet">Pet</span></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-inicio active" href="dashboard.php">Início</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- SIMULADOR DE CARGO (Remover no deploy final) -->
                <div class="d-flex align-items-center bg-light px-2 py-1 rounded-3 border">
                    <small class="text-muted me-2" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Simular:</small>
                    <select class="form-select form-select-sm border-0 bg-transparent fw-bold" onchange="location.href='?sim_role='+this.value" style="font-size: 0.8rem; cursor: pointer; color: var(--secondary-orange);">
                        <option value="usuario" <?php echo $userRole == 'usuario' ? 'selected' : ''; ?>>Usuário</option>
                        <option value="admin" <?php echo $userRole == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="ong" <?php echo $userRole == 'ong' ? 'selected' : ''; ?>>ONG</option>
                        <option value="moderador" <?php echo $userRole == 'moderador' ? 'selected' : ''; ?>>Equipe Moderadora</option>
                        <option value="campo" <?php echo $userRole == 'campo' ? 'selected' : ''; ?>>Equipe de Campo</option>
                        <option value="vet" <?php echo $userRole == 'vet' ? 'selected' : ''; ?>>Veterinário</option>
                    </select>
                </div>

                <button class="notification-btn" title="Notificações">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge"></span>
                </button>
                
                <div class="d-flex align-items-center">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--primary-green); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                        <?php echo strtoupper(substr($userRole, 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>