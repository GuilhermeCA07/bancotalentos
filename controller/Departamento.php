<?php

require_once 'model/DepartamentoModel.php';
require_once 'helper/Autorizacao.php';
require_once 'helper/Retorno.php';

class Departamento
{
    private $model;

    public function __construct()
    {
        validarPermissaoDepartamento();
        $this->model = new DepartamentoModel();
    }

    public function index()
    {
        $filtros = [
            'busca' => trim($_GET['busca'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'pagina' => max(1, (int)($_GET['pagina'] ?? 1))
        ];
        $registrosPorPagina = 10;
        $totalRegistros = $this->model->contarRegistros($filtros);
        $totalPaginas = (int)ceil($totalRegistros / $registrosPorPagina);
        $paginaAtual = $filtros['pagina'];
        $offset = ($paginaAtual - 1) * $registrosPorPagina;
        $departamentos = $this->model->buscarTodos(
            $filtros,
            $registrosPorPagina,
            $offset
        );

        salvarRetorno();
        include 'view/template/cabecalho.php';
        include 'view/template/menu.php';
        include 'view/departamento/listagem.php';
        include 'view/template/paginacao.php';
        include 'view/template/rodape.php';
    }

    public function add()
    {
        include 'view/template/cabecalho.php';
        include 'view/template/menu.php';
        include 'view/departamento/form.php';
        include 'view/template/rodape.php';
    }

    public function editar($id)
    {
        $departamento = $this->model->buscarPorId((int)$id);

        if (!$departamento) {
            $_SESSION['erro'] = 'Departamento não encontrado.';
            header('Location:?c=departamento');
            exit;
        }

        include 'view/template/cabecalho.php';
        include 'view/template/menu.php';
        include 'view/departamento/form.php';
        include 'view/template/rodape.php';
    }

    public function salvar()
    {
        $id = (int)($_POST['idDepartamento'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $cor = strtoupper(trim($_POST['cor'] ?? ''));

        if ($nome === '' || mb_strlen($nome) > 100) {
            $_SESSION['erro'] = 'Informe um nome de departamento válido.';
            header('Location:?c=departamento' . ($id ? '&m=editar&id=' . $id : '&m=add'));
            exit;
        }

        if (!preg_match('/^#[0-9A-F]{6}$/', $cor)) {
            $_SESSION['erro'] = 'Selecione uma cor válida.';
            header('Location:?c=departamento' . ($id ? '&m=editar&id=' . $id : '&m=add'));
            exit;
        }

        try {
            if ($id) {
                $this->model->atualizar($id, $nome, $cor);
            } else {
                $this->model->inserir($nome, $cor);
            }
            $_SESSION['sucesso'] = 'Departamento salvo com sucesso.';
        } catch (mysqli_sql_exception $e) {
            $_SESSION['erro'] = 'Já existe um departamento com esse nome.';
        }

        voltarParaRetorno('Location:?c=departamento');
    }

    public function alterarStatus($id)
    {
        $this->model->alterarStatus((int)$id);
        voltarParaRetorno('Location:?c=departamento');
    }
}
