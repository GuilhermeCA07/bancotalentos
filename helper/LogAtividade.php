<?php
require_once "model/LogModel.php";

function registrarAtividadeSistema(
    $controller,
    $metodo,
    $id = null
) {
    if (empty($_SESSION['usuario'])) {
        return;
    }

    if ($controller === 'Log') {
        return;
    }

    $acao =
        classificarAcaoLog(
            $metodo
        );

    $dados =
        coletarDadosLog();

    $descricao =
        montarDescricaoLog(
            $controller,
            $metodo,
            $acao,
            $id
        );

    try {
        $model =
            new LogModel();

        $registroId =
            is_numeric($id)
            ? (int)$id
            : null;

        $usuario =
            $_SESSION['usuario'];

        $model->inserir([
            'usuario_id' =>
            (int)($usuario['idUsuario'] ?? 0),

            'usuario_nome' =>
            $usuario['nome'] ?? '',

            'usuario_perfil' =>
            $usuario['perfil'] ?? '',

            'modulo' =>
            $controller,

            'metodo' =>
            $metodo,

            'registro_id' =>
            $registroId,

            'acao' =>
            $acao,

            'descricao' =>
            $descricao,

            'dados' =>
            json_encode(
                $dados,
                JSON_UNESCAPED_UNICODE
            ),

            'ip' =>
            '',

            'user_agent' =>
            substr(
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                0,
                255
            )
        ]);
    } catch (Throwable $e) {
        return;
    }
}

function classificarAcaoLog($metodo)
{
    $metodo =
        strtolower($metodo);

    if (
        in_array(
            $metodo,
            [
                'add',
                'index',
                'visualizar',
                'editar',
                'historico',
                'horariosdisponiveis',
                'verificarsessao',
                'buscarhabilidades'
            ]
        )
    ) {
        return 'Acesso';
    }

    if (
        strpos($metodo, 'cadastrar') !== false
        ||
        $metodo === 'salvar'
    ) {
        return 'Cadastro/Atualizacao';
    }

    if (
        strpos($metodo, 'atualizar') !== false
        ||
        strpos($metodo, 'editar') !== false
        ||
        strpos($metodo, 'alterar') !== false
        ||
        strpos($metodo, 'reagendar') !== false
        ||
        strpos($metodo, 'finalizar') !== false
    ) {
        return 'Atualizacao';
    }

    if (
        strpos($metodo, 'excluir') !== false
        ||
        strpos($metodo, 'remover') !== false
    ) {
        return 'Exclusao';
    }

    if (
        strpos($metodo, 'login') !== false
        ||
        strpos($metodo, 'autenticar') !== false
    ) {
        return 'Login';
    }

    if (
        strpos($metodo, 'sair') !== false
    ) {
        return 'Logout';
    }

    return 'Acao';
}

function coletarDadosLog()
{
    $dados = [
        'get' => filtrarDadosSensiveis($_GET),
        'post' => filtrarDadosSensiveis($_POST)
    ];

    if (!empty($_FILES)) {
        $arquivos = [];

        foreach ($_FILES as $campo => $arquivo) {
            $arquivos[$campo] = [
                'name' => $arquivo['name'] ?? '',
                'type' => $arquivo['type'] ?? '',
                'size' => $arquivo['size'] ?? 0,
                'error' => $arquivo['error'] ?? null
            ];
        }

        $dados['files'] = $arquivos;
    }

    return $dados;
}

function filtrarDadosSensiveis($dados)
{
    $bloqueados = [
        'senha',
        'password',
        'token',
        'cf-turnstile-response'
    ];

    $limpos = [];

    foreach ($dados as $chave => $valor) {
        if (
            in_array(
                strtolower($chave),
                $bloqueados
            )
        ) {
            $limpos[$chave] = '[protegido]';
            continue;
        }

        if (is_array($valor)) {
            $limpos[$chave] =
                filtrarDadosSensiveis($valor);
            continue;
        }

        $limpos[$chave] =
            is_string($valor)
            ? substr($valor, 0, 500)
            : $valor;
    }

    return $limpos;
}

function montarDescricaoLog(
    $controller,
    $metodo,
    $acao,
    $id
) {
    $descricao =
        $acao .
        " em " .
        $controller .
        "::" .
        $metodo;

    if ($id !== null && $id !== '') {
        $descricao .=
            " (ID: " .
            $id .
            ")";
    }

    return $descricao;
}
