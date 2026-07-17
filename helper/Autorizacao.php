<?php

function temPermissao($modulo)
{
    $perfil =
        $_SESSION['usuario']['perfil']
        ?? '';

    $permissoes = [

        'Administrador' => [

            '*'

        ],

        'Gerente' => [

            'dashboard',
            'candidato',
            'candidatura',
            'entrevista',
            'vaga',
            'chamada',
            'decisao',
            'contratacao',
            'categoria',
            'habilidade',
            'departamento'

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

        case 'Administrador':
            return '?c=dashboard';

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

function ehAdministrador()
{
    return (
        $_SESSION['usuario']['perfil']
        ?? ''
    ) === 'Administrador';
}

function ehGerente()
{
    return (
        $_SESSION['usuario']['perfil']
        ?? ''
    ) === 'Gerente';
}

function podeVisualizarEntrevistasFinalizadas()
{
    return ehAdministrador() || ehGerente();
}

function podeExcluir()
{
    return ehAdministrador();
}

function validarPermissaoExclusao()
{
    validarSessao();

    if (!podeExcluir()) {
        $_SESSION['erro'] =
            'Voce nao possui permissao para excluir registros.';

        header(
            'Location:' .
            rotaInicial()
        );

        exit;
    }
}

function podeAcessarLog()
{
    return podeExcluir();
}

function validarPermissaoLog()
{
    validarSessao();

    if (!podeAcessarLog()) {
        $_SESSION['erro'] =
            'Voce nao possui permissao para acessar os logs.';

        header(
            'Location:' .
            rotaInicial()
        );

        exit;
    }
}

function podeAcessarTema()
{
    return podeExcluir();
}

function podeConfigurarEmail()
{
    return ehAdministrador();
}

function podeGerenciarDepartamentos()
{
    return ehAdministrador() || ehGerente();
}

function validarPermissaoDepartamento()
{
    validarSessao();

    if (!podeGerenciarDepartamentos()) {
        $_SESSION['erro'] =
            'Apenas administradores e gerentes podem acessar departamentos.';

        header('Location:' . rotaInicial());
        exit;
    }
}

function validarPermissaoConfiguracaoEmail()
{
    validarSessao();

    if (!podeConfigurarEmail()) {
        $_SESSION['erro'] =
            'Apenas administradores podem configurar o e-mail de token.';

        header('Location:' . rotaInicial());
        exit;
    }
}

function validarPermissaoTema()
{
    validarSessao();

    if (!podeAcessarTema()) {
        $_SESSION['erro'] =
            'Voce nao possui permissao para alterar a aparencia.';

        header('Location:' . rotaInicial());
        exit;
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

    sincronizarPerfilSessao();

    $metodoAtual = (string)($_GET['m'] ?? 'index');
    $controllerAtual = strtolower((string)($_GET['c'] ?? ''));

    if (
        !empty($_SESSION['usuario']['troca_senha_obrigatoria'])
        && !(
            $controllerAtual === 'usuario'
            && in_array(
                $metodoAtual,
                ['primeiroAcesso', 'alterarMinhaSenha', 'sair'],
                true
            )
        )
    ) {
        header('Location:?c=usuario&m=primeiroAcesso');
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

function sincronizarPerfilSessao()
{
    $idUsuario = (int)(
        $_SESSION['usuario']['idUsuario']
        ?? 0
    );

    if ($idUsuario <= 0) {
        return;
    }

    require_once __DIR__ . '/../config/Conexao.php';

    $conexao = Conexao::getConnection();
    $comando = $conexao->prepare(
        'SELECT perfil, troca_senha_obrigatoria, dois_fatores_ativo
         FROM usuarios
         WHERE idUsuario = ?
         LIMIT 1'
    );
    $comando->bind_param('i', $idUsuario);
    $comando->execute();
    $usuario = $comando->get_result()->fetch_assoc();

    if (!$usuario) {
        session_unset();
        session_destroy();
        header('Location:?c=usuario&m=login');
        exit;
    }

    $_SESSION['usuario']['perfil'] = $usuario['perfil'];
    $_SESSION['usuario']['troca_senha_obrigatoria'] =
        (int)($usuario['troca_senha_obrigatoria'] ?? 0);
    $_SESSION['usuario']['dois_fatores_ativo'] =
        (int)($usuario['dois_fatores_ativo'] ?? 0);
}
