<?php
require_once "config/Conexao.php";

class ContratacaoModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {

        $sql = "

    SELECT

        c.idCandidatura,
        c.candidato_id,

        c.status,
        c.status_contratacao,

        c.data_contratacao,
        c.data_candidatura,
        c.data_atualizacao,

        c.motivo_desligamento,

        cand.nome,
        cand.telefone,

        v.titulo,

        CASE

            WHEN c.status_contratacao IS NULL
                OR c.status_contratacao = 'Aguardando'
            THEN 'Aguardando'

            ELSE c.status_contratacao

        END AS status_exibicao

    FROM candidaturas c

    INNER JOIN candidatos cand
        ON cand.idCandidato =
        c.candidato_id

    INNER JOIN vagas v
        ON v.idVaga =
        c.vaga_id

    WHERE c.status = 'Aprovado'

    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "

        AND (

            cand.nome LIKE ?

            OR v.titulo LIKE ?

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

        if (!empty($filtros['status'])) {

            if (
                $filtros['status']
                == 'Aguardando'
            ) {

                $sql .= "

            AND (

                c.status_contratacao IS NULL

                OR c.status_contratacao = 'Aguardando'

            )

            ";
            } else {

                $sql .= "

            AND c.status_contratacao = ?

            ";

                $tipos .= "s";

                $valores[] =
                    $filtros['status'];
            }
        }

        if (
            !empty($filtros['data_inicio'])
            &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "

        AND DATE(c.data_contratacao)

        BETWEEN ? AND ?

        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {

            $sql .= "

        AND DATE(c.data_contratacao) >= ?

        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "

        AND DATE(c.data_contratacao) <= ?

        ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
        }

        $sql .= "

    ORDER BY

    COALESCE(

        c.data_contratacao,

        c.data_atualizacao,

        c.data_candidatura

    ) DESC

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

        if (!empty($valores)) {

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

    public function contratar($idCandidatura)
    {
        $sql = "

    UPDATE candidaturas

    SET

        status_contratacao = 'Contratado',

        data_contratacao = CURDATE()

    WHERE idCandidatura = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $idCandidatura
        );

        return $comando->execute();
    }

    public function dispensar(
        $idCandidatura,
        $motivo
    ) {
        $sql = "

    UPDATE candidaturas

    SET

        status_contratacao = 'Dispensado',

        motivo_desligamento = ?

    WHERE idCandidatura = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "si",
            $motivo,
            $idCandidatura
        );

        return $comando->execute();
    }

    public function autoDispensa(
        $idCandidatura,
        $motivo
    ) {
        $sql = "

    UPDATE candidaturas

    SET

        status_contratacao = 'Auto-Dispensa',

        motivo_desligamento = ?

    WHERE idCandidatura = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "si",
            $motivo,
            $idCandidatura
        );

        return $comando->execute();
    }

    public function contarTodos(
        $filtros = []
    ) {

        $sql = "

    SELECT
        COUNT(*) total

    FROM candidaturas c

    INNER JOIN candidatos cand
        ON cand.idCandidato =
        c.candidato_id

    INNER JOIN vagas v
        ON v.idVaga =
        c.vaga_id

    WHERE c.status = 'Aprovado'

    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "

        AND (

            cand.nome LIKE ?

            OR v.titulo LIKE ?

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

        if (!empty($filtros['status'])) {

            if (
                $filtros['status']
                == 'Aguardando'
            ) {

                $sql .= "

            AND (

                c.status_contratacao IS NULL

                OR c.status_contratacao = 'Aguardando'

            )

            ";
            } else {

                $sql .= "
                AND c.status_contratacao = ?
            ";

                $tipos .= "s";

                $valores[] =
                    $filtros['status'];
            }
        }

        if (
            !empty($filtros['data_inicio'])
            &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "
            AND DATE(c.data_contratacao)
            BETWEEN ? AND ?
        ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
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

        return
            $comando
                ->get_result()
                ->fetch_assoc()['total'];
    }
}
