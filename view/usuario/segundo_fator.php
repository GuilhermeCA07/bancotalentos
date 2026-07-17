<?php
require_once 'model/ConfiguracaoModel.php';
$configuracaoSistema = (new ConfiguracaoModel())->buscar();
$erroDoisFatores = $_SESSION['erro_2fa_login'] ?? null;
unset($_SESSION['erro_2fa_login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação em duas etapas</title>
    <link rel="stylesheet" href="public/css/style.css?v=<?= filemtime('public/css/style.css') ?>">
    <link rel="stylesheet" href="public/css/login.css?v=<?= filemtime('public/css/login.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --laranja: <?= htmlspecialchars($configuracaoSistema['corSecundaria']) ?>;
            --laranjaescuro: <?= htmlspecialchars($configuracaoSistema['corSecundariaEscura']) ?>;
            --azul: <?= htmlspecialchars($configuracaoSistema['corDestaque']) ?>;
            --tema-destaque: <?= htmlspecialchars($configuracaoSistema['corDestaque']) ?>;
            --tema-destaque-escura: <?= htmlspecialchars($configuracaoSistema['corDestaqueEscura']) ?>;
        }
    </style>
</head>
<body class="login-body marca-<?= htmlspecialchars($configuracaoSistema['identidade']) ?>">
    <main class="login-container">
        <section class="login-card acesso-seguranca-card">
            <div class="seguranca-icone" aria-hidden="true">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="login-header">
                <h1>Verificação em duas etapas</h1>
                <p>Informe o código de 6 dígitos exibido no Google Authenticator.</p>
            </div>

            <?php if ($erroDoisFatores): ?>
                <div class="alerta-erro" role="alert"><?= htmlspecialchars($erroDoisFatores) ?></div>
            <?php endif; ?>

            <form method="POST" action="?c=usuario&amp;m=validarSegundoFator">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($pendente['csrf_token']) ?>">
                <div class="form-group">
                    <label for="codigoDoisFatores">Código do autenticador</label>
                    <input
                        class="codigo-seguranca"
                        id="codigoDoisFatores"
                        type="text"
                        name="codigo"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autofocus
                        required>
                </div>
                <label class="confiar-navegador">
                    <input
                        type="checkbox"
                        name="confiar_navegador"
                        value="1">
                    <span>Confiar neste navegador por 30 dias</span>
                </label>
                <button type="submit" class="btn-login">Verificar</button>
            </form>
            <a class="sair-primeiro-acesso" href="?c=usuario&amp;m=login">Voltar ao login</a>
        </section>
    </main>
</body>
</html>
