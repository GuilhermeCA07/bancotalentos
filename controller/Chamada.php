<?php
require "model/ChamadaModel.php";
require_once "model/CandidaturaModel.php";
require_once "model/CandidatoModel.php";
require_once "helper/Autorizacao.php";

class Chamada
{
    private $model;
    private $candidaturaModel;
    private $candidatoModel;

    public function __construct()
    {

        validarPermissao('chamada');
        $this->model =
            new ChamadaModel();
        $this->candidaturaModel =
            new CandidaturaModel();
        $this->candidatoModel =
            new CandidatoModel();
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

    public function visualizarCandidato($id)
    {
        $idCandidato = (int)$id;
        $candidato = $this->candidatoModel->buscarPorId($idCandidato);

        if (!$candidato) {
            $_SESSION['erro'] = 'Candidato nao encontrado.';
            header('Location: ?c=chamada');
            exit;
        }

        $habilidades = $this->candidatoModel
            ->buscarHabilidades($idCandidato);
        $controladorCurriculo = 'chamada';

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidato/visualizar.php";
        include "view/template/rodape.php";
    }

    public function baixarCurriculo($id)
    {
        $dados = $this->obterCurriculo($id);

        if (!$dados) {
            http_response_code(404);
            exit;
        }

        $extensao = pathinfo($dados['arquivo'], PATHINFO_EXTENSION);
        $nomeArquivo = 'Curriculo_' . preg_replace(
            '/[^a-zA-Z0-9]/',
            '_',
            $dados['candidato']['nome']
        ) . '.' . $extensao;

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        readfile($dados['arquivo']);
        exit;
    }

    public function visualizarCurriculo($id)
    {
        $dados = $this->obterCurriculo($id);

        if (!$dados) {
            http_response_code(404);
            exit;
        }

        $extensao = strtolower(
            pathinfo($dados['arquivo'], PATHINFO_EXTENSION)
        );
        $tiposPermitidos = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        if (!isset($tiposPermitidos[$extensao])) {
            http_response_code(415);
            exit;
        }

        header('Content-Type: ' . $tiposPermitidos[$extensao]);
        header('Content-Disposition: inline');
        header('X-Content-Type-Options: nosniff');
        readfile($dados['arquivo']);
        exit;
    }

    private function obterCurriculo($id)
    {
        $candidato = $this->candidatoModel->buscarPorId((int)$id);

        if (!$candidato || empty($candidato['curriculo'])) {
            return null;
        }

        $arquivo = $candidato['curriculo'];

        if (!is_file($arquivo)) {
            return null;
        }

        return [
            'candidato' => $candidato,
            'arquivo' => $arquivo
        ];
    }
}
