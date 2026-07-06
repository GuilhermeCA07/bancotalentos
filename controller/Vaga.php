<?php
require "model/VagaModel.php";
require_once "model/CandidaturaModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Vaga
{
    private $model;
    private $candidaturaModel;

    function __construct()
    {

    validarPermissao('vaga');

        $this->model =
            new VagaModel();

        $this->candidaturaModel =
            new CandidaturaModel();
    }

    function index()
    {

        function corDepartamento($departamento)
        {
            switch ($departamento) {
                case 'NOC':
                    return '#0F4DB0';

                case 'Financeiro':
                    return '#16A34A';

                case 'Comercial/Atendimento':
                    return '#FF6B00';

                case 'Suporte Técnico':
                    return '#7C3AED';

                case 'Infra':
                    return '#DC2626';

                case 'Técnico de Rua':
                    return '#0891B2';

                default:
                    return '#6B7280';
            }
        }



        $filtros = [

            'busca' =>
            trim($_GET['busca'] ?? ''),

            'departamento' =>
            $_GET['departamento'] ?? '',

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

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/vaga/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
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
                $_POST['departamento'],
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
                $_POST['departamento'],
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
                    $id
                );
        }

        voltarParaRetorno("Location:?c=vaga");
    }

    function excluir($id)
    {
        $this->model
            ->excluir($id);

        voltarParaRetorno("Location:?c=vaga");
    }
}
