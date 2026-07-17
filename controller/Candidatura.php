<?php
require "model/CandidaturaModel.php";
require "model/CandidatoModel.php";
require "model/VagaModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";

class Candidatura
{
    private $model;
    private $candidatoModel;
    private $vagaModel;

    function __construct()
    {

        validarPermissao('candidatura');

        $this->model = new CandidaturaModel();
        $this->candidatoModel = new CandidatoModel();
        $this->vagaModel = new VagaModel();
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

            'data_inicio' =>
            $_GET['data_inicio']
                ?? '',

            'data_fim' =>
            $_GET['data_fim']
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

        $candidaturas =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );
    
        salvarRetorno();
        
        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidatura/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
        $candidatos =
            $this->candidatoModel->buscarTodos();

        $vagas =
            $this->vagaModel->buscarAbertas();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidatura/form.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {
        if (
            isset($_POST['candidato_id']) &&
            isset($_POST['vaga_id'])
        ) {

            if (
                empty($_POST['idCandidatura'])
            ) {

                // VALIDAÇÃO
                if (
                    $this->model->existeCandidatura(
                        $_POST['candidato_id'],
                        $_POST['vaga_id']
                    )
                ) {

                    echo "
                <script>
                    alert('Este candidato já está vinculado a esta vaga.');
                    window.history.back();
                </script>";
                    exit;
                }

                $this->model->inserir(
                    $_POST['candidato_id'],
                    $_POST['vaga_id']
                );
            } else {

                $this->model->atualizar(
                    $_POST['idCandidatura'],
                    $_POST['candidato_id'],
                    $_POST['vaga_id']
                );
            }

            voltarParaRetorno('Location: ?c=candidatura');
        }
    }

    function editar($id)
    {
        $candidatura =
            $this->model->buscarPorId($id);

        $candidatos =
            $this->candidatoModel->buscarTodos();

        $vagas =
            $this->vagaModel->buscarAbertas();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidatura/form.php";
        include "view/template/rodape.php";
    }

    function excluir($id)
    {
        validarPermissaoExclusao();
        $this->model->excluir($id);

        voltarParaRetorno('Location: ?c=candidatura');
    }

    function alterarStatus($id)
    {
        if (
            isset($_GET['status'])
        ) {

            $this->model->alterarStatus(
                $id,
                $_GET['status']
            );
        }

        header('Location: ?c=candidatura');

        exit;
    }

    public function detalhesRecusa($id)
    {
        $this->responderDetalhesResultado(
            $id,
            'Recusado'
        );
    }

    public function detalhesResultado($id)
    {
        $this->responderDetalhesResultado($id);
    }

    private function responderDetalhesResultado(
        $id,
        $resultadoEsperado = null
    ) {
        header('Content-Type: application/json; charset=utf-8');

        $idCandidatura = (int)$id;

        if ($idCandidatura <= 0) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Candidatura invalida.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $detalhes = $this->model
            ->buscarDetalhesResultadoEntrevista($idCandidatura);

        if (
            $detalhes
            && $resultadoEsperado !== null
            && $detalhes['resultado'] !== $resultadoEsperado
        ) {
            $detalhes = null;
        }

        if (!$detalhes) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Detalhes da entrevista não encontrados.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'sucesso' => true,
            'dados' => $detalhes
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
