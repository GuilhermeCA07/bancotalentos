<?php
require_once "model/ContratacaoModel.php";
require_once "model/CandidaturaModel.php";
require_once "model/VagaModel.php";
require_once "model/CandidatoModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Contratacao
{
    private $model;
    private $candidaturaModel;
    private $vagaModel;
    private $candidatoModel;

    public function __construct()
    {

        validarPermissao('contratacao');
        $this->model =
            new ContratacaoModel();
        $this->candidaturaModel =
            new CandidaturaModel();
        $this->vagaModel =
            new VagaModel();
        $this->candidatoModel =
            new CandidatoModel();
    }

    public function index()
    {
        $pagina =
            $_GET['pagina']
            ?? 1;

        $porPagina = 10;

        $offset =
            ($pagina - 1)
            * $porPagina;

        $filtros = [

            'busca' =>
            $_GET['busca']
                ?? '',

            'status' =>
            $_GET['status']
                ?? '',

            'data_inicio' =>
            $_GET['data_inicio']
                ?? '',

            'data_fim' =>
            $_GET['data_fim']
                ?? ''

        ];

        $contratacoes =
            $this->model
            ->buscarTodos(
                $filtros,
                $porPagina,
                $offset
            );

        $totalRegistros =
            $this->model
            ->contarTodos(
                $filtros
            );

        $totalPaginas =
            ceil(
                $totalRegistros
                    / $porPagina
            );

        $paginaAtual =
            $pagina;

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/contratacao/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    public function contratar($id)
    {
        $this->model->contratar($id);

        $candidatura = $this->candidaturaModel->buscarPorId($id);

        $this->candidatoModel->atualizarStatus($candidatura['candidato_id'], 'Aprovado');

        $idVaga = $candidatura['vaga_id'];

        $contratados = $this->candidaturaModel->contarContratadosPorVaga($idVaga);

        $vaga = $this->vagaModel->buscarPorId($idVaga);

        if ($contratados >= $vaga['quantidade_vagas']) {

            $this->vagaModel->fecharVaga($idVaga);

            $this->candidaturaModel->encerrarPorFechamentoVaga($idVaga);
        }

        voltarParaRetorno("Location:?c=contratacao");
    }

    public function dispensar()
    {
        $this->model->dispensar($_POST['idCandidatura'], $_POST['motivo']);
        $this->candidatoModel
            ->atualizarStatus(

                $_POST['candidato_id'],

                'Reprovado'

            );
        voltarParaRetorno("Location:?c=contratacao");
    }

    public function autoDispensa()
    {
        $this->model
            ->autoDispensa(

                $_POST['idCandidatura'],

                $_POST['motivo']

            );

        $this->candidatoModel
            ->atualizarStatus(

                $_POST['candidato_id'],

                'Reprovado'

            );

        voltarParaRetorno("Location:?c=contratacao");
    }
}
