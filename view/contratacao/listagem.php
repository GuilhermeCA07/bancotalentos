<div class="page-header">

    <h1>
        Contratações
    </h1>
    <br>
</div>


<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="contratacao">

            <input
                type="text"
                name="busca"
                value="<?= $_GET['busca'] ?? '' ?>"
                placeholder="Nome ou vaga">

            <select name="status">

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

                    <span class="
                    badge-status
                    badge-<?= strtolower(
                                str_replace(
                                    ' ',
                                    '-',
                                    $item['status_exibicao'] ?? 'aguardando'
                                )
                            ) ?>">
                        <?= $item['status_exibicao'] ?? 'Aguardando' ?>
                    </span>

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