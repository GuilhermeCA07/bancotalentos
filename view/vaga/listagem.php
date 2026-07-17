<div class="topo">

    <h1>Vagas</h1>

    <a
        href="?c=vaga&m=add"
        class="btn">
        <i class="fa-solid fa-plus" aria-hidden="true"></i>
        Nova Vaga
    </a>

</div>
<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca painel-filtros painel-filtros-vagas">

            <input type="hidden" name="c" value="vaga">

            <input
                type="text"
                name="busca"
                value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                placeholder="Título, cidade, escala...">

            <select name="departamento_id">

                <option value="">
                    Todos os departamentos
                </option>

                <?php foreach ($departamentos as $departamento): ?>
                    <option
                        value="<?= (int)$departamento['idDepartamento'] ?>"
                        <?= (int)($_GET['departamento_id'] ?? 0)
                            === (int)$departamento['idDepartamento']
                            ? 'selected'
                            : '' ?>>
                        <?= htmlspecialchars($departamento['nome']) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <select name="status">

                <option value="">
                    Todos os status
                </option>

                <option value="Aberta"
                    <?= ($_GET['status'] ?? '') == 'Aberta' ? 'selected' : '' ?>>
                    Aberta
                </option>

                <option value="Pausada"
                    <?= ($_GET['status'] ?? '') == 'Pausada' ? 'selected' : '' ?>>
                    Pausada
                </option>

                <option value="Fechada"
                    <?= ($_GET['status'] ?? '') == 'Fechada' ? 'selected' : '' ?>>
                    Fechada
                </option>

            </select>

            <button type="submit" class="btn">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Filtrar
            </button>

        </form>

    </div>

</div>
<table>

    <thead>
        <tr>
            <th>Título</th>
            <th>Departamento</th>
            <th>Cidade</th>
            <th>Modalidade</th>
            <th>Escala</th>
            <th>Vagas</th>
            <th>Status</th>
            <th width="220">
                Ações
            </th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($vagas as $vaga): ?>

            <tr>

                <td>
                    <?= $vaga['titulo'] ?>
                </td>

                <td>
                    <span
                        class="badge-departamento"
                        style="background:<?= htmlspecialchars($vaga['departamento_cor']) ?>">
                        <?= htmlspecialchars($vaga['departamento']) ?>
                    </span>
                </td>

                <td>
                    <?= $vaga['cidade'] ?>
                </td>

                <td>
                    <?= $vaga['modalidade'] ?>
                </td>

                <td>
                    <?= $vaga['escala'] ?>
                </td>

                <td>
                    <?= $vaga['quantidade_vagas'] ?>
                </td>

                <?php

                $classe = '';

                switch ($vaga['status']) {

                    case 'Aberta':
                        $classe = 'status-aberta';
                        break;

                    case 'Fechada':
                        $classe = 'status-fechada';
                        break;

                    case 'Pausada':
                        $classe = 'status-pausada';
                        break;
                }

                ?>
                <td>
                    <span class="status badge-status <?= $classe ?>">
                        <?= $vaga['status'] ?>
                    </span>


                </td>



                <td>
                    <?php if ($vaga['status'] != 'Fechada'): ?>

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
                                    href="?c=vaga&m=editar&id=<?= $vaga['idVaga'] ?>"
                                    class="btn-action btn-editar">

                                    <i class="fa-solid fa-pen"></i>
                                    Editar

                                </a>

                                <?php if ($vaga['status'] == 'Pausada'): ?>

                                    <a
                                        href="?c=vaga&m=alterarStatus&id=<?= $vaga['idVaga'] ?>&status=Aberta"
                                        class="btn-action btn-abrir">

                                        <i class="fa-solid fa-lock-open"></i>
                                        Reabrir

                                    </a>

                                <?php endif; ?>

                                <?php if ($vaga['status'] == 'Aberta'): ?>

                                    <a
                                        href="?c=vaga&m=alterarStatus&id=<?= $vaga['idVaga'] ?>&status=Pausada"
                                        class="btn-action btn-pausar">

                                        <i class="fa-solid fa-pause"></i>
                                        Pausar

                                    </a>

                                <?php endif; ?>

                                <a
                                    href="?c=vaga&m=alterarStatus&id=<?= $vaga['idVaga'] ?>&status=Fechada"
                                    class="btn-action btn-fechar"
                                    onclick="return confirm('Deseja realmente fechar esta vaga? Esta ação não poderá ser desfeita.')">

                                    <i class="fa-solid fa-lock"></i>
                                    Fechar

                                </a>


<?php if (podeExcluir()): ?>
                                <a
                                    href="?c=vaga&m=excluir&id=<?= $vaga['idVaga'] ?>"
                                    class="btn-action btn-excluir"
                                    onclick="return confirm('Deseja realmente excluir?')">

                                    <i class="fa-solid fa-trash"></i>
                                    Excluir

                                </a>
<?php endif; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>
