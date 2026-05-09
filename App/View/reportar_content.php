<?php
/**
 * AmigoPet - Conteúdo do Formulário de Reporte
 * Localização: ~/App/View/reportar_content.php
 */
?>

<style>
    .report-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
    }

    .form-label {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: #4F4F4F;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #E0E0E0;
        background-color: #F9F9F9;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(111, 207, 151, 0.1);
    }

    .upload-box {
        border: 2px dashed #E0E0E0;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
        background: #FDFDFD;
    }

    .upload-box:hover {
        border-color: var(--primary-green);
        background: rgba(111, 207, 151, 0.02);
    }

    .btn-submit-report {
        background-color: var(--secondary-orange);
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        font-family: 'Poppins', sans-serif;
        transition: 0.3s;
        color: white;
        width: 100%;
        margin-top: 20px;
    }

    .btn-submit-report:hover {
        background-color: #d4833b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(242, 153, 74, 0.3);
    }

    .type-icon {
        width: 40px;
        height: 40px;
        background: rgba(111, 207, 151, 0.1);
        color: var(--primary-green);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="report-container">
            <div class="mb-4">
                <h1 class="h3" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Reportar um Caso 🐾</h1>
                <p class="text-muted">Sua denúncia ou alerta ajuda a salvar vidas. Preencha os detalhes abaixo.</p>
            </div>

            <div class="form-card">
                <form id="reportForm" action="#" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        
                        <!-- Tipo de Reporte -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Ocorrência</label>
                            <select class="form-select" name="tipo_ocorrecia" required>
                                <option value="" selected disabled>Selecione uma opção</option>
                                <option value="maus_tratos">Denúncia de Maus-tratos</option>
                                <option value="abandonado">Animal Abandonado</option>
                                <option value="perdido">Animal Perdido (Alerta)</option>
                                <option value="encontrado">Animal Encontrado</option>
                                <option value="emergencia">Emergência Médica</option>
                            </select>
                        </div>

                        <!-- Urgência -->
                        <div class="col-md-6">
                            <label class="form-label">Nível de Urgência</label>
                            <select class="form-select" name="urgencia" required>
                                <option value="baixa">Baixa (Apenas informativo)</option>
                                <option value="media" selected>Média (Precisa de atenção)</option>
                                <option value="alta">Alta (Risco de vida imediato)</option>
                            </select>
                        </div>

                        <!-- Título/Assunto -->
                        <div class="col-12">
                            <label class="form-label">Assunto Curto</label>
                            <input type="text" class="form-control" name="assunto" placeholder="Ex: Cachorro ferido na Praça Central" required>
                        </div>

                        <!-- Localização -->
                        <div class="col-12">
                            <label class="form-label">Localização Aproximada</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i data-lucide="map-pin"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0" name="localizacao" placeholder="Rua, bairro ou ponto de referência" required>
                            </div>
                        </div>

                        <!-- Descrição Detalhada -->
                        <div class="col-12">
                            <label class="form-label">Descrição do Caso</label>
                            <textarea class="form-control" name="descricao" rows="5" placeholder="Descreva as características do animal e a situação detalhadamente..." required></textarea>
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="col-12">
                            <label class="form-label">Fotos da Ocorrência (Opcional)</label>
                            <div class="upload-box" onclick="document.getElementById('fileUpload').click()">
                                <i data-lucide="camera" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                                <p class="mb-0 text-muted small">Clique para selecionar ou arraste fotos aqui</p>
                                <p class="text-secondary" style="font-size: 0.7rem;">PNG, JPG ou JPEG (Máx. 5MB)</p>
                                <input type="file" id="fileUpload" name="fotos[]" multiple hidden accept="image/*">
                            </div>
                            <div id="filePreview" class="mt-2 row g-2"></div>
                        </div>

                        <!-- Botão Enviar -->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-submit-report">
                                <i data-lucide="send" class="me-2" style="width: 18px;"></i> Enviar Reporte
                            </button>
                            <p class="mt-3 text-secondary" style="font-size: 0.75rem;">
                                Ao enviar, sua localização e dados de perfil serão anexados ao caso para contato das equipes.
                            </p>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa ícones
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    const fileInput = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');

    fileInput.addEventListener('change', function() {
        filePreview.innerHTML = '';
        if (this.files) {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded-3 border" style="height: 80px; width: 100%; object-fit: cover;">
                        </div>
                    `;
                    filePreview.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        }
    });

    // Simulação de envio
    document.getElementById('reportForm').onsubmit = function(e) {
        e.preventDefault();
        alert('Reporte enviado com sucesso! Nossa equipe de moderadores e campo já foram notificados.');
        window.location.href = 'dashboard.php';
    }
});
</script>