<?php
require_once "model/CandidaturaModel.php";
require_once "helper/Autorizacao.php";

class Decisao
{
    private $model;

    public function __construct()
    {
        validarPermissao('decisao');
        $this->model =
            new CandidaturaModel();
    }

    public function index()
    {
        $filtros = [

            'busca' =>
            trim($_GET['busca'] ?? ''),

            'status' =>
            $_GET['status'] ?? '',

            'responsavel' =>
            trim($_GET['responsavel'] ?? ''),

            'data_inicio' =>
            $_GET['data_inicio'] ?? '',

            'data_fim' =>
            $_GET['data_fim'] ?? '',

            'pagina' =>
            isset($_GET['pagina'])
                ? (int)$_GET['pagina']
                : 1
        ];

        $totalRegistros =
            $this->model
            ->contarDecisoes(
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

        $registros =
            $this->model
            ->buscarDecisoes(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/decisao/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function visualizar($id)
    {
        $decisao =
            $this->model
            ->buscarDetalhesDecisao($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/decisao/visualizar.php";
        include "view/template/rodape.php";
    }
}
