<?php

require_once "config/Conexao.php";

class CandidatoTokenModel
{
    private $conexao;

    public function __construct()
    {
        $this->conexao =
            Conexao::getConnection();
    }

    public function gerarToken(
        $idCandidato,
        $email
    ) {

        /*
     * Remove tokens expirados
     */

        $sql = "
        DELETE
        FROM candidato_token
        WHERE expira_em < NOW()
    ";

        $this->conexao
            ->query($sql);

        /*
     * Verifica cooldown
     */

        $sql = "

        SELECT data_criacao

        FROM candidato_token

        WHERE email = ?

        ORDER BY data_criacao DESC

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

        $ultimo =
            $comando
            ->get_result()
            ->fetch_assoc();

        if ($ultimo) {

            $segundos =

                time()

                -

                strtotime(
                    $ultimo['data_criacao']
                );

            if ($segundos < 60) {

                return [

                    'sucesso' => false,

                    'mensagem' =>
                    'Aguarde 60 segundos para solicitar um novo código.'

                ];
            }
        }

        /*
     * Invalida tokens anteriores
     */

        $sql = "

        UPDATE candidato_token

        SET utilizado = 1

        WHERE candidato_id = ?

        AND utilizado = 0

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(
            "i",
            $idCandidato
        );

        $comando->execute();

        /*
     * Gera novo token
     */

        $token = str_pad(

            random_int(
                0,
                999999
            ),

            6,

            "0",

            STR_PAD_LEFT

        );

        $expira = date(

            "Y-m-d H:i:s",

            strtotime("+15 minutes")

        );

        /*
     * Salva token
     */

        $sql = "

        INSERT INTO candidato_token
        (

            candidato_id,

            email,

            token,

            expira_em

        )

        VALUES
        (

            ?, ?, ?, ?

        )

    ";

        $comando =
            $this->conexao
            ->prepare($sql);

        $comando->bind_param(

            "isss",

            $idCandidato,

            $email,

            $token,

            $expira

        );

        $comando->execute();

        return [

            'sucesso' => true,

            'token' => $token,

            'expira_em' => $expira

        ];
    }

    public function validarToken($email, $token)
    {
        $sql = "

        SELECT *

        FROM candidato_token

        WHERE email = ?

        AND token = ?

        AND utilizado = 0

        AND expira_em >= NOW()

        LIMIT 1

    ";

        $comando = $this->conexao->prepare($sql);

        $comando->bind_param(

            "ss",

            $email,

            $token

        );

        $comando->execute();

        $resultado = $comando
            ->get_result()
            ->fetch_assoc();

        if (!$resultado) {

            return false;
        }

        $sql = "

        UPDATE candidato_token

        SET utilizado = 1

        WHERE idToken = ?

        ";

        $update = $this->conexao->prepare($sql);

        

        $update->bind_param(

            "i",

            $resultado['id']

        );

        $update->execute();

        return true;
    }

    public function podeGerarNovoToken($email)
    {
        $sql = "

        SELECT data_criacao

        FROM candidato_token

        WHERE email = ?

        ORDER BY data_criacao DESC

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

        $resultado =
            $comando
            ->get_result()
            ->fetch_assoc();

        if (!$resultado) {
            return true;
        }

        $ultima =
            strtotime(
                $resultado['data_criacao']
            );

        return (
            time() - $ultima
        ) >= 60;
    }
}
