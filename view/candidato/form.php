<?php

$id = $candidato['idCandidato'] ?? '';
$nome = $candidato['nome'] ?? '';
$telefone = $candidato['telefone'] ?? '';
$email = $candidato['email'] ?? '';
$observacoes = $candidato['observacoes'] ?? '';
$whatsapp = $candidato['whatsapp'] ?? 0;

?>

<h1>

    <?= empty($id)
        ? 'Novo Candidato'
        : 'Editar Candidato'
    ?>

</h1>

<form
    action="?c=candidato&m=salvar"
    method="post" class="form-card" enctype="multipart/form-data">

    <input
        type="hidden"
        name="idCandidato"
        value="<?= $id ?>">

    <div class="form-grid">

        <div class="form-group">

            <label>Nome</label>

            <input
                type="text"
                name="nome"
                value="<?= $nome ?>"
                required>

        </div>

        <div class="form-group">

            <label>Telefone</label>

            <input
                id="telefone"
                type="text"
                name="telefone"
                value="<?= $telefone ?>"
                required>

        </div>

        <div class="form-group checkbox">

            <input
                type="checkbox"
                name="whatsapp"
                <?= $whatsapp ? 'checked' : '' ?>>

            <label>É WhatsApp</label>

        </div>
    </div>

    <div class="form-group">

        <label>Email</label>

        <input
            type="email"
            name="email"
            value="<?= $email ?>">
        <div class="form-group">
            <br>
            <label>
                Currículo
            </label>

            <input
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



    </div>

    <div class="form-group">

        <label>Observações</label>

        <textarea
            name="observacoes"
            rows="5"><?= $observacoes ?></textarea>

        <h2>Habilidades</h2>

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

            <div
                class="form-group"
                id="grupoDescricao"
                style="display:none;">

                <label>Descrição da Habilidade</label>

                <input
                    type="text"
                    id="descricaoPersonalizada">

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
                Adicionar Habilidade
            </button>

        </div>

        <br>

        <h3>Habilidades Adicionadas</h3>

        <div id="listaHabilidades"></div>

        <div id="inputsOcultos"></div>

    </div>

    <button class="btn">

        Salvar

    </button>

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

        if (
            extensao.toLowerCase() ===
            'pdf'
        ) {

            iframe.style.display =
                'block';

            msg.innerHTML = '';

            iframe.src =
                '?c=candidato&m=visualizarCurriculo&id=' +
                id;

        } else {

            iframe.style.display =
                'none';

            iframe.src = '';

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
    }
</script>