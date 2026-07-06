<div class="topo">

    <h2>
        Entrevistas
    </h2>

</div>

<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="entrevista">

            <input
                type="text"
                name="busca"
                value="<?= $_GET['busca'] ?? '' ?>"
                placeholder="Candidato, vaga ou responsável">

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

            <input
                type="time"
                name="hora_inicio"
                value="<?= $_GET['hora_inicio'] ?? '' ?>">

            <span class="filtro-separador">
                até
            </span>

            <input
                type="time"
                name="hora_fim"
                value="<?= $_GET['hora_fim'] ?? '' ?>">

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
                <th>Data</th>
                <th>Hora</th>
                <th>Responsável</th>
                <th>Reagedamento</th>
                <th>Ações</th>

            </tr>

        </thead>

        <tbody>

            <?php if (!empty($entrevistas)): ?>

                <?php foreach ($entrevistas as $e): ?>

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

                                    <a
                                        href="?c=entrevista&m=excluir&id=<?= $e['idEntrevista'] ?>"
                                        onclick="return confirm('Deseja excluir esta entrevista?')">
                                        <i class="fa-solid fa-trash"></i>
                                        Excluir
                                    </a>

                                    <a
                                        href="?c=entrevista&m=reagendar&id=<?= $e['idEntrevista'] ?>">

                                        <i class="fa-solid fa-calendar-days"></i>

                                        Reagendar

                                    </a>

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

                    <td colspan="7">

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

        console.log(historico);
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
<script src="public/js/pesquisa.js"></script>