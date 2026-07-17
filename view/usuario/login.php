<?php
require_once 'model/ConfiguracaoModel.php';
$configuracaoSistema =
    (new ConfiguracaoModel())->buscar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Banco de Talentos <?= htmlspecialchars($configuracaoSistema['nomeMarca']) ?> - Login</title>

    <link
        rel="stylesheet"
        href="public/css/style.css?v=<?= filemtime('public/css/style.css') ?>">
    <link
        rel="stylesheet"
        href="public/css/login.css?v=<?= filemtime('public/css/login.css') ?>">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --laranja: <?= htmlspecialchars($configuracaoSistema['corSecundaria']) ?>;
            --laranjaescuro: <?= htmlspecialchars($configuracaoSistema['corSecundariaEscura']) ?>;
            --azul: <?= htmlspecialchars($configuracaoSistema['corDestaque']) ?>;
            --tema-primaria: <?= htmlspecialchars($configuracaoSistema['corPrimaria']) ?>;
            --tema-destaque-escura: <?= htmlspecialchars($configuracaoSistema['corDestaqueEscura']) ?>;
        }
    </style>
    <link
        rel="icon"
        type="<?= htmlspecialchars($configuracaoSistema['tipoIconeMarca']) ?>"
        href="<?= htmlspecialchars($configuracaoSistema['iconeMarca']) ?>">

</head>

<body class="login-body marca-<?= htmlspecialchars($configuracaoSistema['identidade']) ?>">

    <div class="login-container">

        <div class="login-card">

            <div class="login-header">
                <div class="login-marca-fundo">
                    <img
                        src="<?= htmlspecialchars($configuracaoSistema['iconeMarca']) ?>"
                        alt="<?= htmlspecialchars($configuracaoSistema['nomeMarca']) ?>">
                </div>

                <h1>
                    Banco de Talentos <?= htmlspecialchars($configuracaoSistema['nomeMarca']) ?>
                </h1>

                <p>
                    Acesse sua conta
                </p>

            </div>

            <?php if (!empty($_SESSION['erro'])): ?>

                <div class="alerta-erro">

                    <?= htmlspecialchars($_SESSION['erro']) ?>

                </div>

                <?php unset($_SESSION['erro']); ?>

            <?php endif; ?>

            <form
                method="POST"
                action="?c=usuario&m=autenticar">

                <div class="form-group">

                    <label>

                        E-mail

                    </label>

                    <input
                        type="email"
                        name="email"
                        required>

                </div>

                <div class="form-group">

                    <label>

                        Senha

                    </label>

                    <div class="senha-container">

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            required>

                        <button
                            type="button"
                            class="btn-olho"
                            onclick="toggleSenha()">

                            <i
                                id="iconeSenha"
                                class="fa-solid fa-eye">
                            </i>

                        </button>

                    </div>

                </div>

                <div class="turnstile-login">
                    <div
                        class="cf-turnstile"
                        data-sitekey="<?= htmlspecialchars($turnstileSiteKey) ?>"
                        data-action="login"
                        data-theme="light">
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn-login">

                    Entrar

                </button>

            </form>

        </div>

    </div>

    <script>

        function toggleSenha() {

            const senha =
                document.getElementById(
                    'senha'
                );

            const icone =
                document.getElementById(
                    'iconeSenha'
                );

            if (
                senha.type === 'password'
            ) {

                senha.type = 'text';

                icone.classList.remove(
                    'fa-eye'
                );

                icone.classList.add(
                    'fa-eye-slash'
                );

            } else {

                senha.type = 'password';

                icone.classList.remove(
                    'fa-eye-slash'
                );

                icone.classList.add(
                    'fa-eye'
                );
            }
        }

    </script>
    <script
        src="https://challenges.cloudflare.com/turnstile/v0/api.js"
        async
        defer>
    </script>

</body>

</html>
