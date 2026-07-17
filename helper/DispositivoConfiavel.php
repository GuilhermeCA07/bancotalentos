<?php

require_once __DIR__ . '/../config/Conexao.php';

class DispositivoConfiavel
{
    private const COOKIE = 'talentos_2fa_confiavel';
    private const DURACAO = 2592000;

    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function validar($idUsuario)
    {
        $cookie = (string)($_COOKIE[self::COOKIE] ?? '');
        $partes = explode('.', $cookie, 2);

        if (
            count($partes) !== 2
            || !preg_match('/^[A-Za-z0-9_-]{24}$/', $partes[0])
            || !preg_match('/^[A-Za-z0-9_-]{43}$/', $partes[1])
        ) {
            if ($cookie !== '') {
                $this->limparCookie();
            }

            return false;
        }

        [$seletor, $validador] = $partes;
        $this->limparExpirados();
        $comando = $this->conexao->prepare(
            'SELECT idDispositivo, token_hash, expira_em
             FROM usuarios_dispositivos_confiaveis
             WHERE usuario_id = ?
             AND seletor = ?
             AND expira_em > NOW()
             LIMIT 1'
        );
        $comando->bind_param('is', $idUsuario, $seletor);
        $comando->execute();
        $registro = $comando->get_result()->fetch_assoc();
        $hashRecebido = hash('sha256', $validador);

        if (!$registro || !hash_equals($registro['token_hash'], $hashRecebido)) {
            $this->limparCookie();
            return false;
        }

        $novoValidador = $this->token(32);
        $novoHash = hash('sha256', $novoValidador);
        $atualizar = $this->conexao->prepare(
            'UPDATE usuarios_dispositivos_confiaveis
             SET token_hash = ?, ultimo_uso_em = NOW()
             WHERE idDispositivo = ?
             AND token_hash = ?'
        );
        $atualizar->bind_param(
            'sis',
            $novoHash,
            $registro['idDispositivo'],
            $registro['token_hash']
        );
        $atualizar->execute();

        if ($atualizar->affected_rows !== 1) {
            $this->limparCookie();
            return false;
        }

        $this->definirCookie(
            $seletor . '.' . $novoValidador,
            strtotime($registro['expira_em'])
        );

        return true;
    }

    public function confiar($idUsuario)
    {
        $this->limparExpirados();
        $seletor = $this->token(18);
        $validador = $this->token(32);
        $hash = hash('sha256', $validador);
        $comando = $this->conexao->prepare(
            'INSERT INTO usuarios_dispositivos_confiaveis
                (usuario_id, seletor, token_hash, expira_em)
             VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))'
        );
        $comando->bind_param('iss', $idUsuario, $seletor, $hash);

        if (!$comando->execute()) {
            return false;
        }

        $this->limitarDispositivos($idUsuario);
        $this->definirCookie(
            $seletor . '.' . $validador,
            time() + self::DURACAO
        );

        return true;
    }

    public function revogarUsuario($idUsuario, $limparCookieAtual = false)
    {
        $comando = $this->conexao->prepare(
            'DELETE FROM usuarios_dispositivos_confiaveis
             WHERE usuario_id = ?'
        );
        $comando->bind_param('i', $idUsuario);
        $executado = $comando->execute();

        if ($limparCookieAtual) {
            $this->limparCookie();
        }

        return $executado;
    }

    private function limparExpirados()
    {
        $this->conexao->query(
            'DELETE FROM usuarios_dispositivos_confiaveis
             WHERE expira_em <= NOW()'
        );
    }

    private function limitarDispositivos($idUsuario)
    {
        $comando = $this->conexao->prepare(
            'DELETE FROM usuarios_dispositivos_confiaveis
             WHERE usuario_id = ?
             AND idDispositivo NOT IN (
                 SELECT idDispositivo FROM (
                     SELECT idDispositivo
                     FROM usuarios_dispositivos_confiaveis
                     WHERE usuario_id = ?
                     ORDER BY criado_em DESC, idDispositivo DESC
                     LIMIT 10
                 ) dispositivos_recentes
             )'
        );
        $comando->bind_param('ii', $idUsuario, $idUsuario);
        $comando->execute();
    }

    private function definirCookie($valor, $expiraEm)
    {
        setcookie(self::COOKIE, $valor, [
            'expires' => $expiraEm,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        $_COOKIE[self::COOKIE] = $valor;
    }

    private function limparCookie()
    {
        setcookie(self::COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        unset($_COOKIE[self::COOKIE]);
    }

    private function token($bytes)
    {
        return rtrim(
            strtr(base64_encode(random_bytes($bytes)), '+/', '-_'),
            '='
        );
    }
}
