<?php
/**
 * AmigoPet - Conteúdo do Acompanhamento de Adoções
 * Localização: ~/App/View/acompanhar_adocao_content.php
 */

// Simulação de dados de pedidos de adoção (Substituir por consulta SQL no Backend)
$meusPedidos = [
    [
        'id' => 101,
        'animal_nome' => 'Max',
        'animal_especie' => 'Cachorro',
        'animal_foto' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=100',
        'data_pedido' => '05/05/2024',
        'status' => 'Entrevista Marcada',
        'status_classe' => 'status-entrevista',
        'cor' => '#56CCF2',
        'descricao' => 'A sua entrevista foi agendada para o dia 15/05 às 14:00 via videochamada.',
        'ong' => 'ONG Vida Animal'
    ],
    [
        'id' => 105,
        'animal_nome' => 'Luna',
        'animal_especie' => 'Gato',
        'animal_foto' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=100',
        'data_pedido' => '02/05/2024',
        'status' => 'Em Análise',
        'status_classe' => 'status-analise',
        'cor' => '#F2994A',
        'descricao' => 'A ONG responsável está a validar o seu perfil e o formulário enviado.',
        'ong' => 'Gatinhos Felizes'
    ],
    [
        'id' => 98,
        'animal_nome' => 'Thor',
        'animal_especie' => 'Cachorro',
        'animal_foto' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?q=80&w=100',
        'data_pedido' => '15/04/2024',
        'status' => 'Adoção Concluída',
        'status_classe' => 'status-concluido',
        'cor' => '#6FCF97',
        'descricao' => 'Parabéns! O processo foi finalizado e o Thor já faz parte da sua família.',
        'ong' => 'ONG Vida Animal'
    ]
];
?>

