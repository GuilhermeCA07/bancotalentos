<?php
require_once "config/Conexao.php";

class VagaModel
{
    private $conexao;

    function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    function inserir(
        $titulo,
        $departamento,
        $quantidade,
        $cidade,
        $modalidade,
        $escala,
        $tipoContratacao,
        $salario,
        $descricao,
        $requisitos,
        $observacoes,
        $status,
        $cnh
    ) {

        $sql = "INSERT INTO vagas
        (
            titulo,
            departamento,
            cnh_obrigatoria,
            quantidade_vagas,
            cidade,
            modalidade,
            escala,
            tipo_contratacao,
            salario,
            descricao,
            requisitos,
            observacoes,
            status
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ssiissssdssss",
            $titulo,
            $departamento,
            $cnh,
            $quantidade,
            $cidade,
            $modalidade,
            $escala,
            $tipoContratacao,
            $salario,
            $descricao,
            $requisitos,
            $observacoes,
            $status
        );

        return $comando->execute();
    }

    function atualizar(
        $id,
        $titulo,
        $departamento,
        $quantidade,
        $cidade,
        $modalidade,
        $escala,
        $tipoContratacao,
        $salario,
        $descricao,
        $requisitos,
        $observacoes,
        $status,
        $cnh
    ) {

        $sql = "UPDATE vagas
        SET
            titulo = ?,
            departamento = ?,
            cnh_obrigatoria = ?,
            quantidade_vagas = ?,
            cidade = ?,
            escala = ?,
            modalidade = ?,
            tipo_contratacao = ?,
            salario = ?,
            descricao = ?,
            requisitos = ?,
            observacoes = ?,
            status = ?
        WHERE idVaga = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ssiissssdssssi",
            $titulo,
            $departamento,
            $cnh,
            $quantidade,
            $cidade,
            $escala,
            $modalidade,
            $tipoContratacao,
            $salario,
            $descricao,
            $requisitos,
            $observacoes,
            $status,
            $id
        );

        return $comando->execute();
    }

    function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {
        $sql = "
        SELECT *
        FROM vagas
        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
        AND (
            titulo LIKE ?
            OR departamento LIKE ?
            OR status LIKE ?
            OR cidade LIKE ?
            OR modalidade LIKE ?
            OR tipo_contratacao LIKE ?
            OR escala LIKE ?
            OR descricao LIKE ?
        )
    ";

            $busca = "%" . $filtros['busca'] . "%";

            $tipos .= "ssssssss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }



        if (
            !empty($filtros['departamento'])
        ) {

            $sql .= "
            AND departamento = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['departamento'];
        }

        if (
            !empty($filtros['status'])
        ) {

            $sql .= "
            AND status = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['status'];
        }

        if (!empty($filtros['modalidade'])) {

            $sql .= "
        AND modalidade = ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['modalidade'];
        }

        if (!empty($filtros['escala'])) {

            $sql .= "
        AND escala = ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['escala'];
        }

        if (!empty($filtros['cidade'])) {

            $sql .= "
        AND cidade LIKE ?
    ";

            $tipos .= "s";

            $valores[] =
                "%" .
                $filtros['cidade'] .
                "%";
        }

        $sql .= "
    ORDER BY data_criacao DESC
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

        if (
            !empty($valores)
        ) {

            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        if (
            $comando->execute()
        ) {

            return $comando
                ->get_result()
                ->fetch_all(
                    MYSQLI_ASSOC
                );
        }

        return [];
    }

    function buscarPorStatus($status)
    {
        $sql = "SELECT *
                FROM vagas
                WHERE status = ?
                ORDER BY titulo";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "s",
            $status
        );

        if ($comando->execute()) {

            return $comando
                ->get_result()
                ->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM vagas
                WHERE idVaga = ?";

        $comando =
            $this->conexao->prepare($sql);

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

    function alterarStatus(
        $id,
        $status
    ) {

        $sql = "UPDATE vagas
                SET status = ?
                WHERE idVaga = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "si",
            $status,
            $id
        );

        return $comando->execute();
    }

    function excluir($id)
    {
        $sql = "DELETE
                FROM vagas
                WHERE idVaga = ?";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        return $comando->execute();
    }

    function buscarAbertas()
    {
        $sql = "
        SELECT *
        FROM vagas
        WHERE status = 'Aberta'
        ORDER BY titulo
    ";

        $comando = $this->conexao->prepare($sql);

        if ($comando->execute()) {
            return $comando
                ->get_result()
                ->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM vagas
        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];


        if (!empty($filtros['busca'])) {

            $sql .= "
        AND (
            titulo LIKE ?
            OR departamento LIKE ?
            OR status LIKE ?
            OR cidade LIKE ?
            OR modalidade LIKE ?
            OR tipo_contratacao LIKE ?
            OR escala LIKE ?
            OR descricao LIKE ?
        )
    ";

            $busca = "%" . $filtros['busca'] . "%";

            $tipos .= "ssssssss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }


        if (!empty($filtros['departamento'])) {

            $sql .= "
            AND departamento = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['departamento'];
        }

        if (!empty($filtros['status'])) {

            $sql .= "
            AND status = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['status'];
        }

        if (!empty($filtros['modalidade'])) {

            $sql .= "
        AND modalidade = ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['modalidade'];
        }

        if (!empty($filtros['escala'])) {

            $sql .= "
        AND escala = ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['escala'];
        }

        if (!empty($filtros['cidade'])) {

            $sql .= "
        AND cidade LIKE ?
    ";

            $tipos .= "s";

            $valores[] =
                "%" .
                $filtros['cidade'] .
                "%";
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

    public function buscarVagasAtivas()
    {
        $sql = "

        SELECT *

        FROM vagas

        WHERE status = 'Aberta'

        ORDER BY titulo
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function fecharVaga($idVaga)
    {
        $sql = "
        UPDATE vagas
        SET status = 'Fechada'
        WHERE idVaga = ?
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $idVaga
        );

        return $comando->execute();
    }
}
