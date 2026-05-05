<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use App\Model\SolicitacaoAdocaoModel;
use App\DAO\SolicitacaoAdocaoDAO;

// Carrega as variáveis do .env (DB_HOST, DB_NAME, etc.)
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dao = new SolicitacaoAdocaoDAO();

$obj = new SolicitacaoAdocaoModel();
$obj->__set('solAdc_data', date('Y-m-d'));
$obj->__set('solAdc_status', 'pendente');
$obj->__set('solAdc_motivo', 'Teste de inserção');
$obj->__set('fk_adotante_id', 1);
$obj->__set('fk_animal_id', 1);

$dao->inserir($obj);

// Pega o ID do último registro inserido
$ultimoId = $dao->getConn()->lastInsertId();
echo "Inserção OK! ID gerado: " . $ultimoId . "\n";