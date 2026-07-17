<div class="topo">
    <div>
        <span class="pagina-eyebrow">Comunicação</span>
        <h1>Chamadas WhatsApp</h1>
    </div>
</div>

<div class="toolbar">
        <form method="GET" class="form-busca painel-filtros">

            <input
                type="hidden"
                name="c"
                value="chamada">

            <div class="campo-filtro campo-filtro-busca">
                <label for="buscaChamada">Buscar</label>
                <div class="entrada-com-icone">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="buscaChamada" type="text" name="busca"
                        value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>"
                        placeholder="Nome, vaga ou telefone">
                </div>
            </div>

            <div class="campo-filtro campo-filtro-status">
                <label for="statusChamada">Status</label>
                <select id="statusChamada" name="status">

                <option value="">
                    Todos
                </option>

                <option
                    value="Aguardando Entrevista"
                    <?= ($_GET['status'] ?? '') == 'Aguardando Entrevista' ? 'selected' : '' ?>>
                    Aguardando Entrevista
                </option>

                <option
                    value="Entrevista Agendada"
                    <?= ($_GET['status'] ?? '') == 'Entrevista Agendada' ? 'selected' : '' ?>>
                    Entrevista Agendada
                </option>

                <option
                    value="Aprovado"
                    <?= ($_GET['status'] ?? '') == 'Aprovado' ? 'selected' : '' ?>>
                    Aprovado
                </option>

                <option
                    value="Recusado"
                    <?= ($_GET['status'] ?? '') == 'Recusado' ? 'selected' : '' ?>>
                    Recusado
                </option>

                <option
                    value="Entrevistado"
                    <?= ($_GET['status'] ?? '') == 'Entrevistado' ? 'selected' : '' ?>>
                    Entrevistado
                </option>

                <option
                    value="Contratado"
                    <?= ($_GET['status'] ?? '') == 'Contratado' ? 'selected' : '' ?>>
                    Contratado
                </option>

                <option
                    value="Dispensado"
                    <?= ($_GET['status'] ?? '') == 'Dispensado' ? 'selected' : '' ?>>
                    Dispensado
                </option>

                <option
                    value="Auto-Dispensa"
                    <?= ($_GET['status'] ?? '') == 'Auto-Dispensa' ? 'selected' : '' ?>>
                    Auto-Dispensa
                </option>

                </select>
            </div>

            <div class="campo-filtro campo-filtro-checkbox">
                <span class="campo-filtro-label">Disponibilidade</span>
                <label class="checkbox-filtro">
                    <input
                        type="checkbox"
                        name="sem_whatsapp"
                        value="1"
                        <?= isset($_GET['sem_whatsapp']) ? 'checked' : '' ?>>
                    <span class="checkbox-indicador" aria-hidden="true"></span>
                    <span>Sem WhatsApp</span>
                </label>
            </div>

            <button class="btn btn-filtrar">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Filtrar
            </button>

        </form>
</div>

<div class="card">

    <h2>

        Convocações para Entrevista

    </h2>

    <table class="tabela-chamadas">

        <thead>

            <tr>

                <th>Candidato</th>

                <th>Telefone</th>

                <th>Vaga</th>

                <th>Status</th>

                <th>WhatsApp</th>

                <th>Ação</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($candidaturas as $c): ?>

                <tr>

                    <td>
                        <a
                            class="link-candidato"
                            href="?c=chamada&amp;m=visualizarCandidato&amp;id=<?= (int)$c['candidato_id'] ?>"
                            title="Abrir perfil do candidato">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                            <?= htmlspecialchars($c['nome']) ?>
                        </a>
                    </td>

                    <td>
                        <?= formatarTelefone($c['telefone']) ?>
                    </td>

                    <td>
                        <?= $c['titulo'] ?>
                    </td>

                    <td>
                        <?php
                        $statusExibicao = $c['status_exibicao'];
                        $classeStatus = strtolower(
                            str_replace(' ', '-', $statusExibicao)
                        );
                        $possuiDetalhes = in_array(
                            $statusExibicao,
                            ['Recusado', 'Entrevistado', 'Aprovado', 'Contratado'],
                            true
                        );
                        ?>

                        <?php if ($possuiDetalhes): ?>
                            <button
                                type="button"
                                class="badge-status badge-detalhes-interativo <?= htmlspecialchars($classeStatus) ?>"
                                data-detalhes-id="<?= (int)$c['idCandidatura'] ?>"
                                data-detalhes-endpoint="?c=chamada&amp;m=detalhesResultado"
                                data-resultado="<?= htmlspecialchars($statusExibicao) ?>"
                                title="Ver detalhes da entrevista">
                                <?= htmlspecialchars($statusExibicao) ?>
                                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            </button>
                        <?php else: ?>
                            <span class="badge-status <?= htmlspecialchars($classeStatus) ?>">
                                <?= htmlspecialchars($statusExibicao) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>

                        <?php if ($c['whatsapp']): ?>

                            <span class="status-whatsapp-sim">

                                <i class="fa-solid fa-circle-check"></i>

                                Sim

                            </span>

                        <?php else: ?>

                            <span class="status-whatsapp-nao">

                                <i class="fa-solid fa-circle-xmark"></i>

                                Não

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if ($c['whatsapp']): ?>

                            <?php

                            $telefone =
                                preg_replace(
                                    '/\D/',
                                    '',
                                    $c['telefone']
                                );

                            ?>

                            <a
                                href="https://wa.me/55<?= $telefone ?>"
                                target="_blank"
                                class="btn-whatsapp">

                                <i class="fa-brands fa-whatsapp"></i>

                                WhatsApp

                            </a>

                        <?php else: ?>

                            -

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>


    </table>

</div>

<?php include "view/candidatura/modal_recusa.php"; ?>
<script src="public/js/recusa-modal.js?v=<?= filemtime('public/js/recusa-modal.js') ?>"></script>
