<div class="topo">

    <h2>
        Categorias
    </h2>

    <a
        href="?c=categoria&m=add"
        class="btn">
        Nova Categoria
    </a>

</div>
<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="categoria">

            <input
                type="text"
                name="busca"
                value="<?= $_GET['busca'] ?? '' ?>">

            <input
                type="hidden"
                name="c"
                value="categoria">

            <select name="status">

                <option
                    value=""
                    <?= ($_GET['status'] ?? '') == ''
                        ? 'selected'
                        : '' ?>>
                    Todas
                </option>

                <option
                    value="1"
                    <?= ($_GET['status'] ?? '') == '1'
                        ? 'selected'
                        : '' ?>>
                    Ativas
                </option>

                <option
                    value="0"
                    <?= ($_GET['status'] ?? '') == '0'
                        ? 'selected'
                        : '' ?>>
                    Inativas
                </option>

            </select>

            <button
                type="submit"
                class="btn">
                Buscar
            </button>

        </form>
    </div>
</div>
<table>

    <thead>

        <tr>

            <th>Nome</th>
            <th>Status</th>
            <th>Ações</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach ($categorias as $categoria): ?>

            <tr>

                <td>
                    <?= $categoria['nome'] ?>
                </td>

                <td>

                    <?php

                    $classe =
                        $categoria['ativo']
                        ? 'status-ativo'
                        : 'status-inativo';

                    ?>

                    <span class="badge-status <?= $classe ?>">

                        <?= $categoria['ativo']
                            ? 'Ativo'
                            : 'Inativo' ?>

                    </span>

                </td>

                <td>
                    <div class="dropdown-acoes">

                        <button
                            type="button"
                            class="btn-dropdown"
                            onclick="toggleDropdown(this)">

                            <i class="fa-solid fa-bars"></i>

                            Ações

                            <i class="fa-solid fa-chevron-down"></i>

                        </button>

                        <div class="dropdown-menu">

                            <a
                                href="?c=categoria&m=editar&id=<?= $categoria['idCategoria'] ?>"
                                class="btn-action btn-editar">
                                <i class="fa-solid fa-pen"></i>
                                Editar
                            </a>

                            <?php if ($categoria['ativo'] == 1): ?>

                                <a
                                    href="?c=categoria&m=alterarStatus&id=<?= $categoria['idCategoria'] ?>"
                                    <?= $categoria['ativo']
                                        ? 'Inativar'
                                        : 'Ativar'
                                    ?>
                                    class="btn-action btn-pausar"
                                    onclick="return confirm('Deseja realmente inativar?')">
                                    <i class="fa-solid fa-pause"></i>
                                    Inativar
                                </a>
                            <?php elseif ($categoria['ativo'] == 0): ?>
                                <a
                                    href="?c=categoria&m=alterarStatus&id=<?= $categoria['idCategoria'] ?>"
                                    <?= $categoria['ativo']
                                        ? 'Inativar'
                                        : 'Ativar'
                                    ?>
                                    class="btn-action btn-pausar"
                                    onclick="return confirm('Deseja realmente inativar?')">
                                    <i class="fa-solid fa-play"></i>
                                    Ativar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>
<script src="public/js/pesquisa.js"></script>