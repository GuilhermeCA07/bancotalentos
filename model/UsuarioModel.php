<?php
require_once "config/Conexao.php";

class UsuarioModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function contarRegistros($filtros = [])
    {
        $sql = "
            SELECT COUNT(*) total
            FROM usuarios
            WHERE 1 = 1
        ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
                AND (
                    nome LIKE ?
                    OR email LIKE ?
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

        if (!empty($filtros['perfil'])) {

            $sql .= "
                AND perfil = ?
            ";

            $tipos .= "s";

            $valores[] =
                $filtros['perfil'];
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

    public function buscarTodos(
        $filtros = [],
        $limite = null,
        $offset = null
    ) {

        $sql = "
            SELECT *
            FROM usuarios
            WHERE 1 = 1
        ";

        $tipos = "";
        $valores = [];

        if (!empty($filtros['busca'])) {

            $sql .= "
                AND (
                    nome LIKE ?
                    OR email LIKE ?
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

        if (!empty($filtros['perfil'])) {

            $sql .= "
                AND perfil = ?
            ";

            $tipos .= "s";

            $valores[] =
                $filtros['perfil'];
        }

        $sql .= "
            ORDER BY nome
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

    public function buscarPorId($id)
    {
        $sql = "
        SELECT *
        FROM usuarios
        WHERE idUsuario = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        $comando->execute();

        return
            $comando
            ->get_result()
            ->fetch_assoc();
    }
    public function cadastrar($dados)
    {
        $sql = "
        INSERT INTO usuarios
        (
            nome,
            email,
            senha,
            perfil
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ";

        $senha =
            password_hash(
                $dados['senha'],
                PASSWORD_DEFAULT
            );

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "ssss",
            $dados['nome'],
            $dados['email'],
            $senha,
            $dados['perfil']
        );

        return $comando->execute();
    }
    public function editar($dados)
    {
        if (
            isset($dados['senha'])
            &&
            trim($dados['senha']) !== ''
        ) {

            $sql = "
            UPDATE usuarios
            SET

                nome = ?,
                email = ?,
                senha = ?,
                perfil = ?

            WHERE idUsuario = ?
        ";

            $senha =
                password_hash(
                    $dados['senha'],
                    PASSWORD_DEFAULT
                );

            $comando =
                $this->conexao
                ->prepare($sql);

            $comando->bind_param(
                "ssssi",
                $dados['nome'],
                $dados['email'],
                $senha,
                $dados['perfil'],
                $dados['idUsuario']
            );
        } else {

            $sql = "
            UPDATE usuarios
            SET

                nome = ?,
                email = ?,
                perfil = ?

            WHERE idUsuario = ?
        ";

            $comando =
                $this->conexao
                ->prepare($sql);

            $comando->bind_param(
                "sssi",
                $dados['nome'],
                $dados['email'],
                $dados['perfil'],
                $dados['idUsuario']
            );
        }

        return $comando->execute();
    }

    public function excluir($id)
    {
        $sql = "
        DELETE FROM usuarios
        WHERE idUsuario = ?
    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $id
        );

        return $comando->execute();
    }

    public function autenticar(
        $email,
        $senha
    ) {

        $sql = "

        SELECT *

        FROM usuarios

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

        $usuario =
            $comando
            ->get_result()
            ->fetch_assoc();


        if (
            $usuario
            &&
            password_verify(
                $senha,
                $usuario['senha']
            )
        ) {

            return $usuario;
        }

        return false;
    }

    public function buscarTodosSimples()
    {
        $sql = "
        SELECT
            idUsuario,
            nome
        FROM usuarios
        ORDER BY nome
    ";

        return $this->conexao
            ->query($sql)
            ->fetch_all(MYSQLI_ASSOC);
    }
}
