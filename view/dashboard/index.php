<link rel="stylesheet" href="public/css/dashboard.css">
<div class="dashboard">

    <h1>
        Dashboard
    </h1>

    <div class="cards-dashboard">

        <div class="card-dashboard">
            <h3>Candidatos</h3>
            <span>
                <?= $totalCandidatos ?>
            </span>
        </div>

        <div class="card-dashboard">
            <h3>Vagas Abertas</h3>
            <span>
                <?= $totalVagasAtivas ?>
            </span>
        </div>

        <div class="card-dashboard">
            <h3>Entrevistas Hoje</h3>
            <span>
                <?= $totalEntrevistasHoje ?>
            </span>
        </div>

        <div class="card-dashboard">
            <h3>Total de Entrevistas</h3>
            <span>
                <?= $totalEntrevistas ?>
            </span>
        </div>



    </div>
    <div class="dashboard-grid">
        <div class="dashboard-entrevistas">
            <div class="dashboard-header">
                <h2>
                    Próximas Entrevistas
                </h2>

                <span class="dashboard-count">
                    <?= count($proximasEntrevistas) ?>
                    registros
                </span>
            </div>

            <div class="dashboard-table-wrapper">
                <table class="dashboard-table">

                    <thead>

                        <tr>

                            <th>Data</th>

                            <th>Hora</th>

                            <th>Candidato</th>

                            <th>Vaga</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($proximasEntrevistas)) : ?>

                            <tr>

                                <td colspan="4">

                                    Nenhuma entrevista agendada.

                                </td>

                            </tr>

                        <?php else : ?>

                            <?php foreach (
                                $proximasEntrevistas
                                as $entrevista
                            ) : ?>

                                <tr>

                                    <td>

                                        <span class="dashboard-badge-date">

                                            <?= date(
                                                'd/m/Y',
                                                strtotime(
                                                    $entrevista['data_entrevista']
                                                )
                                            ) ?>

                                        </span>

                                    </td>

                                    <td>

                                        <span class="dashboard-badge-time">

                                            <?= substr(
                                                $entrevista['hora_entrevista'],
                                                0,
                                                5
                                            ) ?>

                                        </span>

                                    </td>

                                    <td>
                                        <?= $entrevista['candidato'] ?>
                                    </td>

                                    <td>
                                        <?= $entrevista['vaga'] ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>

        <div class="dashboard-grafico">

            <h2>
                Candidaturas
            </h2>

            <canvas id="graficoCandidaturas">

            </canvas>

        </div>

    </div>
</div>
<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const ctx =
                document.getElementById(
                    "graficoCandidaturas"
                );

            new Chart(ctx, {

                type: "doughnut",

                data: {

                    labels: [

                        "Em Análise",

                        "Entrevista Agendada",

                        "Aprovado",

                        "Recusado"

                    ],

                    datasets: [{

                        data: [

                            <?= $resumoCandidaturas['analise'] ?>,

                            <?= $resumoCandidaturas['entrevista'] ?>,

                            <?= $resumoCandidaturas['aprovado'] ?>,

                            <?= $resumoCandidaturas['recusado'] ?>

                        ]

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {

                            position: "bottom"
                        }
                    }
                }
            });
        }
    );
</script>