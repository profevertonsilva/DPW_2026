<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Cadastro de Usuário</h1>
        <a href="/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (isset($_GET['erro'])): ?>

                <?php
                $mensagem = '';

                switch ($_GET['erro']) {
                    case '1':
                        $mensagem = 'Preencha nome, CPF e e-mail.';
                        break;

                    case '2':
                        $mensagem = 'Preencha a senha e a confirmação de senha.';
                        break;

                    case '3':
                        $mensagem = 'As senhas não conferem.';
                        break;

                    case '4':
                        $mensagem = 'Preencha a data de nascimento.';
                        break;

                    case '5':
                        $mensagem = 'Preencha o telefone principal.';
                        break;

                    case '6':
                        $mensagem = 'Este e-mail já está cadastrado.';
                        break;
                    case '7':
                        $mensagem = 'CPF Inválido.';
                        break;

                    case '8':
                        $mensagem = 'Este CPF já está cadastrado.';
                        break;

                    case '9':
                        $mensagem = 'Ocorreu um erro ao realizar o cadastro. Tente novamente.';
                        break;
                }
                ?>

                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-danger">
                        <?= $mensagem ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
            <form method="POST" action="/cadastro/cadastrar">

                <!-- Dados Pessoais -->
                <h5 class="mb-3 text-primary">Dados Pessoais</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="col-md-3">
                        <label for="cpf" class="form-label">CPF</label>

                        <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14" placeholder="000.000.000-00" required>

                    </div>
                    <div class="col-md-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                    </div>
                    <div class="col-md-4">
                        <label for="telefone_1" class="form-label">Telefone 1</label>
                        <input type="text" class="form-control" id="telefone_1" name="telefone_1" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="col-md-4">
                        <label for="telefone_2" class="form-label">Telefone 2 <small class="text-muted">(opcional)</small></label>
                        <input type="text" class="form-control" id="telefone_2" name="telefone_2" placeholder="(00) 00000-0000">
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label for="senha_confirmacao" class="form-label">Confirmação de Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="senha_confirmacao"
                            name="senha_confirmacao"
                            placeholder="Confirme sua senha"
                            required>
                    </div>

                </div>

                <!-- Endereço -->
                <h5 class="mb-3 text-primary">Endereço</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" maxlength="9" placeholder="00000-000">
                    </div>
                    <div class="col-md-6">
                        <label for="logradouro" class="form-label">Logradouro</label>
                        <input type="text" class="form-control" id="logradouro" name="logradouro">
                    </div>
                    <div class="col-md-3">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero">
                    </div>
                    <div class="col-md-4">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro">
                    </div>
                    <div class="col-md-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade">
                    </div>
                    <div class="col-md-1">
                        <label for="estado" class="form-label">UF</label>
                        <input type="text" class="form-control" id="estado" name="estado" maxlength="2">
                    </div>
                </div>
                <!-- Botões -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="/" class="btn btn-secondary">
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
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.erro) return;
                document.getElementById('logradouro').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('cidade').value = data.localidade || '';
                document.getElementById('estado').value = data.uf || '';
                document.getElementById('numero').focus();
            })
            .catch(function() {});
    });
</script>