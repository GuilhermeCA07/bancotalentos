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

            <label>
                Observações
            </label>

            <textarea
                name="observacoes"
                rows="6"></textarea>

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

    resultado.addEventListener(
    "change",
    function() {

        document
            .getElementById(
                "blocoRecusa"
            )
            .style.display =
            this.value === "Recusado"
            ? "block"
            : "none";

    }
);
</script>