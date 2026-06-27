<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;
use App\Api\Jwt;
use App\Api\LoginThrottle;
use App\Api\Validador;
use App\Api\Repository\AuthRepository;

/**
 * Módulo de autenticação (seção C.1 da especificação).
 * Endpoints: login, logout, cadastro (adotante/ong/veterinário),
 * alterar senha e recuperar senha. Sintaxe compatível com PHP 7.0.
 */
class AuthController extends ApiController
{
    /** POST /api/auth/login — público. */
    public function login()
    {
        $body  = $this->body();
        $email = strtolower(trim((string) (isset($body['email']) ? $body['email'] : '')));
        $senha = (string) (isset($body['senha']) ? $body['senha'] : '');

        if ($email === '' || $senha === '') {
            throw new ApiException('Informe e-mail e senha.', 422);
        }

        $ip       = $this->ip();
        $throttle = new LoginThrottle();
        $throttle->verificar($email, $ip); // 429 se bloqueado

        $repo  = new AuthRepository();
        $login = $repo->buscarLoginPorEmail($email);

        if (!$login || !password_verify($senha, $login['senha'])) {
            $throttle->registrarFalha($email, $ip);
            throw new ApiException('E-mail ou senha inválidos.', 401);
        }

        if ((isset($login['status']) ? $login['status'] : 'a') !== 'a') {
            throw new ApiException('Conta inativa. Entre em contato com o suporte.', 403);
        }

        $throttle->registrarSucesso($email, $ip);
        $this->json($this->montarSessao($login, $repo->nomeDoPerfil($login)), 200);
    }

    /** POST /api/auth/logout — protegido. JWT é stateless; cliente descarta o token. */
    public function logout()
    {
        $this->json(['mensagem' => 'Logout efetuado com sucesso.'], 200);
    }

    /** POST /api/auth/cadastrar/adotante — público. */
    public function cadastrarAdotante()
    {
        $body = $this->body();
        $this->exigirCampos($body, [
            'nome', 'cpf', 'data_nascimento', 'cep', 'numero', 'bairro',
            'cidade', 'estado', 'logradouro', 'telefone_1', 'email', 'senha',
        ]);
        $this->validarCredenciais($body['email'], $body['senha']);

        $repo = new AuthRepository();
        $this->garantirEmailLivre($repo, $body['email']);
        if ($repo->valorExiste('adotante', 'cpf', trim($body['cpf']))) {
            throw new ApiException('CPF já cadastrado.', 400);
        }

        $repo->criarAdotante($this->normalizarEndereco($body, [
            'nome'            => trim($body['nome']),
            'cpf'             => trim($body['cpf']),
            'data_nascimento' => $body['data_nascimento'],
        ]));

        $this->responderCadastro($repo, $body['email'], trim($body['nome']));
    }

    /** POST /api/auth/cadastrar/ong — público. */
    public function cadastrarOng()
    {
        $body = $this->body();
        $this->exigirCampos($body, [
            'nome', 'cnpj', 'email', 'senha', 'telefone_1', 'cep',
            'logradouro', 'numero', 'bairro', 'cidade', 'estado',
        ]);
        $this->validarCredenciais($body['email'], $body['senha']);

        $repo = new AuthRepository();
        $this->garantirEmailLivre($repo, $body['email']);
        if ($repo->valorExiste('ong', 'cnpj', trim($body['cnpj']))) {
            throw new ApiException('CNPJ já cadastrado.', 400);
        }

        $repo->criarOng($this->normalizarEndereco($body, [
            'nome' => trim($body['nome']),
            'cnpj' => trim($body['cnpj']),
        ]));

        $this->responderCadastro($repo, $body['email'], trim($body['nome']));
    }

    /** POST /api/auth/cadastrar/veterinario — público. */
    public function cadastrarVeterinario()
    {
        $body = $this->body();
        $this->exigirCampos($body, [
            'nome', 'cpf', 'crmv', 'email', 'senha', 'data_nascimento',
            'telefone_1', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado',
        ]);
        $this->validarCredenciais($body['email'], $body['senha']);

        $repo = new AuthRepository();
        $this->garantirEmailLivre($repo, $body['email']);
        if ($repo->valorExiste('veterinario', 'crmv', trim($body['crmv']))) {
            throw new ApiException('CRMV já cadastrado.', 400);
        }

        $repo->criarVeterinario($this->normalizarEndereco($body, [
            'nome'            => trim($body['nome']),
            'cpf'             => trim($body['cpf']),
            'crmv'            => trim($body['crmv']),
            'data_nascimento' => $body['data_nascimento'],
        ]));

        $this->responderCadastro($repo, $body['email'], trim($body['nome']));
    }

