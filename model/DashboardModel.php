<?php
require_once "config/Conexao.php";

class DashboardModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function totalCandidatos()
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM candidatos
    ";

        $resultado =
            $this->conexao
            ->query($sql);

        return $resultado
            ->fetch_assoc()['total'];
    }

    public function totalVagasAtivas()
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM vagas
        WHERE status = 'Aberta'
    ";

        $resultado =
            $this->conexao
            ->query($sql);

        return $resultado
            ->fetch_assoc()['total'];
    }

    public function totalEntrevistasHoje()
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM entrevistas
        WHERE DATE(data_entrevista) = CURDATE()
    ";

        $resultado =
            $this->conexao
            ->query($sql);

        return $resultado
            ->fetch_assoc()['total'];
    }

    public function totalAprovados()
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM candidaturas
        WHERE status = 'Aprovado'
    ";

        $resultado =
            $this->conexao
            ->query($sql);

        return $resultado
            ->fetch_assoc()['total'];
    }

    public function proximasEntrevistas($limite = 5)
    {
        $sql = "
        SELECT

            e.data_entrevista,
            e.hora_entrevista,

            cand.nome AS candidato,

            v.titulo AS vaga

        FROM entrevistas e

        INNER JOIN candidaturas c
            ON c.idCandidatura = e.candidatura_id

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        WHERE e.data_entrevista >= CURDATE()

        ORDER BY
            e.data_entrevista ASC,
            e.hora_entrevista ASC

        LIMIT ?
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $limite
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

public function resumoCandidaturas()
{
    $sql = "
        SELECT

            SUM(
                CASE
                    WHEN status = 'Em Análise'
                    THEN 1
                    ELSE 0
                END
            ) AS analise,

            SUM(
                CASE
                    WHEN status = 'Entrevista Agendada'
                    THEN 1
                    ELSE 0
                END
            ) AS entrevista,

            SUM(
                CASE
                    WHEN status = 'Aprovado'
                    THEN 1
                    ELSE 0
                END
            ) AS aprovado,

            SUM(
                CASE
                    WHEN status = 'Recusado'
                    THEN 1
                    ELSE 0
                END
            ) AS recusado

        FROM candidaturas
    ";

    return $this->conexao
        ->query($sql)
        ->fetch_assoc();
}

public function totalEntrevistas()
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM entrevistas
    ";

    return $this->conexao
        ->query($sql)
        ->fetch_assoc()['total'];
}

}
