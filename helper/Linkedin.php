<?php

function normalizarLinkedin($valor)
{
    $linkedin = trim((string)$valor);

    if ($linkedin === '') {
        return null;
    }

    $partes = parse_url($linkedin);

    if (!is_array($partes)) {
        $partes = [];
    }

    $host = strtolower($partes['host'] ?? '');
    $caminho = $partes['path'] ?? '';
    $hostLinkedin =
        $host === 'linkedin.com'
        || str_ends_with($host, '.linkedin.com');

    $valido =
        strlen($linkedin) <= 255
        && filter_var($linkedin, FILTER_VALIDATE_URL)
        && ($partes['scheme'] ?? '') === 'https'
        && $hostLinkedin
        && empty($partes['user'])
        && empty($partes['pass'])
        && empty($partes['query'])
        && empty($partes['fragment'])
        && preg_match(
            '#^/in/[A-Za-z0-9._%~-]+/?$#',
            $caminho
        );

    if (!$valido) {
        throw new InvalidArgumentException(
            'Informe um link de perfil válido do LinkedIn usando HTTPS.'
        );
    }

    return 'https://' . $host . rtrim($caminho, '/');
}
