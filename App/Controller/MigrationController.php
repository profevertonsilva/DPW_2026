<?php

namespace App\Controller;

use FW\Controller\Action;
use FW\DB\Connection;

class MigrationController extends Action
{
    public function index()
    {
        // This is a simple migration runner - should be protected in production
        // For now, it's accessible to run the avatar/bio columns migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260620_add_profile_columns.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            echo "Migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Migration failed: " . $e->getMessage();
        }
    }

    public function visitas()
    {
        // Run the visits table migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_visits_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            echo "Visits table migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Visits table migration failed: " . $e->getMessage();
        }
    }

    public function doacoes()
    {
        // Run the donations and expenses tables migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_donations_expenses_tables.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            echo "Donations and expenses tables migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Donations and expenses tables migration failed: " . $e->getMessage();
        }
    }

    public function voluntarios()
    {
        // Run the volunteers tables migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_volunteers_tables.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            echo "Volunteers tables migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Volunteers tables migration failed: " . $e->getMessage();
        }
    }

    public function chamados()
    {
        // Run the chamados table migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_chamados_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            echo "Chamados table migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Chamados table migration failed: " . $e->getMessage();
        }
    }

    public function chamadosAlter()
    {
        // Run the chamados table alter migration to make fk_ong_id nullable
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_alter_chamados_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            $conn->exec($sql);
            
            echo "Chamados table alter migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Chamados table alter migration failed: " . $e->getMessage();
        }
    }

    public function resgates()
    {
        // Run the resgates table migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_resgates_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by statements to handle the ALTER TABLE separately
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore foreign key errors if the constraint already exists
                        if (strpos($e->getMessage(), 'foreign key constraint') === false) {
                            throw $e;
                        }
                    }
                }
            }
            
            echo "Resgates table migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Resgates table migration failed: " . $e->getMessage();
        }
    }

    public function casosVeterinario()
    {
        // Run the casos_veterinario table migration
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260621_add_casos_veterinario_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by statements to handle the ALTER TABLE separately
            $statements = explode(';', $sql);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore foreign key errors if the constraint already exists
                        if (strpos($e->getMessage(), 'foreign key constraint') === false) {
                            throw $e;
                        }
                    }
                }
            }
            
            echo "Casos Veterinario table migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Casos Veterinario table migration failed: " . $e->getMessage();
        }
    }

    public function addFkOngId()
    {
        // Add fk_ong_id column to login table
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260623_add_fk_ong_id_to_login.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore errors if column already exists
                        if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                            throw $e;
                        }
                    }
                }
            }
            
            echo "fk_ong_id column migration executed successfully!";
        } catch (\PDOException $e) {
            echo "fk_ong_id column migration failed: " . $e->getMessage();
        }
    }

    public function fullDatabaseSchema()
    {
        // Execute full database schema migration for local MySQL
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_full_database_schema.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            // Drop routes table first to recreate with correct structure
            try {
                $conn->exec("DROP TABLE IF EXISTS routes");
            } catch (\PDOException $e) {
                // Ignore if table doesn't exist
            }
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore errors if table/column already exists or duplicate key
                        if (strpos($e->getMessage(), 'already exists') === false && 
                            strpos($e->getMessage(), 'Duplicate column name') === false &&
                            strpos($e->getMessage(), 'Duplicate entry') === false &&
                            strpos($e->getMessage(), 'Duplicate key name') === false) {
                            echo "Error executing statement: " . $statement . "\n";
                            echo "Error: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
            
            echo "Full database schema migration executed successfully!";
        } catch (\PDOException $e) {
            echo "Full database schema migration failed: " . $e->getMessage();
        }
    }

    public function createAdminUser()
    {
        // Create admin user with email onayomaker@gmail.com and password 449581263
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            $email = 'onayomaker@gmail.com';
            $password = '449581263';
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into login table
            $sql = "INSERT INTO login (email, senha, tipo_usuario, status, data_cadastro, data_atualizacao) 
                    VALUES (:email, :senha, 'administrador', 'a', NOW(), NOW())
                    ON DUPLICATE KEY UPDATE senha = :senha";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email, ':senha' => $passwordHash]);
            $loginId = $conn->lastInsertId();
            
            // Insert into administrador table
            $sql = "INSERT INTO administrador (id, nome, cpf, data_nascimento, telefone_1, telefone_2, cep, logradouro, numero, bairro, cidade, estado, complemento)
                    VALUES (:id, 'Administrador Padrão', '000.000.000-00', NOW(), '(00) 00000-0000', '(00) 00000-0000', '00000-000', 'Rua Exemplo', 123, 'Bairro Exemplo', 'Cidade Exemplo', 'SP', 'Complemento Exemplo')
                    ON DUPLICATE KEY UPDATE nome = 'Administrador Padrão'";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $loginId]);
            
            echo "Admin user created successfully! Email: $email, Password: $password";
        } catch (\PDOException $e) {
            echo "Error creating admin user: " . $e->getMessage();
        }
    }

    public function populateAnimals()
    {
        // Populate database with sample animals
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_populate_animals.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore duplicate key errors
                        if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                            echo "Error executing statement: " . $statement . "\n";
                            echo "Error: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
            
            echo "Database populated with sample animals successfully!";
        } catch (\PDOException $e) {
            echo "Error populating animals: " . $e->getMessage();
        }
    }

    public function addChamadoColumns()
    {
        // Add missing columns to chamado table
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_add_chamado_columns.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore duplicate column errors
                        if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                            echo "Error executing statement: " . $statement . "\n";
                            echo "Error: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
            
            echo "Chamado columns added successfully!";
        } catch (\PDOException $e) {
            echo "Error adding chamado columns: " . $e->getMessage();
        }
    }

    public function addCasoVeterinarioColumns()
    {
        // Add missing columns to caso_veterinario table
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_add_caso_veterinario_columns.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore duplicate column errors
                        if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                            echo "Error executing statement: " . $statement . "\n";
                            echo "Error: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
            
            echo "Caso veterinario columns added successfully!";
        } catch (\PDOException $e) {
            echo "Error adding caso veterinario columns: " . $e->getMessage();
        }
    }

    public function populateOng()
    {
        // Populate database with sample ONG
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_populate_ong.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Split SQL by semicolon and execute each statement
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Ignore duplicate key errors
                        if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                            echo "Error executing statement: " . $statement . "\n";
                            echo "Error: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
            
            echo "ONG populated successfully!";
        } catch (\PDOException $e) {
            echo "Error populating ONG: " . $e->getMessage();
        }
    }

    public function createProcedimentosTable()
    {
        // Create procedimentos table
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260624_create_procedimentos_table.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Execute the SQL
            $conn->exec($sql);
            
            echo "Procedimentos table created successfully!";
        } catch (\PDOException $e) {
            echo "Error creating procedimentos table: " . $e->getMessage();
        }
    }

    public function adaptToBackendSchema()
    {
        // Adapt current database to backend schema
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260629_adapt_to_backend_schema.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Execute the SQL statements one by one
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--')) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Continue on error (some statements may fail if already exists)
                        echo "Warning: " . $e->getMessage() . "<br>";
                    }
                }
            }
            
            echo "Database adapted to backend schema successfully!";
        } catch (\PDOException $e) {
            echo "Error adapting database: " . $e->getMessage();
        }
    }

    public function removeBackendSchemaColumns()
    {
        // Remove columns not in backend schema
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260629_remove_backend_schema_columns.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Execute the SQL statements one by one
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !str_starts_with($statement, '--')) {
                    try {
                        $conn->exec($statement);
                    } catch (\PDOException $e) {
                        // Continue on error (column may not exist)
                        echo "Warning: " . $e->getMessage() . "<br>";
                    }
                }
            }
            
            echo "Columns not in backend schema removed successfully!";
        } catch (\PDOException $e) {
            echo "Error removing columns: " . $e->getMessage();
        }
    }

    public function fixTipoUsuarioEnum()
    {
        // Fix tipo_usuario enum to match backend schema
        
        $migrationFile = __DIR__ . '/../../DB/migrations/20260629_fix_tipo_usuario_enum.sql';
        
        if (!file_exists($migrationFile)) {
            echo "Migration file not found: $migrationFile";
            return;
        }
        
        $sql = file_get_contents($migrationFile);
        
        try {
            $conexao = new Connection();
            $conn = $conexao->getConn();
            
            if ($conn === null) {
                echo "Database connection failed";
                return;
            }
            
            // Execute the SQL
            $conn->exec($sql);
            
            echo "tipo_usuario enum fixed successfully!";
        } catch (\PDOException $e) {
            echo "Error fixing tipo_usuario enum: " . $e->getMessage();
        }
    }

    public function validaAutenticacao()
    {
        // Migration controller doesn't require authentication
        // This method is required by the parent class but does nothing for this public endpoint
    }
}
