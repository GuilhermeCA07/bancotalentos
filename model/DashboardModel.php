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

            v.titulo AS vaga,

            e.responsavel

        FROM entrevistas e

        INNER JOIN candidaturas c
            ON c.idCandidatura = e.candidatura_id

        INNER JOIN candidatos cand
            ON cand.idCandidato = c.candidato_id

        INNER JOIN vagas v
            ON v.idVaga = c.vaga_id

        WHERE TIMESTAMP(e.data_entrevista, e.hora_entrevista) >= NOW()
        AND c.status = 'Entrevista Agendada'

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

public function resumoCandidaturas($meses = 6)
{
    $inicio = $this->inicioPeriodo($meses);
    $sql = "
        SELECT

            SUM(
                CASE
                    WHEN status = 'Aguardando Entrevista'
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
                    WHEN status = 'Entrevistado'
                    THEN 1
                    ELSE 0
                END
            ) AS entrevistado,

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
            ) AS recusado,

            SUM(
                CASE
                    WHEN status = 'Vaga Preenchida por Contratação'
                    THEN 1
                    ELSE 0
                END
            ) AS vaga_preenchida,

            SUM(
                CASE
                    WHEN status = 'Vaga Fechada'
                    THEN 1
                    ELSE 0
                END
            ) AS vaga_fechada

        FROM candidaturas
        WHERE data_candidatura >= ?
    ";

    $comando = $this->conexao->prepare($sql);
    $comando->bind_param('s', $inicio);
    $comando->execute();

    return $comando
        ->get_result()
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

public function indicadoresComplementares()
{
    $sql = "
        SELECT
            (SELECT COUNT(*) FROM candidaturas) AS total_candidaturas,
            (
                SELECT COUNT(*)
                FROM candidaturas
                WHERE status_contratacao = 'Contratado'
            ) AS total_contratados,
            (
                SELECT COUNT(*)
                FROM candidaturas
                WHERE status = 'Aguardando Entrevista'
            ) AS aguardando_entrevista,
            (
                SELECT COUNT(*)
                FROM entrevistas
                INNER JOIN candidaturas
                    ON candidaturas.idCandidatura = entrevistas.candidatura_id
                WHERE TIMESTAMP(data_entrevista, hora_entrevista)
                    BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
                AND candidaturas.status = 'Entrevista Agendada'
            ) AS entrevistas_proximos_7_dias
    ";

    $dados = $this->conexao
        ->query($sql)
        ->fetch_assoc();

    foreach ($dados as $chave => $valor) {
        $dados[$chave] = (int)$valor;
    }

    $dados['taxa_contratacao'] =
        $dados['total_candidaturas'] > 0
        ? round(
            ($dados['total_contratados'] / $dados['total_candidaturas']) * 100,
            1
        )
        : 0;

    return $dados;
}

public function alertasOperacionais()
{
    $sql = "
        SELECT
            (
                SELECT COUNT(*)
                FROM candidaturas
                WHERE status = 'Aguardando Entrevista'
                AND COALESCE(data_atualizacao, data_candidatura)
                    < DATE_SUB(NOW(), INTERVAL 7 DAY)
            ) AS aguardando_mais_7_dias,
            (
                SELECT COUNT(*)
                FROM entrevistas e
                INNER JOIN candidaturas c
                    ON c.idCandidatura = e.candidatura_id
                WHERE c.status = 'Entrevista Agendada'
                AND TIMESTAMP(e.data_entrevista, e.hora_entrevista) < NOW()
            ) AS entrevistas_atrasadas,
            (
                SELECT COUNT(*)
                FROM vagas v
                WHERE v.status = 'Aberta'
                AND NOT EXISTS (
                    SELECT 1
                    FROM candidaturas c
                    WHERE c.vaga_id = v.idVaga
                )
            ) AS vagas_sem_candidaturas,
            (
                SELECT COUNT(*)
                FROM vagas
                WHERE status = 'Pausada'
            ) AS vagas_pausadas
    ";

    $dados = $this->conexao
        ->query($sql)
        ->fetch_assoc();

    foreach ($dados as $chave => $valor) {
        $dados[$chave] = (int)$valor;
    }

    return $dados;
}

