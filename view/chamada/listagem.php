<div class="page-header">

    <h1>
        Chamadas WhatsApp
    </h1>
    <br>
</div>

<div class="toolbar">
    <div class="busca-container">

        <i class="fa-solid fa-magnifying-glass"></i>
        <form method="GET" class="form-busca">

            <input
                type="hidden"
                name="c"
                value="chamada">

            <input
                type="text"
                name="busca"
                value="<?= $_GET['busca'] ?? '' ?>"
                placeholder="Nome, vaga ou telefone">

            <select name="status">

                <option value="">
                    Todos
                </option>

                <option
                    value="Em Análise"
                    <?= ($_GET['status'] ?? '') == 'Em Análise' ? 'selected' : '' ?>>
                    Em Análise
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

            <label class="checkbox-filtro">

                <input
                    type="checkbox"
                    name="sem_whatsapp"
                    value="1"
                    <?= isset($_GET['sem_whatsapp'])
                        ? 'checked'
                        : '' ?>>

                <span>
                    Sem WhatsApp
                </span>

            </label>

            <button class="btn">
                Buscar
            </button>

        </form>
    </div>
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
                        <?= $c['nome'] ?>
                    </td>

                    <td>
                        <?= formatarTelefone($c['telefone']) ?>
                    </td>

                    <td>
                        <?= $c['titulo'] ?>
                    </td>

                    <td>

                        <span class="
                                badge-status
                                <?= strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $c['status_exibicao']
                                    )
                                ) ?>
                            ">
                            <?= $c['status_exibicao'] ?>
                        </span>
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