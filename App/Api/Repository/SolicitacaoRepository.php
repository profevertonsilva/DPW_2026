<?php

namespace App\Api\Repository;

use FW\DB\Connection;

/**
 * Acesso a dados do módulo de Solicitações de Adoção (seção C.4 + termo/avaliação C.7).
 * Tabelas: solicitacao_adocao, termo_adocao (append-only após assinatura),
 * avaliacao_adotante (append-only). Sintaxe PHP 7.0.
 *
 * O `animal` embutido nas respostas reusa o AnimalRepository (shape único da spec).
 */
class SolicitacaoRepository
{
    /** Status que NÃO contam como solicitação "ativa" (permitem nova solicitação). */
    const STATUS_ENCERRADOS = ['Recusado', 'Concluído'];

    /** @var \PDO */
    private $conn;

    /** @var AnimalRepository */
    private $animalRepo;

    public function __construct()
    {
        $this->conn       = (new Connection())->getConn();
        $this->animalRepo = new AnimalRepository();
    }

    // -------------------------------------------------------------- resolução de perfis

    /** id do adotante a partir do login, ou null. */
    public function adotanteIdPorLogin($loginId)
    {
        return $this->escalarId('SELECT id FROM adotante WHERE fk_login_id = :id LIMIT 1', $loginId);
    }

    /** id da ONG a partir do login, ou null (vínculo ong.fk_login_id — ver memória). */
    public function ongIdPorLogin($loginId)
    {
        return $this->escalarId('SELECT id FROM ong WHERE fk_login_id = :id LIMIT 1', $loginId);
    }

    // -------------------------------------------------------------- regras de criação

    /**
     * Campos obrigatórios do perfil do adotante para poder adotar (RF#08).
     * Retorna a lista de campos faltando (array vazio = perfil completo).
     */
    public function perfilAdotanteIncompleto($adotanteId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT a.nome, a.cpf, a.data_nascimento, a.telefone_1,
                    a.cep, a.cidade, a.estado, l.email
             FROM adotante a
             INNER JOIN login l ON l.id = a.fk_login_id
             WHERE a.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $adotanteId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            return ['perfil'];
        }

