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
        $this->candidatoModel = new CandidatoModel();
    }

    function index()
    {

        $podeVisualizarFinalizadas =
            podeVisualizarEntrevistasFinalizadas();

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

            'incluir_finalizadas' =>
            $podeVisualizarFinalizadas
            && ($_GET['incluir_finalizadas'] ?? '') === '1',

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

                $this->candidatoModel
                    ->atualizarStatus(
                        $candidatura['candidato_id'],
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
        validarPermissaoExclusao();
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
            $_POST['resultado']
            ?? '';

        $observacoes = trim(
            $_POST['observacoes']
            ?? ''
        );

        $motivoRecusa =
            trim(
                $_POST['motivo_recusa']
                ?? ''
            );

        $idEntrevista = (int)
            $_POST['idEntrevista'];

        $resultadosPermitidos = [
            'Aprovado',
            'Recusado',
            'Entrevistado'
        ];

        if (!in_array(
            $resultado,
            $resultadosPermitidos,
            true
        )) {
            $_SESSION['erro'] =
                'Selecione um resultado válido.';

            header(
                'Location: ?c=entrevista&m=finalizar&id=' .
                urlencode($idEntrevista)
            );

            exit;
        }

        if (
            $resultado === 'Recusado'
            && $motivoRecusa === ''
        ) {
            $_SESSION['erro'] =
                'Informe o motivo da recusa.';

            header(
                'Location: ?c=entrevista&m=finalizar&id=' .
                urlencode($idEntrevista)
            );

            exit;
        }

        if (
            $resultado === 'Entrevistado'
            && $observacoes === ''
        ) {
            $_SESSION['erro'] =
                'Informe as observações da entrevista.';

            header(
                'Location: ?c=entrevista&m=finalizar&id=' .
                urlencode($idEntrevista)
            );

            exit;
        }

        $entrevista =
            $this->model
            ->buscarPorIdPersonalizado(
                $idEntrevista
            );

        if (!$entrevista) {
            $_SESSION['erro'] =
                'Entrevista não encontrada.';

            voltarParaRetorno(
                'Location: ?c=entrevista'
            );

            exit;
        }

        $this->model->salvarObservacoes(
            $idEntrevista,
            $observacoes
        );

        if ($resultado === 'Aprovado') {
            $this->candidaturaModel
                ->aprovar(
                    $entrevista['idCandidatura']
                );

            $this->candidatoModel
                ->atualizarStatus(
                    $entrevista['candidato_id'],
                    'Aprovado'
                );
        } elseif ($resultado === 'Recusado') {
            $this->candidaturaModel
                ->recusar(
                    $entrevista['idCandidatura'],
                    $motivoRecusa
                );

            $this->candidatoModel
                ->atualizarStatus(
                    $entrevista['candidato_id'],
                    'Recusado'
                );
        } else {
            $this->candidaturaModel
                ->marcarComoEntrevistado(
                    $entrevista['idCandidatura']
                );

            $this->candidatoModel
                ->atualizarStatus(
                    $entrevista['candidato_id'],
                    'Entrevistado'
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

        $usuarioReagendamento = trim(
            $_SESSION['usuario']['nome']
                ?? $_SESSION['usuario']['email']
                ?? 'Usuário não identificado'
        );

        $this->model
            ->salvarHistorico(

                $_POST['idEntrevista'],

                $entrevista['data_entrevista'],

                $entrevista['hora_entrevista'],

                $_POST['nova_data'],

                $_POST['nova_hora'],

                $_POST['motivo'],

                $usuarioReagendamento

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

    public function detalhesResultado($id)
    {
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

        $detalhes = $this->candidaturaModel
            ->buscarDetalhesResultadoEntrevista($idCandidatura);

        if (!$detalhes) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Detalhes da entrevista nao encontrados.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'sucesso' => true,
            'dados' => $detalhes
        ], JSON_UNESCAPED_UNICODE);
        exit;
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
