<?php
require_once "config/Conexao.php";

class CandidatoModel
{
    private $conexao;

    function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function inserir(
        $nome,
        $telefone,
        $whatsapp,
        $email,
        $curriculo,
        $observacoes,
        $escolaridade = null,
        $estadoCivil = null,
        $fumante = 0,
        $cnh = null
    ) {

        $sql = "

        INSERT INTO candidatos
        (

            nome,
            telefone,
            whatsapp,
            email,
            escolaridade,
            estado_civil,
            fumante,
            cnh,
            curriculo,
            observacoes

        )

        VALUES
        (

            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?

        )

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        if (!$comando) {

            throw new Exception(
                $this->conexao->error
            );
        }

        $comando->bind_param(

            "ssisssisss",

            $nome,

            $telefone,

            $whatsapp,

            $email,

            $escolaridade,

            $estadoCivil,

            $fumante,

            $cnh,

            $curriculo,

            $observacoes

        );

        if (!$comando->execute()) {

            throw new Exception(
                $comando->error
            );
        }

        return $this->conexao->insert_id;
    }

    public function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {

        $sql = "
        SELECT DISTINCT c.*
        FROM candidatos c
    ";

        $where = "
        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        $joinHabilidade = false;

        /*
        |--------------------------------------------------------------------------
        | Busca
        |--------------------------------------------------------------------------
        */

        if (!empty($filtros['busca'])) {

            $where .= "
            AND (
                c.nome LIKE ?
                OR c.telefone LIKE ?
                OR c.email LIKE ?
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
    |--------------------------------------------------------------------------
    | Categoria
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['categoria'])) {

            if (!$joinHabilidade) {

                $sql .= "

                INNER JOIN candidato_habilidade ch
                    ON ch.candidato_id = c.idCandidato

                INNER JOIN habilidades h
                    ON h.idHabilidade = ch.habilidade_id

            ";

                $joinHabilidade = true;
            }

            $placeholders =
                implode(
                    ',',
                    array_fill(
                        0,
                        count($filtros['categoria']),
                        '?'
                    )
                );

            $where .= "
            AND h.categoria_id IN ($placeholders)
        ";

            $tipos .= str_repeat(
                'i',
                count($filtros['categoria'])
            );

            foreach (
                $filtros['categoria']
                as $categoria
            ) {

                $valores[] =
                    (int) $categoria;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Habilidades
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['habilidade'])) {

            if (!$joinHabilidade) {

                $sql .= "

                INNER JOIN candidato_habilidade ch
                    ON ch.candidato_id = c.idCandidato

                INNER JOIN habilidades h
                    ON h.idHabilidade = ch.habilidade_id

            ";

                $joinHabilidade = true;
            }

            $placeholders =
                implode(
                    ',',
                    array_fill(
                        0,
                        count($filtros['habilidade']),
                        '?'
                    )
                );

            $where .= "
            AND h.idHabilidade IN ($placeholders)
        ";

            $tipos .= str_repeat(
                'i',
                count($filtros['habilidade'])
            );

            foreach (
                $filtros['habilidade']
                as $habilidade
            ) {

                $valores[] =
                    (int) $habilidade;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Status do candidato
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['status_candidato'])) {

            $where .= "
            AND c.status_candidato = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['status_candidato'];
        }

        if (!empty($filtros['escolaridade'])) {

            $where .= "
            AND c.escolaridade = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['escolaridade'];
        }

        if (!empty($filtros['estado_civil'])) {

            $where .= "
            AND c.estado_civil = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['estado_civil'];
        }

        if (
            isset($filtros['fumante'])
            &&
            $filtros['fumante'] !== ''
        ) {

            $where .= "
            AND c.fumante = ?
        ";

            $tipos .= "i";

            $valores[] =
                (int)$filtros['fumante'];
        }

        if (!empty($filtros['cnh'])) {

            $where .= "
            AND c.cnh = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['cnh'];
        }

        /*
|--------------------------------------------------------------------------
| Última atualização
|--------------------------------------------------------------------------
*/

        if (!empty($filtros['data_inicial'])) {

            $where .= "
        AND DATE(c.ultima_atualizacao) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicial'];
        }

        if (!empty($filtros['data_final'])) {

            $where .= "
        AND DATE(c.ultima_atualizacao) <= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_final'];
        }

        $sql .= $where;

        $sql .= "
        ORDER BY

        c.ultima_atualizacao DESC,

        c.nome ASC

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
        $sql = "SELECT * FROM candidatos WHERE idCandidato = ? LIMIT 1";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param("i", $id);

        if ($comando->execute()) {
            $resultado = $comando->get_result();
            return $resultado->fetch_assoc();
        }

        return null;
    }

