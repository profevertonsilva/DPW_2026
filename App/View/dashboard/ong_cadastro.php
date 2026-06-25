<?php
/**
 * AmigoPet - Módulo de ONGs (Cadastro - Versão Nativa e Blindada)
 * Localização: ~/App/View/dashboard/ong_cadastro.php
 */
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
            <h1 class="h3 mb-1">Cadastrar Nova ONG 🏢</h1>
            <p class="text-muted mb-0">Insira os dados da instituição parceira para liberar o gerenciamento de animais.</p>
        </div>
    </div>

    <?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (isset($_SESSION['erro_cadastro_ong'])): 
    ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <strong>Atenção:</strong> <?= $_SESSION['erro_cadastro_ong']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php 
        unset($_SESSION['erro_cadastro_ong']); // Limpa a mensagem após exibir
    endif; 
    ?>

    <div class="card shadow-sm border-0 p-4" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-body p-0">
            <form action="/dashboard/ong/salvar" method="POST">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="ong_nome" class="form-label fw-medium">Nome da ONG</label>
                        <input type="text" class="form-control" id="ong_nome" name="ong_nome" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="ong_cnpj" class="form-label fw-medium">CNPJ</label>
                        <input type="text" class="form-control" id="ong_cnpj" name="ong_cnpj" placeholder="00.000.000/0000-00" maxlength="18" required>
                    </div>
                    <div class="mb-3 col-md-2">
                        <label for="ong_qnt_animais" class="form-label fw-medium">Qtd Animais</label>
                        <input type="number" class="form-control" id="ong_qnt_animais" name="ong_qnt_animais" value="0">
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-3">
                        <label for="ong_cep" class="form-label fw-medium">CEP</label>
                        <input type="text" class="form-control" id="ong_cep" name="ong_cep" placeholder="00000-000" maxlength="9" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="ong_logradouro" class="form-label fw-medium">Logradouro (Rua)</label>
                        <input type="text" class="form-control" id="ong_logradouro" name="ong_logradouro" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="ong_numero" class="form-label fw-medium">Número</label>
                        <input type="text" class="form-control" id="ong_numero" name="ong_numero" required>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="ong_bairro" class="form-label fw-medium">Bairro</label>
                        <input type="text" class="form-control" id="ong_bairro" name="ong_bairro" required>
                    </div>
                    <div class="mb-3 col-md-5">
                        <label for="ong_cidade" class="form-label fw-medium">Cidade</label>
                        <input type="text" class="form-control" id="ong_cidade" name="ong_cidade" required>
                    </div>
                    <div class="mb-3 col-md-3">
                        <label for="ong_estado" class="form-label fw-medium">Estado (UF)</label>
                        <input type="text" class="form-control" id="ong_estado" name="ong_estado" maxlength="2" required>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="ong_complemento" class="form-label fw-medium">Complemento</label>
                        <input type="text" class="form-control" id="ong_complemento" name="ong_complemento">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="ong_tel1" class="form-label fw-medium">Telefone Principal</label>
                        <input type="text" class="form-control" id="ong_tel1" name="ong_tel1" placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="ong_tel2" class="form-label fw-medium">Telefone Secundário</label>
                        <input type="text" class="form-control" id="ong_tel2" name="ong_tel2" placeholder="(00) 00000-0000" maxlength="15">
                    </div>
                </div>

                <div class="d-flex mt-3">
                    <button type="submit" class="btn btn-main-success px-4 py-2">Salvar ONG</button>
                    <a href="/dashboard/ong/listar" class="btn btn-light border ms-2 px-4 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputCnpj = document.getElementById("ong_cnpj");
    const inputCep = document.getElementById("ong_cep");
    const inputTel1 = document.getElementById("ong_tel1");
    const inputTel2 = document.getElementById("ong_tel2");

    // MÁSCARA DE CNPJ REALTIME (Insere . / - conforme digita)
    inputCnpj.addEventListener("input", function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
    });

    // MÁSCARA DE TELEFONE DINÂMICA (8 ou 9 dígitos)
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

    // MÁSCARA DO CEP + BUSCA AUTOMÁTICA
    inputCep.addEventListener("input", function(e) {
        let num = e.target.value.replace(/\D/g, "");
        e.target.value = num.replace(/^(\d{5})(\d{3})$/, "$1-$2");

        if (num.length === 8) {
            document.getElementById("ong_logradouro").value = "Buscando...";
            document.getElementById("ong_bairro").value = "...";
            document.getElementById("ong_cidade").value = "...";
            document.getElementById("ong_estado").value = "...";

            fetch(`https://viacep.com.br/ws/${num}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        document.getElementById("ong_logradouro").value = dados.logradouro;
                        document.getElementById("ong_bairro").value = dados.bairro;
                        document.getElementById("ong_cidade").value = dados.localidade;
                        document.getElementById("ong_estado").value = dados.uf;
                        document.getElementById("ong_numero").focus();
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
        document.getElementById("ong_logradouro").value = "";
        document.getElementById("ong_bairro").value = "";
        document.getElementById("ong_cidade").value = "";
        document.getElementById("ong_estado").value = "";
    }
});
</script>