<?php
/**
 * AmigoPet - Módulo de Veterinários (Formulário de Edição)
 * Localização: ~/App/View/dashboard/veterinario_editar.php
 *
 * TESTE DAS URLS:
 *   Acessar edição:  http://localhost:8080/dashboard/veterinario/editar?id=1
 *   Submit do form:  POST http://localhost:8080/dashboard/veterinario/alterar
 *   Cancelar:        http://localhost:8080/dashboard/veterinario/listar
 */
$vet = $this->getView()->veterinario ?? null;
?>

<style>
    .amigopet-wrapper-listar-fix {
        font-family: 'Inter', sans-serif;
        color: #4F4F4F;
        position: absolute !important;
        top: 70px !important;
        left: 260px !important;
        width: calc(100vw - 290px) !important; 
        min-height: calc(100vh - 70px) !important;
        padding: 30px !important;
        box-sizing: border-box !important;
        background-color: #f8f9fa !important;
        z-index: 5 !important;
    }
    
    .amigopet-wrapper-listar-fix h1 {
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
        color: white !important;
        font-weight: 600;
        border: none !important;
    }
    .btn-main-success:hover { background-color: #5bba84 !important; }

    .form-control:focus {
        border-color: #6FCF97 !important;
        box-shadow: 0 0 0 0.25rem rgba(111, 207, 151, 0.25) !important;
    }
</style>

<div class="amigopet-wrapper-listar-fix">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Editar Veterinário 📝</h1>
            <p class="text-muted mb-0">Atualize os dados do profissional médico selecionado.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">

            <?php if (!$vet): ?>
                <div class="alert alert-danger mb-0">Veterinário não encontrado. <a href="/dashboard/veterinario/listar">Voltar à listagem</a>.</div>
            <?php else: ?>

                <form action="/dashboard/veterinario/alterar" method="POST">
                    <!-- ID oculto obrigatório para o alterar() do controller -->
                    <input type="hidden" name="vet_id" value="<?= htmlspecialchars($vet->__get('vet_id') ?? '') ?>">

                    <h5 class="mb-3 section-title"><i class="fa-solid fa-user-doctor me-2"></i>Dados Profissionais</h5>
                    <div class="row">
                        <div class="mb-3 col-md-5">
                            <label for="vet_nome" class="form-label fw-medium">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_nome" name="vet_nome" required
                                   value="<?= htmlspecialchars($vet->__get('vet_nome') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="vet_cpf" class="form-label fw-medium">CPF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_cpf" name="vet_cpf" placeholder="000.000.000-00" maxlength="14" required
                                   value="<?= htmlspecialchars($vet->__get('vet_cpf') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="vet_crmv" class="form-label fw-medium">CRMV <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_crmv" name="vet_crmv" placeholder="Ex: 12345-SP" required
                                   value="<?= htmlspecialchars($vet->__get('vet_crmv') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="vet_dn" class="form-label fw-medium">Data de Nasc.</label>
                            <input type="date" class="form-control" id="vet_dn" name="vet_dn"
                                   value="<?= htmlspecialchars($vet->__get('vet_dn') ?? '') ?>">
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-3">

                    <h5 class="mb-3 section-title"><i class="fa-solid fa-map-location-dot me-2"></i>Endereço</h5>
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="vet_cep" class="form-label fw-medium">CEP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_cep" name="vet_cep" placeholder="00000-000" maxlength="9" required
                                   value="<?= htmlspecialchars($vet->__get('vet_cep') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="vet_logradouro" class="form-label fw-medium">Logradouro (Rua/Avenida)</label>
                            <input type="text" class="form-control" id="vet_logradouro" name="vet_logradouro" required
                                   value="<?= htmlspecialchars($vet->__get('vet_logradouro') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="vet_numero" class="form-label fw-medium">Número <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_numero" name="vet_numero" required
                                   value="<?= htmlspecialchars($vet->__get('vet_numero') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="vet_bairro" class="form-label fw-medium">Bairro</label>
                            <input type="text" class="form-control" id="vet_bairro" name="vet_bairro" required
                                   value="<?= htmlspecialchars($vet->__get('vet_bairro') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-5">
                            <label for="vet_cidade" class="form-label fw-medium">Cidade</label>
                            <input type="text" class="form-control" id="vet_cidade" name="vet_cidade" required
                                   value="<?= htmlspecialchars($vet->__get('vet_cidade') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="vet_estado" class="form-label fw-medium">Estado (UF)</label>
                            <input type="text" class="form-control" id="vet_estado" name="vet_estado" maxlength="2" required
                                   value="<?= htmlspecialchars($vet->__get('vet_estado') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="vet_complemento" class="form-label fw-medium">Complemento</label>
                            <input type="text" class="form-control" id="vet_complemento" name="vet_complemento" placeholder="Ex: Bloco C, Apto 12"
                                   value="<?= htmlspecialchars($vet->__get('vet_complemento') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="vet_tel1" class="form-label fw-medium">Telefone Principal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vet_tel1" name="vet_tel1" placeholder="(00) 00000-0000" maxlength="15" required
                                   value="<?= htmlspecialchars($vet->__get('vet_tel1') ?? '') ?>">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="vet_tel2" class="form-label fw-medium">Telefone Secundário</label>
                            <input type="text" class="form-control" id="vet_tel2" name="vet_tel2" placeholder="(00) 0000-0000" maxlength="15"
                                   value="<?= htmlspecialchars($vet->__get('vet_tel2') ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex mt-3">
                        <button type="submit" class="btn btn-main-success px-4 py-2">Atualizar Veterinário</button>
                        <a href="/dashboard/veterinario/listar" class="btn btn-light border ms-2 px-4 py-2">Cancelar</a>
                    </div>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputCpf  = document.getElementById("vet_cpf");
    const inputCep  = document.getElementById("vet_cep");
    const inputTel1 = document.getElementById("vet_tel1");
    const inputTel2 = document.getElementById("vet_tel2");

    const dispararFormatacaoInicial = (input) => {
        if (input) input.dispatchEvent(new Event('input'));
    };

    // MÁSCARA DE CPF
    inputCpf.addEventListener("input", function(e) {
        let v = e.target.value.replace(/\D/g, '').substring(0, 11);
        if (v.length > 9) {
            v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        } else if (v.length > 6) {
            v = v.replace(/^(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
        } else if (v.length > 3) {
            v = v.replace(/^(\d{3})(\d{0,3})/, '$1.$2');
        }
        e.target.value = v;
    });

    // MÁSCARA DE TELEFONE
    const aplicarMascaraTelefone = (input) => {
        input.addEventListener("input", function(e) {
            let tel = e.target.value.replace(/\D/g, "");
            if (tel.length > 10) {
                e.target.value = tel.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
            } else {
                e.target.value = tel.replace(/^(\d{2})(\d{4})(\d{4})$/, "($1) $2-$3");
            }
        });
    };
    aplicarMascaraTelefone(inputTel1);
    aplicarMascaraTelefone(inputTel2);

    // MÁSCARA DO CEP + BUSCA AUTOMÁTICA VIA-CEP
    inputCep.addEventListener("input", function(e) {
        let num = e.target.value.replace(/\D/g, "");
        e.target.value = num.replace(/^(\d{5})(\d{3})$/, "$1-$2");

        if (num.length === 8) {
            document.getElementById("vet_logradouro").value = "Buscando...";
            document.getElementById("vet_bairro").value = "...";
            document.getElementById("vet_cidade").value = "...";
            document.getElementById("vet_estado").value = "...";

            fetch(`https://viacep.com.br/ws/${num}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        document.getElementById("vet_logradouro").value = dados.logradouro;
                        document.getElementById("vet_bairro").value = dados.bairro;
                        document.getElementById("vet_cidade").value = dados.localidade;
                        document.getElementById("vet_estado").value = dados.uf;
                        document.getElementById("vet_numero").focus();
                    } else {
                        alert("CEP não encontrado.");
                        limparCamposCep();
                    }
                })
                .catch(() => {
                    alert("Erro ao buscar CEP.");
                    limparCamposCep();
                });
        }
    });

    function limparCamposCep() {
        document.getElementById("vet_logradouro").value = "";
        document.getElementById("vet_bairro").value = "";
        document.getElementById("vet_cidade").value = "";
        document.getElementById("vet_estado").value = "";
    }

    // Formata os dados que vieram do banco ao carregar a página
    dispararFormatacaoInicial(inputCpf);
    dispararFormatacaoInicial(inputCep);
    dispararFormatacaoInicial(inputTel1);
    dispararFormatacaoInicial(inputTel2);
});
</script>