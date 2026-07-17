<div class="topo email-config-topo">
    <div>
        <span class="email-config-eyebrow">Administração</span>
        <h1>E-mail do Token</h1>
        <p>Configure o remetente e valide o envio antes de usar no acesso dos candidatos.</p>
    </div>
    <span class="email-config-status <?= !empty($configuracaoEmail['testado_em']) ? 'testado' : '' ?>">
        <i class="fa-solid <?= !empty($configuracaoEmail['testado_em']) ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
        <?= !empty($configuracaoEmail['testado_em']) ? 'Envio testado' : 'Teste pendente' ?>
    </span>
</div>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div class="email-config-alerta sucesso">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($_SESSION['sucesso']) ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<div class="email-config-layout">
    <main class="email-config-principal">
        <form method="POST" action="?c=configuracaoEmail&m=salvar" class="email-config-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="email-config-section-title">
                <div>
                    <h2>Servidor de envio</h2>
                    <p>As credenciais são protegidas no banco de dados.</p>
                </div>
                <i class="fa-solid fa-server"></i>
            </div>

            <div class="email-config-grid">
                <label class="email-config-field email-config-field-wide">
                    <span>Servidor SMTP</span>
                    <input type="text" name="smtp_host" required maxlength="255"
                        value="<?= htmlspecialchars($configuracaoEmail['smtp_host']) ?>"
                        placeholder="smtp.gmail.com">
                </label>

                <label class="email-config-field">
                    <span>Porta</span>
                    <input type="number" name="smtp_port" required min="1" max="65535"
                        value="<?= (int)$configuracaoEmail['smtp_port'] ?>">
                </label>

                <label class="email-config-field">
                    <span>Criptografia</span>
                    <select name="smtp_criptografia" required>
                        <option value="tls" <?= $configuracaoEmail['smtp_criptografia'] === 'tls' ? 'selected' : '' ?>>TLS / STARTTLS</option>
                        <option value="ssl" <?= $configuracaoEmail['smtp_criptografia'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="nenhuma" <?= $configuracaoEmail['smtp_criptografia'] === 'nenhuma' ? 'selected' : '' ?>>Nenhuma</option>
                    </select>
                </label>

                <label class="email-config-field email-config-field-wide">
                    <span>Usuário SMTP</span>
                    <input type="email" name="smtp_usuario" required maxlength="255"
                        autocomplete="username"
                        value="<?= htmlspecialchars($configuracaoEmail['smtp_usuario']) ?>">
                </label>

                <label class="email-config-field email-config-field-wide">
                    <span>
                        Senha de aplicativo
                        <?php if (!empty($configuracaoEmail['senha_configurada'])): ?>
                            <small><i class="fa-solid fa-lock"></i> Configurada</small>
                        <?php endif; ?>
                    </span>
                    <span class="email-config-password">
                        <input id="smtpSenha" type="password" name="smtp_senha"
                            autocomplete="new-password"
                            placeholder="<?= !empty($configuracaoEmail['senha_configurada']) ? 'Deixe em branco para manter a senha atual' : 'Informe a senha SMTP' ?>">
                        <button type="button" id="alternarSenha" title="Mostrar ou ocultar senha" aria-label="Mostrar ou ocultar senha">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </span>
                </label>

                <label class="email-config-field email-config-field-wide">
                    <span>E-mail do remetente</span>
                    <input type="email" name="email_remetente" required maxlength="255"
                        value="<?= htmlspecialchars($configuracaoEmail['email_remetente']) ?>">
                </label>

                <label class="email-config-field email-config-field-wide">
                    <span>Nome do remetente</span>
                    <input type="text" name="nome_remetente" required maxlength="150"
                        value="<?= htmlspecialchars($configuracaoEmail['nome_remetente']) ?>">
                </label>
            </div>

            <div class="email-config-actions">
                <button type="submit" class="btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Salvar configuração
                </button>
            </div>
        </form>

        <section class="email-config-test">
            <div class="email-config-section-title">
                <div>
                    <h2>Testar configuração</h2>
                    <p>O teste usa exatamente os dados salvos acima.</p>
                </div>
                <i class="fa-solid fa-paper-plane"></i>
            </div>

            <form method="POST" action="?c=configuracaoEmail&m=testar">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <label class="email-config-field">
                    <span>Enviar teste para</span>
                    <input type="email" name="email_teste" required
                        value="<?= htmlspecialchars($_SESSION['usuario']['email'] ?? '') ?>">
                </label>
                <button type="submit" class="btn email-config-test-button">
                    <i class="fa-solid fa-paper-plane"></i>
                    Enviar teste
                </button>
            </form>

            <div class="email-config-dates">
                <span><strong>Última alteração:</strong> <?= !empty($configuracaoEmail['atualizado_em']) ? date('d/m/Y H:i', strtotime($configuracaoEmail['atualizado_em'])) : 'Não registrada' ?></span>
                <span><strong>Último teste:</strong> <?= !empty($configuracaoEmail['testado_em']) ? date('d/m/Y H:i', strtotime($configuracaoEmail['testado_em'])) : 'Ainda não realizado' ?></span>
            </div>
        </section>
    </main>

    <aside class="email-config-guide">
        <div class="email-config-section-title">
            <div>
                <h2>Configurar no Gmail</h2>
                <p>Passo a passo para gerar a credencial.</p>
            </div>
            <i class="fa-brands fa-google"></i>
        </div>

        <ol class="email-config-steps">
            <li>
                <strong>Ative a verificação em duas etapas</strong>
                <span>Acesse a segurança da conta Google usada como remetente e habilite a verificação.</span>
                <a href="https://myaccount.google.com/signinoptions/two-step-verification" target="_blank" rel="noopener noreferrer">Abrir verificação em duas etapas <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </li>
            <li>
                <strong>Crie uma senha de app</strong>
                <span>Abra Senhas de app, crie uma credencial para “Banco de Talentos” e guarde os 16 caracteres.</span>
                <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer">Abrir senhas de app <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </li>
            <li>
                <strong>Preencha os dados SMTP</strong>
                <span>Use <b>smtp.gmail.com</b>, porta <b>587</b>, <b>TLS</b>, o e-mail completo e a senha de app gerada.</span>
            </li>
            <li>
                <strong>Salve e envie um teste</strong>
                <span>Confirme a chegada do e-mail antes de considerar a configuração concluída.</span>
            </li>
        </ol>

        <div class="email-config-note">
            <i class="fa-solid fa-circle-info"></i>
            <p>A opção de senha de app exige verificação em duas etapas e pode ser bloqueada pela política da organização. Ao trocar a senha da conta Google, gere uma nova senha de app.</p>
        </div>

        <a class="email-config-help" href="https://support.google.com/accounts/answer/185833?hl=pt-BR" target="_blank" rel="noopener noreferrer">
            Ajuda oficial do Google
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
    </aside>
</div>

<script>
    (() => {
        const senha = document.getElementById('smtpSenha');
        const alternar = document.getElementById('alternarSenha');

        alternar.addEventListener('click', () => {
            const exibindo = senha.type === 'text';
            senha.type = exibindo ? 'password' : 'text';
            alternar.querySelector('i').className =
                `fa-solid ${exibindo ? 'fa-eye' : 'fa-eye-slash'}`;
        });
    })();
</script>
