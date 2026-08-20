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

                        <label for="nome" class="form-label">
                            Nome da Clínica <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            class="form-control"
                            id="nome"
                            name="nome"
                            required>

                    </div>

                    <div class="col-md-3">

                        <label for="cnpj" class="form-label">
                            CNPJ
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cnpj"
                            name="cnpj">

                    </div>

                </div>

                <!-- Endereço -->
                <h5 class="mb-3 text-primary">Endereço</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-2">

                        <label for="cep" class="form-label">
                            CEP
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cep"
                            name="cep">

                    </div>

                    <div class="col-md-2">

                        <label for="estado" class="form-label">
                            Estado
                        </label>

                        <input type="text"
                            class="form-control"
                            id="estado"
                            name="estado">

                    </div>

                    <div class="col-md-4">

                        <label for="cidade" class="form-label">
                            Cidade
                        </label>

                        <input type="text"
                            class="form-control"
                            id="cidade"
                            name="cidade">

                    </div>

                    <div class="col-md-4">

                        <label for="bairro" class="form-label">
                            Bairro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="bairro"
                            name="bairro">

                    </div>

                    <div class="col-md-6">

                        <label for="logradouro" class="form-label">
                            Logradouro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="logradouro"
                            name="logradouro">

                    </div>

                    <div class="col-md-2">

                        <label for="numero" class="form-label">
                            Número
                        </label>

                        <input type="text"
                            class="form-control"
                            id="numero"
                            name="numero">

                    </div>

                    <div class="col-md-4">

                        <label for="complemento" class="form-label">
                            Complemento
                        </label>

                        <input type="text"
                            class="form-control"
                            id="complemento"
                            name="complemento">

                    </div>

                </div>

                <!-- Contato -->
                <h5 class="mb-3 text-primary">Contato</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-4">

                        <label for="telefone_1" class="form-label">
                            telefone_efone 1
                        </label>

                        <input type="text"
                            class="form-control"
                            id="telefone_1"
                            name="telefone_1">

                    </div>

                    <div class="col-md-4">

                        <label for="telefone_2" class="form-label">
                            telefone_efone 2
                        </label>

                        <input type="text"
                            class="form-control"
                            id="telefone_2"
                            name="telefone_2">

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