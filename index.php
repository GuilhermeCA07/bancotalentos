<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set(
    'session.gc_maxlifetime',
    3600
);

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once "helper/LogAtividade.php";

$baseUrl = "https://talentos.netcom.tv.br/index.php";
$controlador_padrao = 'home';
$controller = ucfirst($_GET['c'] ?? $controlador_padrao);
$path_controller = "controller/$controller.php";

if (file_exists($path_controller)) {
    require $path_controller;

    $metodo = $_GET['m'] ?? "index";

    $rotasLivres = [
        'Usuario' => [
            'login',
            'autenticar',
            'segundoFator',
            'validarSegundoFator'
        ],
        'Home' => [
            '*'
        ]
    ];

    if (!isset($_SESSION['usuario'])) {
        $livre = false;

        if (isset($rotasLivres[$controller])) {
            if (
                in_array('*', $rotasLivres[$controller])
                ||
                in_array($metodo, $rotasLivres[$controller])
            ) {
                $livre = true;
            }
        }

        if (!$livre) {
            header(
                "Location:?c=usuario&m=login"
            );

            exit;
        }
    }

    if (
        !empty($_SESSION['usuario']['troca_senha_obrigatoria'])
        && !(
            $controller === 'Usuario'
            && in_array(
                $metodo,
                ['primeiroAcesso', 'alterarMinhaSenha', 'sair'],
                true
            )
        )
    ) {
        header('Location:?c=usuario&m=primeiroAcesso');
        exit;
    }

    $id = $_GET['id'] ?? null;

    $metodoPublico = false;

    if (method_exists($controller, $metodo)) {
        $reflexaoMetodo =
            new ReflectionMethod(
                $controller,
                $metodo
            );

        $metodoPublico =
            $reflexaoMetodo->isPublic();
    }

    $obj = new $controller();

    if ($metodoPublico) {
        registrarAtividadeSistema($controller, $metodo, $id);
    }

    if (
        $metodoPublico
        &&
        is_callable(array($obj, $metodo))
    ) {
        call_user_func_array(array($obj, $metodo), array($id));
    }
}

function baseUrl()
{
    global $baseUrl;
    return $baseUrl;
}
