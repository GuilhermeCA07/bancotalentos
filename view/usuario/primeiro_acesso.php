<?php
require_once 'model/ConfiguracaoModel.php';
$configuracaoSistema = (new ConfiguracaoModel())->buscar();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir senha - Banco de Talentos</title>
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
    <main class="login-container primeiro-acesso-container">
        <section class="login-card acesso-seguranca-card">
            <div class="seguranca-icone" aria-hidden="true">
                <i class="fa-solid fa-key"></i>
            </div>
            <div class="login-header">
                <h1>Defina sua nova senha</h1>
                <p>Antes de continuar, substitua a senha provisória da sua conta.</p>
            </div>

            <?php if ($mensagemConta): ?>
                <div class="alerta-erro" role="alert">
                    <?= htmlspecialchars($mensagemConta['texto']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="?c=usuario&amp;m=alterarMinhaSenha">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_alterar_senha']) ?>">

                <?php foreach ([
                    ['id' => 'senhaAtual', 'nome' => 'senha_atual', 'rotulo' => 'Senha provisória', 'auto' => 'current-password'],
                    ['id' => 'novaSenha', 'nome' => 'nova_senha', 'rotulo' => 'Nova senha', 'auto' => 'new-password'],
                    ['id' => 'confirmarSenha', 'nome' => 'confirmar_senha', 'rotulo' => 'Confirmar nova senha', 'auto' => 'new-password']
                ] as $campo): ?>
                    <div class="form-group">
                        <label for="<?= $campo['id'] ?>"><?= $campo['rotulo'] ?></label>
                        <div class="campo-senha-conta">
                            <input
                                id="<?= $campo['id'] ?>"
                                type="password"
                                name="<?= $campo['nome'] ?>"
                                autocomplete="<?= $campo['auto'] ?>"
                                <?= $campo['nome'] !== 'senha_atual' ? 'minlength="8"' : '' ?>
                                <?= $campo['nome'] !== 'senha_atual' ? 'maxlength="128" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\\s]).{8,}"' : '' ?>
                                required>
                            <button type="button" class="btn-exibir-senha" data-alvo-senha="<?= $campo['id'] ?>" aria-label="Exibir senha" title="Exibir senha">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <ul class="requisitos-senha">
                    <li>8 ou mais caracteres</li>
                    <li>Letra maiúscula e minúscula</li>
                    <li>Número e símbolo especial</li>
                </ul>

                <button type="submit" class="btn-login">Salvar e acessar</button>
            </form>

            <a class="sair-primeiro-acesso" href="?c=usuario&amp;m=sair">Sair da conta</a>
        </section>
    </main>
    <script src="public/js/minha-conta.js?v=<?= filemtime('public/js/minha-conta.js') ?>"></script>
</body>
</html>
