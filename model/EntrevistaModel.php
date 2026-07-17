<?php
require_once "config/Conexao.php";

class EntrevistaModel
{
    private $conexao;

    function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    function inserir(
        $candidaturaId,
        $data,
        $hora,
        $responsavel,
        $local,
        $observacoes
    ) {

        $sql = "
            INSERT INTO entrevistas
            (
                candidatura_id,
                data_entrevista,
                hora_entrevista,
                responsavel,
                local_entrevista,
                observacoes
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "isssss",
            $candidaturaId,
            $data,
            $hora,
            $responsavel,
            $local,
            $observacoes
        );

        return $comando->execute();
    }

    function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {
        $sql = "
        SELECT

            e.*,

            cand.idCandidato AS candidato_id,

            cand.nome AS candidato,

            v.titulo AS vaga,

            CASE
                WHEN c.status_contratacao = 'Contratado'
                    THEN 'Contratado'
                ELSE c.status
            END AS status

        FROM entrevistas e

        INNER JOIN candidaturas c
            ON c.idCandidatura = e.candidatura_id

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        WHERE 1 = 1
    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['incluir_finalizadas'])) {
            $sql .= "
                AND c.status IN (
                    'Entrevista Agendada',
                    'Aprovado',
                    'Recusado',
                    'Entrevistado'
                )
            ";
        } else {
            $sql .= "
                AND c.status = 'Entrevista Agendada'
            ";
        }

        if (!empty($filtros['busca'])) {


            $sql .= "
        AND (
            cand.nome LIKE ?
            OR v.titulo LIKE ?
            OR e.responsavel LIKE ?
        )
    ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "sss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }

        /*
     * DATA
     */

        if (
            !empty($filtros['data_inicio']) &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "
            AND DATE(e.data_entrevista)
            BETWEEN ? AND ?
        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {

            $sql .= "
            AND DATE(e.data_entrevista) = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
            AND DATE(e.data_entrevista) = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
        }

        /*
     * HORA
     */

        if (
            !empty($filtros['hora_inicio']) &&
            !empty($filtros['hora_fim'])
        ) {

            $sql .= "
            AND e.hora_entrevista
            BETWEEN ? AND ?
        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['hora_inicio'];

            $valores[] =
                $filtros['hora_fim'];
        } elseif (!empty($filtros['hora_inicio'])) {

            $sql .= "
            AND e.hora_entrevista >= ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['hora_inicio'];
        } elseif (!empty($filtros['hora_fim'])) {

            $sql .= "
            AND e.hora_entrevista <= ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['hora_fim'];
        }

        $sql .= "
    ORDER BY
        e.data_entrevista DESC,
        e.hora_entrevista DESC
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

        if (!$comando) {

            die($this->conexao->error);
        }

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

    function buscarPorId($id)
    {
        $sql = "
            SELECT *
            FROM entrevistas
            WHERE idEntrevista = ?
        ";

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

    function atualizar(
        $id,
        $data,
        $hora,
        $responsavel,
        $local,
        $observacoes
    ) {

        $sql = "
            UPDATE entrevistas
            SET
                data_entrevista = ?,
                hora_entrevista = ?,
                responsavel = ?,
                local_entrevista = ?,
                observacoes = ?
            WHERE idEntrevista = ?
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "sssssi",
            $data,
            $hora,
            $responsavel,
            $local,
            $observacoes,
            $id
        );

        return $comando->execute();
    }

    function excluir($id)
    {
        $sql = "
            DELETE FROM entrevistas
            WHERE idEntrevista = ?
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        return $comando->execute();
    }

    function buscarPorIdPersonalizado($id)
    {
        $sql = "
    SELECT

    e.*,
    c.nome AS candidato,
    c.telefone,
    v.titulo AS vaga,
    ca.idCandidatura,
    ca.candidato_id
FROM entrevistas e
INNER JOIN candidaturas ca
    ON ca.idCandidatura = e.candidatura_id
INNER JOIN candidatos c
    ON c.idCandidato = ca.candidato_id
INNER JOIN vagas v
    ON v.idVaga = ca.vaga_id
WHERE e.idEntrevista = ?
";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param("i", $id);

        if ($comando->execute()) {
            return $comando
                ->get_result()
                ->fetch_assoc();
        }

        return null;
    }

    function salvarObservacoes(
        $id,
        $observacoes
    ) {
        $sql = "
        UPDATE entrevistas
        SET observacoes = ?
        WHERE idEntrevista = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "si",
            $observacoes,
            $id
        );

        return $comando->execute();
    }

    function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) AS total

        FROM entrevistas e

        INNER JOIN candidaturas c
            ON c.idCandidatura = e.candidatura_id

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['incluir_finalizadas'])) {
            $sql .= "
                AND c.status IN (
                    'Entrevista Agendada',
                    'Aprovado',
                    'Recusado',
                    'Entrevistado'
                )
            ";
        } else {
            $sql .= "
                AND c.status = 'Entrevista Agendada'
            ";
        }

        if (!empty($filtros['busca'])) {


            $sql .= "
        AND (
            cand.nome LIKE ?
            OR v.titulo LIKE ?
            OR e.responsavel LIKE ?
        )
    ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "sss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }

        if (
            !empty($filtros['data_inicio'])
            &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "
            AND DATE(e.data_entrevista)
            BETWEEN ? AND ?
        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {

            $sql .= "
            AND DATE(e.data_entrevista) = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
            AND DATE(e.data_entrevista) = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
        }

        if (
            !empty($filtros['hora_inicio'])
            &&
            !empty($filtros['hora_fim'])
        ) {

            $sql .= "
            AND e.hora_entrevista
            BETWEEN ? AND ?
        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['hora_inicio'];

            $valores[] =
                $filtros['hora_fim'];
        } elseif (!empty($filtros['hora_inicio'])) {

            $sql .= "
            AND e.hora_entrevista >= ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['hora_inicio'];
        } elseif (!empty($filtros['hora_fim'])) {

            $sql .= "
            AND e.hora_entrevista <= ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['hora_fim'];
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

    public function salvarHistorico(
        $entrevistaId,
        $dataAnterior,
        $horaAnterior,
        $dataNova,
        $horaNova,
        $motivo,
        $usuario
    ) {

        $sql = "
        INSERT INTO historico_entrevistas
        (
            entrevista_id,
            data_anterior,
            hora_anterior,
            data_nova,
            hora_nova,
            motivo,
            usuario
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "issssss",
            $entrevistaId,
            $dataAnterior,
            $horaAnterior,
            $dataNova,
            $horaNova,
            $motivo,
            $usuario
        );

        return $comando->execute();
    }

    public function reagendar(
        $idEntrevista,
        $novaData,
        $novaHora
    ) {

        $sql = "
        UPDATE entrevistas
        SET
            data_entrevista = ?,
            hora_entrevista = ?
        WHERE idEntrevista = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "ssi",
            $novaData,
            $novaHora,
            $idEntrevista
        );

        return $comando->execute();
    }

    public function buscarHorariosOcupados(
        $data,
        $responsavel,
        $ignorar = null
    ) {
        $sql = "
SELECT

    e.hora_entrevista,
    e.responsavel,
    c.nome AS candidato

FROM entrevistas e

INNER JOIN candidaturas ca
    ON ca.idCandidatura = e.candidatura_id

INNER JOIN candidatos c
    ON c.idCandidato = ca.candidato_id

WHERE e.data_entrevista = ?
";

        $tipos = "s";

        $valores = [
            $data
        ];

        if ($ignorar) {

            $sql .= "
            AND idEntrevista <> ?
        ";

            $tipos .= "i";

            $valores[] =
                $ignorar;
        }

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            $tipos,
            ...$valores
        );

        $comando->execute();

        return
            $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function horarioOcupado(
        $data,
        $hora,
        $responsavel,
        $idEntrevista = null
    ) {

        $sql = "
        SELECT COUNT(*) total
        FROM entrevistas
        WHERE data_entrevista = ?
        AND hora_entrevista = ?
        AND responsavel = ?
    ";

        if ($idEntrevista) {

            $sql .= "
            AND idEntrevista <> ?
        ";
        }

        $comando =
            $this->conexao->prepare($sql);

        if ($idEntrevista) {

            $comando->bind_param(
                "sssi",
                $data,
                $hora,
                $responsavel,
                $idEntrevista
            );
        } else {

            $comando->bind_param(
                "sss",
                $data,
                $hora,
                $responsavel
            );
        }

        $comando->execute();

        $resultado =
            $comando
            ->get_result()
            ->fetch_assoc();

        return $resultado['total'] > 0;
    }

    public function buscarHistorico(
        $idEntrevista
    ) {
        $sql = "
        SELECT *

        FROM historico_entrevistas

        WHERE entrevista_id = ?

        ORDER BY idHistorico DESC
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $idEntrevista
        );

        $comando->execute();

        return
            $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function contarReagendamentos(
        $idEntrevista
    ) {
        $sql = "
        SELECT COUNT(*) total
        FROM historico_entrevistas
        WHERE entrevista_id = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $idEntrevista
        );

        $comando->execute();

        return
            $comando
                ->get_result()
                ->fetch_assoc()['total'];
    }
}
