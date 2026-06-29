-- =============================================================================
-- Migracao_API_v1.2.sql  ·  AmigoPet API
-- Tabelas/colunas dos módulos adicionados em v1.2 (jun/2026):
--   C.8  Transferência de Responsabilidade  -> tabela `transferencia`
--   C.9  Notificações (in-app)              -> tabela `notificacao`
--   C.12 Clínicas                           -> colunas `clinica.avatar`/`clinica.email`
--   C.11 Perfis                             -> coluna `adotante.ranking` (oficial)
--
-- Idempotente: pode ser executado mais de uma vez (CREATE TABLE IF NOT EXISTS e
-- guards via INFORMATION_SCHEMA). Compatível com MySQL 5.7 / utf8mb4.
-- Sem FKs físicas nas novas tabelas (integridade na aplicação — ver spec F#6).
-- =============================================================================

-- ----------------------------------------------------------------- C.8 transferencia
CREATE TABLE IF NOT EXISTS transferencia (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    fk_animal_id     INT NOT NULL,
    de_usuario_id    INT NOT NULL,
    de_usuario_nome  VARCHAR(150),
    para_usuario_id  INT NOT NULL,
    para_usuario_nome VARCHAR(150),
    motivo           TEXT,
    data             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_transferencia_animal (fk_animal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------- C.9 notificacao
CREATE TABLE IF NOT EXISTS notificacao (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    fk_login_id     INT NOT NULL,
    titulo          VARCHAR(150),
    mensagem        TEXT,
    data            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lida            TINYINT(1) NOT NULL DEFAULT 0,
    tipo            VARCHAR(50),
    destino_tab     VARCHAR(50),
    destino_tela    VARCHAR(100),
    destino_params  TEXT,
    INDEX idx_notificacao_login (fk_login_id),
    INDEX idx_notificacao_lida (fk_login_id, lida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------- C.12 clinica.avatar / clinica.email
-- (Espelha o padrão de ong/adotante: o app pede `foto`, o banco guarda `avatar`.)
SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
       WHERE table_schema = DATABASE() AND table_name = 'clinica' AND column_name = 'avatar') = 0,
    'ALTER TABLE clinica ADD COLUMN avatar VARCHAR(255) NULL',
    'SELECT 1'));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
       WHERE table_schema = DATABASE() AND table_name = 'clinica' AND column_name = 'email') = 0,
    'ALTER TABLE clinica ADD COLUMN email VARCHAR(191) NULL',
    'SELECT 1'));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------- C.11 adotante.ranking (oficial)
-- A coluna oficial de avaliação de adotante passa a ser `ranking`
-- (enum pessimo/regular/bom/muito bom/excelente). `adotante.status` permanece
-- como legado de ranking por compatibilidade. GET /adotante/perfil lê ranking
-- com fallback para status (ver spec H.3#5).
SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
       WHERE table_schema = DATABASE() AND table_name = 'adotante' AND column_name = 'ranking') = 0,
    'ALTER TABLE adotante ADD COLUMN ranking ENUM(''pessimo'',''regular'',''bom'',''muito bom'',''excelente'') NULL',
    'SELECT 1'));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
