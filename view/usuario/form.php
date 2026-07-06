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

                    Deixe em branco para manter a senha atual.

                </small>

            <?php endif; ?>

        </div>


        <div class="form-group">

            <label>Perfil</label>

            <select
                name="perfil"
                required>

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