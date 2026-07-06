<link rel="stylesheet" href="public/css/decisao.css">
<h1>
    Centro de Decisões
</h1>

<div class="detalhe-container">

    <div class="card-detalhe">
        <div class="card-header">
            <i class="fa-solid fa-user"></i>
            Dados do Candidato
        </div>

        <div class="card-body">

            <div class="info-item">
                <span class="label">Nome</span>
                <span><?= $decisao['nome'] ?></span>
            </div>

            <div class="info-item">
                <span class="label">Telefone</span>
                <span><?= $decisao['telefone'] ?></span>
            </div>

            <div class="info-item">
                <span class="label">E-mail</span>
                <span><?= $decisao['email'] ?></span>
            </div>

        </div>

        <?php if ($decisao['whatsapp']): ?>
            <div class="card-footer">



                <a
                    href="https://wa.me/55<?= $decisao['telefone'] ?>"
                    target="_blank"
                    class="btn-whatsapp">

                    <i class="fa-brands fa-whatsapp"></i>
                    WhatsApp

                </a>



            </div>
        <?php endif ?>
    </div>

    <div class="card-detalhe">
        <div class="card-header">
            <i class="fa-solid fa-briefcase"></i>
            Vaga
        </div>

        <div class="card-body">

            <div class="info-item">
                <span class="label">Título</span>
                <span><?= $decisao['titulo'] ?></span>
            </div>

            <div class="info-item">
                <span class="label">Status</span>

                <span class="
                badge-status
                <?= strtolower($decisao['status_candidatura']) ?>
            ">
                    <?= $decisao['status_candidatura'] ?>
                </span>

            </div>

        </div>
    </div>

    <div class="card-detalhe">
        <div class="card-header">
            <i class="fa-solid fa-calendar-check"></i>
            Entrevista
        </div>

        <div class="card-body">

            <div class="info-item">
                <span class="label">Data</span>
                <span>
                    <?= date(
                        'd/m/Y',
                        strtotime(
                            $decisao['data_entrevista']
                        )
                    ) ?>
                </span>
            </div>

            <div class="info-item">
                <span class="label">Hora</span>
                <span>
                    <?= substr(
                        $decisao['hora_entrevista'],
                        0,
                        5
                    ) ?>
                </span>
            </div>

            <div class="info-item">
                <span class="label">Responsável</span>
                <span>
                    <?= $decisao['responsavel'] ?>
                </span>
            </div>

        </div>
    </div>

    <div class="card-detalhe destaque">

        <div class="card-header">

            <i class="fa-solid fa-gavel"></i>

            Decisão Final

        </div>

        <div class="card-body">

            <div class="info-item">

                <span class="label">
                    Resultado
                </span>

                <span class="
                badge-status
                <?= strtolower($decisao['status_candidatura']) ?>
            ">
                    <?= $decisao['status_candidatura'] ?>
                </span>

            </div>

            <?php if (
                $decisao['status_candidatura']
                == 'Recusado'
            ): ?>

                <div class="info-item">

                    <span class="label">
                        Motivo
                    </span>

                    <span>
                        <?= $decisao['motivo_recusa'] ?>
                    </span>

                </div>

            <?php endif; ?>

            <?php if (
                !empty($decisao['status_contratacao'])
            ): ?>

                <div class="info-item">

                    <span class="label">
                        Contratação
                    </span>

                    <span class="
        badge-status
        <?= strtolower(
                    str_replace(
                        ' ',
                        '-',
                        $decisao['status_contratacao'] ?? 'aguardando'
                    )
                ) ?>
    ">

                        <?= $decisao['status_contratacao'] ?? 'Aguardando' ?>

                    </span>

                </div>

            <?php endif; ?>
            <?php if (
                ($decisao['status_contratacao'] ?? '')
                === 'Contratado'
                &&
                !empty($decisao['data_contratacao'])
            ): ?>

                <div class="info-item">

                    <span class="label">
                        Data da Contratação
                    </span>

                    <span>
                        <?= date(
                            'd/m/Y',
                            strtotime(
                                $decisao['data_contratacao']
                            )
                        ) ?>
                    </span>

                </div>

            <?php endif; ?>
            <?php if (
                in_array(
                    $decisao['status_contratacao'] ?? '',
                    ['Dispensado', 'Auto-Dispensa']
                )
                &&
                !empty($decisao['motivo_desligamento'])
            ): ?>

                <div class="info-item">

                    <span class="label">
                        Motivo do Desligamento
                    </span>

                    <span>
                        <?= $decisao['motivo_desligamento'] ?>
                    </span>

                </div>

            <?php endif; ?>
        </div>

    </div>

</div>