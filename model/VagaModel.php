<?php

require_once 'config/Conexao.php';

class VagaModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function inserir(
        $titulo,
        $departamentoId,
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
        $sql = '
            INSERT INTO vagas
            (
                titulo, departamento_id, cnh_obrigatoria,
                quantidade_vagas, cidade, modalidade, escala,
                tipo_contratacao, salario, descricao, requisitos,
                observacoes, status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ';
        $comando = $this->conexao->prepare($sql);
        $comando->bind_param(
            'siiissssdssss',
            $titulo,
            $departamentoId,
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

    public function atualizar(
        $id,
        $titulo,
        $departamentoId,
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
        $sql = '
            UPDATE vagas
            SET titulo = ?, departamento_id = ?, cnh_obrigatoria = ?,
                quantidade_vagas = ?, cidade = ?, escala = ?,
                modalidade = ?, tipo_contratacao = ?, salario = ?,
                descricao = ?, requisitos = ?, observacoes = ?, status = ?
            WHERE idVaga = ?
        ';
        $comando = $this->conexao->prepare($sql);
        $comando->bind_param(
            'siiissssdssssi',
            $titulo,
            $departamentoId,
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

    public function buscarTodos($filtros = [], $limite = null, $offset = null)
    {
        $sql = $this->selectComDepartamento() . ' WHERE 1 = 1';
        [$condicoes, $tipos, $valores] = $this->montarFiltros($filtros);
        $sql .= $condicoes . ' ORDER BY v.data_criacao DESC';

        if ($limite !== null && $offset !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $tipos .= 'ii';
            $valores[] = (int)$limite;
            $valores[] = (int)$offset;
        }

        $comando = $this->conexao->prepare($sql);
        if ($valores) {
            $comando->bind_param($tipos, ...$valores);
        }
        $comando->execute();

        return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorStatus($status)
    {
        $sql = $this->selectComDepartamento()
            . ' WHERE v.status = ? ORDER BY v.titulo';
        $comando = $this->conexao->prepare($sql);
        $comando->bind_param('s', $status);
        $comando->execute();

        return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = $this->selectComDepartamento() . ' WHERE v.idVaga = ?';
        $comando = $this->conexao->prepare($sql);
        $comando->bind_param('i', $id);
        $comando->execute();

        return $comando->get_result()->fetch_assoc();
    }

    public function alterarStatus($id, $status)
    {
        $comando = $this->conexao->prepare(
            'UPDATE vagas SET status = ? WHERE idVaga = ?'
        );
        $comando->bind_param('si', $status, $id);

        return $comando->execute();
    }

    public function excluir($id)
    {
        $comando = $this->conexao->prepare(
            'DELETE FROM vagas WHERE idVaga = ?'
        );
        $comando->bind_param('i', $id);

        return $comando->execute();
    }

    public function buscarAbertas()
    {
        $sql = $this->selectComDepartamento()
            . " WHERE v.status = 'Aberta' ORDER BY v.titulo";

        return $this->conexao
            ->query($sql)
            ->fetch_all(MYSQLI_ASSOC);
    }

    public function contarRegistros($filtros = [])
    {
        $sql = '
            SELECT COUNT(*) total
            FROM vagas v
            INNER JOIN departamentos d
                ON d.idDepartamento = v.departamento_id
            WHERE 1 = 1
        ';
        [$condicoes, $tipos, $valores] = $this->montarFiltros($filtros);
        $comando = $this->conexao->prepare($sql . $condicoes);

        if ($valores) {
            $comando->bind_param($tipos, ...$valores);
        }
        $comando->execute();

        return (int)$comando->get_result()->fetch_assoc()['total'];
    }

    public function buscarVagasAtivas()
    {
        return $this->buscarPorStatus('Aberta');
    }

    public function fecharVaga($idVaga)
    {
        $comando = $this->conexao->prepare(
            "UPDATE vagas SET status = 'Fechada' WHERE idVaga = ?"
        );
        $comando->bind_param('i', $idVaga);

        return $comando->execute();
    }

    private function selectComDepartamento()
    {
        return '
            SELECT
                v.*,
                d.nome AS departamento,
                d.cor AS departamento_cor
            FROM vagas v
            INNER JOIN departamentos d
                ON d.idDepartamento = v.departamento_id
        ';
    }

    private function montarFiltros($filtros)
    {
        $sql = '';
        $tipos = '';
        $valores = [];

        if (!empty($filtros['busca'])) {
            $sql .= '
                AND (
                    v.titulo LIKE ? OR d.nome LIKE ? OR v.status LIKE ?
                    OR v.cidade LIKE ? OR v.modalidade LIKE ?
                    OR v.tipo_contratacao LIKE ? OR v.escala LIKE ?
                    OR v.descricao LIKE ?
                )
            ';
            $busca = '%' . $filtros['busca'] . '%';
            $tipos .= 'ssssssss';
            for ($i = 0; $i < 8; $i++) {
                $valores[] = $busca;
            }
        }

        if (!empty($filtros['departamento_id'])) {
            $sql .= ' AND d.idDepartamento = ?';
            $tipos .= 'i';
            $valores[] = (int)$filtros['departamento_id'];
        }

        foreach (['status', 'modalidade', 'escala'] as $campo) {
            if (!empty($filtros[$campo])) {
                $sql .= " AND v.$campo = ?";
                $tipos .= 's';
                $valores[] = $filtros[$campo];
            }
        }

        if (!empty($filtros['cidade'])) {
            $sql .= ' AND v.cidade LIKE ?';
            $tipos .= 's';
            $valores[] = '%' . $filtros['cidade'] . '%';
        }

        return [$sql, $tipos, $valores];
    }
}
