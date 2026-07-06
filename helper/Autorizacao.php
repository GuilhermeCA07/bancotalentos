<?php

function temPermissao($modulo)
{
    $perfil =
        $_SESSION['usuario']['perfil']
        ?? '';

    $permissoes = [

        'Gerente' => [

            '*'

        ],

        'Secretario' => [

            'entrevista',
            'chamada'

        ],

        'Recrutador' => [

            'dashboard',
            'candidato',
            'candidatura',
            'entrevista',
            'vaga',
            'chamada',
            'decisao',
            'contratacao'

        ]
    ];

    // perfil inexistente
    if (!isset($permissoes[$perfil])) {
        return false;
    }

    if (in_array('*', $permissoes[$perfil])) {
        return true;
    }

    return in_array(
        $modulo,
        $permissoes[$perfil]
    );
}


function rotaInicial()
{
    $perfil =
        $_SESSION['usuario']['perfil']
        ?? '';

    switch ($perfil) {

        case 'Gerente':
            return '?c=dashboard';

        case 'Recrutador':
            return '?c=dashboard';

        case 'Secretario':
            return '?c=entrevista';

        default:
            return '?c=home';
    }
}

function validarPermissao($modulo)
{
    validarSessao();

    if (!temPermissao($modulo)) {

        if (isset($_SESSION['usuario'])) {

            $_SESSION['erro'] =
                "Você não possui permissão para acessar este módulo.";

            header(
                "Location:" .
                rotaInicial()
            );

            exit;
        }

        header(
            "Location:?c=usuario&m=login"
        );

        exit;
    }
}

function validarSessao()
{
    $tempoMaximo = 3600;

    if (!isset($_SESSION['usuario'])) {

        header(
            "Location:?c=usuario&m=login"
        );

        exit;
    }

    if (
        isset($_SESSION['ultimo_acesso']) &&
        (time() - $_SESSION['ultimo_acesso']) > $tempoMaximo
    ) {

        session_unset();
        session_destroy();

        header(
            "Location:?c=usuario&m=login&expirado=1"
        );

        exit;
    }

    $_SESSION['ultimo_acesso'] = time();
}