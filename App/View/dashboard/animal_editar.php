<?php
$animal          = $this->getView()->animal;
$especies        = $this->getView()->especies;
$racas           = $this->getView()->racas           ?? [];
$racasVinculadas = $this->getView()->racasVinculadas ?? [];
$fotoAtual       = $animal->__get('foto') ?? '';
?>
<style>
    .page-header-animal h1 { font-family:'Poppins',sans-serif; font-weight:700; font-size:1.6rem; color:#2D2D2D; }
    .page-header-animal p  { color:#9B9B9B; font-size:0.88rem; margin-bottom:0; }
    .btn-voltar { background:rgba(0,0,0,0.05); border:none; color:#6B7280; font-family:'Poppins',sans-serif; font-weight:600; font-size:0.85rem; padding:10px 18px; border-radius:12px; transition:all 0.2s; }
    .btn-voltar:hover { background:rgba(0,0,0,0.09); color:#4F4F4F; }
    .card-form, .card-racas { border:none; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.05); }
    .card-form .card-body { padding:32px; }
    .card-racas { margin-top:24px; }
    .card-racas .card-header { background:white; border-bottom:2px solid rgba(111,207,151,0.2); border-radius:20px 20px 0 0 !important; padding:20px 32px; }
    .card-racas .card-header h5 { font-family:'Poppins',sans-serif; font-weight:700; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:#6FCF97; margin:0; }
    .card-racas .card-body { padding:28px 32px; }
    .section-title { font-family:'Poppins',sans-serif; font-weight:700; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:#6FCF97; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid rgba(111,207,151,0.2); }
    .form-label { font-family:'Poppins',sans-serif; font-size:0.8rem; font-weight:600; color:#6B7280; margin-bottom:6px; }
    .form-control, .form-select { border:1.5px solid #EFEFEF; border-radius:12px; font-size:0.875rem; color:#2D2D2D; padding:10px 14px; transition:all 0.2s; background-color:#FAFAFA; }
    .form-control:focus, .form-select:focus { border-color:#6FCF97; box-shadow:0 0 0 3px rgba(111,207,151,0.15); background-color:white; }
    .form-control::placeholder { color:#C4C4C4; }
    .form-check-input:checked { background-color:#6FCF97; border-color:#6FCF97; }
    .form-check-label { font-family:'Poppins',sans-serif; font-size:0.85rem; color:#4F4F4F; font-weight:500; }
    .foto-atual-wrap img { width:90px; height:90px; object-fit:cover; border-radius:14px; border:2px solid rgba(111,207,151,0.3); }
    .foto-preview-wrap { margin-top:10px; display:none; }
    .foto-preview-wrap img { width:90px; height:90px; object-fit:cover; border-radius:14px; border:2px dashed #6FCF97; }
    .raca-check-item { background:#FAFAFA; border:1.5px solid #EFEFEF; border-radius:12px; padding:10px 14px; transition:all 0.2s; cursor:pointer; }
    .raca-check-item:has(.form-check-input:checked) { background:rgba(111,207,151,0.08); border-color:#6FCF97; }
    .raca-check-item .form-check-label, .raca-check-item .form-check-input { cursor:pointer; }
    .btn-salvar { background-color:#6FCF97; border:none; color:white; font-family:'Poppins',sans-serif; font-weight:700; font-size:0.88rem; padding:12px 28px; border-radius:12px; transition:all 0.2s; }
    .btn-salvar:hover { background-color:#5BBF87; color:white; transform:translateY(-1px); box-shadow:0 6px 16px rgba(111,207,151,0.35); }
    .btn-cancelar { background:rgba(0,0,0,0.05); border:none; color:#6B7280; font-family:'Poppins',sans-serif; font-weight:600; font-size:0.88rem; padding:12px 24px; border-radius:12px; transition:all 0.2s; }
    .btn-cancelar:hover { background:rgba(0,0,0,0.09); color:#4F4F4F; }
    .divider { border-top:1.5px solid #F5F5F5; margin:28px 0; }
    .empty-racas { background:#FAFAFA; border-radius:14px; padding:28px; text-align:center; color:#9B9B9B; font-family:'Poppins',sans-serif; font-size:0.85rem; }
</style>

<div class="container-fluid">

    <div class="page-header-animal d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Editar Animal</h1>
            <p>Atualize os dados de <strong style="color:#2D2D2D;"><?= htmlspecialchars($animal->__get('nome')) ?></strong>.</p>
        </div>
        <a href="/dashboard/animal/listar" class="btn btn-voltar">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="card card-form">
        <div class="card-body">
            <form method="POST" action="/dashboard/animal/alterar" enctype="multipart/form-data">
                <input type="hidden" name="id"         value="<?= htmlspecialchars($animal->__get('id')) ?>">
                <input type="hidden" name="foto_atual" value="<?= htmlspecialchars($fotoAtual) ?>">

                <div class="section-title">Identificação</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nome" required
                               value="<?= htmlspecialchars($animal->__get('nome')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" name="data_nascimento"
                               value="<?= htmlspecialchars($animal->__get('data_nascimento') ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sexo <span class="text-danger">*</span></label>
                        <select class="form-select" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="m" <?= $animal->__get('sexo') === 'm' ? 'selected' : '' ?>>♂ Macho</option>
                            <option value="f" <?= $animal->__get('sexo') === 'f' ? 'selected' : '' ?>>♀ Fêmea</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Porte</label>
                        <select class="form-select" name="porte">
                            <option value="">Selecione</option>
                            <?php foreach (['pequeno'=>'Pequeno','medio'=>'Médio','grande'=>'Grande'] as $v=>$l): ?>
                                <option value="<?= $v ?>" <?= $animal->__get('porte') === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Espécie</label>
                        <select class="form-select" id="fk_especie_id" name="fk_especie_id">
                            <option value="">Selecione a espécie</option>
                            <?php foreach ($especies as $especie): ?>
                                <option value="<?= $especie->__get('id') ?>"
                                    <?= (int)$animal->__get('fk_especie_id') === (int)$especie->__get('id') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($especie->__get('nome')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cor / Pelagem</label>
                        <input type="text" class="form-control" name="cor"
                               placeholder="Ex: Caramelo, Preto e Branco"
                               value="<?= htmlspecialchars($animal->__get('cor') ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Localização</label>
                        <input type="text" class="form-control" name="localizacao"
                               placeholder="Ex: São Paulo - SP"
                               value="<?= htmlspecialchars($animal->__get('localizacao') ?? '') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="castrado" value="1" id="castrado"
                                   <?= $animal->__get('castrado') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="castrado">Castrado</label>
                        </div>
                    </div>

                    <!-- Foto -->
                    <div class="col-md-4">
                        <label class="form-label">Foto do Animal</label>
                        <?php if ($fotoAtual): ?>
                            <div class="foto-atual-wrap mb-2">
                                <img src="/<?= htmlspecialchars($fotoAtual) ?>" alt="Foto atual">
                                <small class="d-block text-muted mt-1" style="font-size:0.72rem;">Foto atual</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted" style="font-size:0.75rem;">Deixe em branco para manter a foto atual. JPG, PNG ou WEBP.</small>
                        <div class="foto-preview-wrap" id="fotoPreview">
                            <img id="fotoPreviewImg" src="" alt="Nova foto">
                            <small class="d-block text-muted mt-1" style="font-size:0.72rem;">Nova foto</small>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-title">Descrição</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-12">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="3"
                                  placeholder="Descreva o temperamento, histórico e outras informações relevantes."><?= htmlspecialchars($animal->__get('descricao') ?? '') ?></textarea>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-title">Status</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Status do Animal</label>
                        <select class="form-select" name="status">
                            <?php foreach ([
                                'disponivel'    => 'Disponível',
                                'reservado'     => 'Reservado',
                                'em_tratamento' => 'Em Tratamento',
                                'adotado'       => 'Adotado',
                            ] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= $animal->__get('status') === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-salvar">
                        <i class="fas fa-save me-2"></i> Salvar Alterações
                    </button>
                    <a href="/dashboard/animal/listar" class="btn btn-cancelar">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Raças -->
    <div class="card card-racas">
        <div class="card-header">
            <h5>🦴 Raças Vinculadas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($racas)): ?>
                <div class="empty-racas">Nenhuma raça cadastrada para a espécie deste animal.</div>
            <?php else: ?>
                <form method="POST" action="/dashboard/animal-raca/sincronizar">
                    <input type="hidden" name="fk_animal_id" value="<?= (int) $animal->__get('id') ?>">
                    <div class="row g-2 mb-4">
                        <?php foreach ($racas as $raca): ?>
                            <div class="col-md-3 col-sm-4 col-6">
                                <label class="raca-check-item d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input mt-0" type="checkbox" name="fk_raca_id[]"
                                           value="<?= (int) $raca->__get('id') ?>"
                                           <?= in_array((int) $raca->__get('id'), $racasVinculadas, true) ? 'checked' : '' ?>>
                                    <span class="form-check-label"><?= htmlspecialchars($raca->__get('nome')) ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-salvar">
                        <i class="fas fa-save me-2"></i> Salvar Raças
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Atualizar raças ao mudar espécie
document.getElementById('fk_especie_id').addEventListener('change', function() {
    const especieId = this.value;
    const animalId = <?= (int) $animal->__get('id') ?>;

    if (!especieId) {
        // Nenhuma espécie selecionada
        location.reload();
        return;
    }

    // Requisição AJAX para obter raças
    fetch('/dashboard/raca/por-especie?fk_especie_id=' + encodeURIComponent(especieId))
        .then(response => response.json())
        .then(racas => {
            const card = document.querySelector('.card-racas .card-body');
            
            if (racas.length === 0) {
                card.innerHTML = '<div class="empty-racas">Nenhuma raça cadastrada para a espécie selecionada.</div>';
                return;
            }

            let html = '<form method="POST" action="/dashboard/animal-raca/sincronizar">';
            html += '<input type="hidden" name="fk_animal_id" value="' + animalId + '">';
            html += '<div class="row g-2 mb-4">';
            
            racas.forEach(raca => {
                html += '<div class="col-md-3 col-sm-4 col-6">';
                html += '<label class="raca-check-item d-flex align-items-center gap-2 mb-0">';
                html += '<input class="form-check-input mt-0" type="checkbox" name="fk_raca_id[]" value="' + raca.id + '">';
                html += '<span class="form-check-label">' + raca.nome + '</span>';
                html += '</label>';
                html += '</div>';
            });
            
            html += '</div>';
            html += '<button type="submit" class="btn btn-salvar">';
            html += '<i class="fas fa-save me-2"></i> Salvar Raças';
            html += '</button>';
            html += '</form>';
            
            card.innerHTML = html;
        })
        .catch(error => {
            console.error('Erro ao carregar raças:', error);
            const card = document.querySelector('.card-racas .card-body');
            card.innerHTML = '<div class="empty-racas">Erro ao carregar raças. Tente novamente.</div>';
        });
});

// Preview de foto
document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('fotoPreview');
    const img = document.getElementById('fotoPreviewImg');
    if (file) {
        img.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
</script>