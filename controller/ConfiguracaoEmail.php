<?php

require_once 'model/EmailConfiguracaoModel.php';
require_once 'helper/Autorizacao.php';
require_once 'helper/Mail.php';

class ConfiguracaoEmail
{
    private $model;

    public function __construct()
    {
        validarPermissaoConfiguracaoEmail();
        $this->model = new EmailConfiguracaoModel();
    }

    public function index()
    {
        $configuracaoEmail = $this->model->buscar();
        $csrfToken = $this->csrfToken();

        include 'view/template/cabecalho.php';
        include 'view/template/menu.php';
        include 'view/configuracaoEmail/index.php';
        include 'view/template/rodape.php';
    }

    public function salvar()
    {
        $this->validarCsrf();

        $dados = [
            'smtp_host' => strtolower(trim($_POST['smtp_host'] ?? '')),
            'smtp_port' => (int)($_POST['smtp_port'] ?? 0),
            'smtp_usuario' => trim($_POST['smtp_usuario'] ?? ''),
            'smtp_criptografia' => strtolower(trim(
                $_POST['smtp_criptografia'] ?? ''
            )),
            'email_remetente' => trim($_POST['email_remetente'] ?? ''),
            'nome_remetente' => trim($_POST['nome_remetente'] ?? '')
        ];
        $senha = trim($_POST['smtp_senha'] ?? '');
        $erro = $this->validarDados($dados, $senha);

        if ($erro !== null) {
            $_SESSION['erro'] = $erro;
            header('Location:?c=configuracaoEmail');
            exit;
        }

        try {
            $this->model->salvar(
                $dados,
                $senha,
                (int)($_SESSION['usuario']['idUsuario'] ?? 0)
            );
            $_SESSION['sucesso'] =
                'Configuração de e-mail atualizada com sucesso.';
        } catch (Throwable $e) {
            error_log('Falha ao salvar SMTP: ' . $e->getMessage());
            $_SESSION['erro'] =
                'Não foi possível salvar a configuração de e-mail.';
        }

        header('Location:?c=configuracaoEmail');
        exit;
    }

    public function testar()
    {
        $this->validarCsrf();
        $destinatario = trim($_POST['email_teste'] ?? '');

        if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'Informe um e-mail válido para o teste.';
            header('Location:?c=configuracaoEmail');
            exit;
        }

        $resultado = Mail::enviarTeste($destinatario);

        if ($resultado === true) {
            $this->model->marcarTesteEnviado();
            $_SESSION['sucesso'] =
                'E-mail de teste enviado para ' . $destinatario . '.';
        } else {
            $detalhe = is_array($resultado)
                ? trim((string)($resultado['erro'] ?? ''))
                : '';
            $_SESSION['erro'] = 'Falha no teste SMTP.'
                . ($detalhe !== '' ? ' Detalhe: ' . $detalhe : '');
        }

        header('Location:?c=configuracaoEmail');
        exit;
    }

    private function validarDados($dados, $senha)
    {
        if (
            $dados['smtp_host'] === ''
            || strlen($dados['smtp_host']) > 255
            || !preg_match('/^[a-z0-9.-]+$/i', $dados['smtp_host'])
        ) {
            return 'Informe um servidor SMTP válido.';
        }

        if ($dados['smtp_port'] < 1 || $dados['smtp_port'] > 65535) {
            return 'Informe uma porta SMTP válida.';
        }

        if (!filter_var($dados['smtp_usuario'], FILTER_VALIDATE_EMAIL)) {
            return 'Informe um usuário SMTP em formato de e-mail.';
        }

        if (!filter_var($dados['email_remetente'], FILTER_VALIDATE_EMAIL)) {
            return 'Informe um e-mail de remetente válido.';
        }

        if (
            $dados['nome_remetente'] === ''
            || strlen($dados['nome_remetente']) > 150
        ) {
            return 'Informe o nome do remetente.';
        }

        if (!in_array(
            $dados['smtp_criptografia'],
            ['tls', 'ssl', 'nenhuma'],
            true
        )) {
            return 'Selecione uma criptografia SMTP válida.';
        }

        $atual = $this->model->buscar();
        if (!$atual['senha_configurada'] && $senha === '') {
            return 'Informe a senha SMTP na primeira configuração.';
        }

        return null;
    }

    private function validarCsrf()
    {
        $token = (string)($_POST['csrf_token'] ?? '');

        if (!hash_equals($this->csrfToken(), $token)) {
            $_SESSION['erro'] =
                'A sessão do formulário expirou. Tente novamente.';
            header('Location:?c=configuracaoEmail');
            exit;
        }
    }

    private function csrfToken()
    {
        if (empty($_SESSION['csrf_email'])) {
            $_SESSION['csrf_email'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_email'];
    }
}
