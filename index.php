<?php
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
            'autenticar'

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

    $obj = new $controller();

    $id = $_GET['id'] ?? null;

    if (is_callable(array($obj, $metodo))) {
        call_user_func_array(array($obj, $metodo), array($id));
    }
}

function baseUrl()
{
    global $baseUrl;
    return $baseUrl;
}
