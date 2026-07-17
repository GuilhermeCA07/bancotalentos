(function () {
    document.querySelectorAll('[data-alvo-senha]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            const campo = document.getElementById(botao.dataset.alvoSenha);

            if (!campo) {
                return;
            }

            const exibir = campo.type === 'password';
            campo.type = exibir ? 'text' : 'password';
            botao.setAttribute(
                'aria-label',
                exibir ? 'Ocultar senha' : 'Exibir senha'
            );
            botao.title = exibir ? 'Ocultar senha' : 'Exibir senha';
            botao.querySelector('i').className = exibir
                ? 'fa-solid fa-eye-slash'
                : 'fa-solid fa-eye';
        });
    });
}());
