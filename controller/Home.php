<?php

require "model/VagaModel.php";
require "model/CandidatoModel.php";
require "model/CandidaturaModel.php";
require "model/HabilidadeModel.php";
require "config/config.php";
require_once "model/CandidatoTokenModel.php";
require_once "helper/Mail.php";

class Home
{
    private $vagaModel;
    private $candidatoModel;
    private $candidaturaModel;
    private $habilidadeModel;
    private $tokenModel;

    public function __construct()
    {
        $this->vagaModel =
            new VagaModel();

        $this->candidatoModel =
            new CandidatoModel();

        $this->candidaturaModel =
            new CandidaturaModel();

        $this->habilidadeModel =
            new HabilidadeModel();

        $this->tokenModel =
            new CandidatoTokenModel();
    }

    private function atualizarSessaoCandidato($idCandidato)
    {
        $candidato =
            $this->candidatoModel
            ->buscarPorId($idCandidato);

        if (!$candidato) {
            return;
        }

        $candidato['habilidades'] =
            $this->candidatoModel
            ->buscarHabilidadesEditar(
                $idCandidato
            );

        $_SESSION['candidato'] = [

            'idCandidato' => $candidato['idCandidato'],

            'nome' => $candidato['nome'],

            'telefone' => $candidato['telefone'],

            'email' => $candidato['email'],

            'whatsapp' => $candidato['whatsapp'],

            'curriculo' => $candidato['curriculo'],

            'observacoes' => $candidato['observacoes'],

            'habilidades' => $candidato['habilidades']

        ];
    }

    public function index()
    {

        $habilidades =
            $this->habilidadeModel
            ->buscarTodos();

        $vagas =
            $this->vagaModel
            ->buscarVagasAtivas();

        include "view/home/index.php";
    }

    private function validarTurnstile()
    {
        $token =
            $_POST['cf-turnstile-response']
            ?? '';

        if (empty($token)) {

            return false;
        }

        $secret = TURNSTILE_SECRET;

        $dados =
            http_build_query([

                'secret' => $secret,

                'response' => $token

            ]);

        $contexto =
            stream_context_create([

                'http' => [

                    'method'  => 'POST',

                    'header'  =>
                    "Content-type: application/x-www-form-urlencoded",

                    'content' => $dados

                ]

            ]);

        $resultado =
            file_get_contents(

                'https://challenges.cloudflare.com/turnstile/v0/siteverify',

                false,

                $contexto

            );

        $json =
            json_decode(
                $resultado,
                true
            );

        return
            !empty($json['success']);
    }

