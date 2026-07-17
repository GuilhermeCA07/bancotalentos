<div class="form-card">

    <h2>
        Finalizar Entrevista
    </h2>

    <form
        method="POST"
        action="?c=entrevista&m=salvarFinalizacao">

        <input
            type="hidden"
            name="idEntrevista"
            value="<?= $entrevista['idEntrevista'] ?>">

        <div class="form-group">

            <label>Candidato</label>

            <input
                type="text"
                value="<?= $entrevista['candidato'] ?>"
                readonly>

        </div>

        <div class="form-group">

            <label>Vaga</label>

            <input
                type="text"
                value="<?= $entrevista['vaga'] ?>"
                readonly>

        </div>

        <div class="form-group">

            <label id="rotuloObservacoes" for="observacoesEntrevista">
                Observações
            </label>

            <textarea
                id="observacoesEntrevista"
                name="observacoes"
                rows="6"
                placeholder="Registre as observações da entrevista..."></textarea>

        </div>

        <div class="form-group">

            <label>
                Resultado
            </label>

            <select
                name="resultado"
                id="resultado"
                required>

                <option value="">
                    Selecione
                </option>

                <option value="Aprovado">
                    Aprovado
                </option>

                <option value="Recusado">
                    Recusado
                </option>

                <option value="Entrevistado">
                    Entrevistado
                </option>

            </select>

        </div>

        <div
            id="blocoRecusa"
            class="bloco-recusa"
            style="display:none;">

            <label>
                Motivo da Recusa
            </label>

            <textarea
                id="motivoRecusa"
                name="motivo_recusa"
                rows="5"
                placeholder="Descreva o motivo da recusa..."></textarea>

        </div>

        <button
            class="btn"
            type="submit">
            Finalizar Entrevista
        </button>

    </form>


</div>
<script>
    const resultado =
        document.getElementById(
            "resultado"
        );

    const blocoRecusa =
        document.getElementById(
            "blocoRecusa"
        );

    const motivoRecusa =
        document.getElementById(
            "motivoRecusa"
        );

    const observacoesEntrevista =
        document.getElementById(
            "observacoesEntrevista"
        );

    const rotuloObservacoes =
        document.getElementById(
            "rotuloObservacoes"
        );

    function atualizarMotivoRecusa() {
        const recusado =
            resultado.value === "Recusado";

        blocoRecusa.style.display =
            recusado
            ? "block"
            : "none";

        motivoRecusa.required =
            recusado;

        const entrevistado =
            resultado.value === "Entrevistado";

        observacoesEntrevista.required =
            entrevistado;

        rotuloObservacoes.textContent =
            entrevistado
            ? "Observações da entrevista *"
            : "Observações";
    }

    resultado.addEventListener(
        "change",
        atualizarMotivoRecusa
    );

    atualizarMotivoRecusa();
</script>
