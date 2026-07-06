<div class="form-card">

    <form
        method="POST"
        action="?c=habilidade&m=salvar">

        <input
            type="hidden"
            name="idHabilidade"
            value="<?= $habilidade['idHabilidade'] ?? '' ?>">

        <div class="form-group">

            <label>
                Nome
            </label>

            <input
                type="text"
                name="nome"
                required
                value="<?= $habilidade['nome'] ?? '' ?>">

        </div>

        <div class="form-group">

            <label>
                Categoria
            </label>

            <select
                name="categoria"
                required>

                <option value="">
                    Selecione
                </option>

                <?php foreach (
                    $categorias as $categoria
                ): ?>

                    <option
                        value="<?= $categoria['idCategoria'] ?>"

                        <?= isset($habilidade)
                            && $habilidade['categoria_id']
                            == $categoria['idCategoria']
                            ? 'selected'
                            : ''
                        ?>>

                        <?= $categoria['nome'] ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <button
            class="btn"
            type="submit">
            Salvar
        </button>

    </form>

</div>