<?php
/**
 * AmigoPet - Conteúdo da Listagem de Adoção
 * Localização: ~/App/View/adocao_content.php
 */

// Inclui o mock de dados de um ficheiro separado
include_once __DIR__ . '/../Data/animais_mock.php';
?>

<style>
    .search-container {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    .pet-card-adocao {
        transition: all 0.3s ease;
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: white;
    }
    .pet-card-adocao:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .pet-img-wrapper img {
        object-fit: cover;
        object-position: top;
        height: 180px;
        width: 100%;
        border-bottom: 1px solid #f0f0f0;
    }
    .gender-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .badge-category {
        background-color: rgba(111, 207, 151, 0.1);
        color: #6FCF97;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Animais para Adoção 🏠</h1>
                <p class="text-muted">Encontre o seu novo melhor amigo entre os nossos resgatados.</p>
            </div>
        </div>

        <!-- Barra de Busca com suporte a ignorar acentos -->
        <div class="search-container border">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i data-lucide="search"></i>
                        </span>
                        <input type="text" id="petSearch" class="form-control border-start-0 ps-0 shadow-none" 
                               placeholder="Procure por nome, espécie, raça, idade ou género...">
                    </div>
                </div>
                <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                    <span class="text-muted small fw-bold">Total: <span id="petCount" class="text-primary"><?php echo count($animaisSimulados); ?></span> amigos</span>
                </div>
            </div>
        </div>

        <!-- Grid de Animais -->
        <div class="row g-4" id="petGrid">
            <?php foreach ($animaisSimulados as $pet): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 pet-item" 
                 data-search="<?php echo strtolower("{$pet['nome']} {$pet['especie']} {$pet['raca']} {$pet['idade']} {$pet['sexo']}"); ?>">
                
                <div class="pet-card-adocao shadow-sm h-100 border">
                    <div class="pet-img-wrapper position-relative">
                        <img src="<?php echo $pet['imagem']; ?>" alt="<?php echo $pet['nome']; ?>">
                        <div class="gender-badge">
                            <?php if ($pet['sexo'] == 'Macho'): ?>
                                <span class="text-info">♂ Macho</span>
                            <?php else: ?>
                                <span style="color: #F2994A;">♀ Fêmea</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-0" style="font-family: 'Poppins'; font-weight: 700; color: #4F4F4F; font-size: 1.1rem;">
                                    <?php echo $pet['nome']; ?>
                                </h5>
                                <span class="badge-category mt-2 d-inline-block">
                                    <?php echo $pet['especie']; ?>
                                </span>
                            </div>
                            <div class="text-end">
                                <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;"><?php echo $pet['raca']; ?></small>
                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo $pet['idade']; ?></small>
                            </div>
                        </div>

                        <p class="text-muted small mb-3 mt-2" style="line-height: 1.5; height: 3em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo $pet['descricao']; ?>
                        </p>

                        <div class="d-grid gap-2">
                            <div class="d-flex gap-2">
                                <a href="perfil_pet.php?id=<?php echo $pet['id']; ?>" class="btn btn-outline-light border text-muted flex-fill btn-sm py-2">
                                    <i data-lucide="user" class="me-1" style="width: 14px;"></i> Perfil
                                </a>
                                <button class="btn btn-outline-light border text-muted flex-fill btn-sm py-2">
                                    <i data-lucide="info" class="me-1" style="width: 14px;"></i> Info
                                </button>
                            </div>
                            <button class="btn btn-primary shadow-sm py-2 btn-sm" style="background-color: #6FCF97; border: none; font-weight: 600;">
                                <i data-lucide="heart" class="me-1" style="width: 16px;"></i> Adotar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="noResults" class="text-center py-5" style="display: none;">
            <div class="mb-3">
                <i data-lucide="search-x" class="text-muted" style="width: 64px; height: 64px;"></i>
            </div>
            <h5 class="text-muted">Nenhum amigo encontrado.</h5>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('petSearch');
    const petItems = document.querySelectorAll('.pet-item');
    const noResults = document.getElementById('noResults');
    const petCountDisplay = document.getElementById('petCount');

    /**
     * Função para remover acentos e caracteres especiais de uma string
     */
    const removeAccents = (str) => {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = removeAccents(this.value.toLowerCase().trim());
            const terms = query.split(' ').filter(t => t.length > 0);
            let foundCount = 0;

            petItems.forEach(item => {
                const searchText = removeAccents(item.getAttribute('data-search'));
                
                // Verifica se todos os termos da busca estão presentes no texto (ignorando acentos)
                const isMatch = terms.every(term => searchText.includes(term));

                if (isMatch) {
                    item.style.setProperty('display', 'block', 'important');
                    foundCount++;
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            if(petCountDisplay) petCountDisplay.textContent = foundCount;
            if(noResults) noResults.style.display = foundCount === 0 ? 'block' : 'none';
        });
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>