<link rel="stylesheet" href="public/css/log.css">

<div class="topo">

    <h1>Logs do Sistema</h1>

</div>

<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="log">

            <input
                type="text"
                name="busca"
                placeholder="Buscar usuario, modulo ou acao"
                value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">

            <input
                type="text"
                name="usuario"
                placeholder="Usuario"
                value="<?= htmlspecialchars($_GET['usuario'] ?? '') ?>">

            <select name="modulo">
                <option value="">Todos os modulos</option>

                <?php foreach ([
                    'Candidato',
                    'Candidatura',
                    'Categoria',
                    'Chamada',
                    'Contratacao',
                    'Dashboard',
                    'Decisao',
                    'Entrevista',
                    'Habilidade',
                    'Usuario',
                    'Vaga'
                ] as $modulo): ?>

                    <option
                        value="<?= $modulo ?>"
                        <?= ($_GET['modulo'] ?? '') == $modulo ? 'selected' : '' ?>>
                        <?= $modulo ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <select name="acao">
                <option value="">Todas as acoes</option>

                <?php foreach ([
                    'Acesso',
                    'Acao',
                    'Cadastro/Atualizacao',
                    'Atualizacao',
                    'Exclusao',
                    'Login',
                    'Logout'
                ] as $acao): ?>

                    <option
                        value="<?= $acao ?>"
                        <?= ($_GET['acao'] ?? '') == $acao ? 'selected' : '' ?>>
                        <?= $acao ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <div class="grupo-filtro">
                <label for="data_inicio">
                    De
                </label>

                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
            </div>

            <div class="grupo-filtro">
                <label for="data_fim">
                    Ate
                </label>

                <input
                    type="date"
                    id="data_fim"
                    name="data_fim"
                    value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
            </div>

            <button
                type="submit"
                class="btn">
                Filtrar
            </button>

        </form>

    </div>

</div>

<table class="log-table">
    <thead>
        <tr>
            <th>Data/Hora</th>
            <th>Usuario</th>
            <th>Perfil</th>
            <th>Modulo</th>
            <th>Acao</th>
            <th>Descricao</th>
            <th class="col-acoes">Acoes</th>
        </tr>
    </thead>

    <tbody>
        <?php if (!empty($logs)): ?>

            <?php foreach ($logs as $log): ?>

                <tr>
                    <td>
                        <?= date(
                            'd/m/Y H:i:s',
                            strtotime($log['criado_em'])
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($log['usuario_nome']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($log['usuario_perfil']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($log['modulo']) ?>
                    </td>

                    <td>
                        <span class="log-badge log-badge-<?= strtolower(
                            str_replace(
                                ['/', ' '],
                                '-',
                                $log['acao']
                            )
                        ) ?>">
                            <?= htmlspecialchars($log['acao']) ?>
                        </span>
                    </td>

                    <td class="log-descricao">
                        <?= htmlspecialchars($log['descricao']) ?>
                    </td>

                    <td class="acoes-coluna">
                        <a
                            href="?c=log&m=visualizar&id=<?= $log['idLog'] ?>"
                            class="btn-log-detalhes">
                            <i class="fa-solid fa-eye"></i>
                            Ver
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="7">
                    Nenhum log encontrado.
                </td>
            </tr>

        <?php endif; ?>
    </tbody>
</table>
