<?php

namespace App\DAO;

use App\DAO;
use App\Model\AnimalRacaModel;
use App\Logger\Logger;
use FW\Controller\FuncoesGlobais;

class AnimalRacaDAO extends DAO
{
    private Logger $logger;
    
    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logger();
    }
    
    public function inserir($obj)
    {
        try {
            $sql  = "INSERT INTO animal_raca (fk_animal_id, fk_raca_id)
                     VALUES (:fk_animal_id, :fk_raca_id)";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $obj->__get('fk_animal_id'), \PDO::PARAM_INT);
            $stmt->bindValue(':fk_raca_id',   $obj->__get('fk_raca_id'),   \PDO::PARAM_INT);
            $stmt->execute();
            
            $id = (int) $this->getConn()->lastInsertId();
            $this->logger->info('Vínculo animal-raça criado', [
                'animal_id' => $obj->__get('fk_animal_id'),
                'raca_id' => $obj->__get('fk_raca_id'),
                'id' => $id
            ]);
            
            return $id;
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao inserir vínculo animal-raça', [
                'erro' => $ex->getMessage()
            ]);
            return 0;
        }
    }

    public function excluir($id)
    {
        try {
            $sql  = "DELETE FROM animal_raca WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            
            $this->logger->info('Vínculo animal-raça removido', ['id' => $id]);
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao excluir vínculo animal-raça', [
                'id' => $id,
                'erro' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Altera vínculo de raças (sincroniza com novos IDs de raça)
     * Para tabelas pivot, a alteração é feita através de sincronização
     *
     * @param mixed $obj AnimalRacaModel ou array com fk_animal_id e novos raca_ids
     * @return bool
     */
    public function alterar($obj): bool
    {
        try {
            // Suportar ambos os formatos: objeto ou array
            if (is_array($obj)) {
                $animalId = (int) $obj['fk_animal_id'] ?? null;
                $novasRacas = (array) $obj['raca_ids'] ?? [];
            } else {
                $animalId = (int) $obj->__get('fk_animal_id');
                $novasRacas = (array) $obj->__get('raca_ids') ?? [];
            }
            
            if (empty($animalId)) {
                $this->logger->warning('Tentativa de alterar vínculo com animal_id inválido');
                return false;
            }
            
            $this->sincronizar($animalId, $novasRacas);
            return true;
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao alterar vínculo animal-raça', [
                'erro' => $ex->getMessage()
            ]);
            return false;
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql  = "SELECT id, fk_animal_id, fk_raca_id FROM animal_raca WHERE id = :id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado !== false) {
                $model  = new AnimalRacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $resultado);
                return $model;
            }

            return false;

        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao buscar vínculo animal-raça por ID', [
                'id' => $id,
                'erro' => $ex->getMessage()
            ]);
            return false;
        }
    }

    public function listar()
    {
        try {
            $lista = array();

            $sql  = "SELECT id, fk_animal_id, fk_raca_id FROM animal_raca";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model  = new AnimalRacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($lista, $model);
            }

            return $lista;

        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao listar vínculos animal-raça', [
                'erro' => $ex->getMessage()
            ]);
            return [];
        }
    }

    public function vincular(int $animalId, int $racaId): bool
    {
        try {
            if ($this->existeVinculo($animalId, $racaId)) {
                $this->logger->info('Vínculo animal-raça já existe', [
                    'animal_id' => $animalId,
                    'raca_id' => $racaId
                ]);
                return false;
            }

            $sql  = "INSERT INTO animal_raca (fk_animal_id, fk_raca_id)
                     VALUES (:fk_animal_id, :fk_raca_id)";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $animalId, \PDO::PARAM_INT);
            $stmt->bindValue(':fk_raca_id',   $racaId,   \PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $this->logger->info('Vínculo animal-raça criado', [
                    'animal_id' => $animalId,
                    'raca_id' => $racaId
                ]);
            }
            
            return $resultado;
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao vincular animal-raça', [
                'animal_id' => $animalId,
                'raca_id' => $racaId,
                'erro' => $ex->getMessage()
            ]);
            return false;
        }
    }

    public function desvincular(int $animalId, int $racaId): bool
    {
        try {
            $sql  = "DELETE FROM animal_raca
                     WHERE fk_animal_id = :fk_animal_id
                       AND fk_raca_id   = :fk_raca_id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $animalId, \PDO::PARAM_INT);
            $stmt->bindValue(':fk_raca_id',   $racaId,   \PDO::PARAM_INT);
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $this->logger->info('Vínculo animal-raça removido', [
                    'animal_id' => $animalId,
                    'raca_id' => $racaId
                ]);
            }
            
            return $resultado;
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao desvincular animal-raça', [
                'animal_id' => $animalId,
                'raca_id' => $racaId,
                'erro' => $ex->getMessage()
            ]);
            return false;
        }
    }

    public function sincronizar(int $animalId, array $novosRacaIds): void
    {
        try {
            $novos  = array_map('intval', array_filter($novosRacaIds));
            $atuais = array_map(
                fn(AnimalRacaModel $ar) => (int) $ar->__get('fk_raca_id'),
                $this->listarPorAnimal($animalId)
            );

            $paraRemover = array_diff($atuais, $novos);
            $paraAdicionar = array_diff($novos, $atuais);

            foreach ($paraRemover as $racaId) {
                $this->desvincular($animalId, $racaId);
            }
            foreach ($paraAdicionar as $racaId) {
                $this->vincular($animalId, $racaId);
            }
            
            $this->logger->info('Vínculos animal-raça sincronizados', [
                'animal_id' => $animalId,
                'removidos' => count($paraRemover),
                'adicionados' => count($paraAdicionar)
            ]);
        } catch (\Exception $ex) {
            $this->logger->erro('Erro ao sincronizar vínculos animal-raça', [
                'animal_id' => $animalId,
                'erro' => $ex->getMessage()
            ]);
        }
    }

    public function listarPorAnimal(int $animalId): array
    {
        try {
            $lista = array();

            $sql  = "SELECT id, fk_animal_id, fk_raca_id
                     FROM   animal_raca
                     WHERE  fk_animal_id = :fk_animal_id
                     ORDER  BY fk_raca_id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $animalId, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model  = new AnimalRacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($lista, $model);
            }

            return $lista;

        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao listar raças por animal', [
                'animal_id' => $animalId,
                'erro' => $ex->getMessage()
            ]);
            return [];
        }
    }

    public function listarPorRaca(int $racaId): array
    {
        try {
            $lista = array();

            $sql  = "SELECT id, fk_animal_id, fk_raca_id
                     FROM   animal_raca
                     WHERE  fk_raca_id = :fk_raca_id
                     ORDER  BY fk_animal_id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_raca_id', $racaId, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($resultado as $row) {
                $model  = new AnimalRacaModel();
                $global = new FuncoesGlobais();
                $global->popularModel($model, $row);
                array_push($lista, $model);
            }

            return $lista;

        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao listar animais por raça', [
                'raca_id' => $racaId,
                'erro' => $ex->getMessage()
            ]);
            return [];
        }
    }

    public function existeVinculo(int $animalId, int $racaId): bool
    {
        try {
            $sql  = "SELECT COUNT(*) FROM animal_raca
                     WHERE fk_animal_id = :fk_animal_id
                       AND fk_raca_id   = :fk_raca_id";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bindValue(':fk_animal_id', $animalId, \PDO::PARAM_INT);
            $stmt->bindValue(':fk_raca_id',   $racaId,   \PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $ex) {
            $this->logger->erro('Erro ao verificar vínculo animal-raça', [
                'animal_id' => $animalId,
                'raca_id' => $racaId,
                'erro' => $ex->getMessage()
            ]);
            return false;
        }
    }
}