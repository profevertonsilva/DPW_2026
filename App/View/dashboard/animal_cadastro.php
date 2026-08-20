<style>
    .page-header-animal h1 { font-family:'Poppins',sans-serif; font-weight:700; font-size:1.6rem; color:#2D2D2D; }
    .page-header-animal p  { color:#9B9B9B; font-size:0.88rem; margin-bottom:0; }
    .btn-voltar { background:rgba(0,0,0,0.05); border:none; color:#6B7280; font-family:'Poppins',sans-serif; font-weight:600; font-size:0.85rem; padding:10px 18px; border-radius:12px; transition:all 0.2s; }
    .btn-voltar:hover { background:rgba(0,0,0,0.09); color:#4F4F4F; }
    .card-form { border:none; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.05); }
    .card-form .card-body { padding:32px; }
    .section-title { font-family:'Poppins',sans-serif; font-weight:700; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.08em; color:#6FCF97; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid rgba(111,207,151,0.2); }
    .form-label { font-family:'Poppins',sans-serif; font-size:0.8rem; font-weight:600; color:#6B7280; margin-bottom:6px; }
    .form-control, .form-select { border:1.5px solid #EFEFEF; border-radius:12px; font-size:0.875rem; color:#2D2D2D; padding:10px 14px; transition:all 0.2s; background-color:#FAFAFA; }
    .form-control:focus, .form-select:focus { border-color:#6FCF97; box-shadow:0 0 0 3px rgba(111,207,151,0.15); background-color:white; }
    .form-control::placeholder { color:#C4C4C4; }
    .form-check-input:checked { background-color:#6FCF97; border-color:#6FCF97; }
    .form-check-label { font-family:'Poppins',sans-serif; font-size:0.85rem; color:#4F4F4F; font-weight:500; }
    .foto-preview-wrap { margin-top:10px; display:none; }
    .foto-preview-wrap img { width:100px; height:100px; object-fit:cover; border-radius:14px; border:2px solid rgba(111,207,151,0.3); }
    .btn-salvar { background-color:#6FCF97; border:none; color:white; font-family:'Poppins',sans-serif; font-weight:700; font-size:0.88rem; padding:12px 28px; border-radius:12px; transition:all 0.2s; }
    .btn-salvar:hover { background-color:#5BBF87; color:white; transform:translateY(-1px); box-shadow:0 6px 16px rgba(111,207,151,0.35); }
    .btn-cancelar { background:rgba(0,0,0,0.05); border:none; color:#6B7280; font-family:'Poppins',sans-serif; font-weight:600; font-size:0.88rem; padding:12px 24px; border-radius:12px; transition:all 0.2s; }
    .btn-cancelar:hover { background:rgba(0,0,0,0.09); color:#4F4F4F; }
    .divider { border-top:1.5px solid #F5F5F5; margin:28px 0; }
</style>

<div class="container-fluid">
    <div class="page-header-animal d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Cadastrar Animal</h1>
        </div>
        <a href="/dashboard/animal/listar" class="btn btn-voltar">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="card card-form">
        <div class="card-body">
            <form method="POST" action="/dashboard/animal/cadastrar" enctype="multipart/form-data">

                <div class="section-title">Identificação</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nome" placeholder="Ex: Max, Luna, Thor" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" name="data_nascimento">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sexo <span class="text-danger">*</span></label>
                        <select class="form-select" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="m">Macho</option>
                            <option value="f">Fêmea</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Porte</label>
                        <select class="form-select" name="porte">
                            <option value="">Selecione</option>
                            <option value="pequeno">Pequeno</option>
                            <option value="medio">Médio</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Espécie</label>
                        <select class="form-select" id="fk_especie_id" name="fk_especie_id">
                            <option value="">Selecione a espécie</option>
                            <?php foreach ($this->getView()->especies as $especie): ?>
                                <option value="<?= $especie->__get('id') ?>">
                                    <?= htmlspecialchars($especie->__get('nome')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" id="racaContainer" style="display:none;">
                        <label class="form-label">Raça</label>
                        <select class="form-select" id="fk_raca_id" name="fk_raca_id">
                            <option value="">Selecione a raça</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cor / Pelagem</label>
                        <input type="text" class="form-control" name="cor" placeholder="Ex: Caramelo, Preto e Branco">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Localização</label>
                        <input type="text" class="form-control" name="localizacao" placeholder="Ex: São Paulo - SP">
                    </div>
                    <div class="col-md-2 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="castrado" value="1" id="castrado">
                            <label class="form-check-label" for="castrado">Castrado</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto do Animal</label>
                        <input type="file" class="form-control" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted" style="font-size:0.75rem;">JPG, PNG ou WEBP. Máx. 5MB.</small>
                        <div class="foto-preview-wrap" id="fotoPreview">
                            <img id="fotoPreviewImg" src="" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-title">Descrição</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-12">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="3"
                                  placeholder="Descreva o temperamento, histórico e outras informações relevantes."></textarea>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-title">Status</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Status do Animal</label>
                        <select class="form-select" name="status">
                            <option value="disponivel" selected>Disponível</option>
                            <option value="reservado">Reservado</option>
                            <option value="em_tratamento">Em Tratamento</option>
                            <option value="adotado">Adotado</option>
                        </select>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-salvar">
                        <i class="fas fa-save me-2"></i> Salvar Animal
                    </button>
                    <a href="/dashboard/animal/listar" class="btn btn-cancelar">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Carregar raças ao mudar espécie
document.getElementById('fk_especie_id').addEventListener('change', function() {
    const especieId = this.value;
    const racaContainer = document.getElementById('racaContainer');
    const racaSelect = document.getElementById('fk_raca_id');

    if (!especieId) {
        racaContainer.style.display = 'none';
        racaSelect.innerHTML = '<option value="">Selecione a raça</option>';
        return;
    }

    // Requisição AJAX
    fetch('/dashboard/raca/por-especie?fk_especie_id=' + encodeURIComponent(especieId))
        .then(response => response.json())
        .then(racas => {
            racaSelect.innerHTML = '<option value="">Selecione a raça</option>';
            
            if (racas.length > 0) {
                racas.forEach(raca => {
                    const option = document.createElement('option');
                    option.value = raca.id;
                    option.textContent = raca.nome;
                    racaSelect.appendChild(option);
                });
                racaContainer.style.display = 'block';
            } else {
                racaContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar raças:', error);
            racaContainer.style.display = 'none';
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