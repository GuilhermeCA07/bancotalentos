<div class="topo">
    <div>
        <span class="pagina-eyebrow">Agenda</span>
        <h1>Reagendar Entrevista</h1>
    </div>
</div>

<div class="form-card">
<form
    method="POST"
    action="?c=entrevista&m=salvarReagendamento"
    id="formReagendamento">

    <div class="form-grid form-grid-3">

    <div class="form-group">

        <label>
            Responsável
        </label>

        <input
            type="text"
            readonly
            value="<?= $entrevista['responsavel'] ?>">



    </div>

    <input
        type="hidden"
        name="idEntrevista"
        value="<?= $entrevista['idEntrevista'] ?>">

    <input
        type="hidden"
        id="responsavel"
        value="<?= $entrevista['responsavel'] ?>">





    <div class="form-group">

        <label>
            Data Atual
        </label>

        <input
            type="text"
            readonly
            value="<?= date(
                        'd/m/Y',
                        strtotime(
                            $entrevista['data_entrevista']
                        )
                    ) ?>">

    </div>

    <div class="form-group">

        <label>
            Hora Atual
        </label>

        <input
            type="text"
            readonly
            value="<?= substr(
                        $entrevista['hora_entrevista'],
                        0,
                        5
                    ) ?>">

    </div>

    </div>

    <div class="form-grid">

    <div class="form-group">

        <label>
            Nova Data
        </label>

        <input
            type="date"
            id="nova_data"
            name="nova_data"
            required>

    </div>

    <div class="form-group">

        <label>
            Nova Hora
        </label>

        <select
            id="nova_hora"
            name="nova_hora"
            required>

            <option value="">
                Selecione um horário
            </option>

        </select>




    </div>

    </div>

    <div class="form-group">

        <label>
            Motivo
        </label>

        <textarea
            name="motivo"
            rows="4"
            required></textarea>

    </div>

    <div class="form-actions">
        <a class="btn-secundario" href="?c=entrevista">Cancelar</a>
        <button type="submit" class="btn">
            <i class="fa-solid fa-calendar-check"></i>
            Reagendar
        </button>
    </div>

</form>
</div>
<script>

function gerarHorariosReagendamento() {

    const select =
        document.getElementById(
            "nova_hora"
        );

    if (!select)
        return;

    select.innerHTML =
        '<option value="">Selecione um horário</option>';

    for (
        let hora = 0;
        hora < 24;
        hora++
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

async function atualizarHorariosReagendamento() {

    const data =
        document.getElementById(
            "nova_data"
        )?.value;

    const responsavel =
        document.getElementById(
            "responsavel"
        )?.value;

    const idEntrevista =
        document.querySelector(
            '[name="idEntrevista"]'
        )?.value;

    const select =
        document.getElementById(
            "nova_hora"
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
            `?c=entrevista&m=horariosDisponiveis&data=${data}&responsavel=${encodeURIComponent(responsavel)}&ignorar=${idEntrevista}`
        );

    const ocupados =
        await resposta.json();

    gerarHorariosReagendamento();

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
                    `${hora} | Responsável: ${horario.responsavel} - Entrevistado: ${horario.candidato}`;

            }
        }
    );
}

document.addEventListener(
    "DOMContentLoaded",
    () => {

        const campoData =
            document.getElementById(
                "nova_data"
            );

        const campoHora =
            document.getElementById(
                "nova_hora"
            );

        gerarHorariosReagendamento();

        campoHora.disabled = true;

        campoData?.addEventListener(
            "change",
            () => {

                if (!campoData.value) {

                    campoHora.disabled = true;

                    campoHora.innerHTML =
                        '<option value="">Selecione um horário</option>';

                    return;
                }

                campoHora.disabled = false;

                atualizarHorariosReagendamento();
            }
        );

    }
);

</script>
