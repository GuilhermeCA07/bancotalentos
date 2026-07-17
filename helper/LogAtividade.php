<?php

require_once 'model/LogModel.php';

function registrarAtividadeSistema($controller, $metodo, $id = null)
{
    if (
        empty($_SESSION['usuario'])
        || !deveRegistrarAtividade($controller, $metodo)
    ) {
        return false;
    }

    $acao = classificarAcaoLog($controller, $metodo, $id);
    $registroId = resolverRegistroIdLog($id);
    $dadosJson = codificarDadosLog(coletarDadosLog());
    $descricao = montarDescricaoLog(
        $controller,
        $metodo,
        $acao,
        $registroId
    );

    try {
        $usuario = $_SESSION['usuario'];
        $registrado = (new LogModel())->inserir([
            'usuario_id' => (int)($usuario['idUsuario'] ?? 0),
            'usuario_nome' => $usuario['nome'] ?? '',
            'usuario_perfil' => $usuario['perfil'] ?? '',
            'modulo' => $controller,
            'metodo' => $metodo,
            'registro_id' => $registroId,
            'acao' => $acao,
            'descricao' => $descricao,
            'dados' => $dadosJson,
            'user_agent' => substr(
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                0,
                255
            )
        ]);

        if (!$registrado) {
            error_log(
                'Falha ao registrar log de atividade: '
                . $controller . '::' . $metodo
            );
        }

        return $registrado;
    } catch (Throwable $e) {
        error_log('Erro ao registrar log de atividade: ' . $e->getMessage());

        return false;
    }
}

function deveRegistrarAtividade($controller, $metodo)
{
    $controllerNormalizado = strtolower((string)$controller);
    $metodoNormalizado = strtolower((string)$metodo);

    if ($controllerNormalizado === 'log') {
        return false;
    }

    if ($metodoNormalizado === 'index') {
        return !empty(coletarFiltrosAtivosLog());
    }

    $metodosSomenteLeitura = [
        'add',
        'editar',
        'visualizar',
        'finalizar',
        'reagendar',
        'login',
        'verificarsessao',
        'horariosdisponiveis',
        'historico',
        'baixarcurriculo',
        'visualizarcurriculo',
        'buscarhabilidades',
        'detalhesrecusa',
        'alterarminhasenha',
        'verificaremail',
        'verificarcurriculo'
    ];

    if (in_array($metodoNormalizado, $metodosSomenteLeitura, true)) {
        return false;
    }

    if (
        (
            strpos($metodoNormalizado, 'excluir') === 0
            || strpos($metodoNormalizado, 'remover') === 0
        )
        && function_exists('podeExcluir')
        && !podeExcluir()
    ) {
        return false;
    }

    $prefixosGravaveis = [
        'salvar',
        'cadastrar',
        'atualizar',
        'alterar',
        'excluir',
        'remover',
        'contratar',
        'dispensar',
        'candidatar',
        'testar',
        'autodispensa',
        'reenviar'
    ];

    foreach ($prefixosGravaveis as $prefixo) {
        if (strpos($metodoNormalizado, $prefixo) === 0) {
            return true;
        }
    }

    return in_array(
        $metodoNormalizado,
        [
            'autenticar',
            'sair',
            'validartoken',
            'atualizarsenhapropria',
            'ativardoisfatores',
            'desativardoisfatores',
            'resetardoisfatores'
        ],
        true
    );
}

function coletarFiltrosAtivosLog()
{
    if (!empty($_GET['pagina'])) {
        return [];
    }

    $ignorados = [
        'c',
        'm',
        'pagina',
        'id',
        'expirado',
        '_'
    ];
    $filtros = [];

    foreach ($_GET as $campo => $valor) {
        if (in_array(strtolower((string)$campo), $ignorados, true)) {
            continue;
        }

        if (!valorFiltroPreenchidoLog($valor)) {
            continue;
        }

        $filtros[$campo] = $valor;
    }

    return $filtros;
}

function valorFiltroPreenchidoLog($valor)
{
    if (is_array($valor)) {
        foreach ($valor as $item) {
            if (valorFiltroPreenchidoLog($item)) {
                return true;
            }
        }

        return false;
    }

    return trim((string)$valor) !== '';
}

function classificarAcaoLog($controller, $metodo, $id = null)
{
    $controllerNormalizado = strtolower((string)$controller);
    $metodoNormalizado = strtolower((string)$metodo);

    if ($metodoNormalizado === 'index') {
        return 'Busca/Filtro';
    }

    if (in_array($metodoNormalizado, ['autenticar'], true)) {
        return 'Login';
    }

    if ($metodoNormalizado === 'sair') {
        return 'Logout';
    }

    if (
        strpos($metodoNormalizado, 'excluir') === 0
        || strpos($metodoNormalizado, 'remover') === 0
    ) {
        return 'Exclusao';
    }

    if ($metodoNormalizado === 'contratar') {
        return 'Contratacao';
    }

    if (in_array($metodoNormalizado, ['dispensar', 'autodispensa'], true)) {
        return 'Dispensa';
    }

    if ($metodoNormalizado === 'testar') {
        return 'Teste';
    }

    if (strpos($metodoNormalizado, 'reenviar') === 0) {
        return 'Reenvio';
    }

    if (
        in_array($controllerNormalizado, ['configuracao', 'configuracaoemail'], true)
        || in_array(
            $metodoNormalizado,
            [
                'salvarcurriculo',
                'salvarfinalizacao',
                'salvarreagendamento',
                'atualizar',
                'alterarstatus',
                'atualizarsenhapropria',
                'ativardoisfatores',
                'desativardoisfatores',
                'resetardoisfatores'
            ],
            true
        )
        || strpos($metodoNormalizado, 'atualizar') === 0
        || strpos($metodoNormalizado, 'alterar') === 0
    ) {
        return 'Edicao';
    }

    if (
        strpos($metodoNormalizado, 'cadastrar') === 0
        || $metodoNormalizado === 'candidatar'
    ) {
        return 'Adicao';
    }

    if (strpos($metodoNormalizado, 'salvar') === 0) {
        return resolverRegistroIdLog($id) === null
            ? 'Adicao'
            : 'Edicao';
    }

    return 'Acao';
}

