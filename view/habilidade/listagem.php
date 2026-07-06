<div class="topo">

    <h2>Habilidades</h2>

    <a
        href="?c=habilidade&m=add"
        class="btn">
        Nova Habilidade
    </a>

</div>

<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form
            method="GET"
            class="form-busca">

            <input
                type="hidden"
                name="c"
                value="habilidade">

            <input
                type="text"
                name="busca"
                placeholder="Nome da habilidade ou categoria"
                value="<?= $_GET['busca'] ?? '' ?>">

            <select name="categoria">

                <option value="">
                    Todas Categorias
                </option>

                <?php foreach ($categorias as $categoria): ?>

                    <option
                        value="<?= $categoria['idCategoria'] ?>"
                        <?= ($_GET['categoria'] ?? '') == $categoria['idCategoria']
                            ? 'selected'
                            : '' ?>>

                        <?= $categoria['nome'] ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <select name="status">

                <option value="">
                    Todos Status
                </option>

                <option
                    value="1"
                    <?= ($_GET['status'] ?? '') == '1'
                        ? 'selected'
                        : '' ?>>

                    Ativo

                </option>

                <option
                    value="0"
                    <?= ($_GET['status'] ?? '') == '0'
                        ? 'selected'
                        : '' ?>>

                    Inativo

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
            <th>Categoria</th>
            <th>Status</th>
            <th>Ações</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach ($habilidades as $h): ?>

            <tr>

                <td>
                    <?= $h['nome'] ?>
                </td>

                <td>
                    <?= $h['categoria_nome'] ?>
                </td>

                <td>

                    <?php

                    $classe =
                        $h['ativo']
                        ? 'status-ativo'
                        : 'status-inativo';

                    ?>

                    <span class="badge-status <?= $classe ?>">

                        <?= $h['ativo']
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
                                href="?c=habilidade&m=editar&id=<?= $h['idHabilidade'] ?>"
                                class="btn-action btn-editar">
                                <i class="fa-solid fa-pen"></i>
                                Editar
                            </a>

                            <?php if ($h['ativo'] == 1): ?>
                                <a
                                    href="?c=habilidade&m=alterarStatus&id=<?= $h['idHabilidade'] ?>"
                                    <?= $h['ativo']
                                        ? 'Inativar'
                                        : 'Ativar'
                                    ?>
                                    class="btn-action btn-pausar"
                                    onclick="return confirm('Deseja realmente inativar?')">
                                    <i class="fa-solid fa-pause"></i>
                                    Inativar
                                </a>
                            <?php elseif ($h['ativo'] == 0): ?>
                                <a
                                    href="?c=habilidade&m=alterarStatus&id=<?= $h['idHabilidade'] ?>"
                                    <?= $h['ativo']
                                        ? 'Inativar'
                                        : 'Ativar'
                                    ?>
                                    class="btn-action btn-pausar"
                                    onclick="return confirm('Deseja realmente Ativar?')">
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