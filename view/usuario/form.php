<div class="form-card">
    <h2>

        <?= isset($usuario)
            ? 'Editar Usuário'
            : 'Novo Usuário' ?>

    </h2>
    <form
        method="POST"
        action="?c=usuario&m=<?= isset($usuario)
                                    ? 'atualizar'
                                    : 'cadastrar' ?>">

        <?php if (isset($usuario)): ?>

            <input
                type="hidden"
                name="idUsuario"
                value="<?= $usuario['idUsuario'] ?>">

        <?php endif; ?>


        <div class="form-group">

            <label>Nome</label>

            <input
                type="text"
                name="nome"
                required
                value="<?= $usuario['nome'] ?? '' ?>">

        </div>


        <div class="form-group">

            <label>E-mail</label>

            <input
                type="email"
                name="email"
                required
                value="<?= $usuario['email'] ?? '' ?>">

        </div>


        <div class="form-group">

            <div class="senha-container">

                <label>
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    maxlength="128"
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9\s]).{8,}"
                    title="Use ao menos 8 caracteres, com letra maiúscula, minúscula, número e símbolo especial."
                    <?= !isset($usuario) ? 'required' : '' ?>>

                <button
                    type="button"
                    class="btn-olho"
                    onclick="toggleSenha()">

                    <i
                        id="iconeSenha"
                        class="fa-solid fa-eye">
                    </i>

                </button>

            </div>

            <?php if (isset($usuario)): ?>

                <small class="texto-ajuda">

                    Deixe em branco para manter a senha atual. Ao redefinir, o usuário deverá trocá-la no próximo login.

                </small>

            <?php endif; ?>

            <?php if (!isset($usuario)): ?>
                <small class="texto-ajuda">
                    Mínimo de 8 caracteres, com maiúscula, minúscula, número e símbolo especial. Esta será uma senha provisória.
                </small>
            <?php endif; ?>

        </div>


        <div class="form-group">

            <label>Perfil</label>

            <select
                name="perfil"
                required>

                <?php if (ehAdministrador()): ?>
                    <option
                        value="Administrador"
                        <?= (($usuario['perfil'] ?? '') == 'Administrador')
                            ? 'selected'
                            : '' ?>>
                        Administrador
                    </option>
                <?php endif; ?>

                <option
                    value="Gerente"
                    <?= (($usuario['perfil'] ?? '') == 'Gerente')
                        ? 'selected'
                        : '' ?>>

                    Gerente

                </option>

                <option
                    value="Secretario"
                    <?= (($usuario['perfil'] ?? '') == 'Secretario')
                        ? 'selected'
                        : '' ?>>

                    Secretário

                </option>

                <option
                    value="Recrutador"
                    <?= (($usuario['perfil'] ?? '') == 'Recrutador')
                        ? 'selected'
                        : '' ?>>

                    Recrutador

                </option>

            </select>

        </div>


        <button
            type="submit"
            class="btn">

            <i class="fa-solid fa-floppy-disk"></i>

            Salvar

        </button>

        <a
            href="?c=usuario"
            class="btn btn-secondary">

            Cancelar

        </a>

    </form>

    <?php if (
        isset($usuario)
        && !empty($usuario['dois_fatores_ativo'])
        && (int)$usuario['idUsuario'] !== (int)($_SESSION['usuario']['idUsuario'] ?? 0)
    ): ?>
        <form method="POST" action="?c=usuario&amp;m=resetarDoisFatores" class="usuario-reset-2fa">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_2fa_admin']) ?>">
            <input type="hidden" name="idUsuario" value="<?= (int)$usuario['idUsuario'] ?>">
            <div>
                <strong>Google Authenticator ativo</strong>
                <span>Use a redefinição somente se o usuário perdeu o acesso ao autenticador.</span>
            </div>
            <button type="submit" class="btn-contorno-perigo">
                <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                Redefinir 2FA
            </button>
        </form>
    <?php endif; ?>
</div>
<script>
    function toggleSenha() {

        const senha =
            document.getElementById(
                'senha'
            );

        const icone =
            document.getElementById(
                'iconeSenha'
            );

        if (
            senha.type === 'password'
        ) {

            senha.type = 'text';

            icone.classList.remove(
                'fa-eye'
            );

            icone.classList.add(
                'fa-eye-slash'
            );

        } else {

            senha.type = 'password';

            icone.classList.remove(
                'fa-eye-slash'
            );

            icone.classList.add(
                'fa-eye'
            );
        }
    }
</script>
