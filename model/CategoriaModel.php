<?php
require_once "config/Conexao.php";

class CategoriaModel
{
    private $conexao;

    function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {
        $sql = "
        SELECT *
        FROM categorias_habilidade
        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
        AND nome LIKE ?
    ";

            $busca =
                "%"
                . $filtros['busca']
                . "%";

            $tipos .= "s";

            $valores[] = $busca;
        }

        if (
            isset($filtros['status'])
            &&
            $filtros['status'] !== ''
        ) {

            $sql .= "
            AND ativo = ?
        ";

            $tipos .= "i";

            $valores[] =
                (int)$filtros['status'];
        }

        $sql .= "
        ORDER BY nome
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
            $this->conexao->prepare(
                $sql
            );

        if (!empty($valores)) {

            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        if ($comando->execute()) {

            return $comando
                ->get_result()
                ->fetch_all(
                    MYSQLI_ASSOC
                );
        }

        return [];
    }

    function buscarAtivas()
    {
        $sql = "SELECT *
                FROM categorias_habilidade
                WHERE ativo = 1
                ORDER BY nome";

        $comando = $this->conexao->prepare($sql);

        if ($comando->execute()) {

            $resultado = $comando->get_result();

            return $resultado->fetch_all(
                MYSQLI_ASSOC
            );
        }

        return [];
    }

    function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM categorias_habilidade
                WHERE idCategoria = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        if ($comando->execute()) {

            return $comando
                ->get_result()
                ->fetch_assoc();
        }

        return null;
    }

    function inserir($nome)
    {
        $sql = "INSERT INTO categorias_habilidade
                (nome)
                VALUES (?)";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "s",
            $nome
        );

        return $comando->execute();
    }

    function atualizar(
        $id,
        $nome
    ) {

        $sql = "UPDATE categorias_habilidade
                SET nome = ?
                WHERE idCategoria = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "si",
            $nome,
            $id
        );

        return $comando->execute();
    }

    function alterarStatus($id)
    {
        $sql = "SELECT ativo
            FROM categorias_habilidade
            WHERE idCategoria = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        $comando->execute();

        $resultado =
            $comando->get_result();

        $categoria =
            $resultado->fetch_assoc();

        if (!$categoria) {
            return false;
        }

        $novoStatus =
            $categoria['ativo'] ? 0 : 1;

        $sql = "UPDATE categorias_habilidade
            SET ativo = ?
            WHERE idCategoria = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ii",
            $novoStatus,
            $id
        );

        $comando->execute();

        $sql = "UPDATE habilidades
            SET ativo = ?
            WHERE categoria_id = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ii",
            $novoStatus,
            $id
        );

        return $comando->execute();
    }

    function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM categorias_habilidade
        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
        AND nome LIKE ?
    ";

            $busca =
                "%"
                . $filtros['busca']
                . "%";

            $tipos .= "s";

            $valores[] = $busca;
        }

        if (
            isset($filtros['status'])
            &&
            $filtros['status'] !== ''
        ) {

            $sql .= "
            AND ativo = ?
        ";

            $tipos .= "i";

            $valores[] =
                (int)$filtros['status'];
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
            ->fetch_assoc()['total'];
    }
}
