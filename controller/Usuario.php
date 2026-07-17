<?php
require_once "model/UsuarioModel.php";
require_once "helper/Autorizacao.php";
require_once  "helper/Retorno.php";
require_once "helper/Senha.php";
require_once "helper/Turnstile.php";
require_once "helper/DoisFatores.php";
require_once "helper/DispositivoConfiavel.php";

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

        $metodosConta = [
            'minhaConta',
            'alterarMinhaSenha',
            'primeiroAcesso',
            'iniciarDoisFatores',
            'confirmarDoisFatores',
            'desativarDoisFatores'
        ];

        if (in_array($metodo, $metodosConta, true)) {
            validarSessao();
            return;
        }

        if (
            !in_array(
                $metodo,
                [
                    'login',
                    'autenticar',
                    'segundoFator',
                    'validarSegundoFator',
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
        $senha = (string)($_POST['senha'] ?? '');
        $this->validarSenhaForteOuRedirecionar(
            $senha,
            '?c=usuario&m=add'
        );
        $perfil = $this->validarPerfilSolicitado(
            $_POST['perfil'] ?? ''
        );

        $this->model->cadastrar([

            'nome' => $_POST['nome'],

            'email' => $_POST['email'],

            'senha' => $senha,

            'perfil' => $perfil

        ]);

        voltarParaRetorno("Location:?c=usuario");
    }

    public function editar($id)
    {
        $usuario =
            $this->model
            ->buscarPorId($id);

        if (
            !$usuario
            || (
                $usuario['perfil'] === 'Administrador'
                && !ehAdministrador()
            )
        ) {
            $_SESSION['erro'] =
                'Você não pode editar este usuário.';
            header('Location:?c=usuario');
            exit;
        }

        if (empty($_SESSION['csrf_2fa_admin'])) {
            $_SESSION['csrf_2fa_admin'] = bin2hex(random_bytes(32));
        }

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/usuario/form.php";
        include "view/template/rodape.php";
    }

    public function atualizar()
    {
        $idUsuario = (int)($_POST['idUsuario'] ?? 0);
        $usuarioAtual = $this->model->buscarPorId($idUsuario);
        $senha = (string)($_POST['senha'] ?? '');

        if (
            !$usuarioAtual
            || (
                $usuarioAtual['perfil'] === 'Administrador'
                && !ehAdministrador()
            )
        ) {
            $_SESSION['erro'] =
                'Você não pode alterar este usuário.';
            header('Location:?c=usuario');
            exit;
        }

        $perfil = $this->validarPerfilSolicitado(
            $_POST['perfil'] ?? ''
        );

        if ($senha !== '') {
            $this->validarSenhaForteOuRedirecionar(
                $senha,
                '?c=usuario&m=editar&id=' . $idUsuario
            );
        }

        $this->model->editar([

            'idUsuario' => $idUsuario,

            'nome' => $_POST['nome'],

            'email' => $_POST['email'],

            'perfil' => $perfil,

            'senha' => $senha

        ]);

        if ($senha !== '') {
            (new DispositivoConfiavel())->revogarUsuario($idUsuario);
        }

        if (
            $idUsuario === (int)(
                $_SESSION['usuario']['idUsuario'] ?? 0
            )
        ) {
            $_SESSION['usuario']['nome'] = $_POST['nome'];
            $_SESSION['usuario']['email'] = $_POST['email'];
            $_SESSION['usuario']['perfil'] = $perfil;
        }

        voltarParaRetorno("Location:?c=usuario");
    }

    public function excluir($id)
    {
        validarPermissaoExclusao();

        if (
            (int)$id === (int)(
                $_SESSION['usuario']['idUsuario'] ?? 0
            )
        ) {
            $_SESSION['erro'] =
                'Não é possível excluir o próprio usuário.';
            header('Location:?c=usuario');
            exit;
        }

        $this->model->excluir($id);

        voltarParaRetorno("Location:?c=usuario");
    }

    public function login()
    {
        $turnstileSiteKey = Turnstile::siteKey();
        include "view/usuario/login.php";
    }

    public function autenticar()
    {
        if (
            !Turnstile::validar(
                $_POST['cf-turnstile-response'] ?? '',
                'login'
            )
        ) {
            $_SESSION['erro'] =
                'Não foi possível validar o CAPTCHA. Tente novamente.';
            header('Location:?c=usuario&m=login');
            exit;
        }

        $usuario =
            $this->model
            ->autenticar(

                $_POST['email'],

                $_POST['senha']

            );

        if ($usuario) {
            session_regenerate_id(true);

            $navegadorConfiavel = false;

            if (!empty($usuario['dois_fatores_ativo'])) {
                try {
                    $navegadorConfiavel =
                        (new DispositivoConfiavel())->validar(
                            (int)$usuario['idUsuario']
                        );
                } catch (Throwable $erro) {
                    error_log(
                        'Falha ao validar navegador confiável: ' .
                        $erro->getMessage()
                    );
                }
            }

            if (
                !empty($usuario['dois_fatores_ativo'])
                && !$navegadorConfiavel
            ) {
                $_SESSION['autenticacao_pendente'] = [
                    'idUsuario' => (int)$usuario['idUsuario'],
                    'expira_em' => time() + 300,
                    'tentativas' => 0,
                    'csrf_token' => bin2hex(random_bytes(32))
                ];
                header('Location:?c=usuario&m=segundoFator');
                exit;
            }

            $this->concluirAutenticacao($usuario);
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

    public function segundoFator()
    {
        $pendente = $this->autenticacaoPendente();

        if (!$pendente) {
            header('Location:?c=usuario&m=login');
            exit;
        }

        include 'view/usuario/segundo_fator.php';
    }

    public function validarSegundoFator()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location:?c=usuario&m=segundoFator');
            exit;
        }

        $pendente = $this->autenticacaoPendente();

        if (!$pendente) {
            header('Location:?c=usuario&m=login');
            exit;
        }

        if (
            !hash_equals(
                $pendente['csrf_token'] ?? '',
                (string)($_POST['csrf_token'] ?? '')
            )
        ) {
            unset($_SESSION['autenticacao_pendente']);
            $_SESSION['erro'] = 'A etapa de verificação expirou.';
            header('Location:?c=usuario&m=login');
            exit;
        }

        $usuario = $this->model->buscarPorId($pendente['idUsuario']);

        if (!$usuario || empty($usuario['dois_fatores_ativo'])) {
            unset($_SESSION['autenticacao_pendente']);
            $_SESSION['erro'] = 'Não foi possível concluir a autenticação.';
            header('Location:?c=usuario&m=login');
            exit;
        }

        try {
            $doisFatores = new DoisFatores();
            $segredo = $doisFatores->descriptografar(
                $usuario['dois_fatores_segredo'] ?? ''
            );
            $periodo = $doisFatores->validarCodigoNovo(
                $segredo,
                $_POST['codigo'] ?? '',
                $usuario['dois_fatores_ultimo_periodo'] ?? null
            );
        } catch (Throwable $erro) {
            $periodo = false;
            error_log('Falha na validação do 2FA: ' . $erro->getMessage());
        }

        if (
            $periodo === false
            || !$this->model->registrarPeriodoDoisFatores(
                (int)$usuario['idUsuario'],
                (int)$periodo
            )
        ) {
            $_SESSION['autenticacao_pendente']['tentativas']++;

            if ($_SESSION['autenticacao_pendente']['tentativas'] >= 5) {
                unset($_SESSION['autenticacao_pendente']);
                $_SESSION['erro'] =
                    'Limite de tentativas atingido. Faça o login novamente.';
                header('Location:?c=usuario&m=login');
                exit;
            }

            $_SESSION['erro_2fa_login'] =
                'Código inválido ou já utilizado. Aguarde o próximo código.';
            header('Location:?c=usuario&m=segundoFator');
            exit;
        }

        if (!empty($_POST['confiar_navegador'])) {
            try {
                (new DispositivoConfiavel())->confiar(
                    (int)$usuario['idUsuario']
                );
            } catch (Throwable $erro) {
                error_log(
                    'Falha ao confiar no navegador: ' . $erro->getMessage()
                );
            }
        }

        unset($_SESSION['autenticacao_pendente']);
        $this->concluirAutenticacao($usuario);
    }

    public function primeiroAcesso()
    {
        if (empty($_SESSION['usuario']['troca_senha_obrigatoria'])) {
            header('Location:' . rotaInicial());
            exit;
        }

        $idUsuario = (int)($_SESSION['usuario']['idUsuario'] ?? 0);
        $usuario = $this->model->buscarPorId($idUsuario);

        if (!$usuario) {
            session_unset();
            session_destroy();
            header('Location:?c=usuario&m=login');
            exit;
        }

        if (empty($_SESSION['csrf_alterar_senha'])) {
            $_SESSION['csrf_alterar_senha'] = bin2hex(random_bytes(32));
        }

        $mensagemConta = $_SESSION['mensagem_conta'] ?? null;
        unset($_SESSION['mensagem_conta']);

        include 'view/usuario/primeiro_acesso.php';
    }

    public function minhaConta()
    {
        $idUsuario = (int)(
            $_SESSION['usuario']['idUsuario']
            ?? 0
        );
        $usuario = $this->model->buscarPorId($idUsuario);

        if (!$usuario) {
            session_unset();
            session_destroy();
            header('Location:?c=usuario&m=login');
            exit;
        }

        if (empty($_SESSION['csrf_alterar_senha'])) {
            $_SESSION['csrf_alterar_senha'] = bin2hex(random_bytes(32));
        }
        if (empty($_SESSION['csrf_2fa'])) {
            $_SESSION['csrf_2fa'] = bin2hex(random_bytes(32));
        }

        $configuracaoDoisFatores = $_SESSION['configuracao_2fa'] ?? null;
        $qrCodeDoisFatores = null;

        if (
            $configuracaoDoisFatores
            && time() - (int)$configuracaoDoisFatores['criado_em'] > 600
        ) {
            unset($_SESSION['configuracao_2fa']);
            $configuracaoDoisFatores = null;
        }

        if ($configuracaoDoisFatores) {
            $qrCodeDoisFatores = (new DoisFatores())->gerarQrCode(
                $usuario['email'],
                $configuracaoDoisFatores['segredo']
            );
        }

        $mensagemConta = $_SESSION['mensagem_conta'] ?? null;
        unset($_SESSION['mensagem_conta']);

        include "view/template/cabecalho.php";
        include "view/template/menu.php";
        include "view/usuario/minha_conta.php";
        include "view/template/rodape.php";
    }

    public function alterarMinhaSenha()
    {
        $primeiroAcesso =
            !empty($_SESSION['usuario']['troca_senha_obrigatoria']);

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header(
                'Location:?c=usuario&m=' .
                ($primeiroAcesso ? 'primeiroAcesso' : 'minhaConta')
            );
            exit;
        }

        $tokenSessao = $_SESSION['csrf_alterar_senha'] ?? '';
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        unset($_SESSION['csrf_alterar_senha']);

        if (
            !$tokenSessao
            || !$tokenEnviado
            || !hash_equals($tokenSessao, $tokenEnviado)
        ) {
            $this->redirecionarMinhaConta(
                'erro',
                'Sua sessao de alteracao expirou. Tente novamente.'
            );
        }

        $senhaAtual = (string)($_POST['senha_atual'] ?? '');
        $novaSenha = (string)($_POST['nova_senha'] ?? '');
        $confirmacao = (string)($_POST['confirmar_senha'] ?? '');

        if ($senhaAtual === '' || $novaSenha === '' || $confirmacao === '') {
            $this->redirecionarMinhaConta(
                'erro',
                'Preencha todos os campos de senha.'
            );
        }

        $erroSenha = Senha::validarForte($novaSenha);

        if ($erroSenha !== null) {
            $this->redirecionarMinhaConta(
                'erro',
                $erroSenha
            );
        }

        if ($novaSenha !== $confirmacao) {
            $this->redirecionarMinhaConta(
                'erro',
                'A confirmacao nao corresponde a nova senha.'
            );
        }

        $idUsuario = (int)(
            $_SESSION['usuario']['idUsuario']
            ?? 0
        );
        $usuario = $this->model->buscarPorId($idUsuario);

        if (
            !$usuario
            || !password_verify($senhaAtual, $usuario['senha'])
        ) {
            $this->redirecionarMinhaConta(
                'erro',
                'A senha atual esta incorreta.'
            );
        }

        if (password_verify($novaSenha, $usuario['senha'])) {
            $this->redirecionarMinhaConta(
                'erro',
                'A nova senha deve ser diferente da senha atual.'
            );
        }

        if (!$this->model->alterarSenha($idUsuario, $novaSenha)) {
            $this->redirecionarMinhaConta(
                'erro',
                'Nao foi possivel alterar a senha. Tente novamente.'
            );
        }

        (new DispositivoConfiavel())->revogarUsuario($idUsuario, true);

        session_regenerate_id(true);
        $_SESSION['usuario']['troca_senha_obrigatoria'] = 0;

        if ($primeiroAcesso) {
            $_SESSION['sucesso'] =
                'Senha definida com sucesso. Bem-vindo ao sistema.';
            header('Location:' . rotaInicial());
            exit;
        }

        $this->redirecionarMinhaConta('sucesso', 'Senha alterada com sucesso.');
    }

    public function iniciarDoisFatores()
    {
        $this->validarPostCsrfDoisFatores();
        $idUsuario = (int)($_SESSION['usuario']['idUsuario'] ?? 0);
        $usuario = $this->model->buscarPorId($idUsuario);

        if (!$usuario || !empty($usuario['dois_fatores_ativo'])) {
            $this->redirecionarMinhaConta(
                'erro',
                'A autenticação de dois fatores já está ativa.'
            );
        }

        $_SESSION['configuracao_2fa'] = [
            'segredo' => (new DoisFatores())->gerarSegredo(),
            'criado_em' => time()
        ];

        $this->redirecionarMinhaConta(
            'sucesso',
            'Leia o QR Code e confirme o código para concluir a ativação.'
        );
    }

    public function confirmarDoisFatores()
    {
        $this->validarPostCsrfDoisFatores();
        $configuracao = $_SESSION['configuracao_2fa'] ?? null;

        if (
            !$configuracao
            || time() - (int)$configuracao['criado_em'] > 600
        ) {
            unset($_SESSION['configuracao_2fa']);
            $this->redirecionarMinhaConta(
                'erro',
                'A configuração do 2FA expirou. Inicie novamente.'
            );
        }

        $doisFatores = new DoisFatores();
        $periodo = $doisFatores->validarCodigoNovo(
            $configuracao['segredo'],
            $_POST['codigo'] ?? ''
        );

        if ($periodo === false) {
            $this->redirecionarMinhaConta(
                'erro',
                'Código inválido. Confira o horário do celular e tente novamente.'
            );
        }

        $idUsuario = (int)($_SESSION['usuario']['idUsuario'] ?? 0);
        $segredoCriptografado = $doisFatores->criptografar(
            $configuracao['segredo']
        );

        if (
            !$this->model->ativarDoisFatores(
                $idUsuario,
                $segredoCriptografado,
                (int)$periodo
            )
        ) {
            $this->redirecionarMinhaConta(
                'erro',
                'Não foi possível ativar o 2FA. Tente novamente.'
            );
        }

        unset($_SESSION['configuracao_2fa']);
        $_SESSION['usuario']['dois_fatores_ativo'] = 1;

        $this->redirecionarMinhaConta(
            'sucesso',
            'Autenticação de dois fatores ativada com sucesso.'
        );
    }

    public function desativarDoisFatores()
    {
        $this->validarPostCsrfDoisFatores();
        $idUsuario = (int)($_SESSION['usuario']['idUsuario'] ?? 0);
        $usuario = $this->model->buscarPorId($idUsuario);
        $senha = (string)($_POST['senha_atual'] ?? '');

        if (
            !$usuario
            || empty($usuario['dois_fatores_ativo'])
            || !password_verify($senha, $usuario['senha'])
        ) {
            $this->redirecionarMinhaConta(
                'erro',
                'Senha atual inválida ou 2FA não está ativo.'
            );
        }

        try {
            $doisFatores = new DoisFatores();
            $segredo = $doisFatores->descriptografar(
                $usuario['dois_fatores_segredo']
            );
            $periodo = $doisFatores->validarCodigoNovo(
                $segredo,
                $_POST['codigo'] ?? '',
                $usuario['dois_fatores_ultimo_periodo'] ?? null
            );
        } catch (Throwable $erro) {
            $periodo = false;
            error_log('Falha ao desativar 2FA: ' . $erro->getMessage());
        }

        if ($periodo === false) {
            $this->redirecionarMinhaConta(
                'erro',
                'Código do autenticador inválido ou já utilizado.'
            );
        }

        if (!$this->model->desativarDoisFatores($idUsuario)) {
            $this->redirecionarMinhaConta(
                'erro',
                'Não foi possível desativar o 2FA.'
            );
        }

        (new DispositivoConfiavel())->revogarUsuario($idUsuario, true);

        $_SESSION['usuario']['dois_fatores_ativo'] = 0;

        $this->redirecionarMinhaConta(
            'sucesso',
            'Autenticação de dois fatores desativada.'
        );
    }

    public function resetarDoisFatores()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !ehAdministrador()) {
            header('Location:?c=usuario');
            exit;
        }

        $tokenSessao = $_SESSION['csrf_2fa_admin'] ?? '';
        $tokenEnviado = (string)($_POST['csrf_token'] ?? '');
        $idUsuario = (int)($_POST['idUsuario'] ?? 0);

        if (
            $tokenSessao === ''
            || !hash_equals($tokenSessao, $tokenEnviado)
            || $idUsuario <= 0
            || $idUsuario === (int)($_SESSION['usuario']['idUsuario'] ?? 0)
        ) {
            $_SESSION['erro'] = 'Não foi possível redefinir o 2FA deste usuário.';
            header('Location:?c=usuario');
            exit;
        }

        unset($_SESSION['csrf_2fa_admin']);

        if (!$this->model->desativarDoisFatores($idUsuario)) {
            $_SESSION['erro'] = 'Não foi possível redefinir o 2FA.';
            header('Location:?c=usuario&m=editar&id=' . $idUsuario);
            exit;
        }

        (new DispositivoConfiavel())->revogarUsuario($idUsuario);

        $_SESSION['sucesso'] = 'Autenticação de dois fatores redefinida.';
        header('Location:?c=usuario&m=editar&id=' . $idUsuario);
        exit;
    }

    private function concluirAutenticacao($usuario)
    {
        session_regenerate_id(true);
        unset($_SESSION['autenticacao_pendente']);

        $_SESSION['usuario'] = [
            'idUsuario' => (int)$usuario['idUsuario'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil'],
            'troca_senha_obrigatoria' =>
                (int)($usuario['troca_senha_obrigatoria'] ?? 0),
            'dois_fatores_ativo' =>
                (int)($usuario['dois_fatores_ativo'] ?? 0)
        ];
        $_SESSION['ultimo_acesso'] = time();

        if (function_exists('registrarAtividadeSistema')) {
            registrarAtividadeSistema(
                'Usuario',
                'autenticar',
                $usuario['idUsuario']
            );
        }

        $destino = !empty($usuario['troca_senha_obrigatoria'])
            ? '?c=usuario&m=primeiroAcesso'
            : rotaInicial();
        header('Location:' . $destino);
        exit;
    }

    private function autenticacaoPendente()
    {
        $pendente = $_SESSION['autenticacao_pendente'] ?? null;

        if (
            !$pendente
            || empty($pendente['idUsuario'])
            || time() > (int)($pendente['expira_em'] ?? 0)
        ) {
            unset($_SESSION['autenticacao_pendente']);
            return null;
        }

        return $pendente;
    }

    private function validarPostCsrfDoisFatores()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location:?c=usuario&m=minhaConta');
            exit;
        }

        $tokenSessao = $_SESSION['csrf_2fa'] ?? '';
        $tokenEnviado = (string)($_POST['csrf_token'] ?? '');

        if (
            $tokenSessao === ''
            || $tokenEnviado === ''
            || !hash_equals($tokenSessao, $tokenEnviado)
        ) {
            $this->redirecionarMinhaConta(
                'erro',
                'A sessão da configuração de segurança expirou.'
            );
        }
    }

    private function validarSenhaForteOuRedirecionar($senha, $destino)
    {
        $erro = Senha::validarForte($senha);

        if ($erro === null) {
            return;
        }

        $_SESSION['erro'] = $erro;
        header('Location:' . $destino);
        exit;
    }

    private function validarPerfilSolicitado($perfil)
    {
        $perfisValidos = [
            'Administrador',
            'Gerente',
            'Secretario',
            'Recrutador'
        ];

        if (!in_array($perfil, $perfisValidos, true)) {
            $_SESSION['erro'] = 'Perfil de usuário inválido.';
            header('Location:?c=usuario');
            exit;
        }

        if ($perfil === 'Administrador' && !ehAdministrador()) {
            $_SESSION['erro'] =
                'Apenas administradores podem conceder este perfil.';
            header('Location:?c=usuario');
            exit;
        }

        return $perfil;
    }

    private function redirecionarMinhaConta($tipo, $mensagem)
    {
        $_SESSION['mensagem_conta'] = [
            'tipo' => $tipo,
            'texto' => $mensagem
        ];

        $metodo = !empty($_SESSION['usuario']['troca_senha_obrigatoria'])
            ? 'primeiroAcesso'
            : 'minhaConta';
        header('Location:?c=usuario&m=' . $metodo);
        exit;
    }
}
