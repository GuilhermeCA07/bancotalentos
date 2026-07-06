<link rel="stylesheet" href="public/css/decisao.css">
<div class="page-header">

    <h1>
        Centro de Decisões
    </h1>
</div>
<br>
<div class="decisao-busca">

    <form
        method="GET"
        class="decisao-form">

        <input
            type="hidden"
            name="c"
            value="decisao">

        <input
            type="text"
            name="busca"
            placeholder="Buscar candidato, vaga ou responsável"
            value="<?= $_GET['busca'] ?? '' ?>">

        <select name="status">

            <option value="">
                Todos
            </option>

            <option value="Aprovado">
                Aprovados
            </option>

            <option value="Recusado">
                Recusados
            </option>

            <option value="Contratado">
                Contratados
            </option>

            <option value="Dispensado">
                Dispensados
            </option>

            <option value="Auto-Dispensa">
                Auto-Dispensas
            </option>

        </select>

        <input
            type="text"
            name="responsavel"
            placeholder="Responsável"
            value="<?= $_GET['responsavel'] ?? '' ?>">

        <input
            type="date"
            name="data_inicio"
            value="<?= $_GET['data_inicio'] ?? '' ?>">

        <span>até</span>

        <input
            type="date"
            name="data_fim"
            value="<?= $_GET['data_fim'] ?? '' ?>">

        <button
            type="submit"
            class="btn-filtrar-decisao">

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

                    <span class="
                        badge-status
                        <?= strtolower(
                            str_replace(
                                ' ',
                                '-',
                                $r['status_exibicao']
                            )
                        ) ?>
                        ">
                        <?= $r['status_exibicao'] ?>
                    </span>

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