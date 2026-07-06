<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Banco de Talentos - Login
    </title>

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/login.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="login-body">

    <div class="login-container">

        <div class="login-card">

            <div class="login-header">
                <div class="img">
                    <img src="public/img/logo.png" alt="logo netcom">
                </div>

                <h1>
                    Banco de Talentos
                </h1>

                <p>
                    Acesse sua conta
                </p>

            </div>

            <?php if (!empty($_SESSION['erro'])): ?>

                <div class="alerta-erro">

                    <?= $_SESSION['erro']; ?>

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

</body>

</html>