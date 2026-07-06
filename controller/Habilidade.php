<?php
require "model/HabilidadeModel.php";
require "model/CategoriaModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Habilidade
{
    private $model;
    private $categoriaModel;

    function __construct()
    {

        validarPermissao('habilidade');

        $this->model =
            new HabilidadeModel();

        $this->categoriaModel =
            new CategoriaModel();
    }

    function index()
    {
        $filtros = [

            'busca' =>
            trim(
                $_GET['busca']
                    ?? ''
            ),

            'categoria' =>
            $_GET['categoria']
                ?? '',

            'status' =>
            $_GET['status']
                ?? '',

            'pagina' =>
            isset($_GET['pagina'])
                ? (int)$_GET['pagina']
                : 1

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

        $habilidades =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        $categorias =
            $this->categoriaModel
            ->buscarAtivas();

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/habilidade/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
        $categorias =
            $this->categoriaModel
            ->buscarAtivas();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/habilidade/form.php";
        include "view/template/rodape.php";
    }

    function editar($id)
    {
        $habilidade =
            $this->model
            ->buscarPorId($id);

        $categorias =
            $this->categoriaModel
            ->buscarAtivas();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/habilidade/form.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {
        if (
            empty($_POST['idHabilidade'])
        ) {

            $this->model->inserir(
                $_POST['nome'],
                $_POST['categoria']
            );
        } else {

            $this->model->atualizar(
                $_POST['idHabilidade'],
                $_POST['nome'],
                $_POST['categoria']
            );
        }

        voltarParaRetorno("Location: ?c=habilidade");
    }

    function alterarStatus($id)
    {
        // Voltar para arrumar depois
        $retorno =
            $this->model
            ->alterarStatus($id);

        if (!$retorno['sucesso']) {

            echo "
        <script>
            alert('" . $retorno['mensagem'] . "');
            window.location='?c=habilidade';
        </script>
        ";

            exit;
        }

        voltarParaRetorno("Location: ?c=habilidade");
    }
}
