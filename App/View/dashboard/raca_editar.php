<?php
$raca    = $this->getView()->raca;
$especies = $this->getView()->especies;
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Raça</h1>
        <a href="/dashboard/raca/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="/dashboard/raca/alterar">
                <input type="hidden" name="id" value="<?= htmlspecialchars($raca->__get('id')) ?>">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required
                               value="<?= htmlspecialchars($raca->__get('nome')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="fk_especie_id" class="form-label">Espécie <span class="text-danger">*</span></label>
                        <select class="form-select" id="fk_especie_id" name="fk_especie_id" required>
                            <option value="">Selecione a espécie</option>
                            <?php foreach ($especies as $especie): ?>
                                <option value="<?= $especie->__get('id') ?>"
                                    <?= (int)$raca->__get('fk_especie_id') === (int)$especie->__get('id') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($especie->__get('nome')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="/dashboard/raca/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
