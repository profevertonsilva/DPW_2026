<style>
    .main-content {
        margin-left: 0 ;
        padding-left: 0 ;
        min-height: calc(100vh - 500px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Card */
    .card {
        border: 1.5px solid #e8f5ef ;
        border-radius: 24px ;
        box-shadow: 0 4px 32px rgba(111,207,151,0.13), 0 1px 6px rgba(0,0,0,0.05) ;
    }

    .card-body { padding: 2.5rem 2rem ; }

    /* Logo */
    .login-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .login-logo-icon {
        width: 42px; height: 42px;
        background: #6FCF97;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .login-logo-text {
        font-family: 'Poppins', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #4F4F4F;
        letter-spacing: -0.5px;
    }

    .login-logo-text span { color: #6FCF97; }

    /* Cabeçalho ── */
    .card h1 {
        font-family: 'Poppins', sans-serif ;
        font-size: 20px ;
        font-weight: 700 ;
        color: #4F4F4F ;
        margin-bottom: 4px ;
    }

    .card p.text-muted {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #888 ;
        margin-bottom: 1.75rem ;
    }

    .login-divider {
        height: 1.5px;
        background: linear-gradient(90deg, #6FCF97 0%, #FAF9F6 100%);
        border: none;
        border-radius: 2px;
        margin-bottom: 1.75rem;
    }

    /* Alertas PHP */
    .card .alert {
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 10px;
        padding: 11px 14px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.4;
    }

    .card .alert-danger {
        background: #fff2f2 ;
        border: 1.5px solid #f5a0a0 ;
        color: #b00000 ;
    }

    .card .alert-warning {
        background: #fff8f0 ;
        border: 1.5px solid #f5c98a ;
        color: #a05f00 ;
    }

    /* Labels */
    .card .form-label {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #4F4F4F;
        margin-bottom: 5px;
    }

    /* Inputs */
    .card .form-control {
        border: 1.5px solid #e0e0e0 ;
        border-radius: 10px ;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #4F4F4F;
        background: #FAF9F6 ;
        padding: 10px 12px ;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .card .form-control:focus {
        border-color: #6FCF97 ;
        box-shadow: 0 0 0 3px rgba(111,207,151,0.15) ;
        background: #fff ;
    }

    /* Checkbox */
    .card .form-check-input:checked {
        background-color: #6FCF97;
        border-color: #6FCF97;
    }

    .card .form-check-label,
    .card .small.text-muted {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #888 ;
    }

    /* Botão principal */
    .card .btn-primary {
        background: #6FCF97 ;
        border: none ;
        border-radius: 12px ;
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 700;
        padding: 13px ;
        box-shadow: 0 3px 12px rgba(111,207,151,0.35);
        transition: background 0.2s;
    }

    .card .btn-primary:hover,
    .card .btn-primary:focus,
    .card .btn-primary:active {
        background: #58b87e ;
        box-shadow: 0 3px 12px rgba(111,207,151,0.35) ;
    }

    /* Botão secundário */
    .card .btn-outline-secondary {
        border: 1.5px solid #6FCF97 ;
        border-radius: 12px ;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #6FCF97 ;
        padding: 10px ;
        transition: background 0.2s, color 0.2s;
    }

    .card .btn-outline-secondary:hover {
        background: #6FCF97 ;
        color: #fff ;
    }

    /* Divider e rodapé */
    .card hr { border-color: #f0f0f0 ; }

    .card p.mb-2 {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #888;
    }

    .card a {
        color: #56CCF2 ;
        font-weight: 600;
        text-decoration: none ;
    }

    .card a:hover { text-decoration: underline ; }
</style>

<div class="main-content container-fluid  bg-light">
    <div class="card shadow" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4 ">

            <!-- Logo AmigoPet -->
            <div class="login-logo">
                <div class="login-logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 10C13.1046 10 14 9.10457 14 8C14 6.89543 13.1046 6 12 6C10.8954 6 10 6.89543 10 8C10 9.10457 10.8954 10 12 10Z" fill="#fff"/>
                        <path d="M7 9C8.10457 9 9 8.10457 9 7C9 5.89543 8.10457 5 7 5C5.89543 5 5 5.89543 5 7C5 8.10457 5.89543 9 7 9Z" fill="#fff" opacity=".7"/>
                        <path d="M17 9C18.1046 9 19 8.10457 19 7C19 5.89543 18.1046 5 17 5C15.8954 5 15 5.89543 15 7C15 8.10457 15.8954 9 17 9Z" fill="#fff" opacity=".7"/>
                        <path d="M12 21C15.3137 21 18 18.3137 18 15C18 11.6863 15.3137 9 12 9C8.68629 9 6 11.6863 6 15C6 18.3137 8.68629 21 12 21Z" fill="#fff" opacity=".9"/>
                    </svg>
                </div>
                <div class="login-logo-text">Amigo<span>Pet</span></div>
            </div>

            <div class="text-center mb-4">
                <h1 class="h4 mb-1">Login</h1>
                <p class="text-muted mb-0">Acesse sua conta para continuar</p>
            </div>

            <hr class="login-divider">

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
                        Você não possue login no sistema.
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

