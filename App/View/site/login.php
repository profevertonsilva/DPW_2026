<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="card shadow" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h1 class="h4 mb-1">Login</h1>
                <p class="text-muted mb-0">Acesse sua conta para continuar</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <?php if ($_GET['erro'] == 1): ?>
                    <div class="alert alert-danger" role="alert">
                        E-mail ou senha inválidos.
                    </div>
                <?php elseif ($_GET['erro'] == 2): ?>
                    <div class="alert alert-warning" role="alert">
                        Usuário inativo. Entre em contato com o administrador.
                    </div>
                <?php elseif ($_GET['erro'] == 3): ?>
                    <div class="alert alert-danger" role="alert">
                        Usuário não encontrado no sistema.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" action="/login">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input
                        type="password"
                        class="form-control"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="lembrar" disabled>
                        <label class="form-check-label text-muted" for="lembrar">
                            Lembrar-me
                        </label>
                    </div>

                    <a href="#" class="text-decoration-none small text-muted">
                        Esqueci minha senha
                    </a>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Entrar
                </button>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <p class="mb-2 text-muted">Ainda não possui conta?</p>
                <a href="/cadastro" class="btn btn-outline-secondary w-100">
                    Criar cadastro
                </a>
            </div>

        </div>
    </div>
</div>