<div class="form-card">

    <h2>

        <?= isset($vaga)
            ? "Editar Vaga"
            : "Nova Vaga" ?>

    </h2>

    <br>

    <form
        method="post"
        action="?c=vaga&m=salvar">

        <input
            type="hidden"
            name="idVaga"
            value="<?= $vaga['idVaga'] ?? '' ?>">

        <div class="form-grid">

            <div class="form-group">

                <label>
                    Título da Vaga
                </label>

                <input
                    type="text"
                    name="titulo"
                    required
                    value="<?= $vaga['titulo'] ?? '' ?>">

            </div>

            <div class="form-group">

                <label>
                    Departamento
                </label>

                <select
                    name="departamento_id"
                    required>

                    <option value="">
                        Selecione
                    </option>

                    <?php foreach ($departamentos as $departamento): ?>

                        <option
                            value="<?= (int)$departamento['idDepartamento'] ?>"
                            <?= ((int)($vaga['departamento_id'] ?? 0)
                                === (int)$departamento['idDepartamento'])
                                ? 'selected'
                                : '' ?>>
                            <?= htmlspecialchars($departamento['nome']) ?>
                            <?= !$departamento['ativo'] ? ' (inativo)' : '' ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Cidade
                </label>

                <input
                    type="text"
                    name="cidade"
                    value="<?= $vaga['cidade'] ?? '' ?>">

            </div>

            <div class="form-group">

                <label>
                    Quantidade de Vagas
                </label>

                <input
                    type="number"
                    min="1"
                    name="quantidade_vagas"
                    value="<?= $vaga['quantidade_vagas'] ?? 1 ?>">

            </div>

            <div class="form-group">

                <label>
                    Modalidade
                </label>

                <select
                    name="modalidade"
                    required>

                    <option value="">
                        Selecione
                    </option>

                    <?php
                    $modalidades = [
                        "Presencial",
                        "Híbrido",
                        "Remoto"
                    ];

                    foreach ($modalidades as $m):
                    ?>

                        <option
                            value="<?= $m ?>"
                            <?= (($vaga['modalidade'] ?? '') == $m)
                                ? 'selected'
                                : '' ?>>
                            <?= $m ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Escala
                </label>

                <select name="escala">

                    <option value="">
                        Selecione
                    </option>

                    <option
                        value="Horário Comercial"
                        <?= ($vaga['escala'] ?? '') == 'Horário Comercial'
                            ? 'selected'
                            : '' ?>>

                        Horário Comercial

                    </option>

                    <option
                        value="12x36"
                        <?= ($vaga['escala'] ?? '') == '12x36'
                            ? 'selected'
                            : '' ?>>

                        12x36

                    </option>

                    <option
                        value="6x1"
                        <?= ($vaga['escala'] ?? '') == '6x1'
                            ? 'selected'
                            : '' ?>>

                        6x1

                    </option>

                    <option
                        value="5x2"
                        <?= ($vaga['escala'] ?? '') == '5x2'
                            ? 'selected'
                            : '' ?>>

                        5x2

                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Tipo de Contratação
                </label>

                <select
                    name="tipo_contratacao"
                    required>

                    <option value="">
                        Selecione
                    </option>

                    <?php

                    $tipos = [
                        "CLT",
                        "PJ",
                        "Estágio",
                        "Aprendiz"
                    ];

                    foreach ($tipos as $tipo):

                    ?>

                        <option
                            value="<?= $tipo ?>"
                            <?= (($vaga['tipo_contratacao'] ?? '') == $tipo)
                                ? 'selected'
                                : '' ?>>
                            <?= $tipo ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Salário
                </label>

                <input
                    type="text"
                    id="salario"
                    name="salario"
                    value="<?= isset($vaga['salario'])
                                ? number_format($vaga['salario'], 2, ',', '.')
                                : '' ?>">

            </div>

            <div class="form-group">

                <label>
                    CNH Obrigatória
                </label>

                <select
                    name="cnh_obrigatoria">

                    <option value="0">
                        Não
                    </option>

                    <option
                        value="1"
                        <?= isset($vaga) && $vaga['cnh_obrigatoria'] == 1 ? 'selected' : '' ?>>
                        Sim
                    </option>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Status
                </label>

                <select
                    name="status">

                    <?php

                    $status = [
                        "Aberta",
                        "Pausada",
                        "Fechada"
                    ];

                    foreach ($status as $s):

                    ?>

                        <option
                            value="<?= $s ?>"
                            <?= (($vaga['status'] ?? 'Aberta') == $s)
                                ? 'selected'
                                : '' ?>>
                            <?= $s ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <div class="form-group">

            <label>
                Descrição
            </label>

            <textarea
                name="descricao"><?= $vaga['descricao'] ?? '' ?></textarea>

        </div>

        <div class="form-group">

            <label>
                Requisitos
            </label>

            <textarea
                name="requisitos"><?= $vaga['requisitos'] ?? '' ?></textarea>

        </div>

        <div class="form-group">

            <label>
                Observações
            </label>

            <textarea
                name="observacoes"><?= $vaga['observacoes'] ?? '' ?></textarea>

        </div>

        <button
            class="btn"
            type="submit">
            Salvar Vaga
        </button>

    </form>

</div>

<script>
    const salario =
        document.getElementById("salario");

    salario.addEventListener(
        "input",
        function(e) {
            let valor =
                e.target.value
                .replace(/\D/g, "");

            valor =
                (parseInt(valor || 0) / 100)
                .toFixed(2);

            valor =
                valor.replace(".", ",");

            valor =
                valor.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    "."
                );

            e.target.value = valor;
        }
    );
</script>
