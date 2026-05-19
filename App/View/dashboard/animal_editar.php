<?php
$animal = $this->getView()->animal;
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Animal</h1>
        <a href="/dashboard/animal/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="/dashboard/animal/alterar">
                <input type="hidden" name="id" value="<?= htmlspecialchars($animal->__get('id')) ?>">

                <!-- Identificação -->
                <h5 class="mb-3 text-primary">Identificação</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required
                            value="<?= htmlspecialchars($animal->__get('nome')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                            value="<?= htmlspecialchars($animal->__get('data_nascimento') ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="sexo" class="form-label">Sexo <span class="text-danger">*</span></label>
                        <select class="form-select" id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="m" <?= $animal->__get('sexo') === 'm' ? 'selected' : '' ?>>Macho</option>
                            <option value="f" <?= $animal->__get('sexo') === 'f' ? 'selected' : '' ?>>Fêmea</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="porte" class="form-label">Porte</label>
                        <select class="form-select" id="porte" name="porte">
                            <option value="">Selecione</option>
                            <?php
                            $portes = ['pequeno' => 'Pequeno', 'medio' => 'Médio', 'grande' => 'Grande'];
                            foreach ($portes as $valor => $label):
                            ?>
                                <option value="<?= $valor ?>" <?= $animal->__get('porte') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="cor" class="form-label">Cor</label>
                        <input type="text" class="form-control" id="cor" name="cor"
                            placeholder="Ex: Caramelo, Preto"
                            value="<?= htmlspecialchars($animal->__get('cor') ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao"
                            placeholder="Ex: São Paulo - SP"
                            value="<?= htmlspecialchars($animal->__get('localizacao') ?? '') ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="castrado" name="castrado" value="1"
                                <?= $animal->__get('castrado') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="castrado">Castrado</label>
                        </div>
                    </div>
                </div>

                <!-- Descrição -->
                <h5 class="mb-3 text-primary">Descrição</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"
                            placeholder="Descreva o temperamento, histórico e outras informações relevantes do animal."><?= htmlspecialchars($animal->__get('descricao') ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Status -->
                <h5 class="mb-3 text-primary">Status</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status do Animal</label>
                        <select class="form-select" id="status" name="status">
                            <?php
                            $statusOpcoes = [
                                'disponivel'    => 'Disponível',
                                'reservado'     => 'Reservado',
                                'em_tratamento' => 'Em Tratamento',
                                'adotado'       => 'Adotado',
                            ];
                            foreach ($statusOpcoes as $valor => $label):
                            ?>
                                <option value="<?= $valor ?>" <?= $animal->__get('status') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Botões -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="/dashboard/animal/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>