</div>

</div>

<script>
    function toggleDropdown(botao) {
        const menu =
            botao.nextElementSibling;

        document
            .querySelectorAll(
                '.dropdown-menu'
            )
            .forEach(item => {

                if (item !== menu) {
                    item.classList.remove(
                        'show'
                    );
                }

            });

        menu.classList.toggle(
            'show'
        );
    }

    document.addEventListener(
        'click',
        function(e) {
            if (
                !e.target.closest(
                    '.dropdown-acoes'
                )
            ) {
                document
                    .querySelectorAll(
                        '.dropdown-menu'
                    )
                    .forEach(menu => {

                        menu.classList.remove(
                            'show'
                        );

                    });
            }
        }
    );
</script>

</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>