<?php
require "model/VagaModel.php";
require_once "model/CandidaturaModel.php";
require_once "model/DepartamentoModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Vaga
{
    private $model;
    private $candidaturaModel;
    private $departamentoModel;

    function __construct()
    {

    validarPermissao('vaga');

        $this->model =
            new VagaModel();

        $this->candidaturaModel =
            new CandidaturaModel();

        $this->departamentoModel =
            new DepartamentoModel();
    }

    function index()
    {

        $filtros = [

            'busca' =>
            trim($_GET['busca'] ?? ''),

            'departamento_id' =>
            (int)($_GET['departamento_id'] ?? 0),

            'modalidade' =>
            $_GET['modalidade'] ?? '',

            'escala' =>
            $_GET['escala'] ?? '',

            'cidade' =>
            trim($_GET['cidade'] ?? ''),

            'status' =>
            $_GET['status'] ?? '',

            'pagina' =>
            (int)($_GET['pagina'] ?? 1)

        ];

        $totalRegistros =
            $this->model
            ->contarRegistros(
                $filtros
            );

        $registrosPorPagina = 10;

        $totalPaginas =
            ceil(
                $totalRegistros
                    / $registrosPorPagina
            );

        $paginaAtual =
            max(
                1,
                $filtros['pagina']
            );

        $offset =
            ($paginaAtual - 1)
            * $registrosPorPagina;

        $vagas =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        $departamentos =
            $this->departamentoModel
            ->buscarAtivos();

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/vaga/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
        $departamentos =
            $this->departamentoModel
            ->buscarAtivos();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/vaga/form.php";
        include "view/template/rodape.php";
    }

    function editar($id)
    {
        $vaga =
            $this->model
            ->buscarPorId($id);

        if (!$vaga) {
            $_SESSION['erro'] = 'Vaga não encontrada.';
            voltarParaRetorno('Location:?c=vaga');
        }

        $departamentos =
            $this->departamentoModel
            ->buscarAtivos($vaga['departamento_id'] ?? null);

        if (
            $vaga['status']
            == 'Fechada'
        ) {

            voltarParaRetorno("Location:?c=vaga");
        }

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/vaga/form.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {

        $departamentoId = (int)($_POST['departamento_id'] ?? 0);
        $departamento =
            $this->departamentoModel
            ->buscarPorId($departamentoId);

        if (!$departamento || !$departamento['ativo']) {
            $_SESSION['erro'] = 'Selecione um departamento ativo.';
            voltarParaRetorno('Location:?c=vaga');
        }


        if (empty($_POST['idVaga'])) {



            $salario =
                str_replace(
                    ".",
                    "",
                    $_POST['salario']
                );

            $salario =
                str_replace(
                    ",",
                    ".",
                    $salario
                );

            $this->model->inserir(
                $_POST['titulo'],
                $departamentoId,
                $_POST['quantidade_vagas'],
                $_POST['cidade'],
                $_POST['modalidade'],
                $_POST['escala'],
                $_POST['tipo_contratacao'],
                $salario,
                $_POST['descricao'],
                $_POST['requisitos'],
                $_POST['observacoes'],
                $_POST['status'],
                $_POST['cnh_obrigatoria']
            );
        } else {

            $salario =
                str_replace(
                    ".",
                    "",
                    $_POST['salario']
                );

            $salario =
                str_replace(
                    ",",
                    ".",
                    $salario
                );

            $this->model->atualizar(
                $_POST['idVaga'],
                $_POST['titulo'],
                $departamentoId,
                $_POST['quantidade_vagas'],
                $_POST['cidade'],
                $_POST['modalidade'],
                $_POST['escala'],
                $_POST['tipo_contratacao'],
                $salario,
                $_POST['descricao'],
                $_POST['requisitos'],
                $_POST['observacoes'],
                $_POST['status'],
                $_POST['cnh_obrigatoria']
            );
        }

        voltarParaRetorno("Location:?c=vaga");
    }

    function alterarStatus($id)
    {
        $status =
            $_GET['status'];

        $vaga =
            $this->model
            ->buscarPorId($id);

        // Vaga fechada não pode mais mudar

        if (
            $vaga['status'] ==
            'Fechada'
        ) {

            header(
                "Location:?c=vaga"
            );

            exit;
        }

        // Status permitidos

        $permitidos = [
            'Aberta',
            'Pausada',
            'Fechada'
        ];

        if (
            !in_array(
                $status,
                $permitidos
            )
        ) {

            header(
                "Location:?c=vaga"
            );

            exit;
        }

        $this->model
            ->alterarStatus(
                $id,
                $status
            );

        // NOVO BLOCO

        if (
            $status == 'Fechada'
        ) {

            $this->candidaturaModel
                ->encerrarPorFechamentoVaga(
                    $id,
                    'Vaga Fechada'
                );
        }

        voltarParaRetorno("Location:?c=vaga");
    }

    function excluir($id)
    {
        validarPermissaoExclusao();
        $this->model
            ->excluir($id);

        voltarParaRetorno("Location:?c=vaga");
    }
}