function resolverRegistroIdLog($id = null)
{
    if (is_numeric($id) && (int)$id > 0) {
        return (int)$id;
    }

    foreach ($_POST as $campo => $valor) {
        if (
            stripos((string)$campo, 'id') === 0
            && is_numeric($valor)
            && (int)$valor > 0
        ) {
            return (int)$valor;
        }
    }

    return null;
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

function codificarDadosLog($dados)
{
    $opcoes = JSON_UNESCAPED_UNICODE;

    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $opcoes |= JSON_INVALID_UTF8_SUBSTITUTE;
    }

    $json = json_encode($dados, $opcoes);

    if ($json === false) {
        error_log('Erro ao codificar dados do log: ' . json_last_error_msg());

        return '{}';
    }

    return $json;
}

function filtrarDadosSensiveis($dados)
{
    $bloqueados = [
        'senha',
        'smtp_senha',
        'password',
        'token',
        'csrf_token',
        'codigo',
        'cf-turnstile-response'
    ];
    $limpos = [];

    foreach ($dados as $chave => $valor) {
        $chaveNormalizada = strtolower((string)$chave);

        if (
            in_array($chaveNormalizada, $bloqueados, true)
            || strpos($chaveNormalizada, 'senha') !== false
            || strpos($chaveNormalizada, 'password') !== false
            || strpos($chaveNormalizada, 'token') !== false
        ) {
            $limpos[$chave] = '[protegido]';
            continue;
        }

        if (is_array($valor)) {
            $limpos[$chave] = filtrarDadosSensiveis($valor);
            continue;
        }

        $limpos[$chave] = is_string($valor)
            ? substr($valor, 0, 500)
            : $valor;
    }

    return $limpos;
}

function montarDescricaoLog($controller, $metodo, $acao, $id)
{
    $modulos = [
        'Candidato' => 'Candidatos',
        'Candidatura' => 'Candidaturas',
        'Categoria' => 'Categorias',
        'Configuracao' => 'Aparência',
        'ConfiguracaoEmail' => 'E-mail do Token',
        'Contratacao' => 'Contratações',
        'Departamento' => 'Departamentos',
        'Entrevista' => 'Entrevistas',
        'Habilidade' => 'Habilidades',
        'Usuario' => 'Usuários',
        'Vaga' => 'Vagas'
    ];
    $modulo = $modulos[$controller] ?? $controller;
    $metodoNormalizado = strtolower((string)$metodo);

    $descricoesEspecificas = [
        'salvarfinalizacao' => 'Entrevista finalizada',
        'salvarreagendamento' => 'Entrevista reagendada',
        'salvarcurriculo' => 'Currículo atualizado',
        'alterarstatus' => 'Status alterado em ' . $modulo,
        'contratar' => 'Candidato marcado como contratado',
        'dispensar' => 'Candidato dispensado',
        'autodispensa' => 'Autodispensa registrada',
        'testar' => 'Teste da configuração de e-mail realizado',
        'autenticar' => 'Login realizado',
        'sair' => 'Logout realizado',
        'atualizarsenhapropria' => 'Senha da propria conta alterada',
        'ativardoisfatores' => 'Autenticação de dois fatores ativada',
        'desativardoisfatores' => 'Autenticação de dois fatores desativada',
        'resetardoisfatores' => 'Autenticação de dois fatores redefinida por administrador'
    ];

    if ($acao === 'Busca/Filtro') {
        $partes = [];

        foreach (coletarFiltrosAtivosLog() as $campo => $valor) {
            $partes[] = $campo . ': ' . resumirValorLog($valor);
        }

        return 'Filtro aplicado em ' . $modulo
            . ($partes ? ' (' . implode(', ', $partes) . ')' : '');
    }

    $descricao = $descricoesEspecificas[$metodoNormalizado]
        ?? rotuloAcaoLog($acao) . ' em ' . $modulo;

    if ($id !== null) {
        $descricao .= ' (ID: ' . $id . ')';
    }

    return $descricao;
}

function resumirValorLog($valor)
{
    if (is_array($valor)) {
        $valor = implode(', ', array_map('strval', $valor));
    }

    $valor = trim((string)$valor);

    return mb_strlen($valor) > 80
        ? mb_substr($valor, 0, 77) . '...'
        : $valor;
}

function rotuloAcaoLog($acao)
{
    $rotulos = [
        'Adicao' => 'Adição',
        'Edicao' => 'Edição',
        'Exclusao' => 'Exclusão',
        'Contratacao' => 'Contratação',
        'Acao' => 'Ação',
        'Cadastro/Atualizacao' => 'Cadastro/Atualização',
        'Atualizacao' => 'Atualização'
    ];

    return $rotulos[$acao] ?? $acao;
}
