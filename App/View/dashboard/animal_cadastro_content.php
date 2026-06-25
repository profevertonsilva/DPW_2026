<?php
/**
 * AmigoPet - Módulo de Animais (Conteúdo do Cadastro)
 * Localização: ~/App/View/dashboard/animal_cadastro_content.php
 */
?>

<style>
    /* Identidade Visual Obrigatória AmigoPet */
    .amigopet-wrapper {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        padding: 40px;
        margin-top: 40px;
        margin-left: 260px; /* Alinhamento correto com a Sidebar */
    }
    .amigopet-wrapper h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #4F4F4F;
    }
    .section-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #6FCF97 !important; /* Verde Principal do AmigoPet */
    }
    .btn-main-success {
        background-color: #6FCF97 !important;
        color: #ffffff !important;
        font-weight: 600;
        border: none !important;
        transition: background-color 0.2s ease;
    }
    .btn-main-success:hover {
        background-color: #5bba84 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6FCF97 !important;
        box-shadow: 0 0 0 0.25rem rgba(111, 207, 151, 0.25) !important;
    }
</style>

<div class="amigopet-wrapper">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1">Cadastrar Novo Pet 🐾</h1>
            <p class="text-muted">Insira as informações do animal para disponibilizá-lo para adoção.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <form action="/dashboard/animal/cadastrar" method="POST" id="formCadastroAnimal">
            <div class="row g-3">
                
                <h5 class="mb-2 section-title"><i class="fa-solid fa-paw me-2"></i>Informações Básicas</h5>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nome do Pet</label>
                    <input type="text" class="form-control shadow-none" name="nome" placeholder="Ex: Pipoca" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Data de Nascimento</label>
                    <input type="date" class="form-control shadow-none" name="data_nascimento" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Sexo</label>
                    <select class="form-select shadow-none" name="sexo" required>
                        <option value="" selected disabled>Selecione...</option>
                        <option value="Macho">Macho</option>
                        <option value="Fêmea">Fêmea</option>
                    </select>
                </div>

                <hr class="text-muted opacity-25 my-3">
                <h5 class="mb-2 section-title"><i class="fa-solid fa-sliders me-2"></i>Características & Triagem</h5>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Cor da Pelagem</label>
                    <input type="text" class="form-control shadow-none" name="cor" placeholder="Ex: Caramelo, Preto" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Porte</label>
                    <select class="form-select shadow-none" name="porte" required>
                        <option value="" selected disabled>Selecione...</option>
                        <option value="Pequeno">Pequeno</option>
                        <option value="Médio">Médio</option>
                        <option value="Grande">Grande</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Castrado?</label>
                    <select class="form-select shadow-none" name="castrado" required>
                        <option value="Não" selected>Não</option>
                        <option value="Sim">Sim</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Status do Animal</label>
                    <select class="form-select shadow-none" name="status" required>
                        <option value="disponível" selected>Disponível para Adoção</option>
                        <option value="triagem">Em Triagem</option>
                        <option value="adotado">Adotado</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">URL da Foto</label>
                    <input type="url" class="form-control shadow-none" name="foto" placeholder="https://exemplo.com/foto.jpg">
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Histórico / Temperamento (Descrição)</label>
                    <textarea class="form-control shadow-none" name="descricao" rows="3" placeholder="História, comportamento ou observações médicas..." required></textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <a href="/dashboard/animal/listar" class="btn btn-light border me-2" style="color: #4F4F4F;">Cancelar</a>
                    <button type="submit" class="btn btn-main-success px-4 py-2">Salvar Prontuário</button>
                </div>
            </div>
        </form>
    </div>
</div>