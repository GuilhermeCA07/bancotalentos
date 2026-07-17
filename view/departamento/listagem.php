<div class="topo">
    <h1>Departamentos</h1>
    <a href="?c=departamento&m=add" class="btn">
        <i class="fa-solid fa-plus"></i>
        Novo Departamento
    </a>
</div>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div class="configuracao-alerta sucesso">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($_SESSION['sucesso']) ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<div class="toolbar">
    <div class="busca-container">
        <i class="fa-solid fa-magnifying-glass"></i>
        <form method="GET" class="form-busca">
            <input type="hidden" name="c" value="departamento">
            <input
                type="text"
                name="busca"
                value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                placeholder="Buscar departamento">
            <select name="status">
                <option value="">Todos</option>
                <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Ativos</option>
                <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Inativos</option>
            </select>
            <button type="submit" class="btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                Buscar
            </button>
        </form>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Cor</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($departamentos as $departamento): ?>
            <tr>
                <td><?= htmlspecialchars($departamento['nome']) ?></td>
                <td>
                    <span
                        class="badge-departamento"
                        style="background:<?= htmlspecialchars($departamento['cor']) ?>">
                        <?= htmlspecialchars($departamento['nome']) ?>
                    </span>
                </td>
                <td>
                    <span class="badge-status <?= $departamento['ativo'] ? 'status-ativo' : 'status-inativo' ?>">
                        <?= $departamento['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </span>
                </td>
                <td>
                    <div class="dropdown-acoes">
                        <button type="button" class="btn-dropdown" onclick="toggleDropdown(this)">
                            <i class="fa-solid fa-bars"></i>
                            Ações
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a
                                href="?c=departamento&m=editar&id=<?= (int)$departamento['idDepartamento'] ?>"
                                class="btn-action btn-editar">
                                <i class="fa-solid fa-pen"></i>
                                Editar
                            </a>
                            <a
                                href="?c=departamento&m=alterarStatus&id=<?= (int)$departamento['idDepartamento'] ?>"
                                class="btn-action btn-pausar"
                                onclick="return confirm('Deseja alterar o status deste departamento?')">
                                <i class="fa-solid <?= $departamento['ativo'] ? 'fa-pause' : 'fa-play' ?>"></i>
                                <?= $departamento['ativo'] ? 'Inativar' : 'Ativar' ?>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
