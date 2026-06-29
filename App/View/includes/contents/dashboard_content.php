<?php
/**
 * AmigoPet - Conteúdo do Dashboard Principal
 * Localização: ~/App/View/includes/contents/dashboard_content.php
 */

// Garante que a sessão está ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mockFile = __DIR__ . '/../../../Data/configuracoes_mock.php';

// Inicialização padrão (Fallbacks)
$configGeralMock = ['dashboard_titulo' => 'Painel de Adoção 🐾', 'dashboard_subtitulo' => 'Bem-vindo ao painel central.'];
$carrosselMock = [];
$publicacoesMock = [];

// 1. Tenta carregar o arquivo físico como base
if (file_exists($mockFile)) {
    include $mockFile;
}

// 2. Sincroniza com a sessão para refletir alterações recentes imediatamente.
// Isso mantém o mock como fonte primária, mas permite ver a atualização sem depender só do reload do arquivo.
if (isset($_SESSION['config'])) {
    $configGeralMock['dashboard_titulo'] = $_SESSION['config']['titulo'] ?? $configGeralMock['dashboard_titulo'];
    $configGeralMock['dashboard_subtitulo'] = $_SESSION['config']['subtitulo'] ?? $configGeralMock['dashboard_subtitulo'];
}

// Se você também quer que publicações e carrossel sejam persistentes na sessão,
// certifique-se de salvar lá no arquivo de configurações também.
if (isset($_SESSION['publicacoes'])) {
    $publicacoesMock = $_SESSION['publicacoes'];
}
if (isset($_SESSION['carrossel'])) {
    $carrosselMock = $_SESSION['carrossel'];
}

// 3. Tenta carregar animais do banco para o carrossel (vitrine)
try {
    $animalDao = new \App\DAO\AnimalDAO();
    $lista = $animalDao->listarDisponiveis();
    if (!empty($lista)) {
        $carrosselMock = [];
        foreach ($lista as $m) {
            $carrosselMock[] = [
                'id' => $m->__get('id'),
                'imagem' => $m->__get('foto') ?: '/resources/dashboard/images/placeholder.png',
                'nome' => $m->__get('nome'),
                'especie' => $m->__get('especie_nome') ?: '',
                'idade_meses' => $m->__get('idade_meses'),
                'racas' => $m->__get('racas') ?: '',
                'sexo' => $m->__get('sexo') ?: ''
            ];
        }
    }
} catch (\Throwable $ex) {
    // falha silenciosa — mantém mock/session como fallback
}

// Debug: inserir comentário HTML com contagem de registros vindos do DB
$__carrossel_db_count = isset($lista) && is_array($lista) ? count($lista) : 0;
echo "<!-- carrossel_db_count: {$__carrossel_db_count} -->\n";

// Check if user is ONG or admin to show create chamado button
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'adotante';
$roleMap = [
    'administrador' => 'admin',
    'ong' => 'ong',
    'veterinario' => 'vet',
    'moderador' => 'campo',
    'adotante' => 'usuario'
];
$role = $roleMap[$tipoUsuario] ?? 'usuario';
$canCreateChamado = in_array($role, ['admin', 'ong']);
?>

