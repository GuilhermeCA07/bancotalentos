<?php
require "model/CategoriaModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Categoria
{
    private $model;

    function __construct()
    {

        validarPermissao('categoria');

        $this->model =
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

            'status' =>
            $_GET['status']
                ?? '',

            'pagina' =>
            (int)(
                $_GET['pagina']
                ?? 1
            )

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

        $categorias =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/categoria/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/categoria/form.php";
        include "view/template/rodape.php";
    }

    function editar($id)
    {
        $categoria =
            $this->model
            ->buscarPorId($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/categoria/form.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {
        if (
            empty($_POST['idCategoria'])
        ) {

            $this->model->inserir(
                $_POST['nome']
            );
        } else {

            $this->model->atualizar(
                $_POST['idCategoria'],
                $_POST['nome']
            );
        }

        voltarParaRetorno("Location: ?c=categoria");
    }

    function alterarStatus($id)
    {
        $this->model
            ->alterarStatus($id);

        voltarParaRetorno("Location: ?c=categoria");
    }
}
