<div class="topo">

    <h1>Candidatos</h1>

    <a href="?c=candidato&m=add" class="btn">
        <i class="fa-solid fa-plus" aria-hidden="true"></i>
        Novo Candidato
    </a>

</div>

<div class="toolbar">

    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>

        <form method="GET" id="formFiltros" class="form-busca painel-filtros painel-filtros-candidatos">

            <input
                type="hidden"
                name="c"
                value="candidato">

            <input
                type="text"
                name="busca"
                placeholder="Nome, Telefone ou Email"
                value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">

            <select name="status_candidato">

                <option value="">
                    Todos os Status
                </option>

                <option value="Aguardando Entrevista"
                    <?= ($_GET['status_candidato'] ?? '') == 'Aguardando Entrevista' ? 'selected' : '' ?>>
                    Aguardando Entrevista
                </option>

                <option value="Entrevista Agendada"
                    <?= ($_GET['status_candidato'] ?? '') == 'Entrevista Agendada' ? 'selected' : '' ?>>
                    Entrevista Agendada
                </option>

                <option value="Aprovado"
                    <?= ($_GET['status_candidato'] ?? '') == 'Aprovado' ? 'selected' : '' ?>>
                    Aprovado
                </option>

                <option value="Reprovado"
                    <?= ($_GET['status_candidato'] ?? '') == 'Reprovado' ? 'selected' : '' ?>>
                    Reprovado
                </option>

                <option value="Recusado"
                    <?= ($_GET['status_candidato'] ?? '') == 'Recusado' ? 'selected' : '' ?>>
                    Recusado
                </option>

                <option value="Entrevistado"
                    <?= ($_GET['status_candidato'] ?? '') == 'Entrevistado' ? 'selected' : '' ?>>
                    Entrevistado
                </option>

                <option value="Vaga Preenchida por Contratação"
                    <?= ($_GET['status_candidato'] ?? '') == 'Vaga Preenchida por Contratação' ? 'selected' : '' ?>>
                    Vaga Preenchida por Contratação
                </option>

                <option value="Vaga Fechada"
                    <?= ($_GET['status_candidato'] ?? '') == 'Vaga Fechada' ? 'selected' : '' ?>>
                    Vaga Fechada
                </option>

            </select>

            <select name="escolaridade">

                <option value="">
                    Todas as Escolaridades
                </option>

                <?php foreach ([
                    'Ensino Fundamental Incompleto',
                    'Ensino Fundamental Completo',
                    'Ensino Médio Incompleto',
                    'Ensino Médio Completo',
                    'Ensino Superior Incompleto',
                    'Ensino Superior Completo',
                    'Pós-graduação'
                ] as $opcao): ?>

                    <option
                        value="<?= $opcao ?>"
                        <?= ($_GET['escolaridade'] ?? '') == $opcao ? 'selected' : '' ?>>
                        <?= $opcao ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <select name="estado_civil">

                <option value="">
                    Todos os Estados Civis
                </option>

                <?php foreach ([
                    'Solteiro(a)',
                    'Casado(a)',
                    'Divorciado(a)',
                    'Viúvo(a)',
                    'União Estável'
                ] as $opcao): ?>

                    <option
                        value="<?= $opcao ?>"
                        <?= ($_GET['estado_civil'] ?? '') == $opcao ? 'selected' : '' ?>>
                        <?= $opcao ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <select name="fumante">

                <option value="">
                    Fumante?
                </option>

                <option value="1"
                    <?= ($_GET['fumante'] ?? '') === '1' ? 'selected' : '' ?>>
                    Sim
                </option>

                <option value="0"
                    <?= ($_GET['fumante'] ?? '') === '0' ? 'selected' : '' ?>>
                    N&atilde;o
                </option>

            </select>

            <select name="cnh">

                <option value="">
                    Todas as CNHs
                </option>

                <?php foreach ([
                    'A',
                    'B',
                    'AB',
                    'AC',
                    'AD',
                    'AE',
                    'C',
                    'D',
                    'E'
                ] as $opcao): ?>

                    <option
                        value="<?= $opcao ?>"
                        <?= ($_GET['cnh'] ?? '') == $opcao ? 'selected' : '' ?>>
                        <?= $opcao ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <div class="linha-secundaria-filtros">

            <div class="grupo-filtro">

                <label for="data_inicial">
                    Atualização de
                </label>

                <input
                    type="date"
                    id="data_inicial"
                    name="data_inicial"
                    value="<?= htmlspecialchars($filtros['data_inicial'] ?? '') ?>">

            </div>

            <div class="intervalo-seta" aria-hidden="true">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            <div class="grupo-filtro">

                <label for="data_final">
                    até
                </label>

                <input
                    type="date"
                    id="data_final"
                    name="data_final"
                    value="<?= htmlspecialchars($filtros['data_final'] ?? '') ?>">

            </div>

            <div class="filtros-avancados">

                <button
                    type="button"
                    class="btn-filtro"
                    onclick="abrirModalCategorias()">
                    <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                    Categorias

                </button>

                <button
                    type="button"
                    class="btn-filtro"
                    onclick="abrirModalHabilidades()">
                    <i class="fa-solid fa-star" aria-hidden="true"></i>
                    Habilidades

                </button>

            </div>

            <!-- ### MODEL DE VIEW DO CURRICULO ###  -->
            <div id="modalCurriculo" class="modal-curriculo">

                <div class="modal-curriculo-content">

                    <div class="modal-curriculo-header">

                        <h3>
                            Currículo
                        </h3>

                        <button
                            type="button"
                            onclick="fecharCurriculo()">
                            ×
                        </button>

                    </div>

                    <div id="curriculoMensagem">

                    </div>

                    <iframe
                        id="iframeCurriculo"
                        style="
                width:100%;
                height:600px;
                display:none;
            ">
                    </iframe>

                    <img
                        id="imagemCurriculo"
                        class="imagem-curriculo"
                        alt="Visualização do currículo">

                    <div class="modal-curriculo-footer">

                        <a
                            id="btnDownloadCurriculo"
                            class="btn">

                            Baixar Currículo

                        </a>

                    </div>

                </div>

            </div>

            <button
                type="submit"
                class="btn">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Filtrar
            </button>

            </div>

        </form>

        <!-- ### MODAL DE VIEW DAS HABILIDADES ### --->

        <div
            id="modalHabilidades"
            class="modal-filtro">

            <div class="modal-filtro-content">

                <button
                    type="button"
                    class="btn-fechar-modal"
                    onclick="fecharModalHabilidades()">

                    &times;

                </button>

                <h3>Filtrar por Habilidades</h3>

                <input
                    type="text"
                    id="pesquisaHabilidade"
                    placeholder="Pesquisar habilidade...">

                <div class="lista-filtros">

                    <?php foreach ($habilidades as $habilidade): ?>

                        <label
                            class="item-filtro habilidade-item">

                            <input
                                type="checkbox"
                                form="formFiltros"
                                name="habilidade[]"
                                value="<?= $habilidade['idHabilidade'] ?>"

                                <?= in_array(
                                    $habilidade['idHabilidade'],
                                    $_GET['habilidade'] ?? []
                                ) ? 'checked' : '' ?>>

                            <strong>
                                <?= $habilidade['categoria'] ?>
                            </strong>

                            →

                            <?= $habilidade['nome'] ?>

                        </label>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <!-- ### MODAL DE VIEW DAS CATEGORIAS ### --->

        <div
            id="modalCategorias"
            class="modal-filtro">

            <div class="modal-filtro-content">

                <button
                    type="button"
                    class="btn-fechar-modal"
                    onclick="fecharModalCategorias()">

                    &times;

                </button>

                <h3>Filtrar por Categorias</h3>

                <input
                    type="text"
                    id="pesquisaCategoria"
                    placeholder="Pesquisar categoria...">

                <div class="lista-filtros">

                    <?php foreach ($categorias as $categoria): ?>

                        <label class="item-filtro categoria-item">

                            <input
                                type="checkbox"
                                form="formFiltros"
                                name="categoria[]"
                                value="<?= $categoria['idCategoria'] ?>"

                                <?= in_array(
                                    $categoria['idCategoria'],
                                    $_GET['categoria'] ?? []
                                ) ? 'checked' : '' ?>>

                            <?= $categoria['nome'] ?>

                        </label>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<table>


    <thead>

        <tr>
            <th>Nome</th>
            <th class="col-telefone">Telefone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Última Atualização</th>
            <th>Curriculo</th>
            <th class="col-acoes">Ações</th>
        </tr>

    </thead>

    <tbody>

        <?php foreach ($candidatos as $candidato): ?>

            <tr>

                <td>
                    <a
                        class="link-candidato"
                        href="?c=candidato&m=visualizar&id=<?= $candidato['idCandidato'] ?>">

                        <i class="fa-solid fa-user"></i>

                        <?= $candidato['nome'] ?>

                    </a>
                </td>

                <td class="col-telefone"><?= formatarTelefone($candidato['telefone']) ?></td>

                <td><?= $candidato['email'] ?></td>

                <td>

                    <?php

                    $status =
                        $candidato['status_exibicao']
                        ?? $candidato['status_candidato'];

                    ?>

                    <?php
                    $classeStatus = strtolower(
                        str_replace(' ', '-', $status)
                    );
                    ?>

                    <?php if (
                        in_array(
                            $status,
                            ['Recusado', 'Entrevistado', 'Aprovado', 'Contratado'],
                            true
                        )
                        && !empty($candidato['id_candidatura_status'])
                    ): ?>
                        <button
                            type="button"
                            class="status-candidato <?= $classeStatus ?> badge-detalhes-interativo"
                            data-detalhes-id="<?= (int)$candidato['id_candidatura_status'] ?>"
                            data-resultado="<?= htmlspecialchars($status) ?>"
                            title="Ver detalhes da entrevista">
                            <?= htmlspecialchars($status) ?>
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                    <?php else: ?>
                        <span class="status-candidato <?= $classeStatus ?>">
                            <?= htmlspecialchars($status) ?>
                        </span>
                    <?php endif; ?>

                </td>

                <td>

                    <?php

                    if (!empty($candidato['ultima_atualizacao'])) {

                        echo date(

                            "d/m/Y",

                            strtotime(
                                $candidato['ultima_atualizacao']
                            )

                        );
                    } else {

                        echo "-";
                    }

                    ?>

                </td>

                <td>

                    <?php
                    $extensao =
                        strtolower(
                            pathinfo(
                                $candidato['curriculo'],
                                PATHINFO_EXTENSION
                            )
                        );

                    ?>

                    <?php if (!empty($candidato['curriculo'])): ?>

                        <a class="btn"
                            href="#"
                            onclick="
                            abrirCurriculo(
                                <?= $candidato['idCandidato'] ?>,
                                '<?= $extensao ?>'
                            );
                            return false;
                        ">

                            <i class="fa-solid fa-file"></i>

                            Currículo

                        </a>

                    <?php endif; ?>

                </td>

                <td class="acoes-coluna">

                    <div class="dropdown-acoes">

                        <button
                            type="button"
                            class="btn-dropdown col-acoes"
                            onclick="toggleDropdown(this)">

                            <i class="fa-solid fa-bars"></i>

                            Ações

                            <i class="fa-solid fa-chevron-down"></i>

                        </button>

                        <div class="dropdown-menu">

                            <a
                                href="?c=candidato&m=editar&id=<?= $candidato['idCandidato'] ?>"
                                class="btn-action btn-editar">
                                <i class="fa-solid fa-pen"></i>
                                Editar
                            </a>

                            <a
                                href="?c=candidato&m=visualizar&id=<?= $candidato['idCandidato'] ?>"
                                class="btn-action btn-editar">
                                <i class="fa-solid fa-eye"></i>
                                Visualizar
                            </a>


