<link rel="stylesheet" href="public/css/log.css">

<h1 class="titulo-pagina">
    Detalhes do Log
</h1>

<?php if (empty($log)): ?>

    <div class="card-visualizacao">
        <div class="card-body">
            Log nao encontrado.
        </div>
    </div>

<?php else: ?>

    <div class="log-detail-card">

        <div class="log-detail-header">
            <h2>
                <?= htmlspecialchars($log['descricao']) ?>
            </h2>
        </div>

        <div class="log-detail-grid">

            <div class="log-info-item">
                <span class="label">Data/Hora</span>
                <span>
                    <?= date(
                        'd/m/Y H:i:s',
                        strtotime($log['criado_em'])
                    ) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Usuario</span>
                <span>
                    <?= htmlspecialchars($log['usuario_nome']) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Perfil</span>
                <span>
                    <?= htmlspecialchars($log['usuario_perfil']) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Modulo</span>
                <span>
                    <?= htmlspecialchars($log['modulo']) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Metodo</span>
                <span>
                    <?= htmlspecialchars($log['metodo']) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Acao</span>
                <span>
                    <?= htmlspecialchars(rotuloAcaoLog($log['acao'])) ?>
                </span>
            </div>

            <div class="log-info-item">
                <span class="label">Registro</span>
                <span>
                    <?= !empty($log['registro_id'])
                        ? (int)$log['registro_id']
                        : '-' ?>
                </span>
            </div>

            <div class="log-info-item log-info-wide">
                <span class="label">Navegador</span>
                <span>
                    <?= htmlspecialchars($log['user_agent']) ?>
                </span>
            </div>

        </div>

    </div>

    <div class="log-detail-card">

        <div class="log-detail-header log-detail-header-light">
            <h3>Dados da Requisicao</h3>
        </div>

        <div class="log-json-box">
            <pre><?= htmlspecialchars(
                json_encode(
                    json_decode($log['dados'], true),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            ) ?></pre>
        </div>

    </div>

<?php endif; ?>
