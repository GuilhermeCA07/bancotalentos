<?php
require_once "config/Conexao.php";

class LogModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function inserir($dados)
    {
        $sql = "
        INSERT INTO logs_atividade
        (
            usuario_id,
            usuario_nome,
            usuario_perfil,
            modulo,
            metodo,
            registro_id,
            acao,
            descricao,
            dados,
            ip,
            user_agent
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ";

        $comando =
            $this->conexao->prepare($sql);

        if (!$comando) {
            return false;
        }

        $comando->bind_param(
            "issssisssss",
            $dados['usuario_id'],
            $dados['usuario_nome'],
            $dados['usuario_perfil'],
            $dados['modulo'],
            $dados['metodo'],
            $dados['registro_id'],
            $dados['acao'],
            $dados['descricao'],
            $dados['dados'],
            $dados['ip'],
            $dados['user_agent']
        );

        return $comando->execute();
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
            AND DATE(criado_em) >= ?
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= "
            AND DATE(criado_em) <= ?
        ";

            $tipos .= "s";
            $valores[] =
                $filtros['data_fim'];
        }
    }
}
