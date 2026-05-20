<?php

namespace App\DAO;

use App\Model\AnimalRaca;
use PDO;

class AnimalRacaDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ------------------------------------------------------------------ //
    //  Vinculação
    // ------------------------------------------------------------------ //

    public function vincular(int $animalId, int $racaId): bool
    {
        if ($this->existeVinculo($animalId, $racaId)) {
            return false;
        }

        $sql  = 'INSERT INTO animal_raca (fk_animal_id, fk_raca_id)
                 VALUES (:fk_animal_id, :fk_raca_id)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':fk_animal_id' => $animalId,
            ':fk_raca_id'   => $racaId,
        ]);
    }

    /**
     * @param int[] $racaIds
     */
    public function vincularVarias(int $animalId, array $racaIds): void
    {
        foreach ($racaIds as $racaId) {
            $this->vincular($animalId, (int) $racaId);
        }
    }

    // ------------------------------------------------------------------ //
    //  Desvinculação
    // ------------------------------------------------------------------ //

    public function desvincular(int $animalId, int $racaId): bool
    {
        $sql  = 'DELETE FROM animal_raca
                 WHERE fk_animal_id = :fk_animal_id
                   AND fk_raca_id   = :fk_raca_id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':fk_animal_id' => $animalId,
            ':fk_raca_id'   => $racaId,
        ]);
    }

    public function desvinculaTodos(int $animalId): bool
    {
        $sql  = 'DELETE FROM animal_raca WHERE fk_animal_id = :fk_animal_id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':fk_animal_id' => $animalId]);
    }

    // ------------------------------------------------------------------ //
    //  Sincronização — form de edição do animal
    // ------------------------------------------------------------------ //

    /**
     * @param int[] $novosRacaIds
     */
    public function sincronizar(int $animalId, array $novosRacaIds): void
    {
        $novos  = array_map('intval', $novosRacaIds);
        $atuais = array_map(
            fn(AnimalRaca $ar) => (int) $ar->fk_raca_id,
            $this->listarPorAnimal($animalId)
        );

        foreach (array_diff($atuais, $novos) as $racaId) {
            $this->desvincular($animalId, $racaId);
        }
        foreach (array_diff($novos, $atuais) as $racaId) {
            $this->vincular($animalId, $racaId);
        }
    }

    // ------------------------------------------------------------------ //
    //  Consultas
    // ------------------------------------------------------------------ //

    /**
     * @return AnimalRaca[]
     */
    public function listarPorAnimal(int $animalId): array
    {
        $sql = 'SELECT id, fk_animal_id, fk_raca_id
                FROM   animal_raca
                WHERE  fk_animal_id = :fk_animal_id
                ORDER  BY fk_raca_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':fk_animal_id' => $animalId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, AnimalRaca::class);
        return $stmt->fetchAll();
    }

    /**
     * @return AnimalRaca[]
     */
    public function listarPorRaca(int $racaId): array
    {
        $sql = 'SELECT id, fk_animal_id, fk_raca_id
                FROM   animal_raca
                WHERE  fk_raca_id = :fk_raca_id
                ORDER  BY fk_animal_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':fk_raca_id' => $racaId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, AnimalRaca::class);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?AnimalRaca
    {
        $sql  = 'SELECT id, fk_animal_id, fk_raca_id
                 FROM   animal_raca
                 WHERE  id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, AnimalRaca::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function existeVinculo(int $animalId, int $racaId): bool
    {
        $sql  = 'SELECT COUNT(*) FROM animal_raca
                 WHERE fk_animal_id = :fk_animal_id
                   AND fk_raca_id   = :fk_raca_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fk_animal_id' => $animalId,
            ':fk_raca_id'   => $racaId,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}