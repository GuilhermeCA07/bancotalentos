<?php
require_once "model/UsuarioModel.php";
require_once "helper/Autorizacao.php";
require_once  "helper/Retorno.php";

class Usuario
{
    private $model;

    public function __construct()
    {
        $this->model =
            new UsuarioModel();

        $metodo =
            filter_input(
                INPUT_GET,
                'm'
            ) ?? 'index';

        if (
            !in_array(
                $metodo,
                [
                    'login',
                    'autenticar',
                    'sair'
                ]
            )
        ) {

            validarPermissao(
                'usuario'
            );
        }
    }

    public function index()
    {
        $filtros = [

            'busca' =>
            trim(
                $_GET['busca']
                    ?? ''
            ),

            'perfil' =>
            $_GET['perfil']
                ?? '',

            'pagina' =>
            (int)(
                $_GET['pagina']
                ?? 1
            )

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

        $usuarios =
            $this->model
            ->buscarTodos(
                $filtros,
                $registrosPorPagina,
                $offset
            );

        salvarRetorno();

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/usuario/listagem.php";
        include "view/template/paginacao.php";
        include "view/template/rodape.php";
    }
    public function add()
    {
        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/usuario/form.php";
        include "view/template/rodape.php";
    }

    public function cadastrar()
    {
        $this->model->cadastrar([

            'nome' => $_POST['nome'],

            'email' => $_POST['email'],

            'senha' => $_POST['senha'],

            'perfil' => $_POST['perfil']

        ]);

        voltarParaRetorno("Location:?c=usuario");
    }

    public function editar($id)
    {
        $usuario =
            $this->model
            ->buscarPorId($id);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/usuario/form.php";
        include "view/template/rodape.php";
    }

    public function atualizar()
    {
        $this->model->editar([

            'idUsuario' => $_POST['idUsuario'],

            'nome' => $_POST['nome'],

            'email' => $_POST['email'],

            'perfil' => $_POST['perfil'],

            'senha' => $_POST['senha'] ?? ''

        ]);

        voltarParaRetorno("Location:?c=usuario");
    }

    public function excluir($id)
    {
        $this->model->excluir($id);

        voltarParaRetorno("Location:?c=usuario");
    }

    public function login()
    {
        include "view/usuario/login.php";
    }

    public function autenticar()
    {
        $usuario =
            $this->model
            ->autenticar(

                $_POST['email'],

                $_POST['senha']

            );

        if ($usuario) {

            session_regenerate_id(true);

            $_SESSION['usuario'] = [

                'idUsuario' => $usuario['idUsuario'],
                'nome'      => $usuario['nome'],
                'email'     => $usuario['email'],
                'perfil'    => $usuario['perfil']

            ];

            $_SESSION['ultimo_acesso'] =
            time();

            header(
                "Location:" .
                    rotaInicial()
            );

            exit;
        }

        $_SESSION['erro'] =
            "E-mail ou senha inválidos.";

        header("Location:?c=usuario&m=login");

        exit;
    }

    public function sair()
    {
        session_unset();
        session_destroy();

        header(
            "Location:?c=usuario&m=login"
        );

        exit;
    }

    public function verificarSessao()
    {
        if (isset($_SESSION['usuario'])) {

            header(
                "Location:" . rotaInicial()
            );

            exit;
        }

        header(
            "Location:?c=usuario&m=login"
        );

        exit;
    }
}
