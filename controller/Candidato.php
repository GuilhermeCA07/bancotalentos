<?php

require "model/CandidatoModel.php";
require "model/HabilidadeModel.php";
require_once "helper/Autorizacao.php";
require_once "helper/Retorno.php";
require_once "model/CategoriaModel.php";
require_once "helper/Linkedin.php";

class Candidato
{
    private $model;
    private $habilidadeModel;
    private $categoriaModel;


    function __construct()
    {

        validarPermissao('candidato');

        $this->model = new CandidatoModel();
        $this->habilidadeModel = new HabilidadeModel();
        $this->categoriaModel = new CategoriaModel();
    }

    function index()
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
            trim($_GET['busca'] ?? ''),

            'status_candidato' =>
            $_GET['status_candidato'] ?? '',

            'escolaridade' =>
            $_GET['escolaridade'] ?? '',

            'estado_civil' =>
            $_GET['estado_civil'] ?? '',

            'fumante' =>
            $_GET['fumante'] ?? '',

            'cnh' =>
            $_GET['cnh'] ?? '',

            'categoria' =>
            $_GET['categoria'] ?? '',

            'habilidade' =>
            $_GET['habilidade'] ?? '',

            'data_inicial' =>
            $_GET['data_inicial'] ?? '',

            'data_final' =>
            $_GET['data_final'] ?? '',

            'pagina' =>
            isset($_GET['pagina'])
                ? (int)$_GET['pagina']
                : 1

        ];


        $totalRegistros = $this->model->contarRegistros($filtros);

        $registrosPorPagina = 10;

        $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

        $paginaAtual =
            min(
                max(1, $filtros['pagina']),
                max(1, $totalPaginas)
            );

        $offset = ($paginaAtual - 1) * $registrosPorPagina;

        $candidatos = $this->model->buscarTodos($filtros, $registrosPorPagina, $offset);

        $habilidades = $this->habilidadeModel->buscarAtivas();
        $categorias = $this->categoriaModel->buscarAtivas();

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidato/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }

    function add()
    {
        $habilidades =
            $this->habilidadeModel
            ->buscarTodos();

        $habilidades =
            $this->habilidadeModel
            ->buscarTodos();

        $habilidadesSelecionadas = [];

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidato/form.php";
        include "view/template/rodape.php";
    }

    public function visualizar($id)
    {
        $candidato =
            $this->model
            ->buscarPorId($id);

        $habilidades =
            $this->model
            ->buscarHabilidades(
                $id
            );

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidato/visualizar.php";
        include "view/template/rodape.php";
    }

    function salvar()
    {

        if (isset($_POST['nome']) && !empty($_POST['nome'])) {

            try {
                $linkedin = normalizarLinkedin(
                    $_POST['linkedin'] ?? ''
                );
            } catch (InvalidArgumentException $erro) {
                $_SESSION['erro'] = $erro->getMessage();

                $idCandidato = (int)(
                    $_POST['idCandidato'] ?? 0
                );
                $destino = $idCandidato > 0
                    ? '?c=candidato&m=editar&id=' . $idCandidato
                    : '?c=candidato&m=add';

                header('Location: ' . $destino);
                exit;
            }

            $whatsapp = isset($_POST['whatsapp']) ? 1 : 0;

            $curriculo = $this->salvarCurriculo();

            $escolaridade =
                trim($_POST['escolaridade'] ?? '');

            $estadoCivil =
                trim($_POST['estado_civil'] ?? '');

            $fumante =
                isset($_POST['fumante'])
                ? 1
                : 0;

            $cnh =
                trim($_POST['cnh'] ?? '');


            if (empty($_POST['idCandidato'])) {

                $telefone =
                    preg_replace(
                        '/\D/',
                        '',
                        $_POST['telefone']
                    );

                $this->model->inserir(
                    $_POST['nome'],
                    $telefone,
                    $whatsapp,
                    $_POST['email'],
                    $curriculo,
                    $_POST['observacoes'],
                    $escolaridade,
                    $estadoCivil,
                    $fumante,
                    $cnh,
                    $linkedin
                );

                $idCandidato = $this->model->buscarUltimoId();
            } else {

                $curriculoAtual = $_POST['curriculo_atual'] ?? null;

                if (empty($curriculo)) {
                    $curriculo = $curriculoAtual;
                }

                $telefone =
                    preg_replace(
                        '/\D/',
                        '',
                        $_POST['telefone']
                    );

                $this->model->atualizar(
                    $_POST['idCandidato'],
                    $_POST['nome'],
                    $telefone,
                    $whatsapp,
                    $_POST['email'],
                    $curriculo,
                    $_POST['observacoes'],
                    $escolaridade,
                    $estadoCivil,
                    $fumante,
                    $cnh,
                    $linkedin
                );

                $idCandidato = $_POST['idCandidato'];
            }

            $this->model->limparHabilidades($idCandidato);

            if (isset($_POST['habilidade'])) {

                foreach ($_POST['habilidade'] as $indice => $idHabilidade) {

                    $nivel = $_POST['nivel'][$indice] ?? 0;

                    if (
                        isset($_POST['descricao_personalizada'][$indice])
                        && !empty($_POST['descricao_personalizada'][$indice])
                    ) {
                        $descricao =
                            $_POST['descricao_personalizada'][$indice];
                    }

                    $nomeExibicao =
                        $_POST['nome_exibicao'][$indice]
                        ?? '';

                    $this->model->salvarHabilidade(
                        $idCandidato,
                        $idHabilidade,
                        $nomeExibicao,
                        $nivel
                    );
                }
            }

            $this->model
                ->atualizarUltimaAtualizacao(
                    $idCandidato
                );


            voltarParaRetorno("?c=candidato");
        }
    }

    function editar($id)
    {
        $candidato =
            $this->model->buscarPorId($id);

        $habilidades =
            $this->habilidadeModel
            ->buscarTodos();

        $habilidadesSelecionadas =
            $this->model
            ->buscarHabilidades($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/candidato/form.php";
        include "view/template/rodape.php";
    }

    function excluir($id)
    {
        validarPermissaoExclusao();
        $this->model->excluir($id);

        voltarParaRetorno("?c=candidato");
    }

    function salvarCurriculo()
    {
        if (
            isset($_FILES['curriculo']) &&
            $_FILES['curriculo']['error'] === 0
        ) {

            $permitidos = [
                'pdf',
                'doc',
                'docx',
                'jpg',
                'jpeg',
                'png'
            ];

            $extensao = strtolower(
                pathinfo(
                    $_FILES['curriculo']['name'],
                    PATHINFO_EXTENSION
                )
            );

            if (!in_array($extensao, $permitidos)) {
                return null;
            }

            $nomeArquivo =
                time() .
                "_" .
                preg_replace(
                    '/[^a-zA-Z0-9._-]/',
                    '',
                    $_FILES['curriculo']['name']
                );

            $diretorio = "uploads/curriculos/";

            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            $destino = $diretorio . $nomeArquivo;

            if (
                move_uploaded_file(
                    $_FILES['curriculo']['tmp_name'],
                    $destino
                )
            ) {
                return $destino;
            }
        }

        return null;
    }

    public function baixarCurriculo($id)
    {
        $candidato =
            $this->model
            ->buscarPorId($id);

        if (
            !$candidato
            ||
            empty($candidato['curriculo'])
        ) {
            exit;
        }

        $arquivo =
            $candidato['curriculo'];

        if (!file_exists($arquivo)) {
            exit;
        }

        $extensao =
            pathinfo(
                $arquivo,
                PATHINFO_EXTENSION
            );

        $nomeArquivo =
            'Curriculo_' .
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '_',
                $candidato['nome']
            )
            . '.'
            . $extensao;

        header(
            'Content-Type: application/octet-stream'
        );

        header(
            'Content-Disposition: attachment; filename="' .
                $nomeArquivo .
                '"'
        );

        readfile($arquivo);

        exit;
    }

    public function visualizarCurriculo($id)
    {
        $candidato =
            $this->model
            ->buscarPorId($id);

        if (
            !$candidato
            ||
            empty($candidato['curriculo'])
        ) {
            exit;
        }

        $arquivo =
            $candidato['curriculo'];

        if (!file_exists($arquivo)) {
            exit;
        }

        $extensao =
            strtolower(
                pathinfo(
                    $arquivo,
                    PATHINFO_EXTENSION
                )
            );

        $tiposPermitidos = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        if (!isset($tiposPermitidos[$extensao])) {
            exit;
        }

        header(
            'Content-Type: ' .
                $tiposPermitidos[$extensao]
        );

        header(
            'Content-Disposition: inline'
        );

        header(
            'X-Content-Type-Options: nosniff'
        );

        readfile($arquivo);

        exit;
    }

    public function buscarHabilidades()
    {

        header('Content-Type: application/json');

        if (!isset($_SESSION['candidato'])) {

            echo json_encode([

                'sucesso' => false,

                'mensagem' => 'Sessão expirada.'

            ]);

            exit;
        }

        $idCandidato =
            $_SESSION['candidato']['id'];

        $habilidades =
            $this->model->buscarHabilidadesEditar($idCandidato);

        echo json_encode([

            'sucesso' => true,

            'habilidades' => $habilidades

        ]);
    }
}
