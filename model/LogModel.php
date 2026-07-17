<?php
require_once "config/Conexao.php";

class LogModel
{
    private $conexao;
    private $colunas = [];

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();

        $this->garantirTabela();
        $this->carregarColunas();
    }

    private function garantirTabela()
    {
        $tabelaExistente = $this->conexao->query(
            "SHOW TABLES LIKE 'logs_atividade'"
        );

        if ($tabelaExistente && $tabelaExistente->num_rows > 0) {
            return;
        }

        $sql = "
        CREATE TABLE IF NOT EXISTS logs_atividade
        (
            idLog INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NULL,
            usuario_nome VARCHAR(120) NULL,
            usuario_perfil VARCHAR(60) NULL,
            modulo VARCHAR(80) NOT NULL,
            metodo VARCHAR(120) NOT NULL,
            registro_id INT NULL,
            acao VARCHAR(60) NOT NULL,
            descricao VARCHAR(255) NOT NULL,
            dados LONGTEXT NULL,
            user_agent VARCHAR(255) NULL,
            criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_logs_criado_em (criado_em),
            INDEX idx_logs_usuario (usuario_id),
            INDEX idx_logs_modulo (modulo),
            INDEX idx_logs_acao (acao)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

        if (!$this->conexao->query($sql)) {
            error_log(
                'Erro ao garantir tabela de logs: ' .
                    $this->conexao->error
            );
        }
    }

    private function carregarColunas()
    {
        $resultado =
            $this->conexao->query(
                "SHOW COLUMNS FROM logs_atividade"
            );

        if (!$resultado) {
            $this->registrarErro(
                'Erro ao carregar colunas da tabela de logs: ' .
                    $this->conexao->error
            );

            return;
        }

        while ($coluna = $resultado->fetch_assoc()) {
            $this->colunas[] =
                $coluna['Field'];
        }
    }

    public function inserir($dados)
    {
        if (empty($this->colunas)) {
            $this->carregarColunas();
        }
        $campos = [
            'usuario_id' => [
                'tipo' => 'i',
                'valor' => $dados['usuario_id']
            ],
            'usuario_nome' => [
                'tipo' => 's',
                'valor' => $dados['usuario_nome']
            ],
            'usuario_perfil' => [
                'tipo' => 's',
                'valor' => $dados['usuario_perfil']
            ],
            'modulo' => [
                'tipo' => 's',
                'valor' => $dados['modulo']
            ],
            'metodo' => [
                'tipo' => 's',
                'valor' => $dados['metodo']
            ],
            'registro_id' => [
                'tipo' => 'i',
                'valor' => $dados['registro_id']
            ],
            'acao' => [
                'tipo' => 's',
                'valor' => $dados['acao']
            ],
            'descricao' => [
                'tipo' => 's',
                'valor' => $dados['descricao']
            ],
            'dados' => [
                'tipo' => 's',
                'valor' => $dados['dados']
            ],
            'user_agent' => [
                'tipo' => 's',
                'valor' => $dados['user_agent']
            ],
            'criado_em' => [
                'tipo' => 's',
                'valor' => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
                    ->format('Y-m-d H:i:s')
            ]
        ];
        $colunasInsert = [];
        $placeholders = [];
        $tipos = "";
        $valores = [];

        foreach ($campos as $campo => $configuracao) {
            if (
                !in_array(
                    $campo,
                    $this->colunas
                )
            ) {
                continue;
            }

            $colunasInsert[] = $campo;
            $placeholders[] = "?";
            $tipos .= $configuracao['tipo'];
            $valores[] = $configuracao['valor'];
        }

        if (empty($colunasInsert)) {
            $this->registrarErro(
                'Nenhuma coluna compativel encontrada para inserir log.'
            );

            return false;
        }

        $sql = "
        INSERT INTO logs_atividade
        (
            " . implode(", ", $colunasInsert) . "
        )
        VALUES
        (
            " . implode(", ", $placeholders) . "
        )
    ";

        $comando =
            $this->conexao->prepare($sql);

        if (!$comando) {
            $this->registrarErro(
                'Erro ao preparar insert de log: ' .
                    $this->conexao->error
            );

            return false;
        }

        $parametros = [];
        $parametros[] = $tipos;

        foreach ($valores as $indice => $valor) {
            $parametros[] = &$valores[$indice];
        }

        call_user_func_array(
            [$comando, 'bind_param'],
            $parametros
        );

        $executado =
            $comando->execute();

        if (!$executado) {
            $this->registrarErro(
                'Erro ao executar insert de log: ' .
                    $comando->error
            );
        }

        return $executado;
    }

    private function registrarErro($mensagem)
    {
        error_log($mensagem);

        $diretorio =
            __DIR__ .
            '/../debug';

        if (!is_dir($diretorio)) {
            @mkdir(
                $diretorio,
                0777,
                true
            );
        }

        if (is_dir($diretorio) && is_writable($diretorio)) {
            @file_put_contents(
                $diretorio . '/log_atividade_erros.txt',
                date('Y-m-d H:i:s') .
                    ' - ' .
                    $mensagem .
                    PHP_EOL,
                FILE_APPEND
            );
        }
    }

    public function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {
        $sql = "
        SELECT *
        FROM logs_atividade
        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];

        $this->aplicarFiltros(
            $sql,
            $tipos,
            $valores,
            $filtros
        );

        $sql .= "
        ORDER BY criado_em DESC, idLog DESC
    ";

        if (
            $limite !== null
            &&
            $offset !== null
        ) {
            $sql .= "
            LIMIT ?
            OFFSET ?
        ";

            $tipos .= "ii";
            $valores[] = $limite;
            $valores[] = $offset;
        }

        $comando =
            $this->conexao->prepare($sql);

        if (!empty($valores)) {
            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) total
        FROM logs_atividade
        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];

        $this->aplicarFiltros(
            $sql,
            $tipos,
            $valores,
            $filtros
        );

        $comando =
            $this->conexao->prepare($sql);

        if (!empty($valores)) {
            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc()['total'];
    }

    public function buscarPorId($id)
    {
        $sql = "
        SELECT *
        FROM logs_atividade
        WHERE idLog = ?
        LIMIT 1
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc();
    }

    private function aplicarFiltros(
        &$sql,
        &$tipos,
        &$valores,
        $filtros
    ) {
        if (!empty($filtros['busca'])) {
            $sql .= "
            AND (
                usuario_nome LIKE ?
                OR usuario_perfil LIKE ?
                OR modulo LIKE ?
                OR metodo LIKE ?
                OR acao LIKE ?
                OR descricao LIKE ?
            )
        ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "ssssss";

            for ($i = 0; $i < 6; $i++) {
                $valores[] = $busca;
            }
        }

        if (!empty($filtros['usuario'])) {
            $sql .= "
            AND usuario_nome LIKE ?
        ";

            $tipos .= "s";
            $valores[] =
                "%" .
                $filtros['usuario'] .
                "%";
        }

        if (!empty($filtros['modulo'])) {
            $sql .= "
            AND modulo = ?
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['modulo'];
        }

        if (!empty($filtros['acao'])) {
            $sql .= "
            AND acao = ?
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['acao'];
        }

        if (!empty($filtros['data_inicio'])) {
            $sql .= "
            AND criado_em >= CONCAT(?, ' 00:00:00')
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= "
            AND criado_em < DATE_ADD(CONCAT(?, ' 00:00:00'), INTERVAL 1 DAY)
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['data_fim'];
        }
    }
}
