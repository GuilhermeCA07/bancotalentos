<div class="topo">

    <h1>Vagas</h1>

    <a
        href="?c=usuario&m=add"
        class="btn">

        <i class="fa-solid fa-plus"></i>

        Novo Usuário

    </a>

</div>
<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input type="hidden" name="c" value="vaga">

            <input
                type="hidden"
                name="c"
                value="usuario">

            <input
                type="text"
                name="busca"
                placeholder="Nome ou e-mail"
                value="<?= $_GET['busca'] ?? '' ?>">

            <select name="perfil">

                <option value="">
                    Todos os Perfis
                </option>

                <option
                    value="Gerente"
                    <?= ($_GET['perfil'] ?? '') == 'Gerente' ? 'selected' : '' ?>>
                    Gerente
                </option>

                <option
                    value="Secretario"
                    <?= ($_GET['perfil'] ?? '') == 'Secretario' ? 'selected' : '' ?>>
                    Secretário
                </option>

                <option
                    value="Recrutador"
                    <?= ($_GET['perfil'] ?? '') == 'Recrutador' ? 'selected' : '' ?>>
                    Recrutador
                </option>

            </select>

            <button type="submit" class="btn">
                Buscar
            </button>

        </form>

    </div>

</div>
<table>

    <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th width="220">
                Ações
            </th>
        </tr>
    </thead>

    <tbody>

        <?php if (!empty($usuarios)): ?>

            <?php foreach ($usuarios as $u): ?>

                <tr>

                    <td>

                        <?= $u['nome'] ?>

                    </td>

                    <td>

                        <?= $u['email'] ?>

                    </td>

                    <td>

                        <span class="
    badge-perfil
    <?= strtolower($u['perfil']) ?>
">

                            <?= $u['perfil'] ?>

                        </span>

                    </td>

                    <td class="acoes-coluna">

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
                                    href="?c=usuario&m=editar&id=<?= $u['idUsuario'] ?>">

                                    <i class="fa-solid fa-pen"></i>

                                    Editar

                                </a>

                                <a
                                    href="?c=usuario&m=excluir&id=<?= $u['idUsuario'] ?>"
                                    onclick="return confirm('Deseja excluir este usuário?')">

                                    <i class="fa-solid fa-trash"></i>

                                    Excluir

                                </a>

                            </div>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="4">

                    Nenhum usuário encontrado.

                </td>

            </tr>

        <?php endif; ?>


    </tbody>

</table>