<?php
/**
 * AmigoPet - Conteúdo do Perfil do Usuário
 * Localização: ~/App/View/perfil_content.php
 */

// Simulação de dados do usuário (Substituir por consulta ao banco de dados no backend)
$usuario = [
    'nome' => 'Ana Oliveira',
    'email' => 'ana.oliveira@email.com',
    'telefone' => '(11) 98765-4321',
    'cpf' => '123.456.789-00',
    'rg' => '12.345.678-9',
    'endereco' => 'Rua das Flores, 123 - Jardins',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01234-567',
    'bio' => 'Apaixonada por animais e voluntária nas horas vagas. Já adotei dois gatinhos pelo AmigoPet!',
    'foto' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=200',
    'data_cadastro' => '15/01/2024',
    'adocoes_concluidas' => 2,
    'processos_ativos' => 1
];
?>

<style>
    .profile-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 25px rgba(0,0,0,0.03);
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, #56ab2f 100%);
        height: 120px;
        border-radius: 20px 20px 0 0;
    }

    .avatar-wrapper {
        margin-top: -60px;
        position: relative;
        display: inline-block;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        object-fit: cover;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-edit-avatar {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--secondary-orange);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }

    .btn-edit-avatar:hover {
        transform: scale(1.1);
        color: white;
    }

    .nav-tabs-profile {
        border: none;
        gap: 10px;
    }

    .nav-tabs-profile .nav-link {
        border: none;
        color: #828282;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 10px;
        transition: 0.3s;
    }

    .nav-tabs-profile .nav-link.active {
        background-color: rgba(111, 207, 151, 0.1);
        color: var(--primary-green);
    }

    .section-title-profile {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sensitive-data-alert {
        background-color: #FFF9F2;
        border: 1px solid #FFE5CC;
        border-radius: 12px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
    }

    .sensitive-data-alert i {
        color: var(--secondary-orange);
    }

    .form-label-profile {
        font-weight: 600;
        font-size: 0.85rem;
        color: #828282;
        margin-bottom: 8px;
    }

    .form-control-profile {
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #E0E0E0;
        font-size: 0.95rem;
        transition: 0.3s;
    }

    .form-control-profile:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(111, 207, 151, 0.1);
    }

    .btn-save-profile {
        background-color: var(--primary-green);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-save-profile:hover {
        background-color: #5bbd86;
        transform: translateY(-2px);
    }

    .stat-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 15px;
        text-align: center;
    }

    .stat-value {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--primary-green);
        display: block;
    }

    .stat-label {
        font-size: 0.7rem;
        color: #828282;
        text-transform: uppercase;
        font-weight: 600;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <div class="row g-4">
            
            <!-- Coluna da Esquerda: Resumo e Foto -->
            <div class="col-xl-4">
                <div class="profile-card p-0 overflow-hidden mb-4">
                    <div class="profile-header"></div>
                    <div class="px-4 pb-4 text-center">
                        <div class="avatar-wrapper">
                            <img src="<?php echo $usuario['foto']; ?>" alt="Avatar" class="profile-avatar">
                            <button class="btn btn-edit-avatar" title="Mudar Foto">
                                <i data-lucide="camera" style="width: 18px;"></i>
                            </button>
                        </div>
                        <h4 class="mt-3 mb-1" style="font-family: 'Poppins'; font-weight: 700;"><?php echo $usuario['nome']; ?></h4>
                        <p class="text-muted small mb-4">Membro desde <?php echo $usuario['data_cadastro']; ?></p>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="stat-box">
                                    <span class="stat-value"><?php echo $usuario['adocoes_concluidas']; ?></span>
                                    <span class="stat-label">Adoções</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <span class="stat-value text-info"><?php echo $usuario['processos_ativos']; ?></span>
                                    <span class="stat-label">Em curso</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-start">
                            <label class="form-label-profile">Biografia</label>
                            <p class="small text-muted" style="line-height: 1.6;">
                                <?php echo $usuario['bio']; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-amigopet">
                    <h6 class="section-title-profile mb-3">
                        <i data-lucide="shield-check" style="width: 20px;"></i> Segurança
                    </h6>
                    <button class="btn btn-outline-secondary w-100 btn-sm mb-2" style="border-radius: 10px;">Alterar Senha</button>
                    <button class="btn btn-outline-danger w-100 btn-sm" style="border-radius: 10px;">Excluir Conta</button>
                </div>
            </div>

            <!-- Coluna da Direita: Formulário -->
            <div class="col-xl-8">
                <div class="profile-card p-4">
                    <ul class="nav nav-tabs nav-tabs-profile mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pessoal">Dados Pessoais</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documentos">Documentos e Endereço</button>
                        </li>
                    </ul>

                    <form id="profileForm">
                        <div class="tab-content">
                            
                            <!-- TAB 1: Dados Pessoais (Públicos/Gerais) -->
                            <div class="tab-pane fade show active" id="tab-pessoal">
                                <h6 class="section-title-profile">
                                    <i data-lucide="user" style="width: 20px;"></i> Informações Gerais
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-profile">Nome Completo</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['nome']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-profile">E-mail</label>
                                        <input type="email" class="form-control form-control-profile" value="<?php echo $usuario['email']; ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-profile">Sobre mim (Bio)</label>
                                        <textarea class="form-control form-control-profile" rows="4"><?php echo $usuario['bio']; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: Documentos e Endereço (Sensíveis/Privados) -->
                            <div class="tab-pane fade" id="tab-documentos">
                                <div class="sensitive-data-alert">
                                    <i data-lucide="lock" style="width: 24px;"></i>
                                    <div class="small">
                                        <strong class="d-block" style="color: var(--secondary-orange);">Informações Sensíveis</strong>
                                        Estes dados <strong>não são públicos</strong>. São utilizados apenas para identificação legal e análise de segurança nos processos de adoção.
                                    </div>
                                </div>

                                <h6 class="section-title-profile">
                                    <i data-lucide="file-text" style="width: 20px;"></i> Documentação
                                </h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-profile">CPF</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['cpf']; ?>" placeholder="000.000.000-00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-profile">RG</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['rg']; ?>" placeholder="00.000.000-0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-profile">Telefone de Contato</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['telefone']; ?>">
                                    </div>
                                </div>

                                <h6 class="section-title-profile">
                                    <i data-lucide="map-pin" style="width: 20px;"></i> Endereço
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label-profile">Logradouro</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['endereco']; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-profile">CEP</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['cep']; ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label-profile">Cidade</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['cidade']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-profile">Estado</label>
                                        <input type="text" class="form-control form-control-profile" value="<?php echo $usuario['estado']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" style="opacity: 0.1;">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light px-4" style="border-radius: 12px; font-weight: 600;">Cancelar</button>
                            <button type="submit" class="btn btn-save-profile">
                                <i data-lucide="save" class="me-2" style="width: 18px;"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    // Simulação de salvamento
    document.getElementById('profileForm').onsubmit = function(e) {
        e.preventDefault();
        alert('Perfil atualizado com sucesso!');
    }
});
</script>