<?php

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

class DoisFatores
{
    private $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function gerarSegredo()
    {
        return $this->google2fa->generateSecretKey(32);
    }

    public function gerarQrCode($email, $segredo)
    {
        $uri = $this->google2fa->getQRCodeUrl(
            'Banco de Talentos',
            (string)$email,
            (string)$segredo
        );
        $renderer = new ImageRenderer(
            new RendererStyle(280, 2),
            new SvgImageBackEnd()
        );
        $svg = (new Writer($renderer))->writeString($uri);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function validarCodigoNovo($segredo, $codigo, $ultimoPeriodo = null)
    {
        $codigo = preg_replace('/\D/', '', (string)$codigo);

        if (strlen($codigo) !== 6) {
            return false;
        }

        return $this->google2fa->verifyKeyNewer(
            (string)$segredo,
            $codigo,
            $ultimoPeriodo !== null ? (int)$ultimoPeriodo : 0,
            1
        );
    }

    public function criptografar($segredo)
    {
        $iv = random_bytes(12);
        $tag = '';
        $conteudo = openssl_encrypt(
            (string)$segredo,
            'aes-256-gcm',
            hash('sha256', TWO_FACTOR_KEY, true),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($conteudo === false) {
            throw new RuntimeException('Não foi possível proteger o segredo do 2FA.');
        }

        return base64_encode($iv . $tag . $conteudo);
    }

    public function descriptografar($valor)
    {
        $conteudo = base64_decode((string)$valor, true);

        if ($conteudo === false || strlen($conteudo) < 29) {
            throw new RuntimeException('O segredo do 2FA armazenado é inválido.');
        }

        $segredo = openssl_decrypt(
            substr($conteudo, 28),
            'aes-256-gcm',
            hash('sha256', TWO_FACTOR_KEY, true),
            OPENSSL_RAW_DATA,
            substr($conteudo, 0, 12),
            substr($conteudo, 12, 16)
        );

        if ($segredo === false) {
            throw new RuntimeException('Não foi possível ler o segredo do 2FA.');
        }

        return $segredo;
    }
}
