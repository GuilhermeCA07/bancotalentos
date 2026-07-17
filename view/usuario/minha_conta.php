<div class="topo">
    <div>
        <span class="pagina-eyebrow">Conta</span>
        <h1>Minha conta</h1>
    </div>
</div>

<div class="conta-layout">
    <section class="conta-resumo" aria-labelledby="contaUsuarioTitulo">
        <div class="conta-avatar" aria-hidden="true">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <span class="conta-rotulo">Usu&aacute;rio conectado</span>
            <h2 id="contaUsuarioTitulo"><?= htmlspecialchars($usuario['nome']) ?></h2>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
            <span class="badge-conta-perfil">
                <?= htmlspecialchars($usuario['perfil']) ?>
            </span>
        </div>
    </section>

    <section class="form-card conta-senha" aria-labelledby="alterarSenhaTitulo">
        <div class="form-section-header">
            <div>
                <span class="pagina-eyebrow">Seguran&ccedil;a</span>
                <h2 id="alterarSenhaTitulo">Alterar senha</h2>
            </div>
            <i class="fa-solid fa-key" aria-hidden="true"></i>
        </div>

        <?php if ($mensagemConta): ?>
            <div
                class="mensagem-conta <?= $mensagemConta['tipo'] === 'sucesso' ? 'sucesso' : 'erro' ?>"
                role="alert">
                <i
                    class="fa-solid <?= $mensagemConta['tipo'] === 'sucesso' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"
                    aria-hidden="true"></i>
                <span><?= htmlspecialchars($mensagemConta['texto']) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="?c=usuario&amp;m=alterarMinhaSenha">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_alterar_senha']) ?>">

            <div class="form-group">
                <label for="senhaAtual">Senha atual</label>
                <div class="campo-senha-conta">
                    <input
                        id="senhaAtual"
                        type="password"
                        name="senha_atual"
                        autocomplete="current-password"
                        required>
                    <button
                        type="button"
                        class="btn-exibir-senha"
                        data-alvo-senha="senhaAtual"
                        aria-label="Exibir senha atual"
                        title="Exibir senha">
                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="novaSenha">Nova senha</label>
                    <div class="campo-senha-conta">
                        <input
                            id="novaSenha"
                            type="password"
                            name="nova_senha"
                            minlength="8"
                            maxlength="128"
                            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s]).{8,}"
                            autocomplete="new-password"
                            required>
                        <button
                            type="button"
                            class="btn-exibir-senha"
                            data-alvo-senha="novaSenha"
                            aria-label="Exibir nova senha"
                            title="Exibir senha">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <small class="texto-ajuda">Mínimo de 8 caracteres, com maiúscula, minúscula, número e símbolo especial.</small>
                </div>

                <div class="form-group">
                    <label for="confirmarSenha">Confirmar nova senha</label>
                    <div class="campo-senha-conta">
                        <input
                            id="confirmarSenha"
                            type="password"
                            name="confirmar_senha"
                            minlength="8"
                            maxlength="128"
                            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s]).{8,}"
                            autocomplete="new-password"
                            required>
                        <button
                            type="button"
                            class="btn-exibir-senha"
                            data-alvo-senha="confirmarSenha"
                            aria-label="Exibir confirmacao da senha"
                            title="Exibir senha">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                    Salvar nova senha
                </button>
            </div>
        </form>
    </section>

    <section class="form-card conta-seguranca" aria-labelledby="doisFatoresTitulo">
        <div class="form-section-header">
            <div>
                <span class="pagina-eyebrow">Proteção adicional</span>
                <h2 id="doisFatoresTitulo">Google Authenticator</h2>
            </div>
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
        </div>

        <?php if (!empty($usuario['dois_fatores_ativo'])): ?>
            <div class="status-seguranca ativo">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                <span>Autenticação de dois fatores ativa</span>
            </div>

            <form method="POST" action="?c=usuario&amp;m=desativarDoisFatores" class="form-desativar-2fa">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_2fa']) ?>">
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label for="senhaDesativar2fa">Senha atual</label>
                        <input id="senhaDesativar2fa" type="password" name="senha_atual" autocomplete="current-password" required>
                    </div>
                    <div class="form-group">
                        <label for="codigoDesativar2fa">Código do autenticador</label>
                        <input id="codigoDesativar2fa" type="text" name="codigo" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
                    </div>
                </div>
                <button type="submit" class="btn-contorno-perigo">
                    <i class="fa-solid fa-shield" aria-hidden="true"></i>
                    Desativar autenticação em duas etapas
                </button>
            </form>
        <?php elseif ($configuracaoDoisFatores && $qrCodeDoisFatores): ?>
            <div class="configuracao-2fa">
                <div class="qrcode-2fa">
                    <img src="<?= htmlspecialchars($qrCodeDoisFatores) ?>" alt="QR Code para configurar o Google Authenticator">
                </div>
                <div class="configuracao-2fa-conteudo">
                    <h3>Leia o QR Code</h3>
                    <p>Adicione uma conta no Google Authenticator e confirme com o código gerado.</p>
                    <div class="segredo-2fa">
                        <span>Chave manual</span>
                        <code><?= htmlspecialchars($configuracaoDoisFatores['segredo']) ?></code>
                    </div>
                    <form method="POST" action="?c=usuario&amp;m=confirmarDoisFatores">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_2fa']) ?>">
                        <div class="form-group">
                            <label for="codigoAtivar2fa">Código de confirmação</label>
                            <input id="codigoAtivar2fa" type="text" name="codigo" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
                        </div>
                        <button type="submit" class="btn">
                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                            Confirmar ativação
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="texto-seguranca">Use um código temporário no celular além da sua senha ao entrar no sistema.</p>
            <form method="POST" action="?c=usuario&amp;m=iniciarDoisFatores">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_2fa']) ?>">
                <button type="submit" class="btn">
                    <i class="fa-solid fa-qrcode" aria-hidden="true"></i>
                    Configurar Google Authenticator
                </button>
            </form>
        <?php endif; ?>
    </section>
</div>

<script src="public/js/minha-conta.js?v=<?= filemtime('public/js/minha-conta.js') ?>"></script>
