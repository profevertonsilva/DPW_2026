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
        
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12 text-md-start text-center">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Painel de Adoção 🐾</h1>
                <p class="text-muted">Bem-vindo ao painel central do AmigoPet.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- COLUNA PRINCIPAL (ESQUERDA/CENTRO) -->
            <div class="col-xl-9 col-lg-8">
                
                <!-- Carrossel de Pets -->
                <section class="mb-5 position-relative px-md-4 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 style="font-family: 'Poppins'; font-weight: 600; font-size: 1.1rem;">Animais Recém Chegados</h5>
                    </div>
                    <div id="petCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Slide 1 -->
                            <div class="carousel-item active">
                                <div class="row g-3">
                                    <div class="col-md-4 mb-3">
                                        <div class="pet-card-inner shadow-sm h-100 bg-white border-0 rounded-4">
                                            <div class="ratio ratio-16x9 overflow-hidden bg-light rounded-top-4">
                                                <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=400" 
                                                     class="img-fluid" style="object-fit: cover; object-position: top;" alt="Max">
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 600; font-size: 1rem;">Max</h5>
                                                    <span class="badge-category">Cachorro</span>
                                                </div>
                                                <div class="text-muted small">
                                                    <svg class="gender-icon gender-macho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/><path d="m17 7 3-3"/><path d="M16 4h4v4"/></svg>
                                                    Macho • Golden • 2a
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none d-md-block">
                                        <div class="pet-card-inner shadow-sm h-100 bg-white border-0 rounded-4">
                                            <div class="ratio ratio-16x9 overflow-hidden bg-light rounded-top-4">
                                                <img src="https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=400" 
                                                     class="img-fluid" style="object-fit: cover; object-position: top;" alt="Luna">
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 600; font-size: 1rem;">Luna</h5>
                                                    <span class="badge-category">Gato</span>
                                                </div>
                                                <div class="text-muted small">
                                                    <svg class="gender-icon gender-femea" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M12 15v7"/><path d="M9 19h6"/></svg>
                                                    Fêmea • Siamês • 1a
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 d-none d-md-block">
                                        <div class="pet-card-inner shadow-sm h-100 bg-white border-0 rounded-4">
                                            <div class="ratio ratio-16x9 overflow-hidden bg-light rounded-top-4">
                                                <img src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?q=80&w=400" 
                                                     class="img-fluid" style="object-fit: cover; object-position: top;" alt="Bolinha">
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 600; font-size: 1rem;">Bolinha</h5>
                                                    <span class="badge-category">Cachorro</span>
                                                </div>
                                                <div class="text-muted small">
                                                    <svg class="gender-icon gender-macho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/><path d="m17 7 3-3"/><path d="M16 4h4v4"/></svg>
                                                    Macho • SRD • 5m
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Controles -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#petCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#petCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
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

            <!-- COLUNA DE NOTÍCIAS (DIREITA) -->
            <div class="col-xl-3 col-lg-4">
                <div class="card-amigopet shadow-sm h-100">
                    <h5 class="mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.1rem;">
                        <i data-lucide="newspaper" class="me-1 text-primary" style="width: 20px;"></i> Notícias
                    </h5>
                    
                    <!-- Área para ser configurada pelo Backend -->
                    <div class="news-list">
                        <div class="news-item">
                            <span class="news-tag">Novidade</span>
                            <h6 class="mb-1 mt-1" style="font-weight: 600; font-size: 0.9rem;">Campanha de Vacinação 2024</h6>
                            <p class="text-muted small mb-0">A partir da próxima segunda teremos vacinação gratuita para pets resgatados.</p>
                        </div>

                        <div class="news-item">
                            <span class="news-tag">Dica</span>
                            <h6 class="mb-1 mt-1" style="font-weight: 600; font-size: 0.9rem;">Como cuidar de filhotes SRD</h6>
                            <p class="text-muted small mb-0">Confira nosso guia completo para novos tutores de primeira viagem.</p>
                        </div>

                        <div class="news-item">
                            <span class="news-tag">Evento</span>
                            <h6 class="mb-1 mt-1" style="font-weight: 600; font-size: 0.9rem;">Feira de Adoção no Parque</h6>
                            <p class="text-muted small mb-0">Estaremos no Parque Central neste domingo. Venha conhecer nossos amigos!</p>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-sm btn-light w-100 text-muted" style="font-size: 0.75rem; font-weight: 600;">Ver todas as notícias</a>
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
                                <tr>
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
                                    <td class="text-muted small">12/04/2024</td>
                                    <td class="text-end">
                                        <span class="badge" style="background-color: rgba(111, 207, 151, 0.2); color: var(--primary-green); border-radius: 6px;">Concluído</span>
                                    </td>
                                </tr>
                                <tr>
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
                                    <td class="text-muted small">10/04/2024</td>
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