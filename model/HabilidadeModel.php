<?php
require_once "config/Conexao.php";

class HabilidadeModel
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
        SELECT
            h.*,
            c.nome AS categoria_nome

        FROM habilidades h

        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id

        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
        AND (
            h.nome LIKE ?
            OR c.nome LIKE ?
        )
    ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "ss";

            $valores[] = $busca;
            $valores[] = $busca;
        }

        if (!empty($filtros['categoria'])) {

            $sql .= "
            AND h.categoria_id = ?
        ";

            $tipos .= "i";

            $valores[] =
                $filtros['categoria'];
        }

        if (
            isset($filtros['status'])
            &&
            $filtros['status'] !== ''
        ) {

            $sql .= "
            AND h.ativo = ?
        ";

            $tipos .= "i";

            $valores[] =
                (int)$filtros['status'];
        }

        $sql .= "
        ORDER BY c.nome, h.nome
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

            $valores[] =
                $limite;

            $valores[] =
                $offset;
        }

        $comando =
            $this->conexao->prepare($sql);

        if (!empty($valores)) {

            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        if ($comando->execute()) {

            return $comando
                ->get_result()
                ->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function buscarAgrupadasPorCategoria()
    {
        $habilidades = $this->buscarTodos();

        $categorias = [];

        foreach ($habilidades as $habilidade) {
            $categorias[$habilidade['categoria']][] = $habilidade;
        }

        return $categorias;
    }

    function inserir(
        $nome,
        $categoriaId
    ) {
        $sql = "INSERT INTO habilidades
            (
                nome,
                categoria_id
            )
            VALUES (?, ?)";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "si",
            $nome,
            $categoriaId
        );

        return $comando->execute();
    }

    function atualizar(
        $id,
        $nome,
        $categoriaId
    ) {
        $sql = "UPDATE habilidades
            SET
                nome = ?,
                categoria_id = ?
            WHERE idHabilidade = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "sii",
            $nome,
            $categoriaId,
            $id
        );

        return $comando->execute();
    }

    function alterarStatus($id)
    {
        $sql = "
        SELECT
            h.ativo,
            c.ativo AS categoria_ativa
        FROM habilidades h
        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id
        WHERE h.idHabilidade = ?
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        $comando->execute();

        $resultado =
            $comando->get_result();

        $dados =
            $resultado->fetch_assoc();

        if (!$dados) {
            return [
                'sucesso' => false,
                'mensagem' => 'Habilidade não encontrada.'
            ];
        }

        $novoStatus =
            $dados['ativo'] ? 0 : 1;

        if (
            $novoStatus == 1 &&
            $dados['categoria_ativa'] == 0
        ) {
            return [
                'sucesso' => false,
                'mensagem' =>
                'Não é possível ativar uma habilidade cuja categoria está inativa.'
            ];
        }

        $sql = "UPDATE habilidades
            SET ativo = ?
            WHERE idHabilidade = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ii",
            $novoStatus,
            $id
        );

        $comando->execute();

        return [
            'sucesso' => true
        ];
    }

    function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM habilidades
                WHERE idHabilidade = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        if ($comando->execute()) {

            $resultado =
                $comando->get_result();

            return $resultado->fetch_assoc();
        }

        return null;
    }

    function buscarTodosComFiltro(
        $status = null
    ) {
        $sql = "
        SELECT
            h.*,
            c.nome AS categoria_nome
        FROM habilidades h
        LEFT JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id
    ";

        if ($status !== null) {
            $sql .= " WHERE h.ativo = ?";
        }

        $sql .= "
        ORDER BY c.nome, h.nome
    ";

        $comando =
            $this->conexao->prepare($sql);

        if ($status !== null) {
            $comando->bind_param(
                "i",
                $status
            );
        }

        if ($comando->execute()) {

            return $comando
                ->get_result()
                ->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function buscarAtivas()
    {
        $sql = "
        SELECT
            h.*,
            c.nome AS categoria
        FROM habilidades h
        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id
        WHERE h.ativo = 1
        AND c.ativo = 1
        ORDER BY c.nome, h.nome
    ";

        $comando = $this->conexao->prepare($sql);

        if (!$comando) {
            die($this->conexao->error);
        }

        if ($comando->execute()) {
            $resultado = $comando->get_result();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function buscarInativas()
    {
        $sql = "
        SELECT
            h.*,
            c.nome AS categoria
        FROM habilidades h
        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id
        WHERE h.ativo = 0
        ORDER BY c.nome, h.nome
    ";

        $comando = $this->conexao->prepare($sql);

        if (!$comando) {
            die($this->conexao->error);
        }

        if ($comando->execute()) {
            $resultado = $comando->get_result();
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) AS total

        FROM habilidades h

        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id

        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
        AND (
            h.nome LIKE ?
            OR c.nome LIKE ?
        )
    ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "ss";

            $valores[] = $busca;
            $valores[] = $busca;
        }

        if (!empty($filtros['categoria'])) {

            $sql .= "
            AND h.categoria_id = ?
        ";

            $tipos .= "i";

            $valores[] =
                $filtros['categoria'];
        }

        if (
            isset($filtros['status'])
            &&
            $filtros['status'] !== ''
        ) {

            $sql .= "
            AND h.ativo = ?
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


    function buscarTodasAtivas()
    {
        $sql = "
        SELECT
            h.*,
            c.nome AS categoria_nome

        FROM habilidades h

        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id

        WHERE h.ativo = 1

        ORDER BY c.nome, h.nome
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(MYSQLI_ASSOC);
    }
}