<?php if (podeExcluir()): ?>
                            <a
                                href="?c=candidato&m=excluir&id=<?= $candidato['idCandidato'] ?>"
                                class="btn-action btn-excluir"
                                onclick="return confirm('Deseja realmente excluir?')">
                                <i class="fa-solid fa-trash"></i>
                                Excluir
                            </a>
<?php endif; ?>
                        </div>
                    </div>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<script>
    function abrirCurriculo(
        id,
        extensao
    ) {
        const iframe =
            document.getElementById(
                'iframeCurriculo'
            );

        const msg =
            document.getElementById(
                'curriculoMensagem'
            );

        const imagem =
            document.getElementById(
                'imagemCurriculo'
            );

        const extensaoNormalizada =
            extensao.toLowerCase();

        const url =
            '?c=candidato&m=visualizarCurriculo&id=' +
            id;

        if (
            extensaoNormalizada ===
            'pdf'
        ) {

            iframe.style.display =
                'block';

            msg.innerHTML = '';

            imagem.style.display =
                'none';

            imagem.src = '';

            iframe.src =
                url;

            msg.style.display = 'none';

        } else if (
            [
                'jpg',
                'jpeg',
                'png'
            ].includes(extensaoNormalizada)
        ) {

            iframe.style.display =
                'none';

            iframe.src = '';

            imagem.style.display =
                'block';

            imagem.src =
                url;

            msg.innerHTML = '';
            msg.style.display = 'none';

        } else {

            iframe.style.display =
                'none';

            iframe.src = '';

            imagem.style.display =
                'none';

            imagem.src = '';

            msg.style.display = 'flex';

            msg.innerHTML =
                '<p>Visualização disponível apenas para PDF.</p>';
        }

        document
            .getElementById(
                'btnDownloadCurriculo'
            )
            .href =
            '?c=candidato&m=baixarCurriculo&id=' +
            id;

        document
            .getElementById(
                'modalCurriculo'
            )
            .style.display =
            'flex';
    }

    function fecharCurriculo() {
        document
            .getElementById(
                'modalCurriculo'
            )
            .style.display =
            'none';

        document
            .getElementById(
                'iframeCurriculo'
            )
            .src = '';

        document
            .getElementById(
                'imagemCurriculo'
            )
            .src = '';
    }

    function abrirModalCategorias() {

        document
            .getElementById(
                "modalCategorias"
            )
            .style.display = "flex";
    }

    function abrirModalHabilidades() {

        document
            .getElementById(
                "modalHabilidades"
            )
            .style.display =
            "flex";
    }



    function fecharModalCategorias() {

        document
            .getElementById(
                "modalCategorias"
            )
            .style.display = "none";
    }

    function fecharModalHabilidades() {

        document
            .getElementById(
                "modalHabilidades"
            )
            .style.display =
            "none";
    }

    window.addEventListener(
        "click",
        function(e) {

            const modalHabilidades =
                document.getElementById(
                    "modalHabilidades"
                );

            if (e.target === modalHabilidades) {

                fecharModalHabilidades();
            }

        }
    );

    window.addEventListener(
        "click",
        function(e) {

            const modalCategorias =
                document.getElementById(
                    "modalCategorias"
                );

            if (e.target === modalCategorias) {

                fecharModalCategorias();
            }

        }
    );
    const campoPesquisaHabilidade =
        document.getElementById(
            'pesquisaHabilidade'
        );

    if (campoPesquisaHabilidade) {

        campoPesquisaHabilidade.addEventListener(
            'keyup',
            function() {

                const termo =
                    this.value
                    .toLowerCase();

                document
                    .querySelectorAll(
                        '.habilidade-item'
                    )
                    .forEach(item => {

                        const texto =
                            item.textContent
                            .toLowerCase();

                        item.style.display =
                            texto.includes(termo) ?
                            'flex' :
                            'none';
                    });
            }
        );
    }

    const campoPesquisaCategoria =
        document.getElementById(
            'pesquisaCategoria'
        );

    if (campoPesquisaCategoria) {

        campoPesquisaCategoria.addEventListener(
            'keyup',
            function() {

                const termo =
                    this.value
                    .toLowerCase();

                document
                    .querySelectorAll(
                        '.categoria-item'
                    )
                    .forEach(item => {

                        const texto =
                            item.textContent
                            .toLowerCase();

                        item.style.display =
                            texto.includes(termo) ?
                            'flex' :
                            'none';
                    });
            }
        );
    }
</script>
<script src="public/js/pesquisa.js"></script>
<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
