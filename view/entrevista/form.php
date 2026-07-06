<div class="topo">

    <h2>

        <?= isset($entrevista)
            ? 'Editar Entrevista'
            : 'Agendar Entrevista' ?>

    </h2>

</div>

<?php

$modoEdicao =
    isset($entrevista['idEntrevista']);

?>

<div class="form-card">

    <form
        method="POST"
        action="?c=entrevista&m=salvar">

        <input
            type="hidden"
            name="idEntrevista"
            value="<?= $entrevista['idEntrevista'] ?? '' ?>">

        <input
            type="hidden"
            name="candidatura_id"
            value="<?= $candidatura['idCandidatura'] ?>">

        <div class="form-grid">

            <div class="form-group">

                <label>
                    Candidato
                </label>

                <input
                    type="text"
                    value="<?= $candidatura['candidato'] ?>"
                    readonly>

            </div>

            <div class="form-group">

                <label>
                    Vaga
                </label>

                <input
                    type="text"
                    value="<?= $candidatura['vaga'] ?>"
                    readonly>

            </div>

            <div class="form-group">

                <label>
                    Telefone
                </label>

                <input
                    type="text"
                    value="<?= $candidatura['telefone'] ?>"
                    readonly>

            </div>

            <div class="form-group">

                <label>
                    E-mail
                </label>

                <input
                    type="text"
                    value="<?= $candidatura['email'] ?>"
                    readonly>

            </div>

            <div class="form-group">

                <label>
                    Data
                </label>

                <input
                    id="data_entrevista"
                    type="date"
                    name="data_entrevista"
                    class="<?= $modoEdicao ? 'campo-bloqueado' : '' ?>"
                    value="<?= $entrevista['data_entrevista'] ?? '' ?>"
                    <?= !isset($entrevista) ? 'disabled' : '' ?>

                    <?= $modoEdicao ? 'disabled' : '' ?>>

                <?php if ($modoEdicao): ?>

                    <input
                        type="hidden"
                        name="data_entrevista"
                        value="<?= $entrevista['data_entrevista'] ?>">

                    <small class="texto-ajuda">

                        Para alterar data utilize a opção
                        "Reagendar Entrevista".

                    </small>

                <?php endif; ?>



            </div>

            <div class="form-group">

                <label>
                    Hora
                </label>

                <select
                    id="hora_entrevista"
                    name="hora_entrevista"
                    class="<?= $modoEdicao ? 'campo-bloqueado' : '' ?>"
                    <?= !isset($entrevista) ? 'disabled' : '' ?>
                    <?= $modoEdicao ? 'disabled' : '' ?>>

                    <option value="">
                        Selecione um horário
                    </option>

                </select>

                <?php if ($modoEdicao): ?>

                    <input
                        type="hidden"
                        name="hora_entrevista"
                        value="<?= $entrevista['hora_entrevista'] ?>">

                    <small class="texto-ajuda">

                        Para alterar hora utilize a opção
                        "Reagendar Entrevista".

                    </small>

                <?php endif; ?>

            </div>

            <div class="form-group">

                <label>
                    Responsável
                </label>

                <select
                    id="responsavel"
                    name="responsavel"
                    required>

                    <option value="">
                        Selecione
                    </option>

                    <?php foreach ($usuarios as $usuario): ?>

                        <option
                            value="<?= $usuario['nome'] ?>"

                            <?= (
                                isset($entrevista)
                                &&
                                $entrevista['responsavel']
                                == $usuario['nome']
                            )
                                ? 'selected'
                                : '' ?>>

                            <?= $usuario['nome'] ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Local
                </label>

                <input
                    type="text"
                    name="local_entrevista"
                    value="<?= $entrevista['local_entrevista'] ?? '' ?>">

            </div>

        </div>

        <div class="form-group">

            <label>
                Observações
            </label>

            <textarea
                name="observacoes"><?= $entrevista['observacoes'] ?? '' ?></textarea>

        </div>

        <button
            type="submit"
            class="btn">
            <i class="fa-solid fa-calendar-check"></i>
            Salvar Entrevista
        </button>

    </form>

</div>
<script>
    function gerarHorarios() {
        const select =
            document.getElementById(
                "hora_entrevista"
            );

        select.innerHTML =
            '<option value="">Selecione um horário</option>';

        if (!select)
            return;

        for (
            let hora = 0; hora < 24; hora++
        ) {

            ["00", "30"].forEach(
                minuto => {

                    const valor =
                        String(hora)
                        .padStart(2, "0") +
                        ":" +
                        minuto;

                    const option =
                        document.createElement(
                            "option"
                        );

                    option.value =
                        valor;

                    option.textContent =
                        valor;

                    select.appendChild(
                        option
                    );
                }
            );
        }
    }

    document.addEventListener(
        "DOMContentLoaded",
        gerarHorarios
    );

    document.addEventListener(
        "DOMContentLoaded",
        () => {

            const campoResponsavel =
                document.getElementById(
                    "responsavel"
                );

            const campoData =
                document.getElementById(
                    "data_entrevista"
                );

            const campoHora =
                document.getElementById(
                    "hora_entrevista"
                );

            campoResponsavel?.addEventListener(
                "change",
                () => {

                    campoData.disabled = !campoResponsavel.value;

                    campoHora.disabled = true;

                    campoHora.innerHTML =
                        '<option value="">Selecione um horário</option>';

                }
            );

            campoData?.addEventListener(
                "change",
                () => {

                    campoHora.disabled = !campoData.value;

                    atualizarHorariosDisponiveis();

                }
            );

            gerarHorarios();

            const select =
                document.getElementById(
                    "hora_entrevista"
                );

            const horaAtual =
                "<?= $entrevista['hora_entrevista'] ?? '' ?>";

            if (
                horaAtual &&
                select
            ) {

                select.value =
                    horaAtual.substring(0, 5);
            }
        }
    );

    async function atualizarHorariosDisponiveis() {
        const data =
            document.getElementById(
                "data_entrevista"
            )?.value;

        const responsavel =
            document.getElementById(
                "responsavel"
            )?.value;

        const select =
            document.getElementById(
                "hora_entrevista"
            );

        if (
            !data ||
            !responsavel ||
            !select
        ) {
            return;
        }

        const resposta =
            await fetch(
                `?c=entrevista&m=horariosDisponiveis&data=${data}&responsavel=${encodeURIComponent(responsavel)}`
            );

        const ocupados =
            await resposta.json();

        gerarHorarios();

        ocupados.forEach(
            horario => {

                const hora =
                    horario.hora_entrevista
                    .substring(0, 5);

                const option =
                    select.querySelector(
                        `option[value="${hora}"]`
                    );

                if (option) {

                    option.disabled = true;

                    option.textContent =
                        `${hora} | Responsavel: ${horario.responsavel} - Entrevistado: ${horario.candidato}`;

                }
            }
        );
    }

    document
        .getElementById(
            "data_entrevista"
        )
        ?.addEventListener(
            "change",
            atualizarHorariosDisponiveis
        );

    document
        .getElementById(
            "responsavel"
        )
        ?.addEventListener(
            "change",
            atualizarHorariosDisponiveis
        );

    document
        .getElementById(
            "responsavel"
        )
        ?.addEventListener(
            "blur",
            atualizarHorariosDisponiveis
        );
</script>