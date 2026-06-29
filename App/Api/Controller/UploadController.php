<?php

namespace App\Api\Controller;

use App\Api\ApiController;
use App\Api\ApiException;

/**
 * Módulo de Upload (seção C.10 da especificação).
 * Endpoint: POST /upload (protegido por JWT).
 *
 * Aceita multipart/form-data com campo "arquivo".
 * Imagens (JPG/PNG/GIF/WebP) até 5 MB, PDF até 10 MB.
 * Validação de MIME real via finfo.
 *
 * Sintaxe PHP 7.0.
 */
class UploadController extends ApiController
{
 /** Tamanho máximo para imagens em bytes (5 MB). */
 const MAX_IMAGEM = 5242880;

 /** Tamanho máximo para PDF em bytes (10 MB). */
 const MAX_PDF = 10485760;

 /** Mapa de MIME permitidos para extensão correspondente. */
 const MIME_MAP = [
  'image/jpeg'      => '.jpg',
  'image/png'       => '.png',
  'image/gif'       => '.gif',
  'image/webp'      => '.webp',
  'application/pdf' => '.pdf',
 ];

 /** MIME types de imagem. */
 const MIME_IMAGEM = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

 /**
  * POST /upload — Recebe um arquivo via multipart/form-data.
  * Valida MIME real, tamanho conforme tipo, gera nome seguro e
  * retorna a URL absoluta do arquivo salvo.
  */
 public function upload()
 {
  $this->exigirAutenticacao();

  if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] === UPLOAD_ERR_NO_FILE) {
   throw new ApiException('Nenhum arquivo enviado.', 400);
  }

  $arquivo = $_FILES['arquivo'];

  if ($arquivo['error'] !== UPLOAD_ERR_OK) {
   throw new ApiException('Erro no upload do arquivo.', 400);
  }

  // MIME real via finfo — não confia na extensão do cliente
  $finfo  = new \finfo(FILEINFO_MIME_TYPE);
  $mime   = $finfo->file($arquivo['tmp_name']);

  if (!isset(self::MIME_MAP[$mime])) {
   throw new ApiException(
    'Tipo de arquivo não permitido. Envie JPG, PNG, GIF, WebP ou PDF.',
    400
   );
  }

  // Limite conforme o tipo detectado
  $limite = in_array($mime, self::MIME_IMAGEM, true)
   ? self::MAX_IMAGEM
   : self::MAX_PDF;

  if ($arquivo['size'] > $limite) {
   $limiteMb = $limite / 1048576;
   throw new ApiException(
    'Arquivo excede o limite de ' . $limiteMb . ' MB.',
    400
   );
  }

  // Diretório de destino — cria recursivamente se não existir
  $diretorio = __DIR__ . '/../../resources/uploads';
  if (!is_dir($diretorio)) {
   mkdir($diretorio, 0755, true);
  }

  // Nome seguro: 32 hex chars + extensão mapeada do MIME
  $nomeSeguro = bin2hex(random_bytes(16)) . self::MIME_MAP[$mime];
  $caminho    = $diretorio . '/' . $nomeSeguro;

  if (!move_uploaded_file($arquivo['tmp_name'], $caminho)) {
   throw new ApiException('Falha ao salvar o arquivo.', 500);
  }

  // URL absoluta
  $baseUrl = $this->obterBaseUrl();
  $url     = rtrim($baseUrl, '/') . '/resources/uploads/' . $nomeSeguro;

  $this->json(['url' => $url]);
 }

 /**
  * Monta a URL base da aplicação.
  * Prioriza $_ENV['BASE_URL']; fallback para $_SERVER.
  */
 private function obterBaseUrl()
 {
  $env = isset($_ENV['BASE_URL']) ? trim($_ENV['BASE_URL']) : '';
  if ($env !== '') {
   return $env;
  }

  $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
  $host   = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
  $port   = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;

  $url = $scheme . '://' . $host;
  if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
   $url .= ':' . $port;
  }

  return $url;
 }
}
