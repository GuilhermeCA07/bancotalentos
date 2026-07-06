<?php
require "model/ChamadaModel.php";
require_once "helper/Autorizacao.php";

class Chamada
{
    private $model;

    public function __construct()
    {

        validarPermissao('chamada');
        $this->model =
            new ChamadaModel();
    }

    public function index()
    {

        function formatarTelefone($telefone)
        {
            $telefone =
                preg_replace(
                    '/\D/',
                    '',
                    $telefone
                );

            if (strlen($telefone) == 11) {

                return preg_replace(
                    '/(\d{2})(\d{5})(\d{4})/',
                    '($1) $2-$3',
                    $telefone
                );
            }

            return $telefone;
        }

        $filtros = [

            'busca' =>
            trim(
                $_GET['busca']
                    ?? ''
            ),

            'status' =>
            $_GET['status']
                ?? '',

            'sem_whatsapp' =>
            isset(
                $_GET['sem_whatsapp']
            ),

            'pagina' =>
            isset($_GET['pagina'])
                ? (int) $_GET['pagina']
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

        $candidaturas =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/chamada/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }
}
