<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";

class Mail
{
    private static function configurar()
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();

        /*
         * CONFIGURAÇÕES SMTP
         */

        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "talentos.netcom.noreply@gmail.com";
        $mail->Password = "mdfwiurjngrybdwa";
        $mail->Port = 587;

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        /*
         * CONFIGURAÇÕES GERAIS
         */

        $mail->CharSet = "UTF-8";

        $mail->setFrom(

            "talentos.netcom.noreply@gmail.com",

            "Banco de Talentos"

        );

        $mail->isHTML(true);

        return $mail;
    }

    public static function enviar(
        $destinatario,
        $assunto,
        $mensagem
    ) {

        try {

            $mail = self::configurar();

            $mail->addAddress(
                $destinatario
            );

            $mail->Subject = $assunto;

            $mail->Body = $mensagem;

            $mail->send();

            return true;
        } catch (Exception $e) {

            return [

                'sucesso' => false,

                'erro' => $mail->ErrorInfo

            ];
        }
    }

    public static function enviarToken(
        $email,
        $token
    ) {

        $assunto =
            "Código de acesso ao Banco de Talentos";

        $mensagem = "

        <div
            style='
                font-family:Arial,sans-serif;
                max-width:600px;
                margin:auto;
            '>

            <h2
                style='
                    color:#0F4DB0;
                '>

                Banco de Talentos

            </h2>

            <p>

                Recebemos uma solicitação para acessar
                seus dados cadastrados.

            </p>

            <p>

                Utilize o código abaixo para continuar.

            </p>

            <div
                style='
                    text-align:center;
                    margin:30px 0;
                '>

                <span
                    style='
                        font-size:42px;
                        font-weight:bold;
                        letter-spacing:10px;
                        color:#0F4DB0;
                    '>

                    {$token}

                </span>

            </div>

            <p>

                Este código expira em
                <strong>15 minutos</strong>.

            </p>

            <hr>

            <small>

                Caso você não tenha solicitado este código,
                ignore este e-mail.

            </small>

        </div>

        ";

        return self::enviar(

            $email,

            $assunto,

            $mensagem

        );
    }
}
