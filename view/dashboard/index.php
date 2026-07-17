<?php
$dadosStatus = [
    (int)($resumoCandidaturas['analise'] ?? 0),
    (int)($resumoCandidaturas['entrevista'] ?? 0),
    (int)($resumoCandidaturas['entrevistado'] ?? 0),
    (int)($resumoCandidaturas['aprovado'] ?? 0),
    (int)($resumoCandidaturas['recusado'] ?? 0),
    (int)($resumoCandidaturas['vaga_preenchida'] ?? 0),
    (int)($resumoCandidaturas['vaga_fechada'] ?? 0)
];
$totalCandidaturasPeriodo = array_sum($dadosStatus);

$dadosDashboard = [
    'status' => [
        'labels' => [
            'Aguardando Entrevista',
            'Entrevista Agendada',
            'Entrevistado',
            'Aprovado',
            'Recusado',
            'Vaga Preenchida',
            'Vaga Fechada'
        ],
        'valores' => $dadosStatus
    ],
    'evolucao' => $evolucaoMensal,
    'vagas' => [
        'labels' => array_column($candidaturasPorVaga, 'titulo'),
        'candidaturas' => array_map(
            'intval',
            array_column($candidaturasPorVaga, 'total_candidaturas')
        ),
        'oportunidades' => array_map(
            'intval',
            array_column($candidaturasPorVaga, 'quantidade_vagas')
        ),
        'contratados' => array_map(
            'intval',
            array_column($candidaturasPorVaga, 'contratados')
        )
    ],
    'funil' => [
        'labels' => [
            'Candidaturas',
            'Entrevistas',
            'Aprovados',
            'Contratados'
        ],
        'valores' => [
            $funilRecrutamento['candidaturas'],
            $funilRecrutamento['entrevistas'],
            $funilRecrutamento['aprovados'],
            $funilRecrutamento['contratados']
        ]
    ]
];
?>

<link
    rel="stylesheet"
    href="public/css/dashboard.css?v=<?= filemtime('public/css/dashboard.css') ?>">

