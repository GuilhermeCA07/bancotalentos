<?php
require "model/DashboardModel.php";
require_once "helper/Autorizacao.php";

class Dashboard
{
    private $model;

    public function __construct()
    {
        validarPermissao('dashboard');
        $this->model =
            new DashboardModel();
    }

    public function index()
    {
        $periodoMeses = (int)($_GET['periodo'] ?? 6);

        if (!in_array($periodoMeses, [3, 6, 12, 24], true)) {
            $periodoMeses = 6;
        }

        $rotuloPeriodo = 'Últimos ' . $periodoMeses . ' meses';

        $proximasEntrevistas =
            $this->model
            ->proximasEntrevistas(8);

        $totalCandidatos =
            $this->model
            ->totalCandidatos();

        $totalVagasAtivas =
            $this->model
            ->totalVagasAtivas();

        $totalEntrevistasHoje =
            $this->model
            ->totalEntrevistasHoje();

        $totalAprovados =
            $this->model
            ->totalAprovados();

        $resumoCandidaturas =
            $this->model
            ->resumoCandidaturas($periodoMeses);

        $totalEntrevistas =
            $this->model
            ->totalEntrevistas();

        $indicadores =
            $this->model
            ->indicadoresComplementares();

        $alertas =
            $this->model
            ->alertasOperacionais();

        $evolucaoMensal =
            $this->model
            ->evolucaoMensal($periodoMeses);

        $candidaturasPorVaga =
            $this->model
            ->candidaturasPorVaga(6, $periodoMeses);

        $funilRecrutamento =
            $this->model
            ->funilRecrutamento($periodoMeses);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/dashboard/index.php";
        include "view/template/rodape.php";
    }
}
