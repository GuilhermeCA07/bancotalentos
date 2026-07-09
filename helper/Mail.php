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

<div style='
    margin:0;
    padding:40px 0;
    background:#f5f7fa;
    font-family:Arial,Helvetica,sans-serif;
'>

    <table
        width='100%'
        cellpadding='0'
        cellspacing='0'
        style='max-width:650px;margin:auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 6px 20px rgba(0,0,0,.08);'>

        <!-- Cabeçalho -->
        <tr>

            <td
                style='background:#0F4DB0;padding:30px;text-align:center;'>

                <div
    style='
        display:inline-block;
        padding:12px 28px;
        border-radius:10px;
        background:#ffffff;
        box-shadow:0 4px 15px rgba(0,0,0,.18);
    '>

    <span
        style='
            font-size:34px;
            font-weight:800;
            color:#FF6B00;
            font-family:Arial,Helvetica,sans-serif;
            letter-spacing:.5px;
        '>

        Netcom Telecomunicações

    </span>

</div>

            </td>

        </tr>

        <!-- Conteúdo -->
        <tr>

            <td style='padding:40px;'>

                <h2
                    style='margin:0 0 15px;color:#0F4DB0;text-align:center;'>

                    Confirmação de Segurança

                </h2>

                <p
                    style='font-size:16px;color:#444;line-height:1.6;text-align:center;'>

                    Olá!

                    <br><br>

                    Recebemos uma solicitação para acessar ou atualizar seus dados
                    no <strong>Banco de Talentos Netcom</strong>.

                </p>

                <p
                    style='font-size:16px;color:#444;text-align:center;'>

                    Utilize o código abaixo para continuar:

                </p>

                <div
                    style='
                        margin:35px auto;
                        width:fit-content;
                        padding:18px 40px;
                        background:#FFF4E8;
                        border:2px dashed #FF6B00;
                        border-radius:12px;
                        font-size:42px;
                        font-weight:bold;
                        color:#FF6B00;
                        letter-spacing:12px;
                    '>

                    {$token}

                </div>

                <p
                    style='text-align:center;color:#555;'>

                    Este código expira em
                    <strong>5 minutos</strong>.

                </p>

                <hr
                    style='margin:35px 0;border:none;border-top:1px solid #e5e7eb;'>

                <p
                    style='font-size:14px;color:#666;line-height:1.6;'>

                    Se você não realizou esta solicitação,
                    basta ignorar este e-mail.
                    Nenhuma alteração será realizada em seu cadastro.

                </p>

            </td>

        </tr>

        <!-- Rodapé -->
        <tr>

            <td
                style='
                    background:#0F4DB0;
                    color:#fff;
                    text-align:center;
                    padding:20px;
                    font-size:13px;
                '>

                Banco de Talentos Netcom<br>

                © " . date("Y") . " Netcom Telecom. Todos os direitos reservados.

            </td>

        </tr>

    </table>

</div>

";

        return self::enviar(

            $email,

            $assunto,

            $mensagem

        );
    }
}
