<?php

function salvarRetorno()
{
    $_SESSION['url_retorno'] =
        $_SERVER['REQUEST_URI'];
}

function voltarParaRetorno(
    $fallback
)
{
    header(
        "Location:" .
        (
            $_SESSION['url_retorno']
            ?? $fallback
        )
    );

    exit;
}