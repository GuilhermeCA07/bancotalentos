<?php

$id = $candidato['idCandidato'] ?? '';
$nome = $candidato['nome'] ?? '';
$telefone = $candidato['telefone'] ?? '';
$email = $candidato['email'] ?? '';
$linkedin = $candidato['linkedin'] ?? '';
$escolaridade = $candidato['escolaridade'] ?? '';
$estadoCivil = $candidato['estado_civil'] ?? '';
$fumante = $candidato['fumante'] ?? 0;
$cnh = $candidato['cnh'] ?? '';
$observacoes = $candidato['observacoes'] ?? '';
$whatsapp = $candidato['whatsapp'] ?? 0;

?>

<div class="topo">
    <div>
        <span class="pagina-eyebrow">Gestão de candidatos</span>
        <h1>
            <?= empty($id)
                ? 'Novo Candidato'
                : 'Editar Candidato'
            ?>
        </h1>
    </div>
</div>

<form
    action="?c=candidato&m=salvar"
    method="post" class="form-card" enctype="multipart/form-data">

    <input
        type="hidden"
        name="idCandidato"
        value="<?= $id ?>">

    <section class="form-section">
        <div class="form-section-header">
            <i class="fa-solid fa-address-card" aria-hidden="true"></i>
            <h2>Dados pessoais</h2>
        </div>

    <div class="form-grid form-grid-3">

        <div class="form-group">

            <label for="nomeCandidato">Nome</label>

            <input
                type="text"
                id="nomeCandidato"
                name="nome"
                value="<?= htmlspecialchars($nome) ?>"
                required>

        </div>

        <div class="form-group">

            <label for="telefone">Telefone</label>

            <input
                id="telefone"
                type="text"
                name="telefone"
                value="<?= htmlspecialchars($telefone) ?>"
                required>

        </div>

        <div class="form-group checkbox checkbox-campo">

            <input
                type="checkbox"
                name="whatsapp"
                <?= $whatsapp ? 'checked' : '' ?>>

            <label>Este número possui WhatsApp</label>

        </div>
    </div>

    <div class="form-grid">
        <div class="form-group">
            <label for="emailCandidato">E-mail</label>
            <input
                id="emailCandidato"
                type="email"
                name="email"
                autocomplete="email"
                value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="form-group">
            <label for="linkedinCandidato">LinkedIn</label>
            <div class="input-com-icone">
                <i class="fa-brands fa-linkedin" aria-hidden="true"></i>
                <input
                    id="linkedinCandidato"
                    type="url"
                    name="linkedin"
                    maxlength="255"
                    autocomplete="url"
                    placeholder="https://www.linkedin.com/in/seu-perfil"
                    pattern="https://([A-Za-z0-9\-]+\.)*linkedin\.com/in/[A-Za-z0-9._%~\-]+/?"
                    title="Informe um link de perfil do LinkedIn usando HTTPS."
                    value="<?= htmlspecialchars($linkedin) ?>">
            </div>
        </div>
    </div>

        <div class="form-grid form-grid-4">

            <div class="form-group">

                <label>Escolaridade</label>

                <select name="escolaridade">

                    <option value="">
                        Selecione...
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
                            <?= $escolaridade == $opcao ? 'selected' : '' ?>>
                            <?= $opcao ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>Estado Civil</label>

                <select name="estado_civil">

                    <option value="">
                        Selecione...
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
                            <?= $estadoCivil == $opcao ? 'selected' : '' ?>>
                            <?= $opcao ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group checkbox checkbox-campo">

                <input
                    type="checkbox"
                    name="fumante"
                    value="1"
                    <?= $fumante ? 'checked' : '' ?>>

                <label>Candidato fumante</label>

            </div>

            <div class="form-group">

                <label>CNH</label>

                <select name="cnh">

                    <option value="">
                        Não possui
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
                            <?= $cnh == $opcao ? 'selected' : '' ?>>
                            <?= $opcao ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <div class="form-group campo-span-2">
            <label for="curriculoCandidato">
                Currículo
            </label>

            <input
                id="curriculoCandidato"
                type="file"
                name="curriculo"
                accept="
            .pdf,
            .doc,
            .docx,
            .jpg,
            .jpeg,
            .png
        ">
            <div class="curriculo">
                <?php if (
                    isset($candidato['curriculo']) &&
                    !empty($candidato['curriculo'])
                ): ?>

                    <input
                        type="hidden"
                        name="curriculo_atual"
                        value="<?= $candidato['curriculo'] ?>">

                    <p style="margin-top:10px;">
                        Currículo atual:

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
                        "><i class="fa-solid fa-file"></i>

                            Currículo

                        </a>
                    </p>

                <?php endif; ?>
            </div>
        </div>



    </section>

    <section class="form-section">
        <div class="form-section-header">
            <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
            <h2>Informações complementares</h2>
        </div>

    <div class="form-group">

        <label>Observações</label>

        <textarea
            name="observacoes"
            rows="5"><?= htmlspecialchars($observacoes) ?></textarea>

    </div>

    </section>

    <section class="form-section">
        <div class="form-section-header">
            <i class="fa-solid fa-star" aria-hidden="true"></i>
            <h2>Habilidades</h2>
        </div>

        <div class="habilidades-container">

            <div class="form-group">

                <label>Categoria</label>

                <select id="categoria">
                    <option value="">
                        Selecione...
                    </option>
                    <option value="Personalizada">
                        Personalizada
                    </option>
                </select>

            </div>

            <div class="form-group">

                <label>Habilidade</label>

                <select id="habilidade">

                    <option value="">
                        Selecione...
                    </option>

                </select>

            </div>

            <div
                class="form-group"
                id="grupoDescricaoPersonalizada"
                style="display:none;">

                <label>
                    Descrição da Habilidade
                </label>

                <input
                    type="text"
                    id="descricaoPersonalizada"
                    placeholder="Digite a habilidade">

            </div>

            <div class="form-group">

                <div class="nivel-header">

                    <label>Nível</label>

                    <span id="valorNivel">
                        5
                    </span>

                </div>

                <input
                    type="range"
                    id="nivel"
                    min="0"
                    max="10"
                    value="5">

            </div>

            <button
                type="button"
                class="btn"
                onclick="adicionarHabilidade()">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Adicionar Habilidade
            </button>

        </div>

        <br>

        <h3>Habilidades Adicionadas</h3>

        <div id="listaHabilidades"></div>

        <div id="inputsOcultos"></div>

    </section>

    <div class="form-actions">
        <a class="btn-secundario" href="?c=candidato">
            Cancelar
        </a>
        <button class="btn">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            Salvar candidato
        </button>
    </div>

    <div id="teste"></div>

    <script>
        const habilidades =
            <?= json_encode($habilidades) ?>;

        let habilidadesSelecionadas =
            <?= json_encode(
                $habilidadesSelecionadas ?? []
            ) ?>;
        document
            .getElementById("telefone")
            .addEventListener(
                "input",
                function(e) {
                    let v =
                        e.target.value
                        .replace(/\D/g, '');

                    if (v.length > 11) {
                        v = v.substring(0, 11);
                    }

                    if (v.length > 10) {
                        v = v.replace(
                            /^(\d{2})(\d{5})(\d{4}).*/,
                            '($1) $2-$3'
                        );
                    } else {
                        v = v.replace(
                            /^(\d{2})(\d{4})(\d{0,4}).*/,
                            '($1) $2-$3'
                        );
                    }

                    e.target.value = v;
                }
            );
    </script>

    <script src="public/js/habilidades.js"></script>

    <?php if (isset($candidato)): ?>

        <input
            type="hidden"
            name="curriculo_atual"
            value="<?= $candidato['curriculo'] ?>">

    <?php endif; ?>

</form>
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
