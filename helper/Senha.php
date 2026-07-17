<?php

class Senha
{
    public static function validarForte($senha)
    {
        $senha = (string)$senha;

        if (mb_strlen($senha) < 8) {
            return 'A senha deve ter pelo menos 8 caracteres.';
        }

        if (mb_strlen($senha) > 128) {
            return 'A senha deve ter no máximo 128 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $senha)) {
            return 'A senha deve conter pelo menos uma letra maiúscula.';
        }

        if (!preg_match('/[a-z]/', $senha)) {
            return 'A senha deve conter pelo menos uma letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $senha)) {
            return 'A senha deve conter pelo menos um número.';
        }

        if (!preg_match('/[^A-Za-z0-9\s]/', $senha)) {
            return 'A senha deve conter pelo menos um símbolo especial.';
        }

        return null;
    }
}
