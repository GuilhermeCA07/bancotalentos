</main>

</div>

<script>
    (function () {
        let menuAberto = null;
        let botaoAberto = null;

        function restaurarMenu(menu) {
            if (!menu) {
                return;
            }

            menu.classList.remove('show', 'dropdown-flutuante');
            menu.style.removeProperty('top');
            menu.style.removeProperty('left');
            menu.style.removeProperty('right');
            menu.style.removeProperty('visibility');
            menu.style.removeProperty('max-height');

            if (menu._dropdownOrigem && menu._dropdownOrigem.isConnected) {
                menu._dropdownOrigem.appendChild(menu);
            }

            if (menu === menuAberto) {
                menuAberto = null;
                botaoAberto = null;
            }
        }

        function fecharDropdown() {
            if (botaoAberto) {
                botaoAberto.setAttribute('aria-expanded', 'false');
            }

            restaurarMenu(menuAberto);
        }

        function posicionarMenu() {
            if (
                !menuAberto
                || !botaoAberto
                || !botaoAberto.isConnected
            ) {
                fecharDropdown();
                return;
            }

            const margem = 8;
            const espacamento = 6;
            const botaoRect = botaoAberto.getBoundingClientRect();

            menuAberto.style.visibility = 'hidden';
            menuAberto.style.left = '0px';
            menuAberto.style.top = '0px';
            menuAberto.style.right = 'auto';
            menuAberto.style.maxHeight = `${window.innerHeight - (margem * 2)}px`;

            const menuRect = menuAberto.getBoundingClientRect();
            const espacoAbaixo = window.innerHeight - botaoRect.bottom;
            const abrirAcima =
                espacoAbaixo < menuRect.height + espacamento
                && botaoRect.top > menuRect.height + espacamento;
            const topoDesejado = abrirAcima
                ? botaoRect.top - menuRect.height - espacamento
                : botaoRect.bottom + espacamento;
            const esquerdaDesejada = botaoRect.right - menuRect.width;
            const topo = Math.max(
                margem,
                Math.min(topoDesejado, window.innerHeight - menuRect.height - margem)
            );
            const esquerda = Math.max(
                margem,
                Math.min(esquerdaDesejada, window.innerWidth - menuRect.width - margem)
            );

            menuAberto.style.top = `${topo}px`;
            menuAberto.style.left = `${esquerda}px`;
            menuAberto.style.visibility = 'visible';
        }

        window.toggleDropdown = function (botao) {
            const menu = botao._dropdownMenu || botao.nextElementSibling;

            if (!menu || !menu.classList.contains('dropdown-menu')) {
                return;
            }

            const estavaAberto = menu === menuAberto;
            fecharDropdown();

            if (estavaAberto) {
                return;
            }

            botao._dropdownMenu = menu;
            menu._dropdownOrigem = botao.closest('.dropdown-acoes');
            document.body.appendChild(menu);
            menu.classList.add('show', 'dropdown-flutuante');
            botao.setAttribute('aria-expanded', 'true');
            menuAberto = menu;
            botaoAberto = botao;
            posicionarMenu();
        };

        document.addEventListener('click', function (evento) {
            if (
                evento.target.closest('.dropdown-acoes')
                || evento.target.closest('.dropdown-menu')
            ) {
                return;
            }

            fecharDropdown();
        });

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') {
                fecharDropdown();
            }
        });

        document.addEventListener('scroll', posicionarMenu, true);
        window.addEventListener('resize', posicionarMenu);
    }());
</script>

</body>

</html>