    /** POST /api/auth/alterar-senha — protegido. */
    public function alterarSenha()
    {
        $usuario = $this->exigirAutenticacao();
        $body    = $this->body();
        $this->exigirCampos($body, ['senha_atual', 'senha_nova', 'senha_confirmacao']);

        if ($body['senha_nova'] !== $body['senha_confirmacao']) {
            throw new ApiException('A nova senha e a confirmação não coincidem.', 422);
        }
        if (!Validador::senhaForte($body['senha_nova'])) {
            throw new ApiException(
                'Senha fraca: use ao menos 8 caracteres, incluindo um caractere especial.',
                422
            );
        }

        $repo  = new AuthRepository();
        $login = $repo->buscarLoginPorId((int) $usuario->sub);
        if (!$login) {
            throw new ApiException('Usuário não encontrado.', 404);
        }
        if (!password_verify($body['senha_atual'], $login['senha'])) {
            throw new ApiException('Senha atual incorreta.', 401);
        }

        $repo->atualizarSenha((int) $login['id'], $body['senha_nova']);
        $this->json(['mensagem' => 'Senha alterada com sucesso.'], 200);
    }

    /**
     * POST /api/auth/recuperar-senha — público.
     * Sempre 200 (anti-enumeração de e-mails).
     */
    public function recuperarSenha()
    {
        $body  = $this->body();
        $email = strtolower(trim((string) (isset($body['email']) ? $body['email'] : '')));

        if (Validador::email($email)) {
            $repo = new AuthRepository();
            if ($repo->emailExiste($email)) {
                // TODO: gerar token de redefinição e enviar e-mail (RNF#09).
                // Pendente de configuração SMTP (MAIL_* vazios no .env).
                error_log("[API/recuperar-senha] solicitação para {$email} (envio de e-mail pendente).");
            }
        }

        $this->json(
            ['mensagem' => 'Se o e-mail estiver cadastrado, enviaremos instruções de recuperação.'],
            200
        );
    }

    // ----------------------------------------------------------------- helpers

    /** Monta o corpo de resposta { token, usuario } a partir do login. */
    private function montarSessao(array $login, $nome): array
    {
        $usuario = [
            'id'           => (int) $login['id'],
            'nome'         => $nome,
            'email'        => $login['email'],
            'tipo_usuario' => $login['tipo_usuario'],
            'status'       => $login['status'],
        ];

        $token = Jwt::emitir([
            'sub'          => (int) $login['id'],
            'email'        => $login['email'],
            'tipo_usuario' => $login['tipo_usuario'],
            'status'       => $login['status'],
        ]);

        return ['token' => $token, 'usuario' => $usuario];
    }

    private function validarCredenciais($email, $senha)
    {
        if (!Validador::email(trim($email))) {
            throw new ApiException('E-mail inválido.', 422);
        }
        if (!Validador::senhaForte($senha)) {
            throw new ApiException(
                'Senha fraca: use ao menos 8 caracteres, incluindo um caractere especial.',
                422
            );
        }
    }

    private function garantirEmailLivre(AuthRepository $repo, $email)
    {
        if ($repo->emailExiste(trim($email))) {
            throw new ApiException('E-mail já está em uso.', 422);
        }
    }

    /**
     * Junta os campos próprios já tratados ($base) com os de endereço/contato
     * comuns aos três cadastros, tratando os opcionais (telefone_2, complemento).
     */
    private function normalizarEndereco(array $body, array $base): array
    {
        return array_merge($base, [
            'email'       => strtolower(trim($body['email'])),
            'senha'       => $body['senha'],
            'cep'         => trim($body['cep']),
            'logradouro'  => trim($body['logradouro']),
            'numero'      => $body['numero'],
            'bairro'      => trim($body['bairro']),
            'cidade'      => trim($body['cidade']),
            'estado'      => trim($body['estado']),
            'telefone_1'  => trim($body['telefone_1']),
            'telefone_2'  => isset($body['telefone_2']) && trim($body['telefone_2']) !== '' ? trim($body['telefone_2']) : null,
            'complemento' => isset($body['complemento']) && trim($body['complemento']) !== '' ? trim($body['complemento']) : null,
        ]);
    }

    /** Recarrega o login recém-criado e responde 201 com token + usuário. */
    private function responderCadastro(AuthRepository $repo, $email, $nome)
    {
        $login = $repo->buscarLoginPorEmail($email);
        $this->json($this->montarSessao($login, $nome), 201);
    }
}
