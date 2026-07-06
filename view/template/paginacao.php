<div class="paginacao">

    <?php

    $inicio = max(
        1,
        $paginaAtual - 2
    );

    $fim = min(
        $totalPaginas,
        $paginaAtual + 2
    );

    ?>

    <!-- Primeira -->
    <?php if ($paginaAtual > 1): ?>

        <?php
        $query = $_GET;
        $query['pagina'] = 1;
        ?>

        <a
            href="?<?= http_build_query($query) ?>"
            class="nav-pagina">
            << 
        </a>

    <?php endif; ?>


    <!-- Anterior -->
    <?php if ($paginaAtual > 1): ?>

        <?php
        $query = $_GET;
        $query['pagina'] = $paginaAtual - 1;
        ?>

        <a
            href="?<?= http_build_query($query) ?>"
            class="nav-pagina">
            <
        </a>

    <?php endif; ?>


    <?php if ($inicio > 1): ?>

        <?php
        $query = $_GET;
        $query['pagina'] = 1;
        ?>

        <a href="?<?= http_build_query($query) ?>">
            1
        </a>

        <?php if ($inicio > 2): ?>

            <span class="reticencias">
                ...
            </span>

        <?php endif; ?>

    <?php endif; ?>


    <?php for (
        $i = $inicio;
        $i <= $fim;
        $i++
    ): ?>

        <?php

        $query = $_GET;
        $query['pagina'] = $i;

        ?>

        <a
            href="?<?= http_build_query($query) ?>"
            class="<?= $i == $paginaAtual ? 'ativo' : '' ?>">

            <?= $i ?>

        </a>

    <?php endfor; ?>


    <?php if ($fim < $totalPaginas): ?>

        <?php if ($fim < ($totalPaginas - 1)): ?>

            <span class="reticencias">
                ...
            </span>

        <?php endif; ?>

        <?php

        $query = $_GET;
        $query['pagina'] = $totalPaginas;

        ?>

        <a href="?<?= http_build_query($query) ?>">
            <?= $totalPaginas ?>
        </a>

    <?php endif; ?>


    <!-- Próxima -->
    <?php if ($paginaAtual < $totalPaginas): ?>

        <?php
        $query = $_GET;
        $query['pagina'] = $paginaAtual + 1;
        ?>

        <a
            href="?<?= http_build_query($query) ?>"
            class="nav-pagina">
            >
        </a>

    <?php endif; ?>


    <!-- Última -->
    <?php if ($paginaAtual < $totalPaginas): ?>

        <?php
        $query = $_GET;
        $query['pagina'] = $totalPaginas;
        ?>

        <a
            href="?<?= http_build_query($query) ?>"
            class="nav-pagina">
            >>
        </a>

    <?php endif; ?>

</div>