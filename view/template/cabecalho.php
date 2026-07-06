<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Banco de Talentos</title>

    <link rel="stylesheet" href="public/css/style.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="icon" type="image/png" sizes="16x16" href="public/img/icon_logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/img/icon_logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

    <div class="container">
        <?php if (isset($_SESSION['erro'])): ?>

            <script>
                alert("<?= $_SESSION['erro'] ?>");
            </script>

            <?php unset($_SESSION['erro']); ?>

        <?php endif; ?>