<style>
    .adoption-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .adoption-card:hover {
        transform: translateX(5px);
        border-color: var(--primary-green);
    }

    .status-pill {
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-entrevista { background: rgba(86, 204, 242, 0.1); color: #56CCF2; }
    .status-analise { background: rgba(242, 153, 74, 0.1); color: #F2994A; }
    .status-concluido { background: rgba(111, 207, 151, 0.1); color: #6FCF97; }

    .animal-thumb {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
    }

    .timeline-steps {
        display: flex;
        justify-content: space-between;
        padding: 20px 0;
        position: relative;
    }

    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 35px;
        left: 0;
        right: 0;
        height: 2px;
        background: #f0f0f0;
        z-index: 1;
    }

    .step-item {
        position: relative;
        z-index: 2;
        background: white;
        padding: 0 10px;
        text-align: center;
        width: 25%;
    }

    .step-dot {
        width: 12px;
        height: 12px;
        background: #E0E0E0;
        border-radius: 50%;
        margin: 0 auto 10px;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #E0E0E0;
    }

    .step-item.active .step-dot {
        background: var(--primary-green);
        box-shadow: 0 0 0 2px var(--primary-green);
    }

    .step-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #ADB5BD;
    }

    .step-item.active .step-label {
        color: var(--primary-green);
    }

    /* Estilos para o Modal de Chat */
    .chat-modal-header {
        background: var(--primary-green);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .message-bubble {
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        max-width: 80%;
        font-size: 0.9rem;
    }

    .msg-sent {
        background: var(--primary-green);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }

    .msg-received {
        background: #f0f0f0;
        color: var(--text-gray);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }

    .chat-box {
        height: 300px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        padding: 15px;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Acompanhar Adoção 🐾</h1>
                <p class="text-muted">Veja aqui o progresso dos seus pedidos para levar um novo amigo para casa.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-9 col-lg-12">
                
                <?php if (empty($meusPedidos)): ?>
                    <div class="card-amigopet text-center py-5">
                        <i data-lucide="heart-off" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <h5 class="text-muted">Ainda não tem nenhum pedido de adoção.</h5>
                        <a href="adocao.php" class="btn btn-primary mt-3" style="background-color: var(--primary-green); border: none; border-radius: 12px;">Encontrar um amigo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($meusPedidos as $pedido): ?>
                    <div class="adoption-card p-4 shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3 mb-md-0">
                                    <img src="<?php echo $pedido['animal_foto']; ?>" alt="<?php echo $pedido['animal_nome']; ?>" class="animal-thumb me-3 border">
                                    <div>
                                        <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 600;"><?php echo $pedido['animal_nome']; ?></h5>
                                        <small class="text-muted"><?php echo $pedido['animal_especie']; ?> • ID: #<?php echo $pedido['id']; ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="status-pill <?php echo $pedido['status_classe']; ?>">
                                    <?php echo $pedido['status']; ?>
                                </span>
                                <div class="mt-2">
                                    <small class="text-muted">Pedido em: <?php echo $pedido['data_pedido']; ?></small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" style="opacity: 0.05;">

                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="timeline-steps">
                                    <div class="step-item active">
                                        <div class="step-dot"></div>
                                        <span class="step-label">Interesse</span>
                                    </div>
                                    <div class="step-item <?php echo in_array($pedido['status'], ['Em Análise', 'Entrevista Marcada', 'Adoção Concluída']) ? 'active' : ''; ?>">
                                        <div class="step-dot"></div>
                                        <span class="step-label">Análise</span>
                                    </div>
                                    <div class="step-item <?php echo in_array($pedido['status'], ['Entrevista Marcada', 'Adoção Concluída']) ? 'active' : ''; ?>">
                                        <div class="step-dot"></div>
                                        <span class="step-label">Entrevista</span>
                                    </div>
                                    <div class="step-item <?php echo $pedido['status'] == 'Adoção Concluída' ? 'active' : ''; ?>">
                                        <div class="step-dot"></div>
                                        <span class="step-label">Concluído</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center text-md-end">
                                <p class="small text-muted mb-3"><?php echo $pedido['descricao']; ?></p>
                                <div class="d-flex gap-2 justify-content-center justify-content-md-end">
                                    <button class="btn btn-outline-light border text-muted btn-sm px-3" style="border-radius: 10px;">
                                        Detalhes
                                    </button>
                                    <button class="btn btn-primary btn-sm px-3" 
                                            style="border-radius: 10px; background: var(--info-blue); border: none;"
                                            onclick="openChat('<?php echo $pedido['animal_nome']; ?>', '<?php echo $pedido['ong']; ?>')">
                                        <i data-lucide="message-circle" class="me-1" style="width: 14px;"></i> Mensagem
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- Coluna lateral com dicas -->
            <div class="col-xl-3">
                <div class="card-amigopet shadow-sm bg-light border-0">
                    <h6 class="fw-bold mb-3"><i data-lucide="lightbulb" class="me-2 text-warning" style="width: 18px;"></i> Dicas para a entrevista</h6>
                    <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                        <li>Seja sincero nas respostas.</li>
                        <li>Prepare o ambiente para a chegada do pet.</li>
                        <li>Tire todas as suas dúvidas sobre o histórico do animal.</li>
                        <li>Lembre-se: adoção é um compromisso para a vida toda.</li>
                    </ul>
                </div>
                <div class="card-amigopet shadow-sm mt-3 border-0" style="background: rgba(111, 207, 151, 0.1);">
                    <h6 class="fw-bold mb-2 text-success">Precisa de ajuda?</h6>
                    <p class="small text-muted">Se tiver dúvidas sobre o seu processo, entre em contacto com o nosso suporte ou diretamente com a ONG.</p>
                    <button class="btn btn-sm btn-success w-100 py-2" 
                            style="border-radius: 8px; font-weight: 600; border: none;"
                            onclick="openChat('Dúvida Geral', 'Suporte AmigoPet')">
                        Contactar ONG
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal de Chat -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header chat-modal-header py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                        <i data-lucide="building-2" class="text-success" style="width: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title mb-0" id="chatTargetName">ONG Vida Animal</h6>
                        <small style="opacity: 0.8; font-size: 0.7rem;">Assunto: <span id="chatSubject">Max</span></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="chat-box" id="chatContent">
                    <div class="message-bubble msg-received">
                        Olá! Como podemos ajudar com o processo de adoção?
                    </div>
                </div>
                <div class="p-3 border-top">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" id="chatInput" class="form-control" placeholder="Escreva a sua mensagem..." style="border-radius: 10px;">
                        <button type="submit" class="btn btn-success" style="border-radius: 10px; background: var(--primary-green); border: none;">
                            <i data-lucide="send" style="width: 18px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    const chatModal = new bootstrap.Modal(document.getElementById('chatModal'));
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatContent = document.getElementById('chatContent');

    // Função para abrir o chat
    window.openChat = function(animal, ong) {
        document.getElementById('chatTargetName').textContent = ong;
        document.getElementById('chatSubject').textContent = animal;
        chatContent.innerHTML = `<div class="message-bubble msg-received">Olá! Estamos aqui para ajudar com o processo de adoção do(a) <strong>${animal}</strong>. Qual é a sua dúvida?</div>`;
        chatModal.show();
    };

    // Função para enviar mensagem
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (text) {
            const msg = document.createElement('div');
            msg.className = 'message-bubble msg-sent';
            msg.textContent = text;
            chatContent.appendChild(msg);
            chatInput.value = '';
            chatContent.scrollTop = chatContent.scrollHeight;

            // Resposta automática simulada
            setTimeout(() => {
                const reply = document.createElement('div');
                reply.className = 'message-bubble msg-received';
                reply.textContent = "Recebemos a sua mensagem. Um responsável da ONG entrará em contacto em breve.";
                chatContent.appendChild(reply);
                chatContent.scrollTop = chatContent.scrollHeight;
            }, 1000);
        }
    });
});
</script>