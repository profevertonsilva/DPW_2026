-- Migration to remove columns/tables not in backend schema
-- This removes columns and tables that exist in current database but not in backend schema

-- Step 1: Remove especie varchar column from animal table (now using fk_especie_id)
ALTER TABLE animal 
DROP COLUMN IF EXISTS especie;

-- Step 2: Check for any other tables that should not exist
-- (Add DROP TABLE statements here if any tables need to be removed)

-- Note: The following tables are in backend schema and should be kept:
-- login, administrador, veterinario, adotante, ong, rastreador
-- clinica, vet_clinica, historico_animal, animal, animal_raca, raca, especie
-- solicitacao_adocao, ong_animal

-- If there are any other tables in the database, they should be listed here for removal
