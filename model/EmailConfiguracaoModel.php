<?php

require_once 'config/Conexao.php';
require_once 'config/config.php';

class EmailConfiguracaoModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function buscar()
    {
        $resultado = $this->conexao->query(
            'SELECT *
             FROM configuracoes_email
             ORDER BY idConfiguracaoEmail
             LIMIT 1'
        );

        $dados = $resultado
            ? $resultado->fetch_assoc()
            : null;

        if (!$dados) {
            return [
                'idConfiguracaoEmail' => null,
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_usuario' => '',
                'smtp_criptografia' => 'tls',
                'email_remetente' => '',
                'nome_remetente' => 'Banco de Talentos',
                'senha_configurada' => false,
                'atualizado_em' => null,
                'testado_em' => null
            ];
        }

        $dados['smtp_port'] = (int)$dados['smtp_port'];
        $dados['senha_configurada'] = !empty($dados['smtp_senha']);
        unset($dados['smtp_senha']);

        return $dados;
    }

    public function buscarParaEnvio()
    {
        $resultado = $this->conexao->query(
            'SELECT *
             FROM configuracoes_email
             ORDER BY idConfiguracaoEmail
             LIMIT 1'
        );
        $dados = $resultado
            ? $resultado->fetch_assoc()
            : null;

        if (!$dados) {
            throw new RuntimeException(
                'A configuração de e-mail ainda não foi cadastrada.'
            );
        }

        $dados['smtp_senha'] =
            $this->descriptografar($dados['smtp_senha']);
        $dados['smtp_port'] = (int)$dados['smtp_port'];

        return $dados;
    }

    public function salvar($dados, $senha, $idUsuario)
    {
        $atual = $this->buscar();
        $senhaCriptografada = null;

        if ($senha !== '') {
            $senhaCriptografada = $this->criptografar($senha);
        }

        if (!empty($atual['idConfiguracaoEmail'])) {
            if ($senhaCriptografada !== null) {
                $sql = '
                    UPDATE configuracoes_email
                    SET smtp_host = ?, smtp_port = ?, smtp_usuario = ?,
                        smtp_senha = ?, smtp_criptografia = ?,
                        email_remetente = ?, nome_remetente = ?,
                        atualizado_por = ?, atualizado_em = NOW()
                    WHERE idConfiguracaoEmail = ?
                ';
                $comando = $this->conexao->prepare($sql);
                $comando->bind_param(
                    'sisssssii',
                    $dados['smtp_host'],
                    $dados['smtp_port'],
                    $dados['smtp_usuario'],
                    $senhaCriptografada,
                    $dados['smtp_criptografia'],
                    $dados['email_remetente'],
                    $dados['nome_remetente'],
                    $idUsuario,
                    $atual['idConfiguracaoEmail']
                );
            } else {
                $sql = '
                    UPDATE configuracoes_email
                    SET smtp_host = ?, smtp_port = ?, smtp_usuario = ?,
                        smtp_criptografia = ?, email_remetente = ?,
                        nome_remetente = ?, atualizado_por = ?,
                        atualizado_em = NOW()
                    WHERE idConfiguracaoEmail = ?
                ';
                $comando = $this->conexao->prepare($sql);
                $comando->bind_param(
                    'sissssii',
                    $dados['smtp_host'],
                    $dados['smtp_port'],
                    $dados['smtp_usuario'],
                    $dados['smtp_criptografia'],
                    $dados['email_remetente'],
                    $dados['nome_remetente'],
                    $idUsuario,
                    $atual['idConfiguracaoEmail']
                );
            }

            return $comando->execute();
        }

        if ($senhaCriptografada === null) {
            throw new InvalidArgumentException(
                'Informe a senha SMTP na primeira configuração.'
            );
        }

        $sql = '
            INSERT INTO configuracoes_email
            (
                smtp_host, smtp_port, smtp_usuario, smtp_senha,
                smtp_criptografia, email_remetente, nome_remetente,
                atualizado_por, atualizado_em
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ';
        $comando = $this->conexao->prepare($sql);
        $comando->bind_param(
            'sisssssi',
            $dados['smtp_host'],
            $dados['smtp_port'],
            $dados['smtp_usuario'],
            $senhaCriptografada,
            $dados['smtp_criptografia'],
            $dados['email_remetente'],
            $dados['nome_remetente'],
            $idUsuario
        );

        return $comando->execute();
    }

    public function marcarTesteEnviado()
    {
        return $this->conexao->query(
            'UPDATE configuracoes_email SET testado_em = NOW()'
        );
    }

    private function criptografar($valor)
    {
        $chave = hash('sha256', EMAIL_CONFIG_KEY, true);
        $iv = random_bytes(12);
        $tag = '';
        $conteudo = openssl_encrypt(
            $valor,
            'aes-256-gcm',
            $chave,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($conteudo === false) {
            throw new RuntimeException('Não foi possível proteger a senha SMTP.');
        }

        return base64_encode($iv . $tag . $conteudo);
    }

    private function descriptografar($valor)
    {
        $conteudo = base64_decode($valor, true);

        if ($conteudo === false || strlen($conteudo) < 29) {
            throw new RuntimeException('A senha SMTP armazenada é inválida.');
        }

        $iv = substr($conteudo, 0, 12);
        $tag = substr($conteudo, 12, 16);
        $cifrado = substr($conteudo, 28);
        $senha = openssl_decrypt(
            $cifrado,
            'aes-256-gcm',
            hash('sha256', EMAIL_CONFIG_KEY, true),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($senha === false) {
            throw new RuntimeException('Não foi possível ler a senha SMTP.');
        }

        return $senha;
    }
}
