<?php
require_once "model/LogModel.php";
require_once "helper/Autorizacao.php";

class Log
{
    private $model;

    public function __construct()
    {
        validarPermissaoLog();

        $this->model =
            new LogModel();
    }

    public function index()
    {
        $filtrosEnviados = isset($_GET['filtros']);
        $dataInicial = $filtrosEnviados
            ? trim($_GET['data_inicio'] ?? '')
            : date('Y-m-d');
        $dataFinal = $filtrosEnviados
            ? trim($_GET['data_fim'] ?? '')
            : date('Y-m-d');

        $filtros = [
            'busca' =>
            trim($_GET['busca'] ?? ''),

            'usuario' =>
            trim($_GET['usuario'] ?? ''),

            'modulo' =>
            $_GET['modulo'] ?? '',

            'acao' =>
            $_GET['acao'] ?? '',

            'data_inicio' =>
            $dataInicial,

            'data_fim' =>
            $dataFinal,

            'pagina' =>
            isset($_GET['pagina'])
                ? (int)$_GET['pagina']
                : 1
        ];

        $totalRegistros =
            $this->model
            ->contarRegistros($filtros);

        $registrosPorPagina = 15;

        $totalPaginas =
            ceil(
                $totalRegistros
                / $registrosPorPagina
            );

        $paginaAtual =
            min(
                max(1, $filtros['pagina']),
                max(1, $totalPaginas)
            );

        $offset =
            ($paginaAtual - 1)
            * $registrosPorPagina;

        $logs =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/log/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    public function visualizar($id)
    {
        $log =
            $this->model
            ->buscarPorId((int)$id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/log/visualizar.php";
        include "view/template/rodape.php";
    }
}
