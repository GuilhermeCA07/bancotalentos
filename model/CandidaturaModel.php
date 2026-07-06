<?php
require_once "config/Conexao.php";

class CandidaturaModel
{
    private $conexao;

    function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function inserir($candidato, $vaga)
    {
        $sql = "
        INSERT INTO candidaturas
        (
            candidato_id,
            vaga_id,
            status
        )
        VALUES (?, ?, 'Em Análise')
    ";

        $comando =
            $this->conexao->prepare($sql);

        if (!$comando) {

            die("Erro Prepare: "
                . $this->conexao->error);
        }

        $comando->bind_param(
            "ii",
            $candidato,
            $vaga
        );

        if (!$comando->execute()) {

            die("Erro Execute: "
                . $comando->error);
        }

        return true;
    }



    function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {
        $sql = "
        SELECT

            c.idCandidatura,
            cand.idCandidato AS candidato_id,
            cand.nome AS candidato,
            v.titulo AS vaga,
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

            c.data_candidatura

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
    OR v.titulo LIKE ?
)
    ";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            //$tipos .= "sss";
            $tipos .= "ss";

            $valores[] = $busca;
            $valores[] = $busca;
            //$valores[] = $busca;
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
            !empty($filtros['data_inicio']) &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "
    AND DATE(c.data_candidatura)
    BETWEEN ? AND ?
    ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {

            $sql .= "
    AND DATE(c.data_candidatura) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
    AND DATE(c.data_candidatura) <= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
        }

        $sql .= "
    ORDER BY c.data_candidatura DESC
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

        $comando = $this->conexao->prepare($sql);

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

    function buscarPorIdPersonalizado($id)
    {
        $sql = "
        SELECT

            ca.*,

            c.nome,

            c.telefone,

            v.titulo

        FROM candidaturas ca

        INNER JOIN candidatos c
            ON c.idCandidato = ca.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = ca.vaga_id

        WHERE ca.idCandidatura = ?
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

    function buscarPorId($id)
    {
        $sql = "
            SELECT *
            FROM candidaturas
            WHERE idCandidatura = ?
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
        $candidato,
        $vaga
    ) {
        $sql = "
            UPDATE candidaturas
            SET
                candidato_id = ?,
                vaga_id = ?,
                data_atualizacao = NOW()
            WHERE idCandidatura = ?
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "iii",
            $candidato,
            $vaga,
            $id
        );

        return $comando->execute();
    }

    function excluir($id)
    {
        $sql = "
            DELETE FROM candidaturas
            WHERE idCandidatura = ?
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        return $comando->execute();
    }

    function alterarStatus(
        $id,
        $status
    ) {
        $sql = "
            UPDATE candidaturas
            SET
                status = ?,
                data_atualizacao = NOW()
            WHERE idCandidatura = ?
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "si",
            $status,
            $id
        );

