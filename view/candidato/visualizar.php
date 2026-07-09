<link
    rel="stylesheet"
    href="public/css/visualizar.css">
<h1 class="titulo-pagina">
    Perfil do Candidato
</h1>

<div class="card-visualizacao">

    <div class="card-header">

        <h2>
            <?= $candidato['nome'] ?>
        </h2>

    </div>

    <div class="card-body">


        <div class="info-item">
            <span class="label">
                Telefone
            </span>

            <span>
                <?= preg_replace(
                    '/(\d{2})(\d{5})(\d{4})/',
                    '($1) $2-$3',
                    $candidato['telefone']
                ) ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                E-mail
            </span>

            <span>
                <?= $candidato['email'] ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                WhatsApp
            </span>

            <span>
                <?= $candidato['whatsapp']
                    ? 'Sim'
                    : 'Não' ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                Escolaridade
            </span>

            <span>
                <?= !empty($candidato['escolaridade'])
                    ? $candidato['escolaridade']
                    : '-' ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                Estado Civil
            </span>

            <span>
                <?= !empty($candidato['estado_civil'])
                    ? $candidato['estado_civil']
                    : '-' ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                Fumante
            </span>

            <span>
                <?= !empty($candidato['fumante'])
                    ? 'Sim'
                    : 'N&atilde;o' ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                CNH
            </span>

            <span>
                <?= !empty($candidato['cnh'])
                    ? $candidato['cnh']
                    : 'N&atilde;o possui' ?>
            </span>
        </div>

        <div class="info-item">
            <span class="label">
                Observa&ccedil;&otilde;es
            </span>

            <span>
                <?= nl2br(
                    $candidato['observacoes']
                ) ?>
            </span>
        </div>

    </div>

</div>
<div class="card-visualizacao">

    <div class="card-header">

        <h3>
            Habilidades
        </h3>

    </div>

    <div class="card-body">

        <div class="lista-habilidades">
            <?php foreach (
                $habilidades as $habilidade
            ): ?>

                <?php
                $nivel =
                    $habilidade['nivel']
                    ?? $habilidade['nivelHabilidade']
                    ?? $habilidade['nivel_habilidade']
                    ?? '-';
                ?>

                <div class="habilidade-tag">

                    <div>
                        <strong>
                            <?= $habilidade['nomeExibicao'] ?>
                        </strong>
                    </div>

                    <small>
                        Nível <?= $nivel ?>/10
                    </small>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>
<?php if (!empty($candidato['curriculo'])): ?>

    <div class="card-visualizacao">

        <div class="card-header">

            <h3>
                Currículo
            </h3>

        </div>

        <div class="card-body">

            <?php
            $extensao =
                strtolower(
                    pathinfo(
                        $candidato['curriculo'],
                        PATHINFO_EXTENSION
                    )
                );

            ?>

            <a class="btn"
                href="#"
                onclick="
                            abrirCurriculo(
                                <?= $candidato['idCandidato'] ?>,
                                '<?= $extensao ?>'
                            );
                            return false;
                        ">

                <i class="fa-solid fa-file-pdf"></i>

                Abrir Currículo

            </a>

        </div>

    </div>

<?php endif; ?>
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

            imagem.style.display =
                'none';

            imagem.src = '';

            msg.innerHTML = '';
            msg.style.display = 'none';

            iframe.src =
                url;

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
</script>
