<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0">Cadastro ONG x Animal</h1>

        <a href="/dashboard/onganimal/listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <form method="POST" action="/dashboard/onganimal/cadastrar">

                <h5 class="mb-3 text-primary">
                    Relacionamento ONG x Animal
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label for="fk_ong_id" class="form-label">
                            ID da ONG
                        </label>

                        <input type="number"
                            class="form-control"
                            id="fk_ong_id"
                            name="fk_ong_id"
                            required>

                    </div>

                    <div class="col-md-6">

                        <label for="fk_animal_id" class="form-label">
                            ID do Animal
                        </label>

                        <input type="number"
                            class="form-control"
                            id="fk_animal_id"
                            name="fk_animal_id"
                            required>

                    </div>

                </div>

                <!-- Botões -->
                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>

                    <a href="/dashboard/onganimal/listar" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>