<link rel="stylesheet" href="public/css/decisao.css">
<div class="topo">
    <div>
        <span class="pagina-eyebrow">Recrutamento</span>
        <h1>Centro de Decisões</h1>
    </div>
</div>
<div class="toolbar">

    <form
        method="GET"
        class="form-busca painel-filtros painel-filtros-decisao">

        <input
            type="hidden"
            name="c"
            value="decisao">

        <div class="campo-filtro campo-filtro-busca">
            <label for="buscaDecisao">Buscar</label>
            <div class="entrada-com-icone">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input id="buscaDecisao" type="text" name="busca"
                    placeholder="Candidato, vaga ou responsável"
                    value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
            </div>
        </div>

        <div class="campo-filtro campo-filtro-status">
            <label for="statusDecisao">Status</label>
            <select id="statusDecisao" name="status">

            <option value="">
                Todos
            </option>

            <option value="Aprovado"
                <?= ($_GET['status'] ?? '') === 'Aprovado' ? 'selected' : '' ?>>
                Aprovados
            </option>

            <option value="Recusado"
                <?= ($_GET['status'] ?? '') === 'Recusado' ? 'selected' : '' ?>>
                Recusados
            </option>

            <option value="Entrevistado"
                <?= ($_GET['status'] ?? '') === 'Entrevistado' ? 'selected' : '' ?>>
                Entrevistados
            </option>

            <option value="Contratado"
                <?= ($_GET['status'] ?? '') === 'Contratado' ? 'selected' : '' ?>>
                Contratados
            </option>

            <option value="Dispensado"
                <?= ($_GET['status'] ?? '') === 'Dispensado' ? 'selected' : '' ?>>
                Dispensados
            </option>

            <option value="Auto-Dispensa"
                <?= ($_GET['status'] ?? '') === 'Auto-Dispensa' ? 'selected' : '' ?>>
                Auto-Dispensas
            </option>

            </select>
        </div>

        <div class="campo-filtro">
            <label for="responsavelDecisao">Responsável</label>
            <input id="responsavelDecisao" type="text" name="responsavel"
                placeholder="Nome do responsável"
                value="<?= htmlspecialchars($_GET['responsavel'] ?? '') ?>">
        </div>

        <div class="grupo-intervalo">
            <div class="campo-filtro">
                <label for="dataInicioDecisao">Data inicial</label>
                <input id="dataInicioDecisao" type="date" name="data_inicio"
                    value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
            </div>
            <div class="intervalo-seta" aria-hidden="true"><i class="fa-solid fa-arrow-right"></i></div>
            <div class="campo-filtro">
                <label for="dataFimDecisao">Data final</label>
                <input id="dataFimDecisao" type="date" name="data_fim"
                    value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
            </div>
        </div>

        <button
            type="submit"
            class="btn btn-filtrar">
            <i class="fa-solid fa-filter" aria-hidden="true"></i>
            Filtrar

        </button>

    </form>

</div>

<table>

    <thead>

        <tr>

            <th>Candidato</th>

            <th>Vaga</th>

            <th>Status</th>

            <th>Entrevista</th>

            <th>Responsável</th>

            <th>Ações</th>

        </tr>

    </thead>

    <tbody>

        <?php foreach (
            $registros as $r
        ): ?>

            <tr>

                <td>

                    <?= $r['nome'] ?>

                </td>

                <td>

                    <?= $r['titulo'] ?>

                </td>

                <td>

                    <?php
                    $classeStatus = strtolower(
                        str_replace(' ', '-', $r['status_exibicao'])
                    );
                    ?>

                    <?php if (in_array(
                        $r['status_exibicao'],
                        ['Recusado', 'Entrevistado', 'Aprovado', 'Contratado'],
                        true
                    )): ?>
                        <button
                            type="button"
                            class="badge-status <?= $classeStatus ?> badge-detalhes-interativo"
                            data-detalhes-id="<?= (int)$r['idCandidatura'] ?>"
                            data-resultado="<?= htmlspecialchars($r['status_exibicao']) ?>"
                            title="Ver detalhes da entrevista">
                            <?= htmlspecialchars($r['status_exibicao']) ?>
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                    <?php else: ?>
                        <span class="badge-status <?= $classeStatus ?>">
                            <?= htmlspecialchars($r['status_exibicao']) ?>
                        </span>
                    <?php endif; ?>

                </td>

                <td>

                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $r['data_entrevista']
                        )
                    ) ?>

                </td>

                <td>

                    <?= $r['responsavel'] ?>

                </td>

                <td>

                    <a
                        href="?c=decisao&m=visualizar&id=<?= $r['idCandidatura'] ?>"
                        class="btn-detalhes-decisao">

                        <i class="fa-solid fa-eye"></i>
                        Ver Detalhes

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>
<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
