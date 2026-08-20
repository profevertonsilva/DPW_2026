-- ==============================================================
-- MIGRATION: Correções no módulo Animal - Junho 2026
-- ==============================================================
-- Correções de schema para corresponder à implementação do backend
-- Executar após corrigir os validadores e controllers

-- 1. ADICIONAR FK CONSTRAINT EM ANIMAL PARA ESPECIE
-- Status: CRÍTICO
-- Sem isso, é possível inserir animal com especie_id inválido
ALTER TABLE animal 
ADD CONSTRAINT fk_animal_especie
FOREIGN KEY (fk_especie_id) 
REFERENCES especie (id) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- 2. ADICIONAR NOT NULL CONSTRAINT EM FK_ESPECIE_ID
-- Status: IMPORTANTE
-- Garante que todo animal tem uma espécie obrigatoriamente
ALTER TABLE animal 
MODIFY fk_especie_id INT(11) NOT NULL;

-- 3. ADICIONAR ÍNDICES DE PERFORMANCE
-- Status: IMPORTANTE
-- Melhora performance de buscas frequentes
ALTER TABLE animal 
ADD INDEX idx_status (status),
ADD INDEX idx_fk_especie_id (fk_especie_id),
ADD INDEX idx_nome (nome);

-- 4. ADICIONAR ÍNDICES EM PIVOTS
ALTER TABLE animal_raca 
ADD INDEX idx_fk_animal_id (fk_animal_id),
ADD INDEX idx_fk_raca_id (fk_raca_id);

-- ==============================================================
-- ROLLBACK (se necessário)
-- ==============================================================
/*
ALTER TABLE animal DROP FOREIGN KEY fk_animal_especie;
ALTER TABLE animal MODIFY fk_especie_id INT(11) NULL;
ALTER TABLE animal DROP INDEX idx_status;
ALTER TABLE animal DROP INDEX idx_fk_especie_id;
ALTER TABLE animal DROP INDEX idx_nome;
ALTER TABLE animal_raca DROP INDEX idx_fk_animal_id;
ALTER TABLE animal_raca DROP INDEX idx_fk_raca_id;
*/