    function atualizar(
        $id,
        $nome,
        $telefone,
        $whatsapp,
        $email,
        $curriculo,
        $observacoes,
        $escolaridade = null,
        $estadoCivil = null,
        $fumante = 0,
        $cnh = null
    ) {
        $sql = "UPDATE candidatos
            SET
                nome = ?,
                telefone = ?,
                whatsapp = ?,
                email = ?,
                escolaridade = ?,
                estado_civil = ?,
                fumante = ?,
                cnh = ?,
                curriculo = ?,
                observacoes = ?
            WHERE idCandidato = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "ssisssisssi",
            $nome,
            $telefone,
            $whatsapp,
            $email,
            $escolaridade,
            $estadoCivil,
            $fumante,
            $cnh,
            $curriculo,
            $observacoes,
            $id
        );

        return $comando->execute();
    }

    function excluir($id)
    {
        $sql = "DELETE FROM candidatos
                WHERE idCandidato = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param("i", $id);

        return $comando->execute();
    }

    function buscarHabilidades($idCandidato)
    {
        $sql = "
            SELECT
                ch.habilidade_id,
                ch.nivel,
                ch.nome_exibicao,
                h.nome
            FROM candidato_habilidade ch
            INNER JOIN habilidades h
                ON h.idHabilidade = ch.habilidade_id
            WHERE ch.candidato_id = ?
            ORDER BY h.nome
        ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param("i", $idCandidato);

        if ($comando->execute()) {
            $resultado = $comando->get_result();

            $dados = [];

            while ($linha = $resultado->fetch_assoc()) {

                $nomeExibicao =
                    !empty($linha['nome_exibicao'])
                    ? $linha['nome_exibicao']
                    : $linha['nome'];

                $dados[] = [
                    'id' => $linha['habilidade_id'],
                    'nivel' => (int)$linha['nivel'],
                    'nivelHabilidade' => (int)$linha['nivel'],
                    'nome' => $nomeExibicao,
                    'nomeOriginal' => $linha['nome'],
                    'nomeExibicao' => $nomeExibicao,
                    'descricao' =>
                    $linha['nome'] === 'Outra Habilidade'
                        ? $nomeExibicao
                        : '',
                    'salva' => true
                ];
            }

            return $dados;
        }

        return [];
    }

    function limparHabilidades($idCandidato)
    {
        $sql = "DELETE
            FROM candidato_habilidade
            WHERE candidato_id = ?";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param("i", $idCandidato);

        return $comando->execute();
    }

    function salvarHabilidade(
        $idCandidato,
        $idHabilidade,
        $nomeExibicao,
        $nivel
    ) {
        $sql = "INSERT INTO candidato_habilidade
    (
        candidato_id,
        habilidade_id,
        nome_exibicao,
        nivel
    )
    VALUES (?, ?, ?, ?)";

        $comando = $this->conexao->prepare($sql);

        if (!$comando) {
            die($this->conexao->error);
        }

        $comando->bind_param(
            "iisi",
            $idCandidato,
            $idHabilidade,
            $nomeExibicao,
            $nivel
        );

        if (!$comando->execute()) {
            die($comando->error);
        }

        return true;
    }

    function buscarUltimoId()
    {
        return $this->conexao->insert_id;
    }

    public function contarRegistros(
        $filtros = []
    ) {

        $sql = "
        SELECT COUNT(
            DISTINCT c.idCandidato
        ) AS total
        FROM candidatos c
    ";

        $where = "
        WHERE 1 = 1
    ";

        $tipos = "";

        $valores = [];

        $joinHabilidade = false;

        /*
    |--------------------------------------------------------------------------
    | Busca
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['busca'])) {

            $where .= "
            AND (
                c.nome LIKE ?
                OR c.telefone LIKE ?
                OR c.email LIKE ?
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
    |--------------------------------------------------------------------------
    | Categoria
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['categoria'])) {

            if (!$joinHabilidade) {

                $sql .= "

                INNER JOIN candidato_habilidade ch
                    ON ch.candidato_id = c.idCandidato

                INNER JOIN habilidades h
                    ON h.idHabilidade = ch.habilidade_id

            ";

                $joinHabilidade = true;
            }

            $placeholders =
                implode(
                    ',',
                    array_fill(
                        0,
                        count($filtros['categoria']),
                        '?'
                    )
                );

            $where .= "
            AND h.categoria_id IN ($placeholders)
        ";

            $tipos .= str_repeat(
                'i',
                count($filtros['categoria'])
            );

            foreach (
                $filtros['categoria']
                as $categoria
            ) {

                $valores[] =
                    (int) $categoria;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Habilidade
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['habilidade'])) {

            if (!$joinHabilidade) {

                $sql .= "

                INNER JOIN candidato_habilidade ch
                    ON ch.candidato_id = c.idCandidato

                INNER JOIN habilidades h
                    ON h.idHabilidade = ch.habilidade_id

            ";

                $joinHabilidade = true;
            }

            $placeholders =
                implode(
                    ',',
                    array_fill(
                        0,
                        count($filtros['habilidade']),
                        '?'
                    )
                );

            $where .= "
            AND h.idHabilidade IN ($placeholders)
        ";

            $tipos .= str_repeat(
                'i',
                count($filtros['habilidade'])
            );

            foreach (
                $filtros['habilidade']
                as $habilidade
            ) {

                $valores[] =
                    (int) $habilidade;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Status do candidato
    |--------------------------------------------------------------------------
    */

        if (!empty($filtros['status_candidato'])) {

            $where .= "
            AND c.status_candidato = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['status_candidato'];
        }

        if (!empty($filtros['escolaridade'])) {

            $where .= "
            AND c.escolaridade = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['escolaridade'];
        }

        if (!empty($filtros['estado_civil'])) {

            $where .= "
            AND c.estado_civil = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['estado_civil'];
        }

        if (
            isset($filtros['fumante'])
            &&
            $filtros['fumante'] !== ''
        ) {

            $where .= "
            AND c.fumante = ?
        ";

            $tipos .= "i";

            $valores[] =
                (int)$filtros['fumante'];
        }

        if (!empty($filtros['cnh'])) {

            $where .= "
            AND c.cnh = ?
        ";

            $tipos .= "s";

            $valores[] =
                $filtros['cnh'];
        }

        if (!empty($filtros['data_inicial'])) {

            $where .= "
        AND DATE(c.ultima_atualizacao) >= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_inicial'];
        }

        if (!empty($filtros['data_final'])) {

            $where .= "
        AND DATE(c.ultima_atualizacao) <= ?
    ";

            $tipos .= "s";

            $valores[] =
                $filtros['data_final'];
        }

        $sql .= $where;

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

    public function buscarPorEmail(
        $email
    ) {
        $sql = "

        SELECT *

        FROM candidatos

        WHERE email = ?

        LIMIT 1
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "s",
            $email
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_assoc();
    }


    public function buscarPorCandidatoEVaga(
        $idCandidato,
        $idVaga
    ) {
        $sql = "

        SELECT *

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

    public function possuiHabilidade(
        $idCandidato,
        $idHabilidade,
        $nomeHabilidade,
        $nomeExibicao
    ) {

        if ($nomeHabilidade === "Outra Habilidade") {

            $sql = "

            SELECT 1

            FROM candidato_habilidade

            WHERE candidato_id = ?

            AND habilidade_id = ?

            AND nome_exibicao = ?

            LIMIT 1

        ";

            $comando =
                $this->conexao->prepare($sql);

            $comando->bind_param(

                "iis",

                $idCandidato,

                $idHabilidade,

                $nomeExibicao

            );
        } else {

            $sql = "

            SELECT 1

            FROM candidato_habilidade

            WHERE candidato_id = ?

            AND habilidade_id = ?

            LIMIT 1

        ";

            $comando =
                $this->conexao->prepare($sql);

            $comando->bind_param(

                "ii",

                $idCandidato,

                $idHabilidade

            );
        }

        $comando->execute();

        return $comando
            ->get_result()
            ->num_rows > 0;
    }

    public function atualizarDadosModal(
        $idCandidato,
        $telefone,
        $whatsapp,
        $email,
        $curriculo,
        $obs,
        $nome,
        $escolaridade = null,
        $estadoCivil = null,
        $fumante = 0,
        $cnh = null
    ) {
        $sql = "
        UPDATE candidatos
        SET

        nome = ?,

        telefone = ?,

        whatsapp = ?,

        email = ?,

        escolaridade = ?,

        estado_civil = ?,

        fumante = ?,

        cnh = ?,

        curriculo = ?,

        observacoes = ?

        WHERE idCandidato = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "ssisssisssi",
            $nome,
            $telefone,
            $whatsapp,
            $email,
            $escolaridade,
            $estadoCivil,
            $fumante,
            $cnh,
            $curriculo,
            $obs,
            $idCandidato
        );

        return $comando->execute();
    }

    public function atualizarStatus(
        $idCandidato,
        $status
    ) {
        $sql = "

        UPDATE candidatos

        SET status_candidato = ?

        WHERE idCandidato = ?

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "si",
            $status,
            $idCandidato
        );

        return $comando->execute();
    }

    public function buscarHabilidadesEditar($idCandidato)
    {
        $sql = "
        SELECT
            ch.id,
            ch.nivel,
            ch.nome_exibicao,

            h.idHabilidade,
            h.nome,

            c.idCategoria,
            c.nome AS categoria

        FROM candidato_habilidade ch

        INNER JOIN habilidades h
            ON h.idHabilidade = ch.habilidade_id

        INNER JOIN categorias_habilidade c
            ON c.idCategoria = h.categoria_id

        WHERE ch.candidato_id = ?

        ORDER BY c.nome, h.nome
    ";

        $comando = $this->conexao->prepare($sql);

        if (!$comando) {
            die($this->conexao->error . "<br><br>SQL:<br>" . $sql);
        }

        $comando->bind_param(
            "i",
            $idCandidato
        );

        $comando->execute();

        return $comando
            ->get_result()
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function atualizarHabilidade(
        $idCandidato,
        $idHabilidade,
        $nomeExibicao,
        $nivel
    ) {

        $sql = "

        UPDATE candidato_habilidade

        SET

            nome_exibicao = ?,

            nivel = ?

        WHERE

            candidato_id = ?

        AND

            habilidade_id = ?

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        if (!$comando) {

            throw new Exception(
                "Erro Prepare: " .
                    $this->conexao->error
            );
        }

        $comando->bind_param(

            "siii",

            $nomeExibicao,

            $nivel,

            $idCandidato,

            $idHabilidade

        );

        if (!$comando->execute()) {

            throw new Exception(
                "Erro Execute: " .
                    $comando->error
            );
        }

        return true;
    }

    public function excluirHabilidade(
        $idCandidato,
        $idHabilidade,
        $nomeHabilidade,
        $nomeExibicao
    ) {

        if ($nomeHabilidade === "Outra Habilidade") {

            $sql = "

            DELETE

            FROM candidato_habilidade

            WHERE candidato_id = ?

            AND habilidade_id = ?

            AND nome_exibicao = ?

        ";

            $comando =
                $this->conexao
                ->prepare($sql);

            $comando->bind_param(

                "iis",

                $idCandidato,

                $idHabilidade,

                $nomeExibicao

            );
        } else {

            $sql = "

            DELETE

            FROM candidato_habilidade

            WHERE candidato_id = ?

            AND habilidade_id = ?

        ";

            $comando =
                $this->conexao
                ->prepare($sql);

            $comando->bind_param(

                "ii",

                $idCandidato,

                $idHabilidade

            );
        }

        $comando->execute();

        return $comando->affected_rows > 0;
    }

    public function atualizarUltimaAtualizacao($idCandidato)
    {
        $sql = "

        UPDATE candidatos

        SET ultima_atualizacao = NOW()

        WHERE idCandidato = ?

    ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(
            "i",
            $idCandidato
        );

        return $comando->execute();
    }
}