<div class="dashboard">
    <header class="dashboard-topo">
        <div>
            <span class="dashboard-eyebrow">Visão operacional</span>
            <h1>Dashboard de Recrutamento</h1>
        </div>
        <div class="dashboard-topo-acoes">
            <form method="GET" class="dashboard-periodo">
                <input type="hidden" name="c" value="dashboard">
                <label for="dashboardPeriodo">
                    <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                    Período dos gráficos
                </label>
                <select
                    id="dashboardPeriodo"
                    name="periodo"
                    onchange="this.form.submit()">
                    <?php foreach ([3, 6, 12, 24] as $opcaoPeriodo): ?>
                        <option
                            value="<?= $opcaoPeriodo ?>"
                            <?= $periodoMeses === $opcaoPeriodo ? 'selected' : '' ?>>
                            Últimos <?= $opcaoPeriodo ?> meses
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <span class="dashboard-atualizacao">
                <i class="fa-solid fa-clock" aria-hidden="true"></i>
                <?= date('d/m/Y H:i') ?>
            </span>
        </div>
    </header>

    <section class="dashboard-kpis" aria-label="Indicadores principais">
        <a class="dashboard-kpi candidatos" href="?c=candidato">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
            <span>
                <small>Candidatos</small>
                <strong><?= (int)$totalCandidatos ?></strong>
                <em>base cadastrada</em>
            </span>
        </a>

        <a class="dashboard-kpi vagas" href="?c=vaga&status=Aberta">
            <i class="fa-solid fa-briefcase" aria-hidden="true"></i>
            <span>
                <small>Vagas abertas</small>
                <strong><?= (int)$totalVagasAtivas ?></strong>
                <em>processos ativos</em>
            </span>
        </a>

        <a
            class="dashboard-kpi aguardando"
            href="?c=candidatura&status=Aguardando+Entrevista">
            <i class="fa-solid fa-hourglass-half" aria-hidden="true"></i>
            <span>
                <small>Aguardando entrevista</small>
                <strong><?= $indicadores['aguardando_entrevista'] ?></strong>
                <em>exigem triagem</em>
            </span>
        </a>

        <a class="dashboard-kpi hoje" href="?c=entrevista">
            <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
            <span>
                <small>Entrevistas hoje</small>
                <strong><?= (int)$totalEntrevistasHoje ?></strong>
                <em><?= (int)$totalEntrevistas ?> no histórico</em>
            </span>
        </a>

        <a class="dashboard-kpi agenda" href="?c=entrevista">
            <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
            <span>
                <small>Próximos 7 dias</small>
                <strong><?= $indicadores['entrevistas_proximos_7_dias'] ?></strong>
                <em>entrevistas previstas</em>
            </span>
        </a>

        <a class="dashboard-kpi contratados" href="?c=contratacao">
            <i class="fa-solid fa-user-check" aria-hidden="true"></i>
            <span>
                <small>Contratados</small>
                <strong><?= $indicadores['total_contratados'] ?></strong>
                <em><?= number_format($indicadores['taxa_contratacao'], 1, ',', '.') ?>% das candidaturas</em>
            </span>
        </a>
    </section>

    <section class="dashboard-painel dashboard-alertas">
        <header class="dashboard-painel-topo">
            <div>
                <span class="dashboard-painel-icone alerta">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                </span>
                <div>
                    <h2>Pendências operacionais</h2>
                    <p>Itens que merecem acompanhamento do RH</p>
                </div>
            </div>
        </header>

        <div class="dashboard-alertas-grade">
            <a href="?c=candidatura&status=Aguardando+Entrevista">
                <strong><?= $alertas['aguardando_mais_7_dias'] ?></strong>
                <span>Aguardando há mais de 7 dias</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="?c=entrevista">
                <strong><?= $alertas['entrevistas_atrasadas'] ?></strong>
                <span>Entrevistas vencidas</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="?c=vaga&status=Aberta">
                <strong><?= $alertas['vagas_sem_candidaturas'] ?></strong>
                <span>Vagas sem candidaturas</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="?c=vaga&status=Pausada">
                <strong><?= $alertas['vagas_pausadas'] ?></strong>
                <span>Vagas pausadas</span>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <div class="dashboard-graficos-principais">
        <section class="dashboard-painel dashboard-grafico evolucao">
            <header class="dashboard-painel-topo">
                <div>
                    <span class="dashboard-painel-icone">
                        <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>Evolução mensal</h2>
                        <p><?= htmlspecialchars($rotuloPeriodo) ?></p>
                    </div>
                </div>
            </header>
            <div class="dashboard-canvas grande">
                <canvas id="graficoEvolucao"></canvas>
            </div>
        </section>

        <section class="dashboard-painel dashboard-grafico status">
            <header class="dashboard-painel-topo">
                <div>
                    <span class="dashboard-painel-icone">
                        <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>Status das candidaturas</h2>
                        <p><?= $totalCandidaturasPeriodo ?> registros no período</p>
                    </div>
                </div>
            </header>
            <div class="dashboard-canvas">
                <canvas id="graficoCandidaturas"></canvas>
            </div>
        </section>
    </div>

    <div class="dashboard-graficos-secundarios">
        <section class="dashboard-painel dashboard-grafico vagas">
            <header class="dashboard-painel-topo">
                <div>
                    <span class="dashboard-painel-icone">
                        <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>Candidaturas por vaga</h2>
                        <p>Movimentação em <?= mb_strtolower(htmlspecialchars($rotuloPeriodo), 'UTF-8') ?></p>
                    </div>
                </div>
            </header>
            <div class="dashboard-canvas grande">
                <canvas id="graficoVagas"></canvas>
            </div>
        </section>

        <section class="dashboard-painel dashboard-grafico funil">
            <header class="dashboard-painel-topo">
                <div>
                    <span class="dashboard-painel-icone">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>Funil de recrutamento</h2>
                        <p>Avanço em <?= mb_strtolower(htmlspecialchars($rotuloPeriodo), 'UTF-8') ?></p>
                    </div>
                </div>
            </header>
            <div class="dashboard-canvas">
                <canvas id="graficoFunil"></canvas>
            </div>
        </section>
    </div>

    <section class="dashboard-painel dashboard-entrevistas">
        <header class="dashboard-painel-topo">
            <div>
                <span class="dashboard-painel-icone">
                    <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                </span>
                <div>
                    <h2>Próximas entrevistas</h2>
                    <p>Agenda futura por data e responsável</p>
                </div>
            </div>
            <span class="dashboard-count">
                <?= count($proximasEntrevistas) ?> agendadas
            </span>
        </header>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Candidato</th>
                        <th>Vaga</th>
                        <th>Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proximasEntrevistas)): ?>
                        <tr>
                            <td class="dashboard-vazio" colspan="5">
                                <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>
                                Nenhuma entrevista futura agendada.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proximasEntrevistas as $entrevista): ?>
                            <tr>
                                <td>
                                    <span class="dashboard-badge-date">
                                        <?= date(
                                            'd/m/Y',
                                            strtotime($entrevista['data_entrevista'])
                                        ) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="dashboard-badge-time">
                                        <?= substr($entrevista['hora_entrevista'], 0, 5) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($entrevista['candidato']) ?></td>
                                <td><?= htmlspecialchars($entrevista['vaga']) ?></td>
                                <td><?= htmlspecialchars($entrevista['responsavel']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    (() => {
        const idsGraficos = [
            "graficoEvolucao",
            "graficoCandidaturas",
            "graficoVagas",
            "graficoFunil"
        ];

        function mostrarMensagem(id, mensagem) {
            const canvas = document.getElementById(id);

            if (!canvas || canvas.parentElement.querySelector(".dashboard-grafico-vazio")) {
                return;
            }

            canvas.hidden = true;
            const aviso = document.createElement("div");
            aviso.className = "dashboard-grafico-vazio";
            aviso.innerHTML = `<i class="fa-solid fa-chart-simple"></i><span>${mensagem}</span>`;
            canvas.parentElement.appendChild(aviso);
        }

        if (typeof window.Chart === "undefined") {
            idsGraficos.forEach(id => mostrarMensagem(
                id,
                "Não foi possível carregar o gráfico."
            ));
            return;
        }

        const ChartJS = window.Chart;

        function criarGrafico(id, configuracao, possuiDados = true) {
            const canvas = document.getElementById(id);

            if (!canvas) {
                return;
            }

            if (!possuiDados) {
                mostrarMensagem(id, "Sem dados no período selecionado.");
                return;
            }

            try {
                new ChartJS(canvas.getContext("2d"), configuracao);
            } catch (erro) {
                console.error(`Falha ao criar ${id}:`, erro);
                mostrarMensagem(id, "Não foi possível exibir este gráfico.");
            }
        }

        const dados = <?= json_encode(
            $dadosDashboard,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?>;
        const estilos = getComputedStyle(document.documentElement);
        const destaque = estilos.getPropertyValue("--tema-destaque").trim() || "#2563EB";
        const secundaria = estilos.getPropertyValue("--tema-secundaria").trim() || "#0F172A";
        const primaria = estilos.getPropertyValue("--tema-primaria").trim() || "#F59E0B";
        const sumerNet = document.body.dataset.identidade === "sumernet";
        const acento = sumerNet ? primaria : secundaria;

        ChartJS.defaults.color = "#64748B";
        ChartJS.defaults.font.family = "Arial, Helvetica, sans-serif";
        ChartJS.defaults.font.size = 11;

        const grade = {
            color: "rgba(148, 163, 184, 0.18)",
            drawBorder: false
        };
        const legenda = {
            position: "bottom",
            labels: {
                usePointStyle: true,
                boxWidth: 8,
                padding: 16
            }
        };

        criarGrafico("graficoEvolucao", {
            type: "line",
            data: {
                labels: dados.evolucao.labels,
                datasets: [
                    {
                        label: "Candidatos",
                        data: dados.evolucao.candidatos,
                        borderColor: destaque,
                        backgroundColor: destaque,
                        tension: 0.3
                    },
                    {
                        label: "Candidaturas",
                        data: dados.evolucao.candidaturas,
                        borderColor: acento,
                        backgroundColor: acento,
                        tension: 0.3
                    },
                    {
                        label: "Contratações",
                        data: dados.evolucao.contratacoes,
                        borderColor: "#16A34A",
                        backgroundColor: "#16A34A",
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: "index", intersect: false },
                plugins: { legend: legenda },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: grade
                    }
                }
            }
        });

        criarGrafico("graficoCandidaturas", {
            type: "doughnut",
            data: {
                labels: dados.status.labels,
                datasets: [{
                    data: dados.status.valores,
                    backgroundColor: [
                        acento,
                        destaque,
                        "#0891B2",
                        "#16A34A",
                        "#DC2626",
                        "#6366F1",
                        "#64748B"
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "64%",
                plugins: { legend: legenda }
            }
        }, dados.status.valores.some(valor => Number(valor) > 0));

        criarGrafico("graficoVagas", {
            type: "bar",
            data: {
                labels: dados.vagas.labels,
                datasets: [
                    {
                        label: "Candidaturas",
                        data: dados.vagas.candidaturas,
                        backgroundColor: destaque,
                        borderRadius: 4
                    },
                    {
                        label: "Oportunidades",
                        data: dados.vagas.oportunidades,
                        backgroundColor: acento,
                        borderRadius: 4
                    },
                    {
                        label: "Contratados",
                        data: dados.vagas.contratados,
                        backgroundColor: "#16A34A",
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legenda },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: grade
                    },
                    y: { grid: { display: false } }
                }
            }
        }, dados.vagas.labels.length > 0);

        criarGrafico("graficoFunil", {
            type: "bar",
            data: {
                labels: dados.funil.labels,
                datasets: [{
                    data: dados.funil.valores,
                    backgroundColor: [destaque, acento, "#22C55E", "#15803D"],
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: grade
                    },
                    y: { grid: { display: false } }
                }
            }
        }, dados.funil.valores.some(valor => Number(valor) > 0));
    })();
</script>
