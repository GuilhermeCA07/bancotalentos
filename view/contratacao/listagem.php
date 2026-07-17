<div class="topo">
    <div>
        <span class="pagina-eyebrow">Admissões</span>
        <h1>Contratações</h1>
    </div>
</div>


<div class="toolbar">
        <form method="GET" class="form-busca painel-filtros">

            <input
                type="hidden"
                name="c"
                value="contratacao">

            <div class="campo-filtro campo-filtro-busca">
                <label for="buscaContratacao">Buscar</label>
                <div class="entrada-com-icone">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="buscaContratacao" type="text" name="busca"
                        value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                        placeholder="Candidato ou vaga">
                </div>
            </div>

            <div class="campo-filtro campo-filtro-status">
                <label for="statusContratacao">Situação</label>
                <select id="statusContratacao" name="status">

                <option value="">
                    Todos os Status
                </option>

                <option
                    value="Aguardando"
                    <?= ($_GET['status'] ?? '') == 'Aguardando' ? 'selected' : '' ?>>
                    Aguardando
                </option>

                <option
                    value="Contratado"
                    <?= ($_GET['status'] ?? '') == 'Contratado' ? 'selected' : '' ?>>
                    Contratado
                </option>

                <option
                    value="Dispensado"
                    <?= ($_GET['status'] ?? '') == 'Dispensado' ? 'selected' : '' ?>>
                    Dispensado
                </option>

                <option
                    value="Auto-Dispensa"
                    <?= ($_GET['status'] ?? '') == 'Auto-Dispensa' ? 'selected' : '' ?>>
                    Auto-Dispensa
                </option>

                </select>
            </div>

            <div class="grupo-intervalo">
                <div class="campo-filtro">
                    <label for="dataInicioContratacao">Data inicial</label>
                    <input id="dataInicioContratacao" type="date" name="data_inicio"
                        value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
                </div>
                <div class="intervalo-seta" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="campo-filtro">
                    <label for="dataFimContratacao">Data final</label>
                    <input id="dataFimContratacao" type="date" name="data_fim"
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
<table class="table">

    <thead>

        <tr>

            <th>Candidato</th>

            <th>Vaga</th>

            <th>Situação</th>

            <th>Data Contratação</th>

            <th>Ações</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach ($contratacoes as $item): ?>

            <tr>

                <td>
                    <i class="fa-solid fa-user"></i>
                    <a class="link-candidato"
                        href="?c=candidato&m=visualizar&id=<?= $item['candidato_id'] ?>">
                        <?= $item['nome'] ?>
                    </a>

                </td>

                <td>

                    <?= $item['titulo'] ?>

                </td>

                <td>

                    <?php
                    $statusExibicao = $item['status_exibicao'] ?? 'Aguardando';
                    $classeStatus = strtolower(
                        str_replace(' ', '-', $statusExibicao)
                    );
                    $possuiDetalhes = in_array(
                        $statusExibicao,
                        ['Aguardando', 'Contratado'],
                        true
                    );
                    $resultadoDetalhes = $statusExibicao === 'Aguardando'
                        ? 'Aprovado'
                        : $statusExibicao;
                    ?>

                    <?php if ($possuiDetalhes): ?>
                        <button
                            type="button"
                            class="badge-status badge-<?= htmlspecialchars($classeStatus) ?> badge-detalhes-interativo"
                            data-detalhes-id="<?= (int)$item['idCandidatura'] ?>"
                            data-detalhes-endpoint="?c=contratacao&amp;m=detalhesResultado"
                            data-resultado="<?= htmlspecialchars($resultadoDetalhes) ?>"
                            title="Ver detalhes da entrevista">
                            <?= htmlspecialchars($statusExibicao) ?>
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                    <?php else: ?>
                        <span class="badge-status badge-<?= htmlspecialchars($classeStatus) ?>">
                            <?= htmlspecialchars($statusExibicao) ?>
                        </span>
                    <?php endif; ?>

                </td>

                <td>

                    <?= $item['data_contratacao']
                        ? date(
                            'd/m/Y',
                            strtotime(
                                $item['data_contratacao']
                            )
                        )
                        : '-' ?>

                </td>

                <td>

                    <?php
                    $status = $item['status_exibicao'] ?? 'Aguardando';

                    if (
                        $status == 'Aguardando'
                        ||
                        $status == 'Contratado'
                    ): ?>

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

                                <?php if (
                                    $status == 'Aguardando'
                                ): ?>

                                    <a
                                        href="?c=contratacao&m=contratar&id=<?= $item['idCandidatura'] ?>"
                                        class="btn-action btn-abrir">

                                        <i class="fa-solid fa-user-check"></i>

                                        Contratar

                                    </a>

                                <?php endif; ?>

                                <a
                                    href="#"
                                    class="btn-action btn-fechar"
                                    onclick="abrirModalDispensa(
            <?= $item['idCandidatura'] ?>
        )">

                                    <i class="fa-solid fa-user-xmark"></i>

                                    Dispensar

                                </a>

                                <a
                                    href="#"
                                    class="btn-action btn-pausar"
                                    onclick="abrirModalAutoDispensa(
            <?= $item['idCandidatura'] ?>
        )">

                                    <i class="fa-solid fa-person-walking-arrow-right"></i>

                                    Auto-Dispensa

                                </a>

                            </div>

                        </div>

                    <?php else: ?>

                        <span class="acao-finalizada">

                            <i class="fa-solid fa-lock"></i>

                            Finalizado

                        </span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<div
    id="modalDesligamento"
    class="modal-desligamento">

    <div class="modal-content">

        <div class="modal-header">

            <h3>

                <i class="fa-solid fa-circle-info"></i>

                Informar Motivo

            </h3>

            <button
                type="button"
                class="btn-fechar-modal-con"
                onclick="fecharModal()">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>

        <form
            id="formDesligamento"
            method="POST">

            <input
                type="hidden"
                name="idCandidatura"
                id="idCandidatura">

            <div class="form-group">

                <label>

                    Motivo da alteração

                </label>

                <textarea
                    name="motivo"
                    rows="5"
                    placeholder="Descreva o motivo..."
                    required></textarea>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-cancelar"
                    onclick="fecharModal()">

                    Cancelar

                </button>

                <button
                    type="submit"
                    class="btn-salvar">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Salvar

                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function abrirModalDispensa(id) {
        document
            .getElementById(
                "idCandidatura"
            )
            .value = id;

        document
            .getElementById(
                "formDesligamento"
            )
            .action =
            "?c=contratacao&m=dispensar";

        document
            .querySelector(
                ".modal-header h3"
            )
            .innerHTML =
            '<i class="fa-solid fa-user-xmark"></i> Dispensa';

        document
            .querySelector(
                'textarea[name="motivo"]'
            )
            .placeholder =
            "Informe o motivo da dispensa...";

        document
            .getElementById(
                "modalDesligamento"
            )
            .classList.add(
                "ativo"
            );
    }

    function abrirModalAutoDispensa(id) {
        document
            .getElementById(
                "idCandidatura"
            )
            .value = id;

        document
            .getElementById(
                "formDesligamento"
            )
            .action =
            "?c=contratacao&m=autoDispensa";

        document
            .querySelector(
                ".modal-header h3"
            )
            .innerHTML =
            '<i class="fa-solid fa-person-walking-arrow-right"></i> Auto-Dispensa';

        document
            .querySelector(
                'textarea[name="motivo"]'
            )
            .placeholder =
            "Informe o motivo da auto-dispensa...";

        document
            .getElementById(
                "modalDesligamento"
            )
            .classList.add(
                "ativo"
            );
    }

    function fecharModal() {
        document
            .getElementById(
                "modalDesligamento"
            )
            .classList.remove(
                "ativo"
            );

        document
            .getElementById(
                "formDesligamento"
            )
            .reset();
    }

    window.addEventListener(
        "click",
        function(e) {
            const modal =
                document.getElementById(
                    "modalDesligamento"
                );

            if (
                e.target === modal
            ) {

                fecharModal();

            }
        }
    );
</script>
<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
