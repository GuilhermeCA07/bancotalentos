<?php
require_once "config/Conexao.php";

class ChamadaModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function buscarEntrevistasAgendadas()
    {
        $sql = "
            SELECT

                e.idEntrevista,

                cand.nome,

                cand.telefone,

                v.titulo,

                e.data_entrevista,

                e.hora_entrevista,

                e.local_entrevista,

                e.responsavel

            FROM entrevistas e

            INNER JOIN candidaturas c
                ON c.idCandidatura = e.candidatura_id

            INNER JOIN candidatos cand
                ON cand.idCandidato = c.candidato_id

            INNER JOIN vagas v
                ON v.idVaga = c.vaga_id

            WHERE cand.whatsapp = 1

            ORDER BY
                e.data_entrevista,
                e.hora_entrevista
        ";

        return $this->conexao
            ->query($sql)
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {

        $sql = "
    SELECT

        c.idCandidatura,

        c.status,

        c.status_contratacao,

        CASE

            WHEN c.status_contratacao = 'Contratado'
                THEN 'Contratado'

            WHEN c.status_contratacao = 'Dispensado'
                THEN 'Dispensado'

            WHEN c.status_contratacao = 'Auto-Dispensa'
                THEN 'Auto-Dispensa'

            ELSE c.status

        END AS status_exibicao,

        cand.nome,

        cand.telefone,

        cand.whatsapp,

        v.titulo

    FROM candidaturas c

    INNER JOIN candidatos cand
        ON cand.idCandidato = c.candidato_id

    INNER JOIN vagas v
        ON v.idVaga = c.vaga_id

    WHERE 1 = 1
";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
                AND (
                    cand.nome LIKE ?
                    OR cand.telefone LIKE ?
                    OR v.titulo LIKE ?
                )
            ";

            $busca =
                "%"
                . $filtros['busca']
                . "%";

            $tipos .= "sss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }

        if (!empty($filtros['status'])) {

            $sql .= "

        AND (

            c.status = ?

            OR c.status_contratacao = ?

        )

    ";

            $tipos .= "ss";

            $valores[] =
                $filtros['status'];

            $valores[] =
                $filtros['status'];
        }

        if (
            !empty($filtros['sem_whatsapp'])
        ) {

            $sql .= "
                AND cand.whatsapp = 0
            ";
        }

        $sql .= "
            ORDER BY cand.nome
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
            $this->conexao
            ->prepare($sql);

        if (
            !empty($valores)
        ) {

            $comando->bind_param(
                $tipos,
                ...$valores
            );
        }

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function contarRegistros(
        $filtros = []
    ) {

        $sql = "

        SELECT
            COUNT(*) AS total

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato =
            c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga =
            c.vaga_id

        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "

            AND (

                cand.nome LIKE ?

                OR cand.telefone LIKE ?

                OR v.titulo LIKE ?

            )
        ";

            $busca =
                "%"
                . $filtros['busca']
                . "%";

            $tipos .= "sss";

            $valores[] = $busca;
            $valores[] = $busca;
            $valores[] = $busca;
        }

        if (!empty($filtros['status'])) {

            $sql .= "
            AND c.status = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['status'];
        }

        if (
            !empty($filtros['sem_whatsapp'])
        ) {

            $sql .= "
            AND cand.whatsapp = 0
        ";
        }

        $comando =
            $this->conexao
            ->prepare($sql);

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
