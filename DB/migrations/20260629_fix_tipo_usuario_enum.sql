-- Migration to fix tipo_usuario enum in login table
-- Change 'moderador' to 'rastreador' to match backend schema

ALTER TABLE login 
MODIFY COLUMN tipo_usuario ENUM('administrador', 'ong', 'rastreador', 'adotante', 'veterinario');