        return $comando->execute();
    }

    function existeCandidatura(
        $candidato,
        $vaga
    ) {
        $sql = "
        SELECT COUNT(*)
        total
        FROM candidaturas
        WHERE candidato_id = ?
        AND vaga_id = ?
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "ii",
            $candidato,
            $vaga
        );

        $comando->execute();

        return
            $comando
                ->get_result()
                ->fetch_assoc()['total'];
    }

    function buscarDetalhes($id)
    {
        $sql = "
        SELECT

            c.*,

            cand.nome AS candidato,

            cand.telefone,

            cand.email,

            v.titulo AS vaga

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        WHERE c.idCandidatura = ?
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

    function recusar(
        $idCandidatura,
        $motivo
    ) {
        $sql = "
        UPDATE candidaturas
        SET
            status='Recusado',
            motivo_recusa=?
        WHERE idCandidatura=?
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

    function contarRegistros($filtros = [])
    {
        $sql = "
        SELECT COUNT(*) AS total

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
        OR v.titulo LIKE ?
        OR c.status LIKE ?
        OR c.status_contratacao LIKE ?
    )
";

            $busca =
                "%" .
                $filtros['busca'] .
                "%";

            $tipos .= "ssss";

            $valores[] = $busca;
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
            !empty($filtros['data_inicio']) &&
            !empty($filtros['data_fim'])
        ) {

            $sql .= "
    AND DATE(c.data_candidatura)
    BETWEEN ? AND ?
    ";

            $tipos .= "ss";

            $valores[] =
                $filtros['data_inicio'];

            $valores[] =
                $filtros['data_fim'];
        } elseif (!empty($filtros['data_inicio'])) {

            $sql .= "
    AND DATE(c.data_candidatura) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
    AND DATE(c.data_candidatura) <= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
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

    public function buscarPorCandidatoEVaga(
        $idCandidato,
        $idVaga
    ) {

        $sql = "
        SELECT
            idCandidatura

        FROM candidaturas

        WHERE candidato_id = ?
        AND vaga_id = ?

        LIMIT 1
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "ii",
            $idCandidato,
            $idVaga
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc();
    }

    public function buscarAprovados()
    {
        $sql = "

        SELECT

            c.idCandidatura,

            cand.nome,

            cand.telefone,

            cand.email,

            v.titulo,

            c.status

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato =
            c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga =
            c.vaga_id

        WHERE c.status = 'Aprovado'

        ORDER BY cand.nome
    ";

        return
            $this->conexao
            ->query($sql)
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function buscarRecusados()
    {
        $sql = "

        SELECT

            c.idCandidatura,

            cand.nome,

            cand.telefone,

            cand.email,

            v.titulo,

            c.status,

            c.motivo_recusa

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato =
            c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga =
            c.vaga_id

        WHERE c.status = 'Recusado'

        ORDER BY cand.nome
    ";

        return
            $this->conexao
            ->query($sql)
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function buscarPorStatus(
        $status
    ) {

        $sql = "

        SELECT

            c.*,

            cand.nome,
            cand.telefone,
            cand.email,

            v.titulo,

            e.idEntrevista,
            e.data_entrevista,
            e.hora_entrevista,
            e.responsavel,
            e.local_entrevista,
            e.observacoes,
            e.nota

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato =
            c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga =
            c.vaga_id

        LEFT JOIN entrevistas e
            ON e.candidatura_id =
            c.idCandidatura

        WHERE c.status = ?

        ORDER BY
    COALESCE(
        c.data_atualizacao,
        c.data_candidatura
    ) DESC
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "s",
            $status
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function contarDecisoes(
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

        LEFT JOIN entrevistas e
            ON e.candidatura_id =
            c.idCandidatura

        WHERE c.status IN (
            'Aprovado',
            'Recusado'
        )
    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "

            AND (

                cand.nome LIKE ?

                OR v.titulo LIKE ?

                OR e.responsavel LIKE ?

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

        if (!empty($filtros['busca'])) {

            $sql .= "

            AND (

                cand.nome LIKE ?

                OR v.titulo LIKE ?

                OR e.responsavel LIKE ?

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

            switch ($filtros['status']) {

                case 'Contratado':
                case 'Dispensado':
                case 'Auto-Dispensa':

                    $sql .= "
                AND c.status_contratacao = ?
            ";

                    $tipos .= "s";

                    $valores[] =
                        $filtros['status'];

                    break;

                case 'Aprovado':

                    $sql .= "

                AND c.status = 'Aprovado'

                AND (
                    c.status_contratacao IS NULL
                    OR c.status_contratacao = 'Aguardando'
                )

            ";

                    break;

                case 'Recusado':

                    $sql .= "
                AND c.status = 'Recusado'
            ";

                    break;
            }
        }

        if (!empty($filtros['responsavel'])) {

            $sql .= "
        AND e.responsavel LIKE ?
    ";

            $tipos .= "s";

            $valores[] =
                "%" .
                $filtros['responsavel'] .
                "%";
        }

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
        AND DATE(e.data_entrevista) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
        AND DATE(e.data_entrevista) <= ?
    ";

            $tipos .= "s";

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

    public function buscarDecisoes(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {

        $sql = "

        SELECT

    c.*,

    cand.nome,
    cand.telefone,
    cand.email,

    v.titulo,

    e.idEntrevista,
    e.data_entrevista,
    e.hora_entrevista,
    e.responsavel,

    CASE

        WHEN c.status_contratacao = 'Contratado'
            THEN 'Contratado'

        WHEN c.status_contratacao = 'Dispensado'
            THEN 'Dispensado'

        WHEN c.status_contratacao = 'Auto-Dispensa'
            THEN 'Auto-Dispensa'

        ELSE c.status

    END AS status_exibicao

FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato =
            c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga =
            c.vaga_id

        LEFT JOIN entrevistas e
            ON e.candidatura_id =
            c.idCandidatura

        WHERE c.status IN (
            'Aprovado',
            'Recusado'
        )
    ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "

            AND (

                cand.nome LIKE ?

                OR v.titulo LIKE ?

                OR e.responsavel LIKE ?

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

            switch ($filtros['status']) {

                case 'Contratado':
                case 'Dispensado':
                case 'Auto-Dispensa':

                    $sql .= "
                AND c.status_contratacao = ?
            ";

                    $tipos .= "s";

                    $valores[] =
                        $filtros['status'];

                    break;

                case 'Aprovado':

                    $sql .= "

                AND c.status = 'Aprovado'

                AND (
                    c.status_contratacao IS NULL
                    OR c.status_contratacao = 'Aguardando'
                )

            ";

                    break;

                case 'Recusado':

                    $sql .= "
                AND c.status = 'Recusado'
            ";

                    break;
            }
        }

        if (!empty($filtros['responsavel'])) {

            $sql .= "
        AND e.responsavel LIKE ?
    ";

            $tipos .= "s";

            $valores[] =
                "%" .
                $filtros['responsavel'] .
                "%";
        }

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
        AND DATE(e.data_entrevista) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicio'];
        } elseif (!empty($filtros['data_fim'])) {

            $sql .= "
        AND DATE(e.data_entrevista) <= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_fim'];
        }

        $sql .= "

        ORDER BY
    COALESCE(
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

            $valores[] = $limite;
            $valores[] = $offset;
        }

        $comando =
            $this->conexao
            ->prepare($sql);

        if (!$comando) {
            die("Erro SQL: " .
                $this->conexao->error .
                "<hr>" .
                $sql);
        }

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
            ->fetch_all(
                MYSQLI_ASSOC
            );
    }

    public function buscarDetalhesDecisao($idCandidatura)
    {
        $sql = "

        SELECT

            c.idCandidatura,
            c.candidato_id,
            c.vaga_id,
            c.status AS status_candidatura,
            c.status_contratacao,
            c.data_contratacao,
            c.motivo_desligamento,
            c.status_contratacao,
            c.motivo_recusa,
            c.data_candidatura,
            c.data_atualizacao,

            cand.idCandidato,
            cand.nome,
            cand.telefone,
            cand.email,
            cand.whatsapp,

            v.titulo,
            v.departamento,
            v.cidade,
            v.modalidade,

            e.idEntrevista,
            e.data_entrevista,
            e.hora_entrevista,
            e.responsavel,
            e.local_entrevista,
            e.observacoes AS observacoes_entrevista

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        LEFT JOIN entrevistas e
            ON e.candidatura_id = c.idCandidatura

        WHERE c.idCandidatura = ?

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        if (!$comando) {
            die($this->conexao->error);
        }

        $comando->bind_param(
            "i",
            $idCandidatura
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc();
    }

    /*public function buscarDetalhesDecisao($idCandidatura)
    {
        $sql = "

        SELECT

            c.*,

            cand.*,

            v.titulo,
            v.departamento,
            v.cidade,
            v.modalidade,

            e.idEntrevista,
            e.data_entrevista,
            e.hora_entrevista,
            e.responsavel,
            e.local_entrevista,
            e.observacoes AS observacoes_entrevista

        FROM candidaturas c

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        LEFT JOIN entrevistas e
            ON e.candidatura_id = c.idCandidatura

        WHERE c.idCandidatura = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $idCandidatura
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc();
    }*/

    public function aprovar($idCandidatura)
    {
        $sql = "
        UPDATE candidaturas
        SET
            status = 'Aprovado',
            status_contratacao = 'Aguardando',
            data_atualizacao = NOW()
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

    public function encerrarPorFechamentoVaga(
        $idVaga
    ) {
        $sql = "

    UPDATE candidaturas

    SET

        status = 'Recusado',

        motivo_recusa = ?,

        data_atualizacao = NOW()

    WHERE vaga_id = ?

    AND status IN (

        'Em Análise',

        'Entrevista Agendada'

    )
    ";

        $motivo =
            'Vaga encerrada por contratação de outro candidato.';

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "si",
            $motivo,
            $idVaga
        );

        return $comando->execute();
    }

    public function contarContratadosPorVaga($idVaga)
    {
        $sql = "
        SELECT COUNT(*) total
        FROM candidaturas
        WHERE vaga_id = ?
        AND status_contratacao = 'Contratado'
    ";

        $comando =
            $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $idVaga
        );

        $comando->execute();

        return
            (int)
            $comando
                ->get_result()
                ->fetch_assoc()['total'];
    }
}
