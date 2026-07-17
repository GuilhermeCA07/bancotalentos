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
        <form method="GET" class="form-busca painel-filtros">

            <input
                type="hidden"
                name="c"
                value="candidatura">

            <div class="campo-filtro campo-filtro-busca">
                <label for="buscaCandidatura">Buscar</label>
                <div class="entrada-com-icone">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input
                        id="buscaCandidatura"
                        type="text"
                        name="busca"
                        value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                        placeholder="Candidato ou vaga">
                </div>
            </div>

            <div class="campo-filtro campo-filtro-status">
                <label for="statusCandidatura">Status</label>
                <select id="statusCandidatura" name="status">

                <option value="">
                    Todos os Status
                </option>

                <option value="Aguardando Entrevista"
                    <?= ($_GET['status'] ?? '') == 'Aguardando Entrevista' ? 'selected' : '' ?>>
                    Aguardando Entrevista
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

                <option value="Entrevistado"
                    <?= ($_GET['status'] ?? '') == 'Entrevistado' ? 'selected' : '' ?>>
                    Entrevistado
                </option>

                <option value="Vaga Preenchida por Contratação"
                    <?= ($_GET['status'] ?? '') == 'Vaga Preenchida por Contratação' ? 'selected' : '' ?>>
                    Vaga Preenchida por Contratação
                </option>

                <option value="Vaga Fechada"
                    <?= ($_GET['status'] ?? '') == 'Vaga Fechada' ? 'selected' : '' ?>>
                    Vaga Fechada
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
            </div>

            <div class="grupo-intervalo">
                <div class="campo-filtro">
                    <label for="dataInicioCandidatura">Data inicial</label>
                    <input
                        id="dataInicioCandidatura"
                        type="date"
                        name="data_inicio"
                        value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
                </div>

                <div class="intervalo-seta" aria-hidden="true">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

                <div class="campo-filtro">
                    <label for="dataFimCandidatura">Data final</label>
                    <input
                        id="dataFimCandidatura"
                        type="date"
                        name="data_fim"
                        value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
                </div>
            </div>

            <button
                type="submit"
                class="btn btn-filtrar">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Filtrar

            </button>

        </form>

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


                            <?php
                            $classeStatus = strtolower(
                                str_replace(' ', '-', $c['status_exibicao'])
                            );
                            ?>

                            <?php if (in_array(
                                $c['status_exibicao'],
                                ['Recusado', 'Entrevistado', 'Aprovado', 'Contratado'],
                                true
                            )): ?>
                                <button
                                    type="button"
                                    class="badge-status <?= $classeStatus ?> badge-detalhes-interativo"
                                    data-detalhes-id="<?= (int)$c['idCandidatura'] ?>"
                                    data-resultado="<?= htmlspecialchars($c['status_exibicao']) ?>"
                                    title="Ver detalhes da entrevista">
                                    <?= htmlspecialchars($c['status_exibicao']) ?>
                                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                </button>
                            <?php else: ?>
                                <span class="badge-status <?= $classeStatus ?>">
                                    <?= htmlspecialchars($c['status_exibicao']) ?>
                                </span>
                            <?php endif; ?>

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
                                    <?php if ($c['status_exibicao'] == 'Aguardando Entrevista'): ?>
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

<?php if (podeExcluir()): ?>
                                    <a
                                        href="?c=candidatura&m=excluir&id=<?= $c['idCandidatura'] ?>"
                                        onclick="return confirm('Deseja excluir esta candidatura?')">
                                        <i class="fa-solid fa-trash"></i>
                                        Excluir
                                    </a>
<?php endif; ?>

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
<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/pesquisa.js"></script>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
