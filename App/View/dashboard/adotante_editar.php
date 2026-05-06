<?php
$adotante = $this->getView()->adotante;
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Adotante</h1>
        <a href="/dashboard/adotante/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="/dashboard/adotante/alterar">
                <input type="hidden" name="id" value="<?= htmlspecialchars($adotante->__get('adt_id')) ?>">

                <!-- Dados Pessoais -->
                <h5 class="mb-3 text-primary">Dados Pessoais</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required
                               value="<?= htmlspecialchars($adotante->__get('adt_nome')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14"
                               placeholder="000.000.000-00"
                               value="<?= htmlspecialchars($adotante->__get('adt_cpf')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                               value="<?= htmlspecialchars($adotante->__get('adt_dn')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="telefone_1" class="form-label">Telefone 1</label>
                        <input type="text" class="form-control" id="telefone_1" name="telefone_1"
                               placeholder="(00) 00000-0000"
                               value="<?= htmlspecialchars($adotante->__get('adt_tel1')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="telefone_2" class="form-label">Telefone 2 <small class="text-muted">(opcional)</small></label>
                        <input type="text" class="form-control" id="telefone_2" name="telefone_2"
                               placeholder="(00) 00000-0000"
                               value="<?= htmlspecialchars($adotante->__get('adt_tel2')) ?>">
                    </div>
                </div>

                <!-- Endereço -->
                <h5 class="mb-3 text-primary">Endereço</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" maxlength="9"
                               placeholder="00000-000"
                               value="<?= htmlspecialchars($adotante->__get('adt_cep')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="logradouro" class="form-label">Logradouro</label>
                        <input type="text" class="form-control" id="logradouro" name="logradouro" readonly
                               value="<?= htmlspecialchars($adotante->__get('adt_logradouro')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero"
                               value="<?= htmlspecialchars($adotante->__get('adt_numero')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento"
                               value="<?= htmlspecialchars($adotante->__get('adt_complemento')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" readonly
                               value="<?= htmlspecialchars($adotante->__get('adt_bairro')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" readonly
                               value="<?= htmlspecialchars($adotante->__get('adt_cidade')) ?>">
                    </div>
                    <div class="col-md-1">
                        <label for="estado" class="form-label">UF</label>
                        <input type="text" class="form-control" id="estado" name="estado" readonly maxlength="2"
                               value="<?= htmlspecialchars($adotante->__get('adt_estado')) ?>">
                    </div>
                </div>

                <!-- Status -->
                <h5 class="mb-3 text-primary">Status</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status do Adotante</label>
                        <select class="form-select" id="status" name="status">
                            <?php
                            $statusAtual = $adotante->__get('adt_status');
                            $opcoes = [
                                'pessimo'   => 'Péssimo',
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

                <!-- Botões -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="/dashboard/adotante/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// Máscara CPF: 000.000.000-00
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

// Máscara CEP: 00000-000
document.getElementById('cep').addEventListener('input', function(e) {
    var v = e.target.value.replace(/\D/g, '');
    v = v.substring(0, 8);
    if (v.length > 5) {
        v = v.replace(/^(\d{5})(\d{0,3})/, '$1-$2');
    }
    e.target.value = v;
});

// Máscara Telefone
function mascaraTelefone(campo) {
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

// ViaCEP
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
