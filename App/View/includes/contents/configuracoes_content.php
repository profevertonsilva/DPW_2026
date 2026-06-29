<?php
/**
 * AmigoPet - Configurações do Sistema
 * Localização: ~/App/View/includes/contents/configuracoes_content.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega animais disponíveis para associação (substitui uso direto do mock)
use App\DAO\AnimalDAO;
$animalDao = new AnimalDAO();
$animalsForLink = $animalDao->listar();

// Converter modelos para arrays para compatibilidade com o código existente
$animaisSimulados = [];
foreach ($animalsForLink as $animalModel) {
    $animaisSimulados[] = [
        'id' => $animalModel->__get('id'),
        'nome' => $animalModel->__get('nome'),
        'especie' => $animalModel->__get('especie_nome'),
        'raca' => $animalModel->__get('racas'),
        'imagem' => $animalModel->__get('foto')
    ];
}

$mockFile = __DIR__ . '/../../../Data/configuracoes_mock.php';

// Inicialização padrão
$defaults = [
    'dashboard_titulo' => 'Bem-vindo ao Painel', 
    'dashboard_subtitulo' => 'Gestão de Animais e Adoções'
];

// Garantir que os arrays existam antes de carregar o mock
$configGeralMock = [];
$publicacoesMock = [];
$carrosselMock = [];
$logsMock = [];

// Carrega arquivo físico do mock
if (file_exists($mockFile)) {
    include $mockFile;
}

// Carrega mock de auditoria (logs específicos para a aba de auditoria)
$auditFile = __DIR__ . '/../../../Data/auditoria_mock.php';
if (file_exists($auditFile)) {
    include $auditFile; // defines $logsMock
}

// Sincroniza com a Sessão para mostrar o carrossel e as publicações atualizados imediatamente
if (isset($_SESSION['carrossel'])) {
    $carrosselMock = $_SESSION['carrossel'];
}
if (isset($_SESSION['publicacoes'])) {
    $publicacoesMock = $_SESSION['publicacoes'];
}

// Sincroniza com a Sessão para persistência imediata no Dashboard
if (!isset($_SESSION['config'])) {
    $_SESSION['config'] = [
        'titulo' => $configGeralMock['dashboard_titulo'] ?? $defaults['dashboard_titulo'],
        'subtitulo' => $configGeralMock['dashboard_subtitulo'] ?? $defaults['dashboard_subtitulo']
    ];
} else {
    $configGeralMock['dashboard_titulo'] = $_SESSION['config']['titulo'] ?? $configGeralMock['dashboard_titulo'] ?? $defaults['dashboard_titulo'];
    $configGeralMock['dashboard_subtitulo'] = $_SESSION['config']['subtitulo'] ?? $configGeralMock['dashboard_subtitulo'] ?? $defaults['dashboard_subtitulo'];
}

$mensagemSistema = '';
$tipoMensagem = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'update_dashboard':
            // Atualiza na Sessão
            $_SESSION['config']['titulo'] = $_POST['dashboard_titulo'];
            $_SESSION['config']['subtitulo'] = $_POST['dashboard_subtitulo'];
            
            // Atualiza o array mock para persistência no arquivo (mantendo seu padrão)
            $configGeralMock['dashboard_titulo'] = $_POST['dashboard_titulo'];
            $configGeralMock['dashboard_subtitulo'] = $_POST['dashboard_subtitulo'];
            $mensagemSistema = "Dashboard atualizado com sucesso!";
            break;

        case 'create_news':
            array_unshift($publicacoesMock, [
                'id' => time(),
                'titulo' => $_POST['titulo'],
                'tipo' => $_POST['tipo'],
                'autor' => 'Admin',
                'data' => date('d/m/Y'),
                'status' => 'Ativo',
                'descricao' => $_POST['descricao'],
                'animal_id' => !empty($_POST['link_animal']) ? $_POST['animal_id'] : null,
                'animal_nome' => !empty($_POST['link_animal']) ? $_POST['animal_nome'] : null,
                'animal_especie' => !empty($_POST['link_animal']) ? $_POST['animal_especie'] : null,
                'animal_imagem' => !empty($_POST['link_animal']) ? $_POST['animal_imagem'] : null,
            ]);
            $_SESSION['publicacoes'] = $publicacoesMock;
            $mensagemSistema = "Notícia publicada!";
            break;

        case 'edit_pub':
            foreach ($publicacoesMock as &$p) {
                if ($p['id'] == $_POST['pub_id']) {
                    $p['titulo'] = $_POST['pub_titulo'];
                    $p['tipo'] = $_POST['pub_tipo'];
                    $p['status'] = $_POST['pub_status'];
                    $p['descricao'] = $_POST['pub_descricao'];
                    if (!empty($_POST['link_animal'])) {
                        $p['animal_id'] = $_POST['animal_id'];
                        $p['animal_nome'] = $_POST['animal_nome'];
                        $p['animal_especie'] = $_POST['animal_especie'];
                        $p['animal_imagem'] = $_POST['animal_imagem'];
                    } else {
                        $p['animal_id'] = null;
                        $p['animal_nome'] = null;
                        $p['animal_especie'] = null;
                        $p['animal_imagem'] = null;
                    }
                }
            }
            unset($p);
            $_SESSION['publicacoes'] = $publicacoesMock;
            $mensagemSistema = "Publicação atualizada!";
            break;

        case 'create_carousel_item':
            // Validação de limite (máximo 20 animais)
            if (count($carrosselMock) >= 20) {
                $mensagemSistema = "Limite de 20 animais atingido no carrossel!";
                $tipoMensagem = 'warning';
            } else {
                // Validação para evitar duplicatas
                $animal_id = $_POST['animal_id'] ?? null;
                $jaExiste = false;
                
                if ($animal_id) {
                    foreach ($carrosselMock as $item) {
                        if (($item['animal_id'] ?? null) == $animal_id) {
                            $jaExiste = true;
                            break;
                        }
                    }
                }
                
                if ($jaExiste) {
                    $mensagemSistema = "Este animal já está no carrossel!";
                    $tipoMensagem = 'warning';
                } else {
                    $carrosselMock[] = [
                        'id' => time(),
                        'animal_id' => $animal_id,
                        'nome' => $_POST['nome'],
                        'especie' => $_POST['especie'],
                        'imagem' => $_POST['imagem_url']
                    ];
                    $_SESSION['carrossel'] = $carrosselMock;
                    $mensagemSistema = "Animal adicionado ao carrossel!";
                }
            }
            break;

        case 'remove_carousel':
            $carrosselMock = array_values(array_filter($carrosselMock, fn($i) => $i['id'] != $_POST['carousel_id']));
            $_SESSION['carrossel'] = $carrosselMock;
            $mensagemSistema = "Animal removido.";
            break;

        case 'order_carousel':
            $id = (int)$_POST['carousel_id'];
            foreach ($carrosselMock as $index => $item) {
                if ($item['id'] == $id) {
                    if ($_POST['direction'] === 'up' && $index > 0) {
                        [$carrosselMock[$index-1], $carrosselMock[$index]] = [$carrosselMock[$index], $carrosselMock[$index-1]];
                    } elseif ($_POST['direction'] === 'down' && $index < count($carrosselMock) - 1) {
                        [$carrosselMock[$index+1], $carrosselMock[$index]] = [$carrosselMock[$index], $carrosselMock[$index+1]];
                    }
                    break;
                }
            }
            $_SESSION['carrossel'] = $carrosselMock;
            break;
    }

    // Persistência em arquivo
    saveConfiguracoesMock($mockFile, $configGeralMock, $publicacoesMock, $carrosselMock, $logsMock);

    // Redireciona após o POST para evitar que o navegador volte
    // ao estado antigo do formulário quando o usuário navega entre páginas.
    if (!headers_sent()) {
        header('Location: /configuracoes');
        exit;
    }

    echo '<script>window.location.href = "/configuracoes";</script>';
    exit;
}

/**
 * Salva o mock de configurações em disco.
 */
function saveConfiguracoesMock(string $file, array $configGeralMock, array $publicacoesMock, array $carrosselMock, array $logsMock): void
{
    $content = "<?php\n" .
               "/**\n" .
               " * AmigoPet - Mock de Banco de Dados das Configurações\n" .
               " */\n\n" .
               "// Configurações Globais do Sistema\n" .
               "\$configGeralMock = " . var_export($configGeralMock, true) . ";\n\n" .
               "// Mock de Publicações e Notícias\n" .
               "\$publicacoesMock = " . var_export($publicacoesMock, true) . ";\n\n" .
               "// Mock do Carrossel de Animais\n" .
               "\$carrosselMock = " . var_export($carrosselMock, true) . ";\n\n" .
               "// Mock de Logs de Auditoria\n" .
               "\$logsMock = " . var_export($logsMock, true) . ";\n?>";

    file_put_contents($file, $content);
}
?>

<style>
    .config-tabs {
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 25px;
    }

    .config-tabs .nav-link {
        color: #828282;
        font-weight: 600;
        border: none;
        padding: 12px 20px;
        position: relative;
        transition: all 0.3s ease;
    }

    .config-tabs .nav-link:hover {
        color: var(--primary-green);
    }

    .config-tabs .nav-link.active {
        color: var(--primary-green);
        background: transparent;
    }

    .config-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary-green);
        border-radius: 3px 3px 0 0;
    }

    .form-label {
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4F4F4F;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid #E0E0E0;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(111, 207, 151, 0.25);
    }

    .btn-save {
        background-color: var(--primary-green);
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        transition: 0.2s;
    }

    .btn-save:hover {
        background-color: #5bbd86;
        transform: translateY(-2px);
        color: white;
    }

    .rich-editor-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-bottom: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .rich-editor-toolbar button,
    .rich-editor-toolbar select {
        border: 1px solid #E0E0E0;
        background: #ffffff;
        color: #343a40;
        padding: 0.55rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .rich-editor-toolbar button:hover,
    .rich-editor-toolbar select:hover {
        border-color: var(--primary-green);
        background-color: #f8fff6;
    }

    .rich-editor-bottom-text {
        font-size: 0.80rem;
        color: #6c757d;
        margin-top: 0.35rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid #E0E0E0;
    }

    .table-logs td {
        font-size: 0.85rem;
        vertical-align: middle;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Configurações do Sistema ⚙️</h1>
                <p class="text-muted">Gerencie o conteúdo do painel, envie notificações e monitore atividades.</p>
            </div>
        </div>

        <?php if (!empty($mensagemSistema)): ?>
        <div class="alert alert-<?php echo $tipoMensagem; ?> alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 500;">
            <?php echo $mensagemSistema; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card-amigopet p-4">
            <!-- Abas de Navegação -->
            <ul class="nav nav-tabs config-tabs" id="configTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-geral" type="button">
                        <i data-lucide="layout-dashboard" class="me-1" style="width: 18px;"></i> Painel & Notícias
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notificacoes" type="button">
                        <i data-lucide="bell-ring" class="me-1" style="width: 18px;"></i> Enviar Notificações
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-publicacoes" type="button">
                        <i data-lucide="layers" class="me-1" style="width: 18px;"></i> Gerenciar Publicações
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-auditoria" type="button">
                        <i data-lucide="shield" class="me-1" style="width: 18px;"></i> LOG de Auditoria
                    </button>
                </li>
            </ul>

            <!-- Conteúdo das Abas -->
            <div class="tab-content" id="configTabsContent">
                
                <!-- ABA 1: Painel e Notícias -->
                <div class="tab-pane fade show active" id="tab-geral">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-4 bg-light h-100">
                                <h6 class="mb-3 fw-bold"><i data-lucide="edit-3" class="me-2 text-primary" style="width: 18px;"></i>Card Inicial do Dashboard</h6>
                                <!-- FORMULÁRIO: Atualizar Dashboard -->
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_dashboard">
                                    <div class="mb-3">
                                        <label class="form-label">Título de Boas-vindas</label>
                                        <input type="text" name="dashboard_titulo" class="form-control" value="<?php echo htmlspecialchars($configGeralMock['dashboard_titulo']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Subtítulo / Mensagem</label>
                                        <input type="text" name="dashboard_subtitulo" class="form-control" value="<?php echo htmlspecialchars($configGeralMock['dashboard_subtitulo']); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-save w-100">Atualizar Card</button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-4 h-100">
                                <h6 class="mb-3 fw-bold"><i data-lucide="newspaper" class="me-2 text-success" style="width: 18px;"></i>Criar Nova Notícia</h6>
                                <!-- FORMULÁRIO: Criar Notícia -->
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="create_news">
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tag / Categoria</label>
                                            <select name="tipo" class="form-select" required>
                                                <option value="Novidade">Novidade</option>
                                                <option value="Dica">Dica</option>
                                                <option value="Evento">Evento</option>
                                                <option value="Alerta">Alerta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Título da Notícia</label>
                                            <input type="text" name="titulo" class="form-control" placeholder="Ex: Campanha de Inverno" required>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="linkAnimalCheckbox" name="link_animal" value="1" onchange="toggleAnimalLink('create')">
                                        <label class="form-check-label" for="linkAnimalCheckbox">Linkar com animal</label>
                                    </div>
                                    <div class="mb-3" id="animalLinkGroup-create" style="display:none;">
                                        <label class="form-label">Animal vinculado</label>
                                        <select name="animal_id" id="animalSelectCreate" class="form-select" onchange="updateLinkedAnimalPreview('create')">
                                            <option value="">Selecione um animal...</option>
                                            <?php foreach ($animaisSimulados as $animal): ?>
                                                <option value="<?= $animal['id'] ?>"
                                                        data-nome="<?= htmlspecialchars($animal['nome'], ENT_QUOTES) ?>"
                                                        data-especie="<?= htmlspecialchars($animal['especie'], ENT_QUOTES) ?>"
                                                        data-imagem="<?= htmlspecialchars($animal['imagem'], ENT_QUOTES) ?>">
                                                    <?= htmlspecialchars($animal['nome']) ?> - <?= htmlspecialchars($animal['raca']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="animal_nome" id="animalNomeCreate">
                                        <input type="hidden" name="animal_especie" id="animalEspecieCreate">
                                        <input type="hidden" name="animal_imagem" id="animalImagemCreate">
                                        <div class="card mt-3 p-3" id="animalLinkPreview-create" style="display:none;">
                                            <div class="d-flex align-items-center gap-3">
                                                <img id="animalLinkImageCreate" src="" alt="Animal" style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px;">
                                                <div>
                                                    <div id="animalLinkNameCreate" class="fw-bold"></div>
                                                    <div id="animalLinkSpeciesCreate" class="text-muted"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notícia</label>
                                        <div class="rich-editor-toolbar">
                                            <button type="button" onclick="applyFormatting('newsContent', 'title')">Título</button>
                                            <button type="button" onclick="applyFormatting('newsContent', 'subtitle')">Subtítulo</button>
                                            <button type="button" onclick="applyFormatting('newsContent', 'bold')">Negrito</button>
                                            <button type="button" onclick="applyFormatting('newsContent', 'italic')">Itálico</button>
                                            <select onchange="applyFormatting('newsContent', 'color', this.value)">
                                                <option value="">Cor do Texto</option>
                                                <option value="#1F8A70">Verde</option>
                                                <option value="#0A58CA">Azul</option>
                                                <option value="#D63384">Roxo</option>
                                                <option value="#FD7E14">Laranja</option>
                                            </select>
                                        </div>
                                        <textarea name="descricao" id="newsContent" class="form-control" rows="4" maxlength="800" placeholder="Escreva a notícia inteira aqui..." required oninput="updateCharCount('newsContent','newsCharCount',800)"></textarea>
                                        <div class="rich-editor-bottom-text" id="newsCharCount">0 / 800 caracteres</div>
                                    </div>
                                    <button type="submit" class="btn btn-save w-100" style="background-color: var(--secondary-orange);">Publicar Notícia</button>
                                </form>
                            </div>
                        </div>

                        <!-- Gerenciamento de Carrossel -->
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="mb-3 fw-bold">Gerenciamento do Carrossel</h6>
                                    <button type="button" class="btn btn-sm btn-save" data-bs-toggle="modal" data-bs-target="#addCarouselModal">
                                        <i data-lucide="plus-circle" class="me-1" style="width:16px;"></i> Adicionar Animal
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Animal</th>
                                                    <th class="text-center" style="width: 150px;">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($carrosselMock as $item): ?>
                                                <tr>
                                                    <td class="d-flex align-items-center">
                                                        <img src="<?php echo $item['imagem']; ?>" class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/50'">
                                                        <div>
                                                            <div class="fw-bold"><?php echo $item['nome']; ?></div>
                                                            <small class="text-muted"><?php echo $item['especie']; ?></small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="order_carousel">
                                                            <input type="hidden" name="carousel_id" value="<?php echo $item['id']; ?>">
                                                            <button name="direction" value="up" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-up">^</i></button>
                                                            <button name="direction" value="down" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-down">v</i></button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="remove_carousel">
                                                            <input type="hidden" name="carousel_id" value="<?php echo $item['id']; ?>"><hr>
                                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash">Remover</i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: Enviar Notificações -->
                <div class="tab-pane fade" id="tab-notificacoes">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form onsubmit="event.preventDefault(); alert('Notificação(ões) enviada(s) à fila com sucesso!'); this.reset(); toggleDestinatario();">
                                <div class="mb-3">
                                    <label class="form-label">Destinatário(s)</label>
                                    <select class="form-select" id="tipoDestinatario" onchange="toggleDestinatario()" required>
                                        <option value="todos">Todos os Usuários Ativos</option>
                                        <option value="ongs">Todas as ONGs</option>
                                        <option value="especifico">Usuário Específico</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="campoUsuarioEspecifico" style="display: none;">
                                    <label class="form-label">ID ou E-mail do Usuário</label>
                                    <input type="text" class="form-control" placeholder="Ex: joao@email.com ou ID 45">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Notificação</label>
                                    <select class="form-select" required>
                                        <option value="info">Informação (Ícone Azul)</option>
                                        <option value="alerta">Alerta / Importante (Ícone Laranja)</option>
                                        <option value="sucesso">Sucesso / Conquista (Ícone Verde)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Título da Notificação</label>
                                    <input type="text" class="form-control" placeholder="Título curto e direto" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Mensagem Completa</label>
                                    <textarea class="form-control" rows="3" placeholder="Escreva a mensagem que o usuário receberá..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-save w-100">
                                    <i data-lucide="send" class="me-2" style="width: 18px;"></i> Disparar Notificação
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ABA 3: Gerenciar Publicações -->
                <div class="tab-pane fade" id="tab-publicacoes">
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" class="form-control w-25" placeholder="Buscar publicação...">
                        <button class="btn btn-outline-secondary"><i data-lucide="filter" style="width:16px;"></i> Filtros</button>
                    </div>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover table-logs mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Autor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publicacoesMock as $pub): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $pub['id']; ?></td>
                                    <td><?php echo $pub['titulo']; ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $pub['tipo']; ?></span></td>
                                    <td><?php echo $pub['autor']; ?></td>
                                    <td><?php echo $pub['data']; ?></td>
                                    <td>
                                        <?php if ($pub['status'] == 'Ativo'): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light border" title="Visualizar" data-bs-toggle="modal" data-bs-target="#viewPubModal"
                                                data-titulo="<?php echo htmlspecialchars($pub['titulo'], ENT_QUOTES); ?>"
                                                data-tipo="<?php echo htmlspecialchars($pub['tipo'], ENT_QUOTES); ?>"
                                                data-autor="<?php echo htmlspecialchars($pub['autor'], ENT_QUOTES); ?>"
                                                data-data="<?php echo htmlspecialchars($pub['data'], ENT_QUOTES); ?>"
                                                data-status="<?php echo htmlspecialchars($pub['status'], ENT_QUOTES); ?>"
                                                data-description="<?php echo htmlspecialchars($pub['descricao'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-id="<?php echo htmlspecialchars($pub['animal_id'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-nome="<?php echo htmlspecialchars($pub['animal_nome'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-especie="<?php echo htmlspecialchars($pub['animal_especie'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-imagem="<?php echo htmlspecialchars($pub['animal_imagem'] ?? '', ENT_QUOTES); ?>"
                                                onclick="carregarVisualizacaoPublicacao(this)">
                                            <i data-lucide="eye" style="width: 14px;"></i>
                                        </button>
                                        <!-- Botão de Editar que abre o modal, passando os dados incluindo o ID -->
                                        <button class="btn btn-sm btn-light border text-primary" title="Editar Publicação" data-bs-toggle="modal" data-bs-target="#editPubModal"
                                                data-id="<?php echo $pub['id']; ?>"
                                                data-titulo="<?php echo htmlspecialchars($pub['titulo'], ENT_QUOTES); ?>"
                                                data-tipo="<?php echo htmlspecialchars($pub['tipo'], ENT_QUOTES); ?>"
                                                data-status="<?php echo htmlspecialchars($pub['status'], ENT_QUOTES); ?>"
                                                data-description="<?php echo htmlspecialchars($pub['descricao'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-id="<?php echo htmlspecialchars($pub['animal_id'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-nome="<?php echo htmlspecialchars($pub['animal_nome'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-especie="<?php echo htmlspecialchars($pub['animal_especie'] ?? '', ENT_QUOTES); ?>"
                                                data-animal-imagem="<?php echo htmlspecialchars($pub['animal_imagem'] ?? '', ENT_QUOTES); ?>"
                                                onclick="carregarDadosModal(this)">
                                            <i data-lucide="edit" style="width: 14px;"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border text-danger" title="Ocultar/Excluir"><i data-lucide="trash-2" style="width: 14px;"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal de Visualização de Publicação -->
                <div class="modal fade" id="viewPubModal" tabindex="-1" aria-labelledby="viewPubModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content" style="border-radius: 15px; border: none;">
                            <div class="modal-header bg-light" style="border-radius: 15px 15px 0 0;">
                                <h6 class="modal-title fw-bold" id="viewPubModalLabel">
                                    <i data-lucide="eye" class="me-2 text-primary" style="width: 18px;"></i> Visualizar Publicação
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <span class="badge bg-info text-dark" id="viewPubType"></span>
                                    <span class="badge bg-secondary ms-2" id="viewPubStatus"></span>
                                </div>
                                <div class="row g-4">
                                    <div class="col-lg-8">
                                        <h4 id="viewPubTitle" class="mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 700;"></h4>
                                        <p class="text-muted mb-4" id="viewPubMeta" style="font-family: 'Poppins', sans-serif; font-size: 0.9rem;"></p>
                                        <div id="viewPubDescription" style="font-family: 'Poppins', sans-serif; line-height: 1.7; color: #343a40;"></div>
                                    </div>
                                    <div class="col-lg-4" id="viewPubAnimalInfo" style="display:none;">
                                        <div class="card shadow-sm border-0">
                                            <img id="viewPubAnimalImage" src="" alt="Animal vinculado" class="img-fluid rounded-top" style="object-fit: cover; height: 220px; width: 100%;">
                                            <div class="card-body">
                                                <div class="fw-semibold text-secondary small mb-2">Animal vinculado</div>
                                                <h6 class="fw-bold" id="viewPubAnimalName"></h6>
                                                <p class="mb-1 text-muted" id="viewPubAnimalSpecies"></p>
                                                <p class="small text-secondary mb-0" id="viewPubAnimalDescription"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 4: LOG de Auditoria -->
                <div class="tab-pane fade" id="tab-auditoria">
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i data-lucide="info" class="me-3"></i>
                        <div>
                            <strong>Modo de Segurança:</strong> Os logs de auditoria são imutáveis e guardam as ações críticas dos últimos 90 dias para compliance.
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <input type="date" class="form-control" style="max-width: 200px;">
                        <input type="text" class="form-control" placeholder="Buscar por IP ou Usuário" style="max-width: 300px;">
                        <button class="btn btn-secondary">Buscar Logs</button>
                    </div>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-striped table-logs mb-0">
                            <thead>
                                <tr>
                                    <th>Data / Hora</th>
                                    <th>Usuário</th>
                                    <th>Ação Realizada</th>
                                    <th>Endereço IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logsMock as $log): ?>
                                <tr>
                                    <td class="text-muted"><?php echo $log['data']; ?></td>
                                    <td class="fw-bold"><?php echo $log['user']; ?></td>
                                    <td><?php echo $log['acao']; ?></td>
                                    <td><code class="text-secondary"><?php echo $log['ip']; ?></code></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- Fim Tab Content -->
        </div>
    </div>
</div>

<!-- Modal de Edição de Publicação -->
<div class="modal fade" id="editPubModal" tabindex="-1" aria-labelledby="editPubModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header bg-light" style="border-radius: 15px 15px 0 0;">
                <h6 class="modal-title fw-bold" id="editPubModalLabel">
                    <i data-lucide="edit-2" class="me-2 text-primary" style="width: 18px;"></i>Editar Publicação
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- FORMULÁRIO: Editar Publicação -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="edit_pub">
                    <input type="hidden" name="pub_id" id="modalPubId">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Título da Publicação</label>
                        <input type="text" class="form-control" name="pub_titulo" id="modalPubTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Tipo de Conteúdo</label>
                        <select class="form-select" name="pub_tipo" id="modalPubType" required>
                            <option value="Adoção">Adoção</option>
                            <option value="Notícia">Notícia</option>
                            <option value="Evento">Evento</option>
                            <option value="Alerta">Alerta</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="linkAnimalCheckboxEdit" name="link_animal" value="1" onchange="toggleAnimalLink('edit')">
                        <label class="form-check-label" for="linkAnimalCheckboxEdit">Linkar com animal</label>
                    </div>
                    <div class="mb-3" id="animalLinkGroup-edit" style="display:none;">
                        <label class="form-label text-muted small mb-1">Animal vinculado</label>
                        <select name="animal_id" id="animalSelectEdit" class="form-select" onchange="updateLinkedAnimalPreview('edit')">
                            <option value="">Selecione um animal...</option>
                            <?php foreach ($animaisSimulados as $animal): ?>
                                <option value="<?= $animal['id'] ?>"
                                        data-nome="<?= htmlspecialchars($animal['nome'], ENT_QUOTES) ?>"
                                        data-especie="<?= htmlspecialchars($animal['especie'], ENT_QUOTES) ?>"
                                        data-imagem="<?= htmlspecialchars($animal['imagem'], ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($animal['nome']) ?> - <?= htmlspecialchars($animal['raca']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="animal_nome" id="animalNomeEdit">
                        <input type="hidden" name="animal_especie" id="animalEspecieEdit">
                        <input type="hidden" name="animal_imagem" id="animalImagemEdit">
                        <div class="card mt-3 p-3" id="animalLinkPreview-edit" style="display:none;">
                            <div class="d-flex align-items-center gap-3">
                                <img id="animalLinkImageEdit" src="" alt="Animal" style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px;">
                                <div>
                                    <div id="animalLinkNameEdit" class="fw-bold"></div>
                                    <div id="animalLinkSpeciesEdit" class="text-muted"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Notícia</label>
                        <div class="rich-editor-toolbar">
                            <button type="button" onclick="applyFormatting('modalPubDescription', 'title')">Título</button>
                            <button type="button" onclick="applyFormatting('modalPubDescription', 'subtitle')">Subtítulo</button>
                            <button type="button" onclick="applyFormatting('modalPubDescription', 'bold')">Negrito</button>
                            <button type="button" onclick="applyFormatting('modalPubDescription', 'italic')">Itálico</button>
                            <select onchange="applyFormatting('modalPubDescription', 'color', this.value)">
                                <option value="">Cor do Texto</option>
                                <option value="#1F8A70">Verde</option>
                                <option value="#0A58CA">Azul</option>
                                <option value="#D63384">Roxo</option>
                                <option value="#FD7E14">Laranja</option>
                            </select>
                        </div>
                        <textarea class="form-control" name="pub_descricao" id="modalPubDescription" rows="4" placeholder="Adicione detalhes da publicação aqui..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small mb-1">Visualização (Status)</label>
                        <select class="form-select" name="pub_status" id="modalPubStatus" required>
                            <option value="Ativo">Ativo (Visível para todos)</option>
                            <option value="Oculto">Oculto (Invisível no painel)</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-save">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- NOVO MODAL: Adicionar Animal ao Carrossel -->
<div class="modal fade" id="addCarouselModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" class="modal-content" style="border-radius: 15px;">
            <input type="hidden" name="action" value="create_carousel_item">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Selecionar Animal</h6>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Lista de Animais Registados</label>
                    <select id="selectAnimal" class="form-select" required onchange="preencherDados()">
                        <option value="">Selecione um pet...</option>
                        <?php foreach ($animaisSimulados as $animal): ?>
                            <option value="<?= $animal['id'] ?>" 
                                    data-nome="<?= htmlspecialchars($animal['nome']) ?>" 
                                    data-especie="<?= htmlspecialchars($animal['especie']) ?>" 
                                    data-imagem="<?= htmlspecialchars($animal['imagem']) ?>">
                                <?= htmlspecialchars($animal['nome']) ?> - <?= htmlspecialchars($animal['raca']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nome do Pet</label>
                    <input type="text" id="displayNome" name="nome" class="form-control" readonly required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Espécie</label>
                    <input type="text" id="displayEspecie" name="especie" class="form-control" readonly required>
                </div>
                <input type="hidden" id="displayAnimalId" name="animal_id">
                <input type="hidden" id="displayImagem" name="imagem_url">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Confirmar Adição</button>
            </div>
        </form>
    </div>
</div>

<script>
function preencherDados() {
    const select = document.getElementById('selectAnimal');
    const option = select.options[select.selectedIndex];
    
    // Preencher campos automaticamente e bloquear edições
    document.getElementById('displayAnimalId').value = option.value || '';
    document.getElementById('displayNome').value = option.getAttribute('data-nome') || '';
    document.getElementById('displayEspecie').value = option.getAttribute('data-especie') || '';
    document.getElementById('displayImagem').value = option.getAttribute('data-imagem') || '';
}
</script>

<script>
// Lógica para mostrar/esconder o campo de usuário específico nas notificações
function toggleDestinatario() {
    const tipo = document.getElementById('tipoDestinatario').value;
    const campoEspecifico = document.getElementById('campoUsuarioEspecifico');
    const inputEspecifico = campoEspecifico.querySelector('input');
    
    if (tipo === 'especifico') {
        campoEspecifico.style.display = 'block';
        inputEspecifico.required = true;
    } else {
        campoEspecifico.style.display = 'none';
        inputEspecifico.required = false;
        inputEspecifico.value = '';
    }
}

// Lógica para preencher o Modal de Edição com os dados da linha clicada
function carregarDadosModal(button) {
    const description = button.dataset.description || button.getAttribute('data-description') || '';
    const animalId = button.dataset.animalId || button.getAttribute('data-animal-id') || '';
    const animalNome = button.dataset.animalNome || button.getAttribute('data-animal-nome') || '';
    const animalEspecie = button.dataset.animalEspecie || button.getAttribute('data-animal-especie') || '';
    const animalImagem = button.dataset.animalImagem || button.getAttribute('data-animal-imagem') || '';

    document.getElementById('modalPubId').value = button.dataset.id || button.getAttribute('data-id');
    document.getElementById('modalPubTitle').value = button.dataset.titulo || button.getAttribute('data-titulo');
    document.getElementById('modalPubType').value = button.dataset.tipo || button.getAttribute('data-tipo');
    document.getElementById('modalPubStatus').value = button.dataset.status || button.getAttribute('data-status');
    document.getElementById('modalPubDescription').value = description;

    const linkCheckbox = document.getElementById('linkAnimalCheckboxEdit');
    const linkGroup = document.getElementById('animalLinkGroup-edit');
    const select = document.getElementById('animalSelectEdit');
    const nomeHidden = document.getElementById('animalNomeEdit');
    const especieHidden = document.getElementById('animalEspecieEdit');
    const imagemHidden = document.getElementById('animalImagemEdit');
    const preview = document.getElementById('animalLinkPreview-edit');
    const previewImage = document.getElementById('animalLinkImageEdit');
    const previewName = document.getElementById('animalLinkNameEdit');
    const previewSpecies = document.getElementById('animalLinkSpeciesEdit');

    if (animalId) {
        linkCheckbox.checked = true;
        linkGroup.style.display = 'block';
        select.value = animalId;
        nomeHidden.value = animalNome;
        especieHidden.value = animalEspecie;
        imagemHidden.value = animalImagem;
        preview.style.display = 'block';
        previewImage.src = animalImagem || '';
        previewName.innerText = animalNome || '';
        previewSpecies.innerText = animalEspecie || '';
    } else {
        linkCheckbox.checked = false;
        linkGroup.style.display = 'none';
        select.value = '';
        nomeHidden.value = '';
        especieHidden.value = '';
        imagemHidden.value = '';
        preview.style.display = 'none';
    }
}

function toggleAnimalLink(formType) {
    const checkbox = document.getElementById(formType === 'edit' ? 'linkAnimalCheckboxEdit' : 'linkAnimalCheckbox');
    const group = document.getElementById(`animalLinkGroup-${formType}`);
    const preview = document.getElementById(`animalLinkPreview-${formType}`);
    const select = document.getElementById(formType === 'edit' ? 'animalSelectEdit' : 'animalSelectCreate');
    const nomeHidden = document.getElementById(formType === 'edit' ? 'animalNomeEdit' : 'animalNomeCreate');
    const especieHidden = document.getElementById(formType === 'edit' ? 'animalEspecieEdit' : 'animalEspecieCreate');
    const imagemHidden = document.getElementById(formType === 'edit' ? 'animalImagemEdit' : 'animalImagemCreate');

    if (checkbox.checked) {
        group.style.display = 'block';
    } else {
        group.style.display = 'none';
        select.value = '';
        nomeHidden.value = '';
        especieHidden.value = '';
        imagemHidden.value = '';
        preview.style.display = 'none';
    }
}

function updateLinkedAnimalPreview(formType) {
    const select = document.getElementById(formType === 'edit' ? 'animalSelectEdit' : 'animalSelectCreate');
    const nomeHidden = document.getElementById(formType === 'edit' ? 'animalNomeEdit' : 'animalNomeCreate');
    const especieHidden = document.getElementById(formType === 'edit' ? 'animalEspecieEdit' : 'animalEspecieCreate');
    const imagemHidden = document.getElementById(formType === 'edit' ? 'animalImagemEdit' : 'animalImagemCreate');
    const preview = document.getElementById(`animalLinkPreview-${formType}`);
    const previewImg = document.getElementById(formType === 'edit' ? 'animalLinkImageEdit' : 'animalLinkImageCreate');
    const previewName = document.getElementById(formType === 'edit' ? 'animalLinkNameEdit' : 'animalLinkNameCreate');
    const previewSpecies = document.getElementById(formType === 'edit' ? 'animalLinkSpeciesEdit' : 'animalLinkSpeciesCreate');
    const option = select.options[select.selectedIndex];

    if (option && option.value) {
        const nome = option.getAttribute('data-nome');
        const especie = option.getAttribute('data-especie');
        const imagem = option.getAttribute('data-imagem');

        nomeHidden.value = nome || '';
        especieHidden.value = especie || '';
        imagemHidden.value = imagem || '';
        previewImg.src = imagem || '';
        previewName.innerText = nome || '';
        previewSpecies.innerText = especie || '';
        preview.style.display = 'block';
    } else {
        nomeHidden.value = '';
        especieHidden.value = '';
        imagemHidden.value = '';
        preview.style.display = 'none';
    }
}

function carregarVisualizacaoPublicacao(button) {
    const title = button.dataset.titulo || button.getAttribute('data-titulo');
    const type = button.dataset.tipo || button.getAttribute('data-tipo');
    const author = button.dataset.autor || button.getAttribute('data-autor');
    const date = button.dataset.data || button.getAttribute('data-data');
    const status = button.dataset.status || button.getAttribute('data-status');
    const description = button.dataset.description || button.getAttribute('data-description') || '';
    const animalId = button.dataset.animalId || button.getAttribute('data-animal-id') || '';
    const animalNome = button.dataset.animalNome || button.getAttribute('data-animal-nome') || '';
    const animalEspecie = button.dataset.animalEspecie || button.getAttribute('data-animal-especie') || '';
    const animalImagem = button.dataset.animalImagem || button.getAttribute('data-animal-imagem') || '';

    document.getElementById('viewPubTitle').innerText = title;
    document.getElementById('viewPubType').innerText = type;
    document.getElementById('viewPubStatus').innerText = status;
    document.getElementById('viewPubMeta').innerText = `Publicado por ${author} em ${date}`;
    document.getElementById('viewPubDescription').innerHTML = description.replace(/\n/g, '<br>');

    const animalInfo = document.getElementById('viewPubAnimalInfo');
    if (animalId) {
        document.getElementById('viewPubAnimalImage').src = animalImagem || 'https://via.placeholder.com/350x220?text=Animal';
        document.getElementById('viewPubAnimalName').innerText = animalNome || 'Animal vinculado';
        document.getElementById('viewPubAnimalSpecies').innerText = animalEspecie ? `Espécie: ${animalEspecie}` : '';
        document.getElementById('viewPubAnimalDescription').innerText = `Linkado com o animal selecionado.`;
        animalInfo.style.display = 'block';
    } else {
        animalInfo.style.display = 'none';
    }
}

function applyFormatting(fieldId, formatType, value = '') {
    const textarea = document.getElementById(fieldId);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);
    let formatted = selected;

    switch (formatType) {
        case 'title':
            formatted = `<h2>${selected || 'Título'}</h2>`;
            break;
        case 'subtitle':
            formatted = `<h3>${selected || 'Subtítulo'}</h3>`;
            break;
        case 'bold':
            formatted = `<strong>${selected || 'texto em negrito'}</strong>`;
            break;
        case 'italic':
            formatted = `<em>${selected || 'texto em itálico'}</em>`;
            break;
        case 'color':
            if (!value) return;
            formatted = `<span style="color: ${value};">${selected || 'texto colorido'}</span>`;
            break;
        default:
            return;
    }

    textarea.setRangeText(formatted, start, end, 'end');
    textarea.focus();
}

function updateCharCount(fieldId, counterId, maxLength) {
    const textarea = document.getElementById(fieldId);
    const counter = document.getElementById(counterId);
    const length = textarea.value.length;
    counter.textContent = `${length} / ${maxLength} caracteres`;
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    updateCharCount('newsContent', 'newsCharCount', 800);
});
</script>