        $faltando = [];
        foreach (['nome', 'cpf', 'data_nascimento', 'telefone_1', 'cep', 'cidade', 'estado', 'email'] as $campo) {
            if (!isset($row[$campo]) || trim((string) $row[$campo]) === '') {
                $faltando[] = $campo;
            }
        }
        return $faltando;
    }

    /** O animal existe e está disponível para adoção? */
    public function animalDisponivel($animalId): bool
    {
        $stmt = $this->conn->prepare('SELECT status FROM animal WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $animalId]);
        $status = $stmt->fetchColumn();
        return $status !== false && $status === 'disponivel';
    }

    /** Já existe solicitação ATIVA (não encerrada) deste adotante para este animal? */
    public function existeSolicitacaoAtiva($adotanteId, $animalId): bool
    {
        $placeholders = implode(',', array_fill(0, count(self::STATUS_ENCERRADOS), '?'));
        $sql = "SELECT 1 FROM solicitacao_adocao
                WHERE fk_adotante_id = ? AND fk_animal_id = ?
                  AND status NOT IN ({$placeholders})
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array_merge([(int) $adotanteId, (int) $animalId], self::STATUS_ENCERRADOS));
        return (bool) $stmt->fetchColumn();
    }

    /** Cria a solicitação (status inicial Pendente). Retorna o id. */
    public function criar($adotanteId, $animalId, $motivo, $aceiteTermo, $timestampAceite): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO solicitacao_adocao
             (data, status, motivo, fk_adotante_id, fk_animal_id, aceite_termo, timestamp_aceite, termo_assinado)
             VALUES (NOW(), 'Pendente', :motivo, :adotante, :animal, :aceite, :ts, 0)"
        );
        $stmt->execute([
            ':motivo'   => $motivo,
            ':adotante' => (int) $adotanteId,
            ':animal'   => (int) $animalId,
            ':aceite'   => $aceiteTermo ? 1 : 0,
            ':ts'       => $this->dataParaSql($timestampAceite),
        ]);
        return (int) $this->conn->lastInsertId();
    }

    // -------------------------------------------------------------- consultas

    /** Linha crua da solicitação (sem montar shape), ou null. */
    public function buscarLinha($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM solicitacao_adocao WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Solicitação montada (com animal), ou null. */
    public function buscarPorId($id, $incluirAdotante = false)
    {
        $row = $this->buscarLinha($id);
        return $row ? $this->montarSolicitacao($row, $incluirAdotante) : null;
    }

    /** Solicitações de um adotante (com animal), mais recentes primeiro. */
    public function listarDoAdotante($adotanteId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM solicitacao_adocao WHERE fk_adotante_id = :id ORDER BY id DESC'
        );
        $stmt->execute([':id' => (int) $adotanteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->montarSolicitacao($r, false);
        }
        return $out;
    }

    /** Solicitações recebidas por uma ONG (animais dela), com dados do adotante. */
    public function listarRecebidasDaOng($ongId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT s.* FROM solicitacao_adocao s
             INNER JOIN ong_animal oa ON oa.fk_animal_id = s.fk_animal_id
             WHERE oa.fk_ong_id = :ong_id
             ORDER BY s.id DESC"
        );
        $stmt->execute([':ong_id' => (int) $ongId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $out[] = $this->montarSolicitacao($r, true);
        }
        return $out;
    }

    /** A ONG é dona do animal desta solicitação? (autorização) */
    public function ongPossuiAnimalDaSolicitacao($solicitacaoId, $ongId): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT 1 FROM solicitacao_adocao s
             INNER JOIN ong_animal oa ON oa.fk_animal_id = s.fk_animal_id
             WHERE s.id = :sid AND oa.fk_ong_id = :ong_id LIMIT 1"
        );
        $stmt->execute([':sid' => (int) $solicitacaoId, ':ong_id' => (int) $ongId]);
        return (bool) $stmt->fetchColumn();
    }

    // -------------------------------------------------------------- mudança de status

    /**
     * Atualiza o status da solicitação. Ao concluir, marca o animal como adotado
     * (transação). Retorna a solicitação montada.
     */
    public function atualizarStatus($id, $novoStatus, $motivoRecusa = null)
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                'UPDATE solicitacao_adocao SET status = :status, motivo_recusa = :motivo WHERE id = :id'
            );
            $stmt->execute([
                ':status' => $novoStatus,
                ':motivo' => $novoStatus === 'Recusado' ? $motivoRecusa : null,
                ':id'     => (int) $id,
            ]);

            if ($novoStatus === 'Concluído') {
                $this->concluir((int) $id);
            }

            $this->conn->commit();
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }

        return $this->buscarPorId($id, true);
    }

    /** Marca o animal da solicitação como adotado (usado ao Concluir). */
    private function concluir($solicitacaoId)
    {
        $stmt = $this->conn->prepare(
            "UPDATE animal a
             INNER JOIN solicitacao_adocao s ON s.fk_animal_id = a.id
             SET a.status = 'adotado'
             WHERE s.id = :id"
        );
        $stmt->execute([':id' => (int) $solicitacaoId]);
    }

    // -------------------------------------------------------------- avaliação (C.7)

    public function avaliacaoExiste($solicitacaoId): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT 1 FROM avaliacao_adotante WHERE fk_solicitacao_id = :id LIMIT 1'
        );
        $stmt->execute([':id' => (int) $solicitacaoId]);
        return (bool) $stmt->fetchColumn();
    }

    public function criarAvaliacao($solicitacaoId, array $d, $criadoPor): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO avaliacao_adotante
             (fk_solicitacao_id, tipo_moradia, experiencia_previa, tem_criancas,
              tem_outros_animais, parecer, resultado, criado_em, criado_por)
             VALUES (:sid, :tipo, :exp, :criancas, :outros, :parecer, :resultado, NOW(), :por)"
        );
        $stmt->execute([
            ':sid'       => (int) $solicitacaoId,
            ':tipo'      => $d['tipo_moradia'],
            ':exp'       => !empty($d['experiencia_previa']) ? 1 : 0,
            ':criancas'  => !empty($d['tem_criancas']) ? 1 : 0,
            ':outros'    => !empty($d['tem_outros_animais']) ? 1 : 0,
            ':parecer'   => $d['parecer'],
            ':resultado' => $d['resultado'],
            ':por'       => $criadoPor,
        ]);
        return (int) $this->conn->lastInsertId();
    }

    public function buscarAvaliacao($solicitacaoId)
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM avaliacao_adotante WHERE fk_solicitacao_id = :id LIMIT 1'
        );
        $stmt->execute([':id' => (int) $solicitacaoId]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$r) {
            return null;
        }
        return [
            'id'                 => (int) $r['id'],
            'solicitacao_id'     => (int) $r['fk_solicitacao_id'],
            'tipo_moradia'       => $r['tipo_moradia'],
            'experiencia_previa' => (bool) $r['experiencia_previa'],
            'tem_criancas'       => (bool) $r['tem_criancas'],
            'tem_outros_animais' => (bool) $r['tem_outros_animais'],
            'parecer'            => $r['parecer'],
            'resultado'          => $r['resultado'],
            'criado_em'          => $r['criado_em'],
            'criado_por'         => $r['criado_por'],
        ];
    }

    // -------------------------------------------------------------- termo (C.4 / C.7)

    /** Garante a existência da linha de termo (gera conteudo_texto na 1ª vez). Retorna a linha. */
    public function termoGarantir($solicitacaoId)
    {
        $row = $this->termoLinha($solicitacaoId);
        if ($row) {
            return $row;
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO termo_adocao (fk_solicitacao_id, conteudo_texto, assinado)
             VALUES (:sid, :conteudo, 0)"
        );
        $stmt->execute([
            ':sid'      => (int) $solicitacaoId,
            ':conteudo' => $this->gerarConteudoTermo($solicitacaoId),
        ]);
        return $this->termoLinha($solicitacaoId);
    }

    public function termoLinha($solicitacaoId)
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM termo_adocao WHERE fk_solicitacao_id = :id LIMIT 1'
        );
        $stmt->execute([':id' => (int) $solicitacaoId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function termoAssinado($solicitacaoId): bool
    {
        $row = $this->termoLinha($solicitacaoId);
        return $row && (int) $row['assinado'] === 1;
    }

    /**
     * Registra a assinatura do termo (IP/user-agent capturados server-side) e
     * conclui a adoção (status Concluído + animal adotado), tudo em transação.
     */
    public function assinarTermo($solicitacaoId, $ip, $userAgent)
    {
        $this->termoGarantir($solicitacaoId);

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                "UPDATE termo_adocao
                 SET assinado = 1, data_assinatura = NOW(), ip_assinatura = :ip, user_agent = :ua
                 WHERE fk_solicitacao_id = :sid"
            );
            $stmt->execute([
                ':ip'  => $ip,
                ':ua'  => $userAgent,
                ':sid' => (int) $solicitacaoId,
            ]);

            // Reflete no agregado da solicitação e conclui a adoção.
            $stmt2 = $this->conn->prepare(
                "UPDATE solicitacao_adocao
                 SET termo_assinado = 1, status = 'Concluído'
                 WHERE id = :sid"
            );
            $stmt2->execute([':sid' => (int) $solicitacaoId]);

            $this->concluir((int) $solicitacaoId);

            $this->conn->commit();
        } catch (\Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }

        return $this->montarTermo($this->termoLinha($solicitacaoId));
    }

    /** Monta o shape do termo (spec C.4). pdf_url/pdf_assinado_url são STUB (null). */
    public function montarTermo(array $t): array
    {
        return [
            'solicitacao_id'   => (int) $t['fk_solicitacao_id'],
            'pdf_url'          => $t['pdf_url'],            // TODO(PDF): gerar via TCPDF
            'pdf_assinado_url' => $t['pdf_assinado_url'],  // TODO(PDF): gerar via TCPDF na assinatura
            'conteudo_texto'   => $t['conteudo_texto'],
            'assinado'         => (bool) $t['assinado'],
            'data_assinatura'  => $t['data_assinatura'],
        ];
    }

    // -------------------------------------------------------------- helpers

    /** Monta o shape de Solicitacao (com animal; opcionalmente com dados do adotante). */
    private function montarSolicitacao(array $s, $incluirAdotante): array
    {
        $out = [
            'id'               => (int) $s['id'],
            'data'             => $s['data'],
            'status'           => $s['status'],
            'motivo'           => $s['motivo'],
            'motivo_recusa'    => $s['motivo_recusa'],
            'termo_assinado'   => (bool) $s['termo_assinado'],
            'pdf_termo_url'    => $s['pdf_termo_url'],
            'aceite_termo'     => (bool) $s['aceite_termo'],
            'timestamp_aceite' => $s['timestamp_aceite'],
            'animal'           => $this->animalRepo->buscarPorId((int) $s['fk_animal_id']),
        ];

        if ($incluirAdotante) {
            $dados = $this->dadosAdotante((int) $s['fk_adotante_id']);
            $out['adotante_nome']  = $dados ? $dados['nome'] : null;
            $out['adotante_email'] = $dados ? $dados['email'] : null;
        }

        return $out;
    }

    private function dadosAdotante($adotanteId)
    {
        $stmt = $this->conn->prepare(
            "SELECT a.nome, l.email
             FROM adotante a
             INNER JOIN login l ON l.id = a.fk_login_id
             WHERE a.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $adotanteId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Gera o texto do termo de responsabilidade a partir do adotante e do animal. */
    private function gerarConteudoTermo($solicitacaoId): string
    {
        $stmt = $this->conn->prepare(
            "SELECT a.nome AS adotante_nome, a.cpf AS adotante_cpf,
                    an.nome AS animal_nome, e.nome AS especie_nome
             FROM solicitacao_adocao s
             INNER JOIN adotante a ON a.id = s.fk_adotante_id
             INNER JOIN animal an ON an.id = s.fk_animal_id
             LEFT JOIN especie e ON e.id = an.fk_especie_id
             WHERE s.id = :id LIMIT 1"
        );
        $stmt->execute([':id' => (int) $solicitacaoId]);
        $d = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$d) {
            return 'Termo de Responsabilidade de Adoção.';
        }

        $especie = $d['especie_nome'] ? $d['especie_nome'] : 'animal';
        $nome    = $d['adotante_nome'];
        $cpf     = $d['adotante_cpf'];
        $animal  = $d['animal_nome'];

        return
            "TERMO DE RESPONSABILIDADE E GUARDA RESPONSÁVEL\n\n" .
            "Eu, {$nome}, inscrito(a) no CPF {$cpf}, declaro para os devidos fins que assumo " .
            "a adoção e a guarda responsável do animal \"{$animal}\" ({$especie}), comprometendo-me a:\n\n" .
            "1. Oferecer alimentação adequada, água, abrigo, higiene e cuidados veterinários;\n" .
            "2. Não abandonar, maltratar ou submeter o animal a maus-tratos, sob pena da Lei nº 9.605/1998;\n" .
            "3. Manter a vacinação e a vermifugação em dia e zelar pela castração quando indicada;\n" .
            "4. Comunicar à organização responsável eventual impossibilidade de manter a guarda;\n" .
            "5. Permitir acompanhamento posterior da adaptação do animal, quando solicitado.\n\n" .
            "Declaro estar ciente de que a presente adoção é um ato de responsabilidade e afeto, " .
            "assumido de livre e espontânea vontade.\n\n" .
            "A assinatura eletrônica deste termo, nos termos da Lei nº 14.063/2020, será registrada " .
            "com data, endereço IP e identificação do dispositivo no momento do aceite.";
    }

    /** Converte data/datetime (ISO ou similar) para 'Y-m-d H:i:s', ou null. */
    private function dataParaSql($valor)
    {
        if (empty($valor)) {
            return null;
        }
        $ts = strtotime($valor);
        return $ts === false ? null : date('Y-m-d H:i:s', $ts);
    }

    private function escalarId($sql, $id)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (int) $v;
    }
}
