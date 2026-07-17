<?php

require_once 'model/ConfiguracaoModel.php';
require_once 'helper/Autorizacao.php';

class Configuracao
{
    private $model;

    public function __construct()
    {
        validarPermissaoTema();
        $this->model = new ConfiguracaoModel();
    }

    public function index()
    {
        $configuracao = $this->model->buscar();
        $csrfToken = $this->csrfToken();

        include 'view/template/cabecalho.php';
        include 'view/template/menu.php';
        include 'view/configuracao/index.php';
        include 'view/template/rodape.php';
    }

    public function salvar()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!hash_equals($this->csrfToken(), $token)) {
            $_SESSION['erro'] =
                'A sessão do formulário expirou. Tente novamente.';
            header('Location:?c=configuracao');
            exit;
        }

        $corPrimaria = strtoupper(trim($_POST['corPrimaria'] ?? ''));
        $corSecundaria = strtoupper(trim($_POST['corSecundaria'] ?? ''));
        $identidade = strtolower(trim($_POST['identidade'] ?? 'padrao'));

        foreach ([$corPrimaria, $corSecundaria] as $cor) {
            if (!preg_match('/^#[0-9A-F]{6}$/', $cor)) {
                $_SESSION['erro'] = 'Selecione cores válidas para o tema.';
                header('Location:?c=configuracao');
                exit;
            }
        }

        if (!in_array(
            $identidade,
            ['padrao', 'netcom', 'sumernet', 'netaki'],
            true
        )) {
            $_SESSION['erro'] = 'Selecione uma identidade visual válida.';
            header('Location:?c=configuracao');
            exit;
        }

        $this->model->salvar(
            $corPrimaria,
            $corSecundaria,
            $identidade
        );
        $_SESSION['sucesso'] = 'Tema atualizado com sucesso.';

        header('Location:?c=configuracao');
        exit;
    }

    private function csrfToken()
    {
        if (empty($_SESSION['csrf_tema'])) {
            $_SESSION['csrf_tema'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_tema'];
    }
}
