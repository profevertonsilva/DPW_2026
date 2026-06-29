-- Migration to adapt current database to backend schema
-- This migration aligns the current database structure with the backend schema

-- Step 1: Add FKs to login table
ALTER TABLE login 
ADD COLUMN fk_adotante_id int(11) NULL,
ADD COLUMN fk_rastreador_id int(11) NULL,
ADD COLUMN fk_ong_id int(11) NULL,
ADD COLUMN fk_administrador_id int(11) NULL,
ADD COLUMN fk_veterinario_id int(11) NULL;

-- Add constraints for login FKs
ALTER TABLE login 
ADD CONSTRAINT fk_login_adotante 
FOREIGN KEY (fk_adotante_id) REFERENCES adotante(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_login_rastreador 
FOREIGN KEY (fk_rastreador_id) REFERENCES rastreador(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_login_ong 
FOREIGN KEY (fk_ong_id) REFERENCES ong(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_login_administrador 
FOREIGN KEY (fk_administrador_id) REFERENCES administrador(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_login_veterinario 
FOREIGN KEY (fk_veterinario_id) REFERENCES veterinario(id) ON DELETE CASCADE;

-- Step 2: Add fk_login_id to adotante table
ALTER TABLE adotante 
ADD COLUMN fk_login_id int(11) NULL;

ALTER TABLE adotante 
ADD CONSTRAINT fk_adotante_login 
FOREIGN KEY (fk_login_id) REFERENCES login(id) ON DELETE CASCADE;

-- Step 3: Modify email column length and add unique constraint
ALTER TABLE login 
MODIFY COLUMN email VARCHAR(191) NOT NULL;

ALTER TABLE login 
ADD CONSTRAINT uq_login_email UNIQUE (email);

ALTER TABLE adotante 
MODIFY COLUMN cpf VARCHAR(14) NOT NULL;

ALTER TABLE adotante 
ADD CONSTRAINT uq_adotante_cpf UNIQUE (cpf);

-- Step 4: Create especie table
CREATE TABLE IF NOT EXISTS especie (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    nome varchar(100)
);

-- Step 5: Create raca table
CREATE TABLE IF NOT EXISTS raca (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    nome varchar(100),
    fk_especie_id int(11),
    CONSTRAINT fk_raca_especie 
    FOREIGN KEY (fk_especie_id) REFERENCES especie(id) ON DELETE CASCADE
);

-- Step 6: Create animal_raca table
CREATE TABLE IF NOT EXISTS animal_raca (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_raca_id int(11),
    fk_animal_id int(11),
    CONSTRAINT fk_animal_raca_raca 
    FOREIGN KEY (fk_raca_id) REFERENCES raca(id) ON DELETE CASCADE,
    CONSTRAINT fk_animal_raca_animal 
    FOREIGN KEY (fk_animal_id) REFERENCES animal(id) ON DELETE RESTRICT
);

-- Step 7: Adjust animal table structure
-- Add missing columns
ALTER TABLE animal 
ADD COLUMN cor varchar(50) NULL,
ADD COLUMN castrado tinyint(1) DEFAULT 0,
ADD COLUMN descricao text NULL;

-- Add fk_especie_id column
ALTER TABLE animal 
ADD COLUMN fk_especie_id int(11) NULL AFTER sexo;

-- Convert porte from enum to varchar
ALTER TABLE animal 
MODIFY COLUMN porte varchar(20) NULL;

-- Adjust status enum to match backend
ALTER TABLE animal 
MODIFY COLUMN status enum('disponivel','reservado') DEFAULT 'disponivel';

-- Step 8: Adjust historico_animal tipo enum
ALTER TABLE historico_animal 
MODIFY COLUMN tipo enum('vacinacao', 'procedimento', 'ocorrencia');

-- Step 9: Add numero column to rastreador
ALTER TABLE rastreador 
ADD COLUMN numero int(11) NULL;

-- Step 10: Create clinica table
CREATE TABLE IF NOT EXISTS clinica (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    nome varchar(100),
    cnpj varchar(18),
    cep varchar(9),
    telefone_1 varchar(20),
    logradouro varchar(150),
    numero int(11),
    bairro varchar(100),
    cidade varchar(100),
    estado varchar(100),
    complemento text,
    telefone_2 varchar(20)
);

-- Step 11: Create vet_clinica table
CREATE TABLE IF NOT EXISTS vet_clinica (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    fk_clinica_id int(11),
    fk_veterinario_id int(11),
    CONSTRAINT fk_vet_clinica_clinica 
    FOREIGN KEY (fk_clinica_id) REFERENCES clinica(id) ON DELETE CASCADE,
    CONSTRAINT fk_vet_clinica_veterinario 
    FOREIGN KEY (fk_veterinario_id) REFERENCES veterinario(id) ON DELETE CASCADE
);

-- Step 12: Fix veterinario crmv column name (if it exists as crvm)
ALTER TABLE veterinario 
CHANGE COLUMN crvm crmv VARCHAR(8);

-- Step 13: Add numero column to ong and administrador
ALTER TABLE ong 
ADD COLUMN numero int(11) NULL;

ALTER TABLE administrador 
ADD COLUMN numero int(11) NULL;

-- Step 14: Adjust adotante status enum to include 'ruim'
ALTER TABLE adotante 
MODIFY COLUMN status enum('pessimo','ruim', 'bom', 'muito bom', 'excelente') NOT NULL DEFAULT 'bom';

-- Step 15: Add data_atualizacao to login
ALTER TABLE login 
ADD COLUMN data_atualizacao datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Step 16: Adjust login table columns
ALTER TABLE login 
MODIFY COLUMN senha varchar(255) NOT NULL,
MODIFY COLUMN email varchar(191) NOT NULL,
MODIFY COLUMN tipo_usuario enum('administrador','ong', 'rastreador', 'adotante', 'veterinario') DEFAULT 'adotante' NOT NULL,
MODIFY COLUMN status enum('a', 'i') DEFAULT 'a' NOT NULL,
MODIFY COLUMN data_cadastro datetime DEFAULT CURRENT_TIMESTAMP NOT NULL;
