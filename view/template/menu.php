<div class="sidebar">

    <div class="logo">
        Banco de Talentos
    </div>

    <ul>
        <?php if (temPermissao('dashboard')): ?>
            <li>
                <a href="?c=dashboard">
                    <i class="fa-solid fa-chart-line"></i>
                    Dashboard
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('candidato')): ?>
            <li>
                <a href="?c=candidato">
                    <i class="fa-solid fa-users"></i>
                    Candidatos
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('candidatura')): ?>
            <li>
                <a href="?c=candidatura">
                    <i class="fa-solid fa-file-signature"></i>
                    Candidaturas
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('entrevista')): ?>
            <li>
                <a href="?c=entrevista">
                    <i class="fa-solid fa-calendar-days"></i>
                    Entrevistas
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('vaga')): ?>
            <li>
                <a href="?c=vaga">
                    <i class="fa-solid fa-briefcase"></i>
                    Vagas
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('categoria')): ?>
            <li>
                <a href="?c=categoria">
                    <i class="fa-solid fa-layer-group"></i>
                    Categorias
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('habilidade')): ?>
            <li>
                <a href="?c=habilidade">
                    <i class="fa-solid fa-star"></i>
                    Habilidades
                </a>
            </li>
        <?php endif; ?>

        <?php if(temPermissao('usuario')): ?>
            <li>
                <a href="?c=usuario">
                    <i class="fa-solid fa-user-shield"></i>
                    Usuários
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('chamada')): ?>
            <li>
                <a href="?c=chamada">
                    <i class="fa-brands fa-whatsapp"></i>
                    Chamadas
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('decisao')): ?>
            <li>
                <a href="?c=decisao">
                    <i class="fa-solid fa-scale-balanced"></i>
                    Centro de Decisões
                </a>
            </li>
        <?php endif; ?>

        <?php if (temPermissao('contratacao')): ?>
            <li>
                <a href="?c=contratacao">
                    <i class="fa-solid fa-user-check"></i>
                    Contratações
                </a>
            </li>
        <?php endif; ?>
            <li>
                <a href="?c=usuario&m=sair">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </a>
            </li>

    </ul>
</div>
<div class="content">