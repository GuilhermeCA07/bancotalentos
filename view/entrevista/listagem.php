<div class="topo">

    <h2>
        Entrevistas
    </h2>

</div>

<div class="toolbar">
        <form method="GET" class="form-busca painel-filtros">

            <input
                type="hidden"
                name="c"
                value="entrevista">

            <div class="campo-filtro campo-filtro-busca">
                <label for="buscaEntrevista">Buscar</label>
                <div class="entrada-com-icone">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input
                        id="buscaEntrevista"
                        type="text"
                        name="busca"
                        value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                        placeholder="Candidato, vaga ou responsável">
                </div>
            </div>

            <div class="grupo-intervalo">
                <div class="campo-filtro">
                    <label for="dataInicioEntrevista">Data inicial</label>
                    <input id="dataInicioEntrevista" type="date" name="data_inicio"
                        value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
                </div>
                <div class="intervalo-seta" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="campo-filtro">
                    <label for="dataFimEntrevista">Data final</label>
                    <input id="dataFimEntrevista" type="date" name="data_fim"
                        value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
                </div>
            </div>

            <div class="grupo-intervalo grupo-intervalo-hora">
                <div class="campo-filtro">
                    <label for="horaInicioEntrevista">Hora inicial</label>
                    <input id="horaInicioEntrevista" type="time" name="hora_inicio"
                        value="<?= htmlspecialchars($_GET['hora_inicio'] ?? '') ?>">
                </div>
                <div class="intervalo-seta" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="campo-filtro">
                    <label for="horaFimEntrevista">Hora final</label>
                    <input id="horaFimEntrevista" type="time" name="hora_fim"
                        value="<?= htmlspecialchars($_GET['hora_fim'] ?? '') ?>">
                </div>
            </div>

            <?php if ($podeVisualizarFinalizadas): ?>
                <div class="campo-filtro campo-filtro-checkbox">
                    <span class="campo-filtro-label">Exibi&ccedil;&atilde;o</span>
                    <label class="checkbox-filtro">
                        <input
                            type="checkbox"
                            name="incluir_finalizadas"
                            value="1"
                            <?= !empty($filtros['incluir_finalizadas']) ? 'checked' : '' ?>>
                        <span class="checkbox-indicador" aria-hidden="true"></span>
                        <span>Incluir finalizadas</span>
                    </label>
                </div>
            <?php endif; ?>

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
                <th>Data</th>
                <th>Hora</th>
                <th>Status</th>
                <th>Responsável</th>
                <th>Reagedamento</th>
                <th>Ações</th>

            </tr>

        </thead>

        <tbody>

            <?php if (!empty($entrevistas)): ?>

                <?php foreach ($entrevistas as $e): ?>

                    <?php
                    $entrevistaFinalizada = in_array(
                        $e['status'],
                        ['Aprovado', 'Recusado', 'Entrevistado', 'Contratado'],
                        true
                    );
                    $classeStatus = strtolower(
                        str_replace(' ', '-', $e['status'])
                    );
                    ?>

                    <tr>

                        <td>
                            <a
                                class="link-candidato"
                                href="?c=candidato&m=visualizar&id=<?= $e['candidato_id'] ?>">

                                <i class="fa-solid fa-user"></i>

                                <?= $e['candidato'] ?>

                            </a>
                        </td>

                        <td>
                            <?= $e['vaga'] ?>
                        </td>

                        <td>
                            <?= date(
                                'd/m/Y',
                                strtotime(
                                    $e['data_entrevista']
                                )
                            ) ?>
                        </td>

                        <td>
                            <?= substr(
                                $e['hora_entrevista'],
                                0,
                                5
                            ) ?>
                        </td>

                        <td>
                            <?php if ($entrevistaFinalizada): ?>
                            <button
                                type="button"
                                class="badge-status <?= htmlspecialchars($classeStatus) ?> badge-detalhes-interativo"
                                data-detalhes-id="<?= (int)$e['candidatura_id'] ?>"
                                data-detalhes-endpoint="?c=entrevista&amp;m=detalhesResultado"
                                data-resultado="<?= htmlspecialchars($e['status']) ?>"
                                title="Ver detalhes da entrevista">
                                <?= htmlspecialchars($e['status']) ?>
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            </button>
                            <?php else: ?>
                                <span class="badge-status <?= htmlspecialchars($classeStatus) ?>">
                                    <?= htmlspecialchars($e['status']) ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= $e['responsavel'] ?>
                        </td>

                        <td>

                            <?php if ($e['total_reagendamentos'] > 0): ?>

                                <span
                                    class="badge-reagendada">

                                    <?= $e['total_reagendamentos'] ?>

                                    <?= $e['total_reagendamentos'] > 1
                                        ? 'reagendamentos'
                                        : 'reagendamento' ?>

                                </span>

                            <?php else: ?>

                                <span
                                    class="badge-naoreagendada">
                                    Sem reagendamentos
                                </span>

                            <?php endif; ?>

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

                                    <?php if (!$entrevistaFinalizada): ?>
                                    <a
                                        href="?c=entrevista&m=editar&id=<?= $e['idEntrevista'] ?>">
                                        <i class="fa-solid fa-pen"></i>
                                        Editar
                                    </a>

                                    <a
                                        href="?c=entrevista&m=finalizar&id=<?= $e['idEntrevista'] ?>">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                        Finalizar Entrevista
                                    </a>
                                    <?php endif; ?>


<?php if (podeExcluir()): ?>
                                    <a
                                        href="?c=entrevista&m=excluir&id=<?= $e['idEntrevista'] ?>"
                                        onclick="return confirm('Deseja excluir esta entrevista?')">
                                        <i class="fa-solid fa-trash"></i>
                                        Excluir
                                    </a>
<?php endif; ?>

                                    <?php if (!$entrevistaFinalizada): ?>
                                        <a
                                            href="?c=entrevista&m=reagendar&id=<?= $e['idEntrevista'] ?>">

                                            <i class="fa-solid fa-calendar-days"></i>

                                            Reagendar

                                        </a>
                                    <?php endif; ?>

                                    <a
                                        type="button"
                                        class="btn-historico"
                                        onclick="abrirHistorico(<?= $e['idEntrevista'] ?>)">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        Histórico da Entrevista

                                    </a>

                                </div>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="8">

                        Nenhuma entrevista encontrada.

                    </td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>
<div
    id="modalHistorico"
    class="modal">

    <div class="modal-content">

        <span
            class="fecharHistorico">

            &times;

        </span>

        <h2>
            Histórico de Reagendamentos
        </h2>

        <div id="conteudoHistorico">

            Carregando...

        </div>

    </div>

</div>
<script>
    function formatarData(data) {

        const partes =
            data.split("-");

        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }

    const modalHistorico =
        document.getElementById(
            "modalHistorico"
        );

    async function abrirHistorico(
        idEntrevista
    ) {

        modalHistorico.style.display =
            "flex";

        const conteudo =
            document.getElementById(
                "conteudoHistorico"
            );

        conteudo.innerHTML =
            "Carregando...";

        const resposta =
            await fetch(
                `?c=entrevista&m=historico&id=${idEntrevista}`
            );

        const historico =
            await resposta.json();

        if (
            historico.length === 0
        ) {

            conteudo.innerHTML =
                "<p>Nenhum reagendamento encontrado.</p>";

            return;
        }

        let html = "";

        historico.forEach(
            item => {

                html += `

<div class="card-historico">

    <div class="historico-header">

        <span class="historico-data">

            ${formatarData(item.data_registro)}

        </span>

    </div>

    <div class="historico-linha">

        <strong>Data anterior:</strong>

        ${formatarData(item.data_anterior)}

    </div>

    <div class="historico-linha">

        <strong>Hora anterior:</strong>

        ${item.hora_anterior}

    </div>

    <div class="historico-linha">

        <strong>Nova data:</strong>

        ${formatarData(item.data_nova)}

    </div>

    <div class="historico-linha">

        <strong>Nova hora:</strong>

        ${item.hora_nova}

    </div>

    <div class="historico-linha">

        <strong>Motivo:</strong>

        ${item.motivo}

    </div>

    <div class="historico-linha">

        <strong>Reagendado por:</strong>

        ${item.usuario}

    </div>

</div>

`;
            }
        );

        conteudo.innerHTML =
            html;
    }

    document
        .querySelector(
            ".fecharHistorico"
        )
        ?.addEventListener(
            "click",
            () => {

                modalHistorico.style.display =
                    "none";

            }
        );

    window.addEventListener(
        "click",
        function(e) {

            if (
                e.target === modalHistorico
            ) {
                modalHistorico.style.display =
                    "none";
            }

        }
    );
</script>
<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
<script src="public/js/pesquisa.js"></script>
