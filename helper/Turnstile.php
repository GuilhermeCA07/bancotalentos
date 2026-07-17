<?php

require_once __DIR__ . '/../config/config.php';

class Turnstile
{
    public static function siteKey()
    {
        return TURNSTILE_SITE_KEY;
    }

    public static function validar($token, $acaoEsperada = null)
    {
        $token = trim((string)$token);

        if ($token === '' || strlen($token) > 2048) {
            return false;
        }

        $dados = [
            'secret' => TURNSTILE_SECRET,
            'response' => $token
        ];
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '';

        if ($ip !== '') {
            $dados['remoteip'] = $ip;
        }

        $contexto = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($dados),
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ]);
        $resultado = @file_get_contents(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            false,
            $contexto
        );

        if ($resultado === false) {
            return false;
        }

        $resposta = json_decode($resultado, true);

        if (empty($resposta['success'])) {
            return false;
        }

        return $acaoEsperada === null
            || ($resposta['action'] ?? '') === $acaoEsperada;
    }
}