public function evolucaoMensal($meses = 6)
{
    $meses = max(3, min(24, (int)$meses));
    $inicio = (new DateTimeImmutable('first day of this month'))
        ->modify('-' . ($meses - 1) . ' months');
    $inicioSql = $inicio->format('Y-m-d');

    $series = [
        'candidatos' => $this->consultarSerieMensal(
            'candidatos',
            'dataCadastro',
            $inicioSql
        ),
        'candidaturas' => $this->consultarSerieMensal(
            'candidaturas',
            'data_candidatura',
            $inicioSql
        ),
        'contratacoes' => $this->consultarSerieMensal(
            'candidaturas',
            'data_contratacao',
            $inicioSql
        )
    ];

    $resultado = [
        'labels' => [],
        'candidatos' => [],
        'candidaturas' => [],
        'contratacoes' => []
    ];

    for ($indice = 0; $indice < $meses; $indice++) {
        $mes = $inicio->modify('+' . $indice . ' months');
        $chave = $mes->format('Y-m');
        $resultado['labels'][] = $mes->format('m/Y');

        foreach (['candidatos', 'candidaturas', 'contratacoes'] as $serie) {
            $resultado[$serie][] = $series[$serie][$chave] ?? 0;
        }
    }

    return $resultado;
}

public function candidaturasPorVaga($limite = 6, $meses = 6)
{
    $inicio = $this->inicioPeriodo($meses);
    $sql = "
        SELECT
            v.titulo,
            v.quantidade_vagas,
            COUNT(c.idCandidatura) AS total_candidaturas,
            COALESCE(
                SUM(c.status_contratacao = 'Contratado'),
                0
            ) AS contratados
        FROM vagas v
        LEFT JOIN candidaturas c
            ON c.vaga_id = v.idVaga
            AND c.data_candidatura >= ?
        GROUP BY v.idVaga, v.titulo, v.quantidade_vagas
        HAVING total_candidaturas > 0
        ORDER BY total_candidaturas DESC, v.data_criacao DESC
        LIMIT ?
    ";

    $comando = $this->conexao->prepare($sql);
    $comando->bind_param('si', $inicio, $limite);
    $comando->execute();

    $dados = $comando
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);

    foreach ($dados as &$item) {
        $item['quantidade_vagas'] = (int)$item['quantidade_vagas'];
        $item['total_candidaturas'] = (int)$item['total_candidaturas'];
        $item['contratados'] = (int)$item['contratados'];
    }
    unset($item);

    return $dados;
}

public function funilRecrutamento($meses = 6)
{
    $inicio = $this->inicioPeriodo($meses);
    $sql = "
        SELECT
            COUNT(*) AS candidaturas,
            SUM(
                EXISTS (
                    SELECT 1
                    FROM entrevistas e
                    WHERE e.candidatura_id = c.idCandidatura
                )
            ) AS entrevistas,
            SUM(c.status = 'Aprovado') AS aprovados,
            SUM(c.status_contratacao = 'Contratado') AS contratados
        FROM candidaturas c
        WHERE c.data_candidatura >= ?
    ";

    $comando = $this->conexao->prepare($sql);
    $comando->bind_param('s', $inicio);
    $comando->execute();
    $dados = $comando
        ->get_result()
        ->fetch_assoc();

    foreach ($dados as $chave => $valor) {
        $dados[$chave] = (int)$valor;
    }

    return $dados;
}

private function consultarSerieMensal($tabela, $campo, $inicio)
{
    $sql = "
        SELECT
            DATE_FORMAT($campo, '%Y-%m') AS mes,
            COUNT(*) AS total
        FROM $tabela
        WHERE $campo >= ?
        GROUP BY DATE_FORMAT($campo, '%Y-%m')
    ";

    $comando = $this->conexao->prepare($sql);
    $comando->bind_param('s', $inicio);
    $comando->execute();
    $resultado = [];

    foreach ($comando->get_result()->fetch_all(MYSQLI_ASSOC) as $item) {
        $resultado[$item['mes']] = (int)$item['total'];
    }

    return $resultado;
}

private function inicioPeriodo($meses)
{
    $meses = max(3, min(24, (int)$meses));

    return (new DateTimeImmutable('first day of this month'))
        ->modify('-' . ($meses - 1) . ' months')
        ->format('Y-m-d');
}

}
