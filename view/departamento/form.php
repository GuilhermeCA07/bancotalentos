<div class="topo">
    <h1><?= isset($departamento) ? 'Editar Departamento' : 'Novo Departamento' ?></h1>
</div>

<div class="form-card">
    <form method="POST" action="?c=departamento&m=salvar">
        <input
            type="hidden"
            name="idDepartamento"
            value="<?= (int)($departamento['idDepartamento'] ?? 0) ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="nomeDepartamento">Nome</label>
                <input
                    id="nomeDepartamento"
                    type="text"
                    name="nome"
                    maxlength="100"
                    required
                    value="<?= htmlspecialchars($departamento['nome'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="corDepartamento">Cor do badge</label>
                <input
                    id="corDepartamento"
                    type="color"
                    name="cor"
                    required
                    value="<?= htmlspecialchars($departamento['cor'] ?? '#7C3AED') ?>">
            </div>
        </div>

        <div class="form-acoes">
            <a class="btn btn-secundario" href="?c=departamento">Cancelar</a>
            <button class="btn" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                Salvar
            </button>
        </div>
    </form>
</div>
