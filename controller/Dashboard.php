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

        $proximasEntrevistas =
            $this->model
            ->proximasEntrevistas();

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
            ->resumoCandidaturas();

        $totalEntrevistas =
            $this->model
            ->totalEntrevistas();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/dashboard/index.php";
        include "view/template/rodape.php";
    }
}
