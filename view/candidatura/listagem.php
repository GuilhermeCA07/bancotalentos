<div class="topo">

    <h2>
        Candidaturas
    </h2>

    <a
        href="?c=candidatura&m=add"
        class="btn">
        <i class="fa-solid fa-plus"></i>
        Nova Candidatura
    </a>

</div>

<div class="toolbar">
    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="candidatura">

            <input
                type="text"
                name="busca"
                value="<?= $_GET['busca'] ?? '' ?>"
                placeholder="Nome do candidato ou vaga">

            <select name="status">

                <option value="">
                    Todos os Status
                </option>

                <option value="Em Análise"
                    <?= ($_GET['status'] ?? '') == 'Em Análise' ? 'selected' : '' ?>>
                    Em Análise
                </option>

                <option value="Entrevista Agendada"
                    <?= ($_GET['status'] ?? '') == 'Entrevista Agendada' ? 'selected' : '' ?>>
                    Entrevista Agendada
                </option>

                <option value="Aprovado"
                    <?= ($_GET['status'] ?? '') == 'Aprovado' ? 'selected' : '' ?>>
                    Aprovado
                </option>

                <option value="Recusado"
                    <?= ($_GET['status'] ?? '') == 'Recusado' ? 'selected' : '' ?>>
                    Recusado
                </option>

                <option value="Contratado"
                    <?= ($_GET['status'] ?? '') == 'Contratado' ? 'selected' : '' ?>>
                    Contratado
                </option>

                <option value="Dispensado"
                    <?= ($_GET['status'] ?? '') == 'Dispensado' ? 'selected' : '' ?>>
                    Dispensado
                </option>

                <option value="Auto-Dispensa"
                    <?= ($_GET['status'] ?? '') == 'Auto-Dispensa' ? 'selected' : '' ?>>
                    Auto-Dispensa
                </option>

            </select>

            <input
                type="date"
                name="data_inicio"
                value="<?= $_GET['data_inicio'] ?? '' ?>">

            <span class="filtro-separador">
                até
            </span>

            <input
                type="date"
                name="data_fim"
                value="<?= $_GET['data_fim'] ?? '' ?>">

            <button
                type="submit"
                class="btn">

                Buscar

            </button>

        </form>

    </div>
</div>

<div class="table-container">

    <table>

        <thead>

            <tr>

                <th>Candidato</th>

                <th>Vaga</th>

                <th>Status</th>

                <th>Data</th>

                <th>Ações</th>

            </tr>

        </thead>

        <tbody>

            <?php if (!empty($candidaturas)): ?>

                <?php foreach ($candidaturas as $c): ?>

                    <tr>

                        <td>
                            <a
                                class="link-candidato"
                                href="?c=candidato&m=visualizar&id=<?= $c['candidato_id'] ?>">

                                <i class="fa-solid fa-user"></i>

                                <?= $c['candidato'] ?>

                            </a>
                        </td>

                        <td>
                            <?= $c['vaga'] ?>
                        </td>

                        <td>


                            <span class="
                                badge-status
                                <?= strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $c['status_exibicao']
                                    )
                                ) ?>
                            ">
                                <?= $c['status_exibicao'] ?>
                            </span>

                        </td>

                        <td>

                            <?= date(
                                'd/m/Y',
                                strtotime(
                                    $c['data_candidatura']
                                )
                            ) ?>

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
                                    <?php if ($c['status_exibicao'] == 'Em Análise'): ?>
                                        <a
                                            href="?c=candidatura&m=editar&id=<?= $c['idCandidatura'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                            Editar
                                        </a>
                                        <a
                                            href="?c=entrevista&m=add&id=<?= $c['idCandidatura'] ?>">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            Agendar Entrevista
                                        </a>
                                    <?php endif; ?>
                                    <a
                                        href="?c=candidatura&m=excluir&id=<?= $c['idCandidatura'] ?>"
                                        onclick="return confirm('Deseja excluir esta candidatura?')">
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

                    <td colspan="5">
                        Nenhuma candidatura encontrada.
                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>
<script src="public/js/pesquisa.js"></script>