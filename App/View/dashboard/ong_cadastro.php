<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">Cadastro de ONG</h1>

        <a href="/dashboard/ong/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <form method="POST" action="/dashboard/ong/cadastrar">

                <!-- Dados da ONG -->
                <h5 class="mb-3 text-primary">Dados da ONG</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label for="ong_nome" class="form-label">
                            Nome da ONG <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_nome"
                            name="ong_nome"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label for="ong_cnpj" class="form-label">
                            CNPJ
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_cnpj"
                            name="ong_cnpj">
                    </div>

                    <div class="col-md-3">
                        <label for="ong_qnt_animais" class="form-label">
                            Quantidade de Animais
                        </label>

                        <input type="number"
                            class="form-control"
                            id="ong_qnt_animais"
                            name="ong_qnt_animais">
                    </div>

                </div>

                <!-- Endereço -->
                <h5 class="mb-3 text-primary">Endereço</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-2">
                        <label for="ong_cep" class="form-label">
                            CEP
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_cep"
                            name="ong_cep">
                    </div>

                    <div class="col-md-2">
                        <label for="ong_estado" class="form-label">
                            Estado
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_estado"
                            name="ong_estado">
                    </div>

                    <div class="col-md-4">
                        <label for="ong_cidade" class="form-label">
                            Cidade
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_cidade"
                            name="ong_cidade">
                    </div>

                    <div class="col-md-4">
                        <label for="ong_bairro" class="form-label">
                            Bairro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_bairro"
                            name="ong_bairro">
                    </div>

                    <div class="col-md-6">
                        <label for="ong_logradouro" class="form-label">
                            Logradouro
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_logradouro"
                            name="ong_logradouro">
                    </div>

                    <div class="col-md-2">
                        <label for="ong_numero" class="form-label">
                            Número
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_numero"
                            name="ong_numero">
                    </div>

                    <div class="col-md-4">
                        <label for="ong_complemento" class="form-label">
                            Complemento
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_complemento"
                            name="ong_complemento">
                    </div>

                </div>

                <!-- Contato -->
                <h5 class="mb-3 text-primary">Contato</h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-4">
                        <label for="ong_tel1" class="form-label">
                            Telefone 1
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_tel1"
                            name="ong_tel1">
                    </div>

                    <div class="col-md-4">
                        <label for="ong_tel2" class="form-label">
                            Telefone 2
                        </label>

                        <input type="text"
                            class="form-control"
                            id="ong_tel2"
                            name="ong_tel2">
                    </div>

                    <div class="col-md-4">
                        <label for="ong_status" class="form-label">
                            Status
                        </label>

                        <select class="form-select"
                            id="ong_status"
                            name="ong_status">

                            <option value="Ativa" selected>
                                Ativa
                            </option>

                            <option value="Inativa">
                                Inativa
                            </option>

                        </select>

                    </div>

                </div>

                <!-- Botões -->
                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>

                    <a href="/dashboard/ong/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>