<style>
    /* Estilos para o efeito de exibição da imagem ampliada "à frente" */
    .pet-card-inner, .table tr {
        cursor: pointer;
        transition: z-index 0.3s;
    }

    /* Garante que o card em foco fique sobreposto a tudo */
    .pet-card-inner:hover {
        z-index: 50;
        position: relative;
    }

    /* Transição suave para as imagens */
    .pet-card-inner img, .table img {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        transform-origin: center center;
    }

    /* Permite que a imagem saia do card no hover */
    .pet-card-inner:hover .ratio {
        overflow: visible !important;
    }

    /* Efeito de hover: centraliza a imagem e aplica escala aumentada */
    .pet-card-inner:hover img {
        object-fit: contain !important;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: scale(1.25);
        background-color: #ffffff;
        z-index: 100;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        border-radius: 12px;
    }

    /* Ajustes finos para telas menores */
    @media (max-width: 1200px) {
        .pet-card-inner:hover img { 
            transform: scale(1.15); 
        }
    }

    @media (max-width: 768px) {
        .pet-card-inner:hover img { 
            transform: scale(1.1); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        tr:hover .img-thumbnail {
            transform: scale(1.7) !important;
            z-index: 100;
        }

        /* Em mobile, os botões ficam sobre o conteúdo */
        .carousel-control-prev { left: 5px !important; }
        .carousel-control-next { right: 5px !important; }
    }

    /* Efeito na tabela: miniatura expande com limites de segurança */
    tr:hover .img-thumbnail {
        object-fit: contain !important;
        transform: scale(2.2);
        z-index: 100;
        position: relative;
        background-color: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        max-height: 200px;
    }

    /* Estilo para os ícones de gênero */
    .gender-icon {
        width: 14px;
        height: 14px;
        vertical-align: middle;
        margin-right: 4px;
    }
    
    .gender-macho { color: #56CCF2; }
    .gender-femea { color: #F2994A; }

    /* Customização dos Botões do Carrossel (Padrão AmigoPet) */
    .carousel-control-prev, .carousel-control-next {
        width: 45px;
        height: 45px;
        background-color: var(--primary-green);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 1;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 60;
        position: absolute;
    }

    /* Posicionamento Negativo para afastar dos cards */
    .carousel-control-prev {
        left: -25px; 
    }

    .carousel-control-next {
        right: -25px;
    }

    .carousel-control-prev:hover, .carousel-control-next:hover {
        background-color: var(--secondary-orange);
        color: white;
    }

    /* Estilo para a coluna de notícias */
    .news-item {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 12px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .news-item:last-child { border: 0; }
    .news-item:hover { opacity: 0.8; }
    .news-tag {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--primary-green);
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- Cabeçalho (Puxando da Sessão / Mock) -->
        <div class="row mb-4">
            <div class="col-12 text-md-start text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                            <?php echo htmlspecialchars($configGeralMock['dashboard_titulo']); ?>
                        </h1>
                        <p class="text-muted">
                            <?php echo htmlspecialchars($configGeralMock['dashboard_subtitulo']); ?>
                        </p>
                    </div>
                    <?php if ($canCreateChamado): ?>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createChamadoModal">
                            <i data-lucide="phone-call" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                            Criar Chamado de Resgate
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- COLUNA PRINCIPAL (ESQUERDA/CENTRO) -->
            <div class="col-xl-9 col-lg-8">
                
                <!-- Carrossel de Pets (Puxando da Sessão / Mock) -->
                <section class="mb-5 position-relative px-md-4 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 style="font-family: 'Poppins'; font-weight: 600; font-size: 1.1rem;">Animais Recém Chegados</h5>
                    </div>
                    
                    <?php if (empty($carrosselMock)): ?>
                        <div class="card-amigopet text-center py-4 text-muted">
                            <i data-lucide="images" class="mb-2" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                            <p class="mb-0">Não há animais em destaque no momento.</p>
                        </div>
                    <?php else: ?>
                    <div id="petCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php 
                            // Divide os animais em grupos de 3 para múltiplos slides
                            $slides = array_chunk($carrosselMock, 3);
                            foreach ($slides as $slideIndex => $slide): 
                            ?>
                            <div class="carousel-item <?php echo $slideIndex === 0 ? 'active' : ''; ?>">
                                <div class="row g-3">
                                    <?php 
                                    foreach ($slide as $index => $pet): 
                                    ?>
                                    <!-- Apenas o primeiro aparece em mobile, os outros escondem -->
                                    <div class="col-md-4 mb-3 <?php echo $index > 0 ? 'd-none d-md-block' : ''; ?>" >
                                        <div class="pet-card-inner shadow-sm h-100 bg-white border-0 rounded-4" data-pet-id="<?php echo $pet['id']; ?>" data-pet-name="<?php echo htmlspecialchars($pet['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="ratio ratio-16x9 overflow-hidden bg-light rounded-top-4">
                                                <img src="<?php echo $pet['imagem']; ?>" 
                                                     class="img-fluid" style="object-fit: cover; object-position: top; cursor:pointer;" alt="<?php echo $pet['nome']; ?>" data-pet-id="<?php echo $pet['id']; ?>" data-pet-name="<?php echo htmlspecialchars($pet['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 600; font-size: 1rem;"><?php echo $pet['nome']; ?></h5>
                                                    <span class="badge-category"><?php echo $pet['especie']; ?></span>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php if (isset($pet['sexo']) && strtolower($pet['sexo']) === 'm'): ?>
                                                        <svg class="gender-icon gender-macho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/><path d="m17 7 3-3"/><path d="M16 4h4v4"/></svg>
                                                        Macho
                                                    <?php else: ?>
                                                        <svg class="gender-icon gender-femea" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M12 15v7"/><path d="M9 19h6"/></svg>
                                                        Fêmea
                                                    <?php endif; ?>
                                                    • ID: #<?php echo $pet['id']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Controles -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#petCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#petCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php // inclui modal de perfil para abrir a partir do carrossel ?>
                    <?php include __DIR__ . '/animal_profile_modal.php'; ?>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Handler direto para o carrossel
                        document.querySelectorAll('.pet-card-inner').forEach(function(card) {
                            card.addEventListener('click', function(e) {
                                if (e.target.closest('a, button')) return;
                                const petId = this.getAttribute('data-pet-id');
                                if (petId && typeof openAnimalProfile === 'function') {
                                    openAnimalProfile(petId);
                                }
                            });
                        });

                        // Handler direto para a tabela
                        document.querySelectorAll('tbody tr[data-pet-id]').forEach(function(row) {
                            row.addEventListener('click', function(e) {
                                if (e.target.closest('a, button')) return;
                                const petId = this.getAttribute('data-pet-id');
                                if (petId && typeof openAnimalProfile === 'function') {
                                    openAnimalProfile(petId);
                                }
                            });
                        });
                    });
                    </script>
                </section>

                <!-- Seção de Gráficos -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card-amigopet shadow-sm h-100">
                            <h5 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.95rem;">Estatísticas de Espécies</h5>
                            <div style="height: 220px;"><canvas id="chartEspecies"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-amigopet shadow-sm h-100">
                            <h5 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.95rem;">Adoções Mensais</h5>
                            <div style="height: 220px;"><canvas id="chartStatus"></canvas></div>
                        </div>
                    </div>
                </div>

            </div> <!-- FIM COLUNA PRINCIPAL -->

            <!-- COLUNA DE NOTÍCIAS (DIREITA - Puxando da Sessão / Mock) -->
            <div class="col-xl-3 col-lg-4">
                <div class="card-amigopet shadow-sm h-100">
                    <h5 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.1rem;">
                        <i data-lucide="newspaper" class="me-1 text-primary" style="width: 20px;"></i> Notícias
                    </h5>

                    <div class="news-list">
                        <?php
                        $hasNews = false;
                        foreach ($publicacoesMock as $pub):
                            if ($pub['status'] === 'Ativo'):
                                $hasNews = true;
                        ?>
                            <div class="news-item">
                                <span class="news-tag"><?php echo $pub['tipo']; ?></span>
                                <h6 class="mb-1 mt-1" style="font-weight: 600; font-size: 0.9rem;"><?php echo $pub['titulo']; ?></h6>
                                <p class="text-muted small mb-0">Publicado por <?php echo $pub['autor']; ?> em <?php echo $pub['data']; ?>.</p>
                            </div>
                        <?php
                            endif;
                        endforeach;

                        if (!$hasNews): ?>
                            <p class="text-muted small">Nenhuma novidade no momento.</p>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-sm btn-light w-100 text-muted" style="font-size: 0.75rem; font-weight: 600;">Ver todas as publicações</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- FIM DA ROW SUPERIOR -->

        <!-- LISTA (TELA TODA): Últimas Adoções -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card-amigopet border-0 shadow-sm">
                    <h5 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Últimos animais adotados</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary">
                                <tr style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <th>Animal</th>
                                    <th>Espécie</th>
                                    <th>Adotante</th>
                                    <th>Data</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-pet-id="98" data-pet-name="Thor" style="cursor: pointer;">
                                    <td>
                                            <div class="d-flex align-items-center">
                                            <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?q=80&w=100" 
                                                class="img-thumbnail me-2" 
                                                style="width: 40px; height: 40px; object-fit: cover; object-position: top;" alt="Thor">
                                            <div>
                                                <span class="fw-bold d-block">Thor</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">Macho</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Cachorro</td>
                                    <td>Ana Oliveira</td>
                                    <td class="text-muted small">12/04/2026</td>
                                    <td class="text-end">
                                        <span class="badge" style="background-color: rgba(111, 207, 151, 0.2); color: var(--primary-green); border-radius: 6px;">Concluído</span>
                                    </td>
                                </tr>
                                <tr data-pet-id="99" data-pet-name="Mimi" style="cursor: pointer;">
                                    <td>
                                            <div class="d-flex align-items-center">
                                            <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=100" 
                                                class="img-thumbnail me-2" 
                                                style="width: 40px; height: 40px; object-fit: cover; object-position: top;" alt="Mimi">
                                            <div>
                                                <span class="fw-bold d-block">Mimi</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">Fêmea</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Gato</td>
                                    <td>Carlos Souza</td>
                                    <td class="text-muted small">10/04/2026</td>
                                    <td class="text-end">
                                        <span class="badge" style="background-color: rgba(111, 207, 151, 0.2); color: var(--primary-green); border-radius: 6px;">Concluído</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($canCreateChamado): ?>
<!-- Modal para Criar Chamado Manual -->
<div class="modal fade" id="createChamadoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title">
                    <i data-lucide="phone-call" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle;"></i>
                    Criar Chamado de Resgate
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="createChamadoForm">
                    <input type="hidden" name="origem" value="manual">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Chamado *</label>
                        <select class="form-select" name="tipo" id="chamadoTipo" required>
                            <option value="resgate">Resgate</option>
                            <option value="abandono">Abandono</option>
                            <option value="maus_tratos">Maus Tratos</option>
                            <option value="perdido">Perdido</option>
                            <option value="encontrado">Encontrado</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Urgência *</label>
                        <select class="form-select" name="urgencia" id="chamadoUrgencia" required>
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assunto *</label>
                        <input type="text" class="form-control" name="assunto" id="chamadoAssunto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Localização *</label>
                        <input type="text" class="form-control" name="localizacao" id="chamadoLocalizacao" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição *</label>
                        <textarea class="form-control" name="descricao" id="chamadoDescricao" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome do Contato</label>
                        <input type="text" class="form-control" name="contato_nome" id="chamadoContatoNome">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Telefone do Contato</label>
                        <input type="tel" class="form-control" name="contato_telefone" id="chamadoContatoTelefone" placeholder="(00) 00000-0000">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="criarChamado()">
                    <i data-lucide="phone-call" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                    Criar Chamado
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function criarChamado() {
    const form = document.getElementById('createChamadoForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch('/chamados-criar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Chamado criado com sucesso!');
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('createChamadoModal')).hide();
        } else {
            alert('Erro ao criar chamado: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao criar chamado. Tente novamente.');
    });
}
</script>
<?php endif; ?>
