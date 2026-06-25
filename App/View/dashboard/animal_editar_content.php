<?php
/**
 * AmigoPet - Módulo de Animais (Conteúdo da Edição)
 * Localização: ~/App/View/dashboard/animal_editar_content.php
 */

// Resgata o animal disponível na View
$animal = $this->getView()->animal ?? null;

if (!$animal || !is_object($animal)) {
    echo "<div class='alert alert-danger' style='margin-left: 280px; margin-top: 80px;'>Erro crítico: Os dados do pet não puderam ser carregados pelo Controller. Verifique o ID no banco.</div>";
    return;
}
?>

<style>
    /* Identidade Visual Obrigatória AmigoPet - Alinhamento com Menu */
    .amigopet-wrapper-content-fix {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        padding: 40px;
        margin-top: 40px;
        margin-left: 260px; /* Recuo perfeito para não ficar atrás do menu */
    }
    .amigopet-wrapper-content-fix h1 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #4F4F4F;
    }
    .section-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #6FCF97 !important;
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

<div class="amigopet-wrapper-content-fix">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1">Editar Prontuário do Pet ✏️</h1>
            <p class="text-muted mb-0">Atualize as características ou altere o status de triagem e adoção.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <form action="/dashboard/animal/alterar" method="POST" id="formEditarAnimal">
            
            <!-- ID Oculto para o WHERE do SQL -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($animal->__get('id')) ?>">

            <div class="row g-3">
                <h5 class="mb-2 section-title"><i class="fa-solid fa-paw me-2"></i>Informações Básicas</h5>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nome do Pet</label>
                    <input type="text" class="form-control shadow-none" name="nome" value="<?= htmlspecialchars($animal->__get('nome') ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Data de Nascimento</label>
                    <input type="date" class="form-control shadow-none" name="data_nascimento" value="<?= htmlspecialchars($animal->__get('data_nascimento') ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Sexo</label>
                    <select class="form-select shadow-none" name="sexo" required>
                        <option value="Macho" <?= $animal->__get('sexo') === 'Macho' ? 'selected' : '' ?>>Macho</option>
                        <option value="Fêmea" <?= $animal->__get('sexo') === 'Fêmea' ? 'selected' : '' ?>>Fêmea</option>
                    </select>
                </div>

                <hr class="text-muted opacity-25 my-3">
                <h5 class="mb-2 section-title"><i class="fa-solid fa-sliders me-2"></i>Características & Triagem</h5>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Cor da Pelagem</label>
                    <input type="text" class="form-control shadow-none" name="cor" value="<?= htmlspecialchars($animal->__get('cor') ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Porte</label>
                    <select class="form-select shadow-none" name="porte" required>
                        <option value="Pequeno" <?= $animal->__get('porte') === 'Pequeno' ? 'selected' : '' ?>>Pequeno</option>
                        <option value="Médio" <?= $animal->__get('porte') === 'Médio' ? 'selected' : '' ?>>Médio</option>
                        <option value="Grande" <?= $animal->__get('porte') === 'Grande' ? 'selected' : '' ?>>Grande</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Castrado?</label>
                    <select class="form-select shadow-none" name="castrado" required>
                        <option value="Não" <?= $animal->__get('castrado') === 'Não' ? 'selected' : '' ?>>Não</option>
                        <option value="Sim" <?= $animal->__get('castrado') === 'Sim' ? 'selected' : '' ?>>Sim</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Status do Animal</label>
                    <select class="form-select shadow-none" name="status" required>
                        <option value="disponível" <?= strtolower($animal->__get('status')) === 'disponível' ? 'selected' : '' ?>>Disponível para Adoção</option>
                        <option value="triagem" <?= strtolower($animal->__get('status')) === 'triagem' ? 'selected' : '' ?>>Em Triagem</option>
                        <option value="adotado" <?= strtolower($animal->__get('status')) === 'adotado' ? 'selected' : '' ?>>Adotado</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">URL da Foto</label>
                    <input type="url" class="form-control shadow-none" name="foto" value="<?= htmlspecialchars($animal->__get('foto') ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Histórico / Temperamento (Descrição)</label>
                    <textarea class="form-control shadow-none" name="descricao" rows="3" required><?= htmlspecialchars($animal->__get('descricao') ?? '') ?></textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <a href="/dashboard/animal/listar" class="btn btn-light border me-2" style="color: #4F4F4F;">Cancelar</a>
                    <button type="submit" class="btn btn-main-success px-4 py-2">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>