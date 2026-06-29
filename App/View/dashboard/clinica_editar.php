<?php
$clinica = $this->getView()->clinica;
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">Editar Clínica</h1>

        <a href="/dashboard/clinica/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <form method="POST" action="/dashboard/clinica/alterar">

                <input type="hidden"
                    name="cln_id"
                    value="<?= htmlspecialchars($clinica->__get('cln_id')) ?>">

                <!-- Dados da Clínica -->
                <h5 class="mb-3 text-primary">Dados da Clínica</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label for="cln_nome" class="form-label">
                            Nome da Clínica
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_nome"
                            name="cln_nome"
                            required
                            value="<?= htmlspecialchars($clinica->__get('cln_nome')) ?>">

                    </div>

                    <div class="col-md-3">

                        <label for="cln_cnpj" class="form-label">
                            CNPJ
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_cnpj"
                            name="cln_cnpj"
                            value="<?= htmlspecialchars($clinica->__get('cln_cnpj')) ?>">

                    </div>

                </div>

                <!-- Endereço -->
                <h5 class="mb-3 text-primary">Endereço</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-2">

                        <label for="cln_cep" class="form-label">
                            CEP
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_cep"
                            name="cln_cep"
                            value="<?= htmlspecialchars($clinica->__get('cln_cep')) ?>">

                    </div>

                    <div class="col-md-2">

                        <label for="cln_estado" class="form-label">
                            Estado
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_estado"
                            name="cln_estado"
                            value="<?= htmlspecialchars($clinica->__get('cln_estado')) ?>">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_cidade" class="form-label">
                            Cidade
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_cidade"
                            name="cln_cidade"
                            value="<?= htmlspecialchars($clinica->__get('cln_cidade')) ?>">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_bairro" class="form-label">
                            Bairro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_bairro"
                            name="cln_bairro"
                            value="<?= htmlspecialchars($clinica->__get('cln_bairro')) ?>">

                    </div>

                    <div class="col-md-6">

                        <label for="cln_logradouro" class="form-label">
                            Logradouro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_logradouro"
                            name="cln_logradouro"
                            value="<?= htmlspecialchars($clinica->__get('cln_logradouro')) ?>">

                    </div>

                    <div class="col-md-2">

                        <label for="cln_numero" class="form-label">
                            Número
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_numero"
                            name="cln_numero"
                            value="<?= htmlspecialchars($clinica->__get('cln_numero')) ?>">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_complemento" class="form-label">
                            Complemento
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_complemento"
                            name="cln_complemento"
                            value="<?= htmlspecialchars($clinica->__get('cln_complemento')) ?>">

                    </div>

                </div>

                <!-- Contato -->
                <h5 class="mb-3 text-primary">Contato</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-4">

                        <label for="cln_tel1" class="form-label">
                            Telefone 1
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_tel1"
                            name="cln_tel1"
                            value="<?= htmlspecialchars($clinica->__get('cln_tel1')) ?>">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_tel2" class="form-label">
                            Telefone 2
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_tel2"
                            name="cln_tel2"
                            value="<?= htmlspecialchars($clinica->__get('cln_tel2')) ?>">

                    </div>

                </div>

                <!-- Botões -->
                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>

                    <a href="/dashboard/clinica/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>