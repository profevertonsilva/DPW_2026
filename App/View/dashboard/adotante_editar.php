<?php
/**
 * AmigoPet - Módulo de Adotantes (Edição)
 * Localização: ~/App/View/dashboard/adotante_editar.php
 */

// Captura o adotante enviado pelo Controller
$adotante = $this->getView()->adotante ?? null;
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
        color: #F2994A !important; /* Laranja secundário para subtítulos */
    }
    .btn-main-success {
        background-color: #6FCF97 !important;
        color: white !important;
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
    .form-control[readonly] {
        background-color: #FAF9F6 !important;
        opacity: 0.8;
    }
</style>

<div class="amigopet-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Adotante ✏️</h1>
            <p class="text-muted mb-0">Modifique as informações cadastrais e atualize a triagem do tutor.</p>
        </div>
        <a href="/dashboard/adotante/listar" class="btn btn-light border" style="color: #4F4F4F;">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <?php if (!$adotante || (method_exists($adotante, '__get') && empty($adotante->__get('adt_nome')))): ?>
        
    <?php endif; ?>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <form method="POST" action="/dashboard/adotante/alterar">
                <input type="hidden" name="id" value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_id') ?? '') : '' ?>">

                <h5 class="mb-3 section-title"><i class="fa-regular fa-user me-2"></i>Dados Pessoais</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nome" class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-none" id="nome" name="nome" required
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_nome') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cpf" class="form-label fw-medium">CPF</label>
                        <input type="text" class="form-control shadow-none" id="cpf" name="cpf" maxlength="14"
                               placeholder="000.000.000-00"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_cpf') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="data_nascimento" class="form-label fw-medium">Data de Nascimento</label>
                        <input type="date" class="form-control shadow-none" id="data_nascimento" name="data_nascimento"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_dn') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="telefone_1" class="form-label fw-medium">Telefone Principal</label>
                        <input type="text" class="form-control shadow-none" id="telefone_1" name="telefone_1"
                               placeholder="(00) 00000-0000"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_tel1') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="telefone_2" class="form-label fw-medium">Telefone Secundário <small class="text-muted">(Opcional)</small></label>
                        <input type="text" class="form-control shadow-none" id="telefone_2" name="telefone_2"
                               placeholder="(00) 00000-0000"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_tel2') ?? '') : '' ?>">
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <h5 class="mb-3 section-title"><i class="fa-solid fa-location-dot me-2"></i>Localização e Endereço</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="cep" class="form-label fw-medium">CEP</label>
                        <input type="text" class="form-control shadow-none" id="cep" name="cep" maxlength="9"
                               placeholder="00000-000"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_cep') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="logradouro" class="form-label fw-medium">Logradouro</label>
                        <input type="text" class="form-control shadow-none" id="logradouro" name="logradouro" readonly
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_logradouro') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="numero" class="form-label fw-medium">Número</label>
                        <input type="text" class="form-control shadow-none" id="numero" name="numero"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_numero') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="complemento" class="form-label fw-medium">Complemento</label>
                        <input type="text" class="form-control shadow-none" id="complemento" name="complemento"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_complemento') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label fw-medium">Bairro</label>
                        <input type="text" class="form-control shadow-none" id="bairro" name="bairro" readonly
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_bairro') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cidade" class="form-label fw-medium">Cidade</label>
                        <input type="text" class="form-control shadow-none" id="cidade" name="cidade" readonly
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_cidade') ?? '') : '' ?>">
                    </div>
                    <div class="col-md-1">
                        <label for="estado" class="form-label fw-medium">UF</label>
                        <input type="text" class="form-control shadow-none text-center" id="estado" name="estado" readonly maxlength="2"
                               value="<?= (is_object($adotante) && method_exists($adotante, '__get')) ? htmlspecialchars($adotante->__get('adt_estado') ?? '') : '' ?>">
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <h5 class="mb-3 section-title"><i class="fa-solid fa-ranking-star me-2"></i>Triagem de Segurança</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-medium">Avaliação de Perfil</label>
                        <select class="form-select shadow-none" id="status" name="status">
                            <?php
                            $statusAtual = (is_object($adotante) && method_exists($adotante, '__get')) ? ($adotante->__get('adt_status') ?? '') : '';
                            $opcoes = [
                                'pessimo'   => 'Péssimo (Bloqueado)',
                                'regular'   => 'Regular',
                                'bom'       => 'Bom',
                                'muito bom' => 'Muito Bom',
                                'excelente' => 'Excelente',
                            ];
                            foreach ($opcoes as $valor => $label):
                            ?>
                                <option value="<?= $valor ?>"<?= ($statusAtual === $valor) ? ' selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="/dashboard/adotante/listar" class="btn btn-light border px-4" style="color: #4F4F4F;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-main-success px-4">
                        <i class="fas fa-save me-2"></i>Salvar Alterações
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// Mantém as suas regras de validação nativas e funcionais em Vanilla JS
document.getElementById('cpf').addEventListener('input', function(e) {
    var v = e.target.value.replace(/\D/g, '');
    v = v.substring(0, 11);
    if (v.length > 9) {
        v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    } else if (v.length > 6) {
        v = v.replace(/^(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    } else if (v.length > 3) {
        v = v.replace(/^(\d{3})(\d{0,3})/, '$1.$2');
    }
    e.target.value = v;
});

document.getElementById('cep').addEventListener('input', function(e) {
    var v = e.target.value.replace(/\D/g, '');
    v = v.substring(0, 8);
    if (v.length > 5) {
        v = v.replace(/^(\d{5})(\d{0,3})/, '$1-$2');
    }
    e.target.value = v;
});

function mascaraTelefone(campo) {
    if (!campo) return;
    campo.addEventListener('input', function(e) {
        var v = e.target.value.replace(/\D/g, '');
        v = v.substring(0, 11);
        if (v.length > 10) {
            v = v.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (v.length > 6) {
            v = v.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (v.length > 2) {
            v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (v.length > 0) {
            v = v.replace(/^(\d{0,2})/, '($1');
        }
        e.target.value = v;
    });
}
mascaraTelefone(document.getElementById('telefone_1'));
mascaraTelefone(document.getElementById('telefone_2'));

// Integração nativa com a API do ViaCEP
document.getElementById('cep').addEventListener('blur', function() {
    var cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.erro) return;
            document.getElementById('logradouro').value = data.logradouro  || '';
            document.getElementById('bairro').value     = data.bairro      || '';
            document.getElementById('cidade').value     = data.localidade   || '';
            document.getElementById('estado').value     = data.uf           || '';
            document.getElementById('numero').focus();
        })
        .catch(function() {});
});
</script>