    public function candidatar()
    {
        $tipoCadastro =
            $_POST['tipo_cadastro'] ?? 'vaga';

        $vagaId = 0;

        if ($tipoCadastro === "vaga") {

            if (empty($_POST['vaga_id'])) {

                header("Location:?c=home");
                exit;
            }

            $vagaId =
                (int) $_POST['vaga_id'];
        }

        if (
            !isset($_POST['aceite_lgpd'])
        ) {

            $_SESSION['erro'] =
                "É necessário aceitar os termos para continuar.";

            header(
                "Location:?c=home"
            );

            exit;
        }
        /*
        if (!$this->validarTurnstile()) {

            $_SESSION['erro'] =
                "Falha na validação de segurança.";

            header(
                "Location:?c=home"
            );

            exit;
        }*/

        $nome =
            trim($_POST['nome']);

        $telefone =
            preg_replace(
                '/\D/',
                '',
                $_POST['telefone']
            );

        $email =
            trim($_POST['email']);

        /*
        |--------------------------------------------------------------------------
        | Verifica se já existe sessão do candidato
        |--------------------------------------------------------------------------
        */

        if (!empty($_SESSION['candidato'])) {

            $idCandidato =
                $_SESSION['candidato']['idCandidato'];

            $candidato =
                $this->candidatoModel
                ->buscarPorId(
                    $idCandidato
                );
        } else {

            $candidato =
                $this->candidatoModel
                ->buscarPorEmail(
                    $email
                );
        }


        if (
            $tipoCadastro === "vaga"
            &&
            $candidato
        ) {

            $jaCandidatado =
                $this->candidaturaModel
                ->buscarPorCandidatoEVaga(
                    $candidato['idCandidato'],
                    $vagaId
                );

            if ($jaCandidatado) {

                $_SESSION['ja_candidatado'] =
                    "Você já possui uma candidatura registrada para esta vaga.";

                header("Location:?c=home");
                exit;
            }
        }

        $observacoes =
            trim(
                $_POST['observacoes']
                    ?? ''
            );

        $whatsapp =
            isset($_POST['whatsapp'])
            ? 1
            : 0;

        $curriculo =
            $this->salvarCurriculo();

        /*
        |--------------------------------------------------------------------------
        | Cria candidato caso não exista
        |--------------------------------------------------------------------------
        */

        if (!$candidato) {

            $idCandidato =
                $this->candidatoModel
                ->inserir(
                    $nome,
                    $telefone,
                    $whatsapp,
                    $email,
                    $curriculo,
                    $observacoes
                );

            $_SESSION['sucesso_candidatura'] =
                "Currilo enviado com sucesso!";

            $idCandidato =
                $this->candidatoModel
                ->buscarUltimoId();
        } else {

            /*
            |--------------------------------------------------------------------------
            | Atualiza currículo existente
            |--------------------------------------------------------------------------
            */

            if (
                $curriculo &&
                !empty($candidato['curriculo']) &&
                file_exists($candidato['curriculo'])
            ) {

                unlink(
                    $candidato['curriculo']
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Verifica se o novo e-mail já pertence a outro candidato
            |--------------------------------------------------------------------------
            */

            $emailExistente =
                $this->candidatoModel
                ->buscarPorEmail($email);

            if (

                $emailExistente

                &&

                $emailExistente['idCandidato']
                !=
                $idCandidato

            ) {

                $_SESSION['erro'] =
                    "Este e-mail já está sendo utilizado por outro candidato.";

                $this->atualizarSessaoCandidato($idCandidato);

                header("Location:?c=home");

                exit;
            }


            $this->candidatoModel
                ->atualizarDadosModal(

                    $candidato['idCandidato'],
                    $telefone,
                    $whatsapp,
                    $email,
                    $curriculo
                        ?: $candidato['curriculo'],
                    $observacoes,
                    $nome

                );

            $idCandidato =
                $candidato['idCandidato'];

            $_SESSION['candidatura_atualizada'] =
                "Seus dados foram atualizados e sua candidatura foi registrada com sucesso!";
        }

        /*
        |--------------------------------------------------------------------------
        | Salva apenas habilidades novas
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Salva / Atualiza habilidades
        |--------------------------------------------------------------------------
        */

        if (isset($_POST['habilidade'])) {

            foreach ($_POST['habilidade'] as $indice => $idHabilidade) {

                $nivel =
                    $_POST['nivel'][$indice]
                    ?? 0;

                $nomeExibicao =
                    $_POST['nome_exibicao'][$indice]
                    ?? '';

                $nomeHabilidade =
                    $_POST['habilidade_nome'][$indice]
                    ?? '';

                $jaExiste =
                    $this->candidatoModel
                    ->possuiHabilidade(

                        $idCandidato,

                        $idHabilidade,

                        $nomeHabilidade,

                        $nomeExibicao

                    );

                if ($jaExiste) {

                    $this->candidatoModel
                        ->atualizarHabilidade(

                            $idCandidato,

                            $idHabilidade,

                            $nomeExibicao,

                            $nivel

                        );
                } else {

                    $this->candidatoModel
                        ->salvarHabilidade(

                            $idCandidato,

                            $idHabilidade,

                            $nomeExibicao,

                            $nivel

                        );
                }
            }
        }

        $this->atualizarSessaoCandidato(
            $idCandidato
        );

        /*
        |--------------------------------------------------------------------------
        | Nova candidatura
        |--------------------------------------------------------------------------
        */

        if ($tipoCadastro === "vaga") {

            $this->candidaturaModel
                ->inserir(
                    $idCandidato,
                    $vagaId
                );
        }


        header(
            "Location:?c=home"
        );

        exit;
    }

    private function salvarCurriculo()
    {

        if (
            !isset($_FILES['curriculo'])
            ||
            $_FILES['curriculo']['error'] !== 0
        ) {
            return null;
        }

        /*
        * EXTENSÃO
        */

        $extensao =
            strtolower(
                pathinfo(
                    $_FILES['curriculo']['name'],
                    PATHINFO_EXTENSION
                )
            );

        $extensoesPermitidas = [

            'pdf',
            'doc',
            'docx'

        ];

        if (
            !in_array(
                $extensao,
                $extensoesPermitidas
            )
        ) {

            return null;
        }

        /*
        * MIME
        */

        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );

        $mime =
            finfo_file(
                $finfo,
                $_FILES['curriculo']['tmp_name']
            );

        finfo_close($finfo);

        $mimesPermitidos = [

            'application/pdf',

            'application/msword',

            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'

        ];

        if (
            !in_array(
                $mime,
                $mimesPermitidos
            )
        ) {

            return null;
        }
        var_dump("MIME OK");
        /*
        * TAMANHO
        */

        // 10 MEGA
        $tamanhoMaximo =
            10 * 1024 * 1024;

        if ($_FILES['curriculo']['size'] > $tamanhoMaximo) {

            return null;
        }
        var_dump("TAMANHO OK");
        /*
        * DIRETÓRIO
        */

        $diretorio =
            "uploads/curriculos/";

        if (!is_dir($diretorio)) {

            mkdir(
                $diretorio,
                0777,
                true
            );
        }
        var_dump("DIRETORIO OK");

        /*
        * NOME SEGURO
        */

        $nomeArquivo =
            date('YmdHis')
            . '_'
            . uniqid()
            . '.'
            . $extensao;

        $destino =
            $diretorio
            . $nomeArquivo;


        if (
            move_uploaded_file(
                $_FILES['curriculo']['tmp_name'],
                $destino
            )
        ) {

            var_dump("UPLOAD OK");
            return $destino;
        }

        return null;
    }
    public function cadastrarCurriculo()
    {
        /*
    if (!$this->validarTurnstile()) {

        $_SESSION['erro'] =
            "Falha na validação de segurança.";

        header("Location:?c=home");
        exit;
    }
    */

        if (!isset($_POST['aceite_lgpd'])) {

            $_SESSION['erro'] =
                "É necessário aceitar os termos para continuar.";

            header("Location:?c=home");
            exit;
        }

        $nome =
            trim($_POST['nome']);

        $telefone =
            preg_replace(
                '/\D/',
                '',
                $_POST['telefone']
            );

        $email =
            trim($_POST['email']);

        $observacoes =
            trim($_POST['observacoes'] ?? '');

        $whatsapp =
            isset($_POST['whatsapp']) ? 1 : 0;

        /*
        |--------------------------------------------------------------------------
        | Localiza candidato
        |--------------------------------------------------------------------------
        */

        if (!empty($_SESSION['candidato'])) {

            $idCandidato =
                $_SESSION['candidato']['idCandidato'];

            $candidato =
                $this->candidatoModel
                ->buscarPorId($idCandidato);
        } else {

            $candidato =
                $this->candidatoModel
                ->buscarPorEmail($email);
        }

        /*
        |--------------------------------------------------------------------------
        | Atualização
        |--------------------------------------------------------------------------
        */

        if ($candidato) {

            $idCandidato =
                $candidato['idCandidato'];

            /*
            |--------------------------------------------------------------------------
            | Verifica se o novo e-mail pertence a outro candidato
            |--------------------------------------------------------------------------
            */

            $emailExistente =
                $this->candidatoModel
                ->buscarPorEmail($email);

            if (

                $emailExistente

                &&

                $emailExistente['idCandidato']
                !=
                $idCandidato

            ) {

                $_SESSION['erro'] =
                    "Este e-mail já está sendo utilizado por outro candidato.";

                $this->atualizarSessaoCandidato($idCandidato);

                header("Location:?c=home");
                exit;
            }

            $curriculoNovo =
                $this->salvarCurriculo();

            if (

                $curriculoNovo

                &&

                !empty($candidato['curriculo'])

                &&

                file_exists($candidato['curriculo'])

            ) {

                unlink(
                    $candidato['curriculo']
                );
            }

            $this->candidatoModel
                ->atualizarDadosModal(

                    $idCandidato,

                    $nome,

                    $telefone,

                    $whatsapp,

                    $email,

                    $curriculoNovo
                        ?: $candidato['curriculo'],

                    $observacoes

                );

            $_SESSION['curriculo_atualizado'] =
                "Você já estava em nosso banco de talentos. Seus dados, currículo e habilidades foram atualizados com sucesso!";
        }

        /*
        |--------------------------------------------------------------------------
        | Novo candidato
        |--------------------------------------------------------------------------
        */ else {

            $curriculo =
                $this->salvarCurriculo();

            $this->candidatoModel
                ->inserir(

                    $nome,

                    $telefone,

                    $whatsapp,

                    $email,

                    $curriculo,

                    $observacoes

                );

            $idCandidato =
                $this->candidatoModel
                ->buscarUltimoId();

            $_SESSION['sucesso_curriculo'] =
                "Currículo enviado com sucesso! Agora você faz parte do nosso banco de talentos.";
        }

        /*
    |--------------------------------------------------------------------------
    | Salva / Atualiza habilidades
    |--------------------------------------------------------------------------
    */

        if (isset($_POST['habilidade'])) {

            foreach ($_POST['habilidade'] as $indice => $idHabilidade) {

                $nivel =
                    $_POST['nivel'][$indice] ?? 0;

                $nomeExibicao =
                    $_POST['nome_exibicao'][$indice] ?? '';

                $nomeHabilidade =
                    $_POST['habilidade_nome'][$indice] ?? '';

                $jaExiste =
                    $this->candidatoModel
                    ->possuiHabilidade(

                        $idCandidato,

                        $idHabilidade,

                        $nomeHabilidade,

                        $nomeExibicao

                    );

                if ($jaExiste) {

                    $this->candidatoModel
                        ->atualizarHabilidade(

                            $idCandidato,

                            $idHabilidade,

                            $nomeExibicao,

                            $nivel

                        );
                } else {

                    $this->candidatoModel
                        ->salvarHabilidade(

                            $idCandidato,

                            $idHabilidade,

                            $nomeExibicao,

                            $nivel

                        );
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Atualiza sessão
    |--------------------------------------------------------------------------
    */

        $this->atualizarSessaoCandidato($idCandidato);

        header("Location:?c=home");
        exit;
    }
    public function verificarEmail()
    {

        header(
            "Content-Type: application/json"
        );

        $email = trim($_POST['email']);

        if (
            empty($email)
        ) {

            echo json_encode([

                'sucesso' => false,

                'mensagem' =>
                'Informe um e-mail.'

            ]);

            exit;
        }

        $candidato =
            $this->candidatoModel
            ->buscarPorEmail(
                $email
            );

        /*
     * Não possui cadastro
     */

        if (!$candidato) {

            echo json_encode([

                'sucesso' => true,

                'novo' => true

            ]);

            exit;
        }

        /*
     * Já possui cadastro
     */

        $_SESSION['email_verificacao'] = $email;

        $resultado =
            $this->tokenModel
            ->gerarToken(

                $candidato['idCandidato'],

                $email

            );

        if (
            !$resultado['sucesso']
        ) {

            echo json_encode($resultado);

            exit;
        }

        $enviado = Mail::enviarToken(

            $email,
            $resultado['token']

        );


        if (!$enviado) {

            echo json_encode([

                'sucesso' => false,

                'mensagem' =>
                'Não foi possível enviar o e-mail.'

            ]);

            exit;
        }

        echo json_encode([

            'sucesso' => true,

            'novo' => false,

            'mensagem' =>
            'Código enviado para seu e-mail.'

        ]);

        exit;
    }

    public function verificarCurriculo()
    {
        $email =
            preg_replace(
                '/\D/',
                '',
                $_GET['email'] ?? ''
            );

        $candidato =
            $this->candidatoModel
            ->buscarPorEmail($email);

        echo json_encode([

            'possuiCurriculo' =>
            !empty($candidato['curriculo']),

            'dataAtualizacao' =>
            $candidato['data_atualizacao']
                ?? null

        ]);

        exit;
    }

    public function validarToken()
    {
        header('Content-Type: application/json');

        $email = $_SESSION['email_verificacao'] ?? '';

        $token = trim($_POST['token'] ?? '');

        if (empty($email) || empty($token)) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados inválidos.'
            ]);
        }


        $resultado = $this->tokenModel->validarToken(
            $email,
            $token
        );

        if (!$resultado) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Código inválido ou expirado.'
            ]);

            return;
        }


        $candidato = $this->candidatoModel->buscarPorEmail($email);
        $candidato['habilidades'] = $this->candidatoModel->buscarHabilidadesEditar($candidato['idCandidato']);

        $_SESSION['candidato'] = $candidato;

        echo json_encode([

            'sucesso' => true,
            'dados' => $_SESSION['candidato']

        ]);
    }

    public function verificarSessao()
    {
        header('Content-Type: application/json');

        if (!empty($_SESSION['candidato'])) {

            echo json_encode([

                'autenticado' => true,

                'dados' => $_SESSION['candidato']

            ]);

            return;
        }

        echo json_encode([

            'autenticado' => false

        ]);
    }

    public function removerHabilidade()
    {

        header('Content-Type: application/json');

        if (!isset($_SESSION['candidato'])) {

            echo json_encode([

                'sucesso' => false,

                'mensagem' => 'Sessão expirada.'

            ]);

            exit;
        }

        $idCandidato = $_SESSION['candidato']['idCandidato'];

        $idHabilidade =
            (int)($_POST['habilidade_id'] ?? 0);

        $nomeExibicao =
            trim($_POST['nome_exibicao'] ?? '');
        $nomeHabilidade =
            $_POST['habilidade_nome'] ?? '';

        $resultado = $this->candidatoModel->excluirHabilidade(
            $idCandidato,
            $idHabilidade,
            $nomeHabilidade,
            $nomeExibicao
        );
        $this->atualizarSessaoCandidato(
            $idCandidato
        );

        echo json_encode([

            'sucesso' => $resultado,
            'habilidades' => $_SESSION['candidato']['habilidades']

        ]);
    }

    public function reenviarCodigo()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['email_verificacao'])) {

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Sessão expirada."
            ]);

            return;
        }

        $email = $_SESSION['email_verificacao'];

        $candidato =
            $this->candidatoModel
            ->buscarPorEmail($email);

        if (!$candidato) {

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Candidato não encontrado."
            ]);

            return;
        }

        $resultado =
            $this->tokenModel
            ->gerarToken(

                $candidato['idCandidato'],

                $email

            );

        if (!$resultado['sucesso']) {

            echo json_encode($resultado);

            return;
        }

        $enviado =
            Mail::enviarToken(

                $email,

                $resultado['token']

            );

        echo json_encode([

            "sucesso" => $enviado === true,

            "mensagem" =>

            $enviado === true
                ? "Novo código enviado com sucesso."
                : "Erro ao enviar o código."

        ]);
    }
}
