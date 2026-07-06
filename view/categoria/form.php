<div class="form-card">

    <form
        method="POST"
        action="?c=categoria&m=salvar"
    >

        <input
            type="hidden"
            name="idCategoria"
            value="<?= $categoria['idCategoria'] ?? '' ?>"
        >

        <div class="form-group">

            <label>
                Nome da Categoria
            </label>

            <input
                type="text"
                name="nome"
                required
                value="<?= $categoria['nome'] ?? '' ?>"
            >

        </div>

        <button
            class="btn"
            type="submit"
        >
            Salvar
        </button>

    </form>

</div>