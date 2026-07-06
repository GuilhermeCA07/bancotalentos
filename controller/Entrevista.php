<?php
require "model/EntrevistaModel.php";
require "model/CandidaturaModel.php";
require "model/CandidatoModel.php";
require_once "helper/Autorizacao.php";
require_once "model/UsuarioModel.php";
require_once "helper/Retorno.php";

class Entrevista
{
    private $model;
    private $candidaturaModel;
    private $candidatoModel;
    private $usuarioModel;

    function __construct()
    {
        validarPermissao('entrevista');
        $this->model = new EntrevistaModel();
        $this->candidaturaModel =
            new CandidaturaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    function index()
    {


        $filtros = [

            'busca' =>
            trim(
                $_GET['busca']
                    ?? ''
            ),

            'data_inicio' =>
            $_GET['data_inicio'] ?? '',

            'data_fim' =>
            $_GET['data_fim'] ?? '',

            'hora_inicio' =>
            $_GET['hora_inicio'] ?? '',

            'hora_fim' =>
            $_GET['hora_fim'] ?? '',

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

        $entrevistas =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        foreach (
            $entrevistas as &$entrevista
        ) {

            $entrevista['total_reagendamentos'] =
                $this->model
                ->contarReagendamentos(
                    $entrevista['idEntrevista']
                );
        }


        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/entrevista/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add($idCandidatura)
    {
        $candidatura =
            $this->candidaturaModel
            ->buscarDetalhes($idCandidatura);

        $usuarios = $this->usuarioModel->buscarTodosSimples();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/entrevista/form.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {
        if (isset($_POST['candidatura_id'])) {

            if (
                $this->model->horarioOcupado(
                    $_POST['data_entrevista'],
                    $_POST['hora_entrevista'],
                    $_POST['responsavel']
                )
            ) {

                echo "
            <script>

            alert(
            'Já existe uma entrevista agendada para este responsável neste horário.'
            );

            history.back();

            </script>
            ";

                exit;
            }

            if (
                empty($_POST['idEntrevista'])
            ) {

                $this->model->inserir(
                    $_POST['candidatura_id'],
                    $_POST['data_entrevista'],
                    $_POST['hora_entrevista'],
                    $_POST['responsavel'],
                    $_POST['local_entrevista'],
                    $_POST['observacoes']
                );

                $candidatura =
                    $this->candidaturaModel
                    ->buscarPorId(
                        $_POST['candidatura_id']
                    );

                $this->candidaturaModel
                    ->alterarStatus(
                        $_POST['candidatura_id'],
                        'Entrevista Agendada'
                    );
            } else {

                $this->model->atualizar(
                    $_POST['idEntrevista'],
                    $_POST['data_entrevista'],
                    $_POST['hora_entrevista'],
                    $_POST['responsavel'],
                    $_POST['local_entrevista'],
                    $_POST['observacoes']
                );
            }

            voltarParaRetorno(
                'Location: ?c=entrevista'
            );

        }
    }

    function editar($id)
    {
        $entrevista =
            $this->model->buscarPorId($id);

        $candidatura =
            $this->candidaturaModel
            ->buscarDetalhes(
                $entrevista['candidatura_id']
            );
        $usuarios = $this->usuarioModel->buscarTodosSimples();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/entrevista/form.php";
        include "view/template/rodape.php";
    }

    function excluir($id)
    {
        $this->model->excluir($id);

        voltarParaRetorno(
                'Location: ?c=entrevista'
            );

        exit;
    }

    function finalizar($id)
    {
        $entrevista =
            $this->model
            ->buscarPorIdPersonalizado($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/entrevista/finalizar.php";
        include "view/template/rodape.php";
    }

    function salvarFinalizacao()
    {



        if (
            !isset($_POST['idEntrevista'])
        ) {
            return;
        }

        $resultado =
            $_POST['resultado'];

        $observacoes =
            $_POST['observacoes'];

        $motivoRecusa =
            $_POST['motivo_recusa']
            ?? null;

        $idEntrevista =
            $_POST['idEntrevista'];

        $entrevista =
            $this->model
            ->buscarPorIdPersonalizado(
                $idEntrevista
            );

        $this->model->salvarObservacoes(
            $idEntrevista,
            $observacoes
        );

        if (
            $resultado == 'Aprovado'
        ) {
            $this->candidaturaModel
                ->aprovar(
                    $entrevista['idCandidatura']
                );
        } else {
            $this->candidaturaModel
                ->recusar(
                    $entrevista['idCandidatura'],
                    $motivoRecusa
                );
        }


        voltarParaRetorno(
                'Location: ?c=entrevista'
            );
    }

    function reagendar($id)
    {
        $entrevista =
            $this->model
            ->buscarPorId($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/entrevista/reagendar.php";
        include "view/template/rodape.php";
    }

    function salvarReagendamento()
    {



        // Voltar para arrumar depois
        $entrevista =
            $this->model
            ->buscarPorId(
                $_POST['idEntrevista']
            );

        if (
            $this->model->horarioOcupado(
                $_POST['nova_data'],
                $_POST['nova_hora'],
                $entrevista['responsavel'],
                $_POST['idEntrevista']
            )
        ) {

            echo "
            <script>

            alert(
            'Já existe uma entrevista agendada para este responsável neste horário.'
            );

            history.back();

            </script>
            ";

            exit;
        }

        $this->model
            ->salvarHistorico(

                $_POST['idEntrevista'],

                $entrevista['data_entrevista'],

                $entrevista['hora_entrevista'],

                $_POST['nova_data'],

                $_POST['nova_hora'],

                $_POST['motivo'],

                'Sistema'

            );

        $this->model
            ->reagendar(

                $_POST['idEntrevista'],

                $_POST['nova_data'],

                $_POST['nova_hora']

            );

        voltarParaRetorno(
                'Location: ?c=entrevista'
            );
    }

    function horariosDisponiveis()
    {
        $data =
            $_GET['data'];

        $responsavel =
            $_GET['responsavel'];

        $ignorar =
            $_GET['ignorar']
            ?? null;

        $ocupados =
            $this->model
            ->buscarHorariosOcupados(
                $data,
                $responsavel,
                $ignorar
            );

        header(
            'Content-Type: application/json'
        );

        echo json_encode(
            $ocupados
        );
    }

    public function historico()
    {
        $idEntrevista =
            (int)$_GET['id'];

        $historico =
            $this->model
            ->buscarHistorico(
                $idEntrevista
            );

        header(
            'Content-Type: application/json'
        );

        echo json_encode(
            $historico
        );
    }
}
