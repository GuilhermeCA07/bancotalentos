<?php

require_once 'config/Conexao.php';

class DepartamentoModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function buscarTodos($filtros = [], $limite = null, $offset = null)
    {
        $sql = 'SELECT * FROM departamentos WHERE 1 = 1';
        $tipos = '';
        $valores = [];

        if (!empty($filtros['busca'])) {
            $sql .= ' AND nome LIKE ?';
            $tipos .= 's';
            $valores[] = '%' . $filtros['busca'] . '%';
        }

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $sql .= ' AND ativo = ?';
            $tipos .= 'i';
            $valores[] = (int)$filtros['status'];
        }

        $sql .= ' ORDER BY nome';

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

    public function buscarAtivos($incluirId = null)
    {
        $sql = 'SELECT * FROM departamentos WHERE ativo = 1';
        $tipos = '';
        $valores = [];

        if ($incluirId) {
            $sql .= ' OR idDepartamento = ?';
            $tipos = 'i';
            $valores[] = (int)$incluirId;
        }

        $sql .= ' ORDER BY nome';
        $comando = $this->conexao->prepare($sql);
        if ($valores) {
            $comando->bind_param($tipos, ...$valores);
        }
        $comando->execute();

        return $comando->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id)
    {
        $comando = $this->conexao->prepare(
            'SELECT * FROM departamentos WHERE idDepartamento = ?'
        );
        $comando->bind_param('i', $id);
        $comando->execute();

        return $comando->get_result()->fetch_assoc();
    }

    public function inserir($nome, $cor)
    {
        $comando = $this->conexao->prepare(
            'INSERT INTO departamentos (nome, cor) VALUES (?, ?)'
        );
        $comando->bind_param('ss', $nome, $cor);

        return $comando->execute();
    }

    public function atualizar($id, $nome, $cor)
    {
        $comando = $this->conexao->prepare(
            'UPDATE departamentos SET nome = ?, cor = ? WHERE idDepartamento = ?'
        );
        $comando->bind_param('ssi', $nome, $cor, $id);

        return $comando->execute();
    }

    public function alterarStatus($id)
    {
        $comando = $this->conexao->prepare(
            'UPDATE departamentos SET ativo = NOT ativo WHERE idDepartamento = ?'
        );
        $comando->bind_param('i', $id);

        return $comando->execute();
    }

    public function contarRegistros($filtros = [])
    {
        $sql = 'SELECT COUNT(*) total FROM departamentos WHERE 1 = 1';
        $tipos = '';
        $valores = [];

        if (!empty($filtros['busca'])) {
            $sql .= ' AND nome LIKE ?';
            $tipos .= 's';
            $valores[] = '%' . $filtros['busca'] . '%';
        }

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $sql .= ' AND ativo = ?';
            $tipos .= 'i';
            $valores[] = (int)$filtros['status'];
        }

        $comando = $this->conexao->prepare($sql);
        if ($valores) {
            $comando->bind_param($tipos, ...$valores);
        }
        $comando->execute();

        return (int)$comando->get_result()->fetch_assoc()['total'];
    }
}
