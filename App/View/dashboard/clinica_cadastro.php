<?php
/**
 * AmigoPet - Módulo de Clínicas (Formulário de Cadastro)
 * Localização: ~/App/View/dashboard/clinica_cadastro.php
 */
?>

<style>
    /* Alinhamento padrão para não quebrar com o menu lateral fixo */
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

    .btn-main-success {
        background-color: #6FCF97 !important;
        color: white !important;
        font-weight: 600;
        border: none !important;
    }
    .btn-main-success:hover { background-color: #5bba84 !important; }
</style>

<div class="amigopet-wrapper-listar-fix">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Cadastrar Clínica 🏥</h1>
            <p class="text-muted mb-0">Adicione uma nova clínica veterinária parceira ao sistema.</p>
        </div>
    </div>

    <?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (isset($_SESSION['erro_cadastro_clinica'])): 
    ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <strong>Atenção:</strong> <?= $_SESSION['erro_cadastro_clinica']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php 
        unset($_SESSION['erro_cadastro_clinica']); // Limpa a mensagem após exibir
    endif; 
    ?>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <form action="/dashboard/clinica/cadastrar" method="POST">
                
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label for="cln_nome" class="form-label fw-medium">Nome da Clínica</label>
                        <input type="text" class="form-control" id="cln_nome" name="cln_nome" placeholder="Ex: Clínica Veterinária São Francisco" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="cln_cnpj" class="form-label fw-medium">CNPJ</label>
                        <input type="text" class="form-control" id="cln_cnpj" name="cln_cnpj" placeholder="00.000.000/0000-00" maxlength="18" required>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label for="cln_cep" class="form-label fw-medium">CEP</label>
                        <input type="text" class="form-control" id="cln_cep" name="cln_cep" placeholder="00000-000" maxlength="9" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="cln_logradouro" class="form-label fw-medium">Logradouro (Rua/Avenida)</label>
                        <input type="text" class="form-control" id="cln_logradouro" name="cln_logradouro" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="cln_numero" class="form-label fw-medium">Número</label>
                        <input type="text" class="form-control" id="cln_numero" name="cln_numero" required>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="cln_bairro" class="form-label fw-medium">Bairro</label>
                        <input type="text" class="form-control" id="cln_bairro" name="cln_bairro" required>
                    </div>
                    <div class="mb-3 col-md-5">
                        <label for="cln_cidade" class="form-label fw-medium">Cidade</label>
                        <input type="text" class="form-control" id="cln_cidade" name="cln_cidade" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="cln_estado" class="form-label fw-medium">Estado (UF)</label>
                        <input type="text" class="form-control" id="cln_estado" name="cln_estado" maxlength="2" required>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="cln_complemento" class="form-label fw-medium">Complemento</label>
                        <input type="text" class="form-control" id="cln_complemento" name="cln_complemento" placeholder="Ex: Sala 02, Bloco B">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="cln_tel1" class="form-label fw-medium">Telefone Principal</label>
                        <input type="text" class="form-control" id="cln_tel1" name="cln_tel1" placeholder="(00) 00000-0000" maxlength="15" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="cln_tel2" class="form-label fw-medium">Telefone Secundário</label>
                        <input type="text" class="form-control" id="cln_tel2" name="cln_tel2" placeholder="(00) 0000-0000" maxlength="15">
                    </div>
                </div>

                <div class="d-flex mt-3">
                    <button type="submit" class="btn btn-main-success px-4 py-2">Salvar Cadastro</button>
                    <a href="/dashboard/clinica/listar" class="btn btn-light border ms-2 px-4 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputCnpj = document.getElementById("cln_cnpj");
    const inputCep = document.getElementById("cln_cep");
    const inputTel1 = document.getElementById("cln_tel1");
    const inputTel2 = document.getElementById("cln_tel2");

    // MÁSCARA DE CNPJ
    inputCnpj.addEventListener("input", function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
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
            document.getElementById("cln_logradouro").value = "Buscando...";
            document.getElementById("cln_bairro").value = "...";
            document.getElementById("cln_cidade").value = "...";
            document.getElementById("cln_estado").value = "...";

            fetch(`https://viacep.com.br/ws/${num}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        document.getElementById("cln_logradouro").value = dados.logradouro;
                        document.getElementById("cln_bairro").value = dados.bairro;
                        document.getElementById("cln_cidade").value = dados.localidade;
                        document.getElementById("cln_estado").value = dados.uf;
                        document.getElementById("cln_numero").focus();
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
        document.getElementById("cln_logradouro").value = "";
        document.getElementById("cln_bairro").value = "";
        document.getElementById("cln_cidade").value = "";
        document.getElementById("cln_estado").value = "";
    }
});
</script>