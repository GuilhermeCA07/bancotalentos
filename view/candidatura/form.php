<div class="topo">

    <h2>

        <?= isset($candidatura)
            ? "Editar Candidatura"
            : "Nova Candidatura" ?>

    </h2>

</div>

<div class="form-card">

    <form
        method="POST"
        action="?c=candidatura&m=salvar"
    >

        <input
            type="hidden"
            name="idCandidatura"
            value="<?= $candidatura['idCandidatura'] ?? '' ?>"
        >

        <div class="form-grid">

        <div class="form-group">

            <label>
                Candidato
            </label>

            <select
                name="candidato_id"
                required
            >

                <option value="">
                    Selecione
                </option>

                <?php foreach($candidatos as $c): ?>

                    <option
                        value="<?= $c['idCandidato'] ?>"
                        <?= (($candidatura['candidato_id'] ?? '') == $c['idCandidato'])
                            ? 'selected'
                            : '' ?>
                    >
                        <?= $c['nome'] ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="form-group">

            <label>
                Vaga
            </label>

            <select
                name="vaga_id"
                required
            >

                <option value="">
                    Selecione
                </option>

                <?php foreach($vagas as $vaga): ?>

                    <option
                        value="<?= $vaga['idVaga'] ?>"
                        <?= (($candidatura['vaga_id'] ?? '') == $vaga['idVaga'])
                            ? 'selected'
                            : '' ?>
                    >
                        <?= $vaga['titulo'] ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        </div>

        <div class="form-actions">
            <a class="btn-secundario" href="?c=candidatura">Cancelar</a>
            <button type="submit" class="btn">
                <i class="fa-solid fa-floppy-disk"></i>
                Salvar candidatura
            </button>
        </div>

    </form>

</div>
