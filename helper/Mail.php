<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/EmailConfiguracaoModel.php';
require_once __DIR__ . '/../model/ConfiguracaoModel.php';

class Mail
{
    private static function configurar()
    {
        $configuracao = (new EmailConfiguracaoModel())->buscarParaEnvio();
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $configuracao['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $configuracao['smtp_usuario'];
        $mail->Password = $configuracao['smtp_senha'];
        $mail->Port = (int)$configuracao['smtp_port'];
        $mail->Timeout = 20;

        if ($configuracao['smtp_criptografia'] === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($configuracao['smtp_criptografia'] === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(
            $configuracao['email_remetente'],
            $configuracao['nome_remetente']
        );
        $mail->isHTML(true);

        return $mail;
    }

    public static function enviar(
        $destinatario,
        $assunto,
        $mensagem,
        $mensagemTexto = ''
    ) {
        $mail = null;

        try {
            $mail = self::configurar();
            $mail->addAddress($destinatario);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;
            $mail->AltBody = $mensagemTexto !== ''
                ? $mensagemTexto
                : trim(html_entity_decode(strip_tags($mensagem), ENT_QUOTES, 'UTF-8'));
            $mail->send();

            return true;
        } catch (Throwable $e) {
            $erro = $mail && $mail->ErrorInfo !== ''
                ? $mail->ErrorInfo
                : $e->getMessage();
            error_log('Falha no envio de e-mail: ' . $erro);

            return [
                'sucesso' => false,
                'erro' => $erro
            ];
        }
    }

    public static function enviarToken($email, $token)
    {
        $tema = (new ConfiguracaoModel())->buscar();
        $marca = htmlspecialchars($tema['nomeMarca'], ENT_QUOTES, 'UTF-8');
        $corPrincipal = self::corSegura($tema['corPrimaria'], '#0F4DB0');
        $corDestaque = self::corSegura($tema['corDestaque'], '#0F4DB0');
        $corSecundaria = self::corSegura($tema['corSecundaria'], '#FF6B00');
        $corTexto = self::corSegura($tema['corTextoPrimaria'], '#FFFFFF');
        $siteOficial = self::siteOficial($tema['identidade'] ?? 'padrao');
        $blocoSiteOficial = self::blocoSiteOficial(
            $siteOficial,
            $corDestaque
        );
        $codigo = htmlspecialchars((string)$token, ENT_QUOTES, 'UTF-8');
        $ano = date('Y');
        $assunto = "Código de acesso - Banco de Talentos {$tema['nomeMarca']}";

        $mensagem = <<<HTML
<!doctype html>
<html lang="pt-BR">
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#334155;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td align="center" style="background:{$corPrincipal};padding:24px;">
                            <strong style="color:{$corTexto};display:block;font-size:24px;line-height:1.25;">{$marca}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="color:{$corDestaque};font-size:12px;font-weight:700;margin:0 0 8px;text-align:center;text-transform:uppercase;">Confirmação de segurança</p>
                            <h1 style="color:#0f172a;font-size:24px;margin:0 0 18px;text-align:center;">Seu código de acesso</h1>
                            <p style="font-size:15px;line-height:1.6;margin:0;text-align:center;">Recebemos uma solicitação para acessar ou atualizar seus dados no <strong>Banco de Talentos {$marca}</strong>.</p>
                            <div style="background:#f8fafc;border:2px dashed {$corSecundaria};border-radius:8px;color:{$corDestaque};font-size:38px;font-weight:800;letter-spacing:8px;margin:30px auto 18px;max-width:320px;padding:18px 12px;text-align:center;">{$codigo}</div>
                            <p style="color:#475569;font-size:14px;margin:0;text-align:center;">Este código expira em <strong>5 minutos</strong>.</p>
                            <div style="border-top:1px solid #e2e8f0;margin:30px 0 20px;"></div>
                            <p style="color:#64748b;font-size:13px;line-height:1.55;margin:0;">Se você não realizou esta solicitação, ignore este e-mail. Nenhuma alteração será feita em seu cadastro.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background:{$corPrincipal};color:{$corTexto};font-size:12px;line-height:1.5;padding:18px;">
                            Banco de Talentos {$marca}<br>
                            &copy; {$ano} {$marca}. Todos os direitos reservados.
                        </td>
                    </tr>
                    {$blocoSiteOficial}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        $texto = "Seu código de acesso ao Banco de Talentos {$tema['nomeMarca']}"
            . ": {$token}. Este código expira em 5 minutos.";
        if ($siteOficial) {
            $texto .= " Conheça nossos planos: {$siteOficial['url']}.";
        }

        return self::enviar($email, $assunto, $mensagem, $texto);
    }

    public static function enviarTeste($email)
    {
        $tema = (new ConfiguracaoModel())->buscar();
        $marca = htmlspecialchars($tema['nomeMarca'], ENT_QUOTES, 'UTF-8');
        $corPrincipal = self::corSegura($tema['corPrimaria'], '#0F4DB0');
        $corDestaque = self::corSegura($tema['corDestaque'], '#0F4DB0');
        $corTexto = self::corSegura($tema['corTextoPrimaria'], '#FFFFFF');
        $siteOficial = self::siteOficial($tema['identidade'] ?? 'padrao');
        $blocoSiteOficial = self::blocoSiteOficial(
            $siteOficial,
            $corDestaque
        );

        $mensagem = <<<HTML
<!doctype html>
<html lang="pt-BR">
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#334155;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                <tr><td align="center" style="background:{$corPrincipal};padding:22px;"><strong style="color:{$corTexto};display:block;font-size:22px;line-height:1.25;">{$marca}</strong></td></tr>
                <tr><td style="padding:34px 38px;text-align:center;">
                    <div style="color:{$corDestaque};font-size:38px;line-height:1;">&#10003;</div>
                    <h1 style="color:#0f172a;font-size:22px;margin:14px 0 10px;">Configuração aprovada</h1>
                    <p style="font-size:15px;line-height:1.6;margin:0;">O Banco de Talentos {$marca} conseguiu enviar este e-mail usando a configuração SMTP cadastrada.</p>
                </td></tr>
                <tr><td style="background:#f8fafc;color:#64748b;font-size:12px;padding:15px;text-align:center;">Mensagem automática de teste</td></tr>
                {$blocoSiteOficial}
            </table>
        </td></tr>
    </table>
</body>
</html>
HTML;

        return self::enviar(
            $email,
            "Teste de e-mail - Banco de Talentos {$tema['nomeMarca']}",
            $mensagem,
            'A configuração SMTP do Banco de Talentos foi testada com sucesso.'
                . ($siteOficial ? " Conheça nossos planos: {$siteOficial['url']}." : '')
        );
    }

    private static function siteOficial($identidade)
    {
        $sites = [
            'netcom' => [
                'nome' => 'netcom.tv.br',
                'url' => 'https://netcom.tv.br/'
            ],
            'sumernet' => [
                'nome' => 'sumer.net.br',
                'url' => 'https://sumer.net.br/'
            ],
            'netaki' => [
                'nome' => 'netaki.com.br',
                'url' => 'https://netaki.com.br/'
            ]
        ];

        return $sites[strtolower(trim((string)$identidade))] ?? null;
    }

    private static function blocoSiteOficial($site, $corDestaque)
    {
        if (!$site) {
            return '';
        }

        $nome = htmlspecialchars($site['nome'], ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($site['url'], ENT_QUOTES, 'UTF-8');

        return <<<HTML
<tr>
    <td align="center" style="background:#eef2f7;border-top:1px solid #dbe3ec;padding:22px 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:440px;background:#ffffff;border:1px solid #dbe3ec;border-left:5px solid {$corDestaque};border-radius:6px;">
            <tr>
                <td align="center" style="padding:20px 18px;">
                    <strong style="color:#0f172a;display:block;font-size:18px;line-height:1.3;margin-bottom:12px;">Conheça nossos planos</strong>
                    <a href="{$url}" target="_blank" rel="noopener noreferrer" style="background:{$corDestaque};border-radius:6px;color:#ffffff;display:inline-block;font-size:14px;font-weight:700;line-height:1.2;padding:11px 20px;text-decoration:none;">{$nome}</a>
                </td>
            </tr>
        </table>
    </td>
</tr>
HTML;
    }

    private static function corSegura($cor, $padrao)
    {
        return preg_match('/^#[0-9A-F]{6}$/i', (string)$cor)
            ? strtoupper($cor)
            : $padrao;
    }
}
