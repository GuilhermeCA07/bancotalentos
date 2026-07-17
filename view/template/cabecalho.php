<?php
require_once 'model/ConfiguracaoModel.php';
$configuracaoSistema =
    (new ConfiguracaoModel())->buscar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Banco de Talentos</title>

    <link
        rel="stylesheet"
        href="public/css/style.css?v=<?= filemtime('public/css/style.css') ?>">
    <link
        rel="stylesheet"
        href="public/css/configuracao-email.css?v=<?= filemtime('public/css/configuracao-email.css') ?>">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link
        rel="icon"
        type="<?= htmlspecialchars($configuracaoSistema['tipoIconeMarca']) ?>"
        href="<?= htmlspecialchars($configuracaoSistema['iconeMarca']) ?>">
    <script
        src="public/js/vendor/chart.umd.min.js?v=<?= filemtime('public/js/vendor/chart.umd.min.js') ?>"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --laranja: <?= htmlspecialchars($configuracaoSistema['corSecundaria']) ?>;
            --laranjaescuro: <?= htmlspecialchars($configuracaoSistema['corSecundariaEscura']) ?>;
            --azul: <?= htmlspecialchars($configuracaoSistema['corDestaque']) ?>;
            --texto-menu: <?= htmlspecialchars($configuracaoSistema['corTextoMenu']) ?>;
            --tema-primaria: <?= htmlspecialchars($configuracaoSistema['corPrimaria']) ?>;
            --tema-secundaria: <?= htmlspecialchars($configuracaoSistema['corSecundaria']) ?>;
            --tema-destaque: <?= htmlspecialchars($configuracaoSistema['corDestaque']) ?>;
            --tema-destaque-escura: <?= htmlspecialchars($configuracaoSistema['corDestaqueEscura']) ?>;
        }
    </style>
</head>

<body
    class="marca-<?= htmlspecialchars($configuracaoSistema['identidade']) ?>"
    data-identidade="<?= htmlspecialchars($configuracaoSistema['identidade']) ?>">

    <div class="container">
        <?php if (isset($_SESSION['erro'])): ?>

            <script>
                alert(<?= json_encode(
                    (string)$_SESSION['erro'],
                    JSON_UNESCAPED_UNICODE
                    | JSON_HEX_TAG
                    | JSON_HEX_AMP
                    | JSON_HEX_APOS
                    | JSON_HEX_QUOT
                ) ?>);
            </script>

            <?php unset($_SESSION['erro']); ?>

        <?php endif; ?>
