<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">Cadastro de Clínica</h1>

        <a href="/dashboard/clinica/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <form method="POST" action="/dashboard/clinica/cadastrar">

                <!-- Dados da Clínica -->
                <h5 class="mb-3 text-primary">Dados da Clínica</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label for="cln_nome" class="form-label">
                            Nome da Clínica <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_nome"
                            name="cln_nome"
                            required>

                    </div>

                    <div class="col-md-3">

                        <label for="cln_cnpj" class="form-label">
                            CNPJ
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_cnpj"
                            name="cln_cnpj">

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
                            name="cln_cep">

                    </div>

                    <div class="col-md-2">

                        <label for="cln_estado" class="form-label">
                            Estado
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_estado"
                            name="cln_estado">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_cidade" class="form-label">
                            Cidade
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_cidade"
                            name="cln_cidade">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_bairro" class="form-label">
                            Bairro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_bairro"
                            name="cln_bairro">

                    </div>

                    <div class="col-md-6">

                        <label for="cln_logradouro" class="form-label">
                            Logradouro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_logradouro"
                            name="cln_logradouro">

                    </div>

                    <div class="col-md-2">

                        <label for="cln_numero" class="form-label">
                            Número
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_numero"
                            name="cln_numero">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_complemento" class="form-label">
                            Complemento
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_complemento"
                            name="cln_complemento">

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
                            name="cln_tel1">

                    </div>

                    <div class="col-md-4">

                        <label for="cln_tel2" class="form-label">
                            Telefone 2
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cln_tel2"
                            name="cln_tel2">

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