<?php
$moduloAtualMenu = strtolower((string)($_GET['c'] ?? 'dashboard'));
$metodoAtualMenu = (string)($_GET['m'] ?? 'index');

$gruposMenu = [
    [
        'id' => 'talentos',
        'rotulo' => 'Talentos',
        'icone' => 'fa-users',
        'itens' => array_values(array_filter([
            temPermissao('candidato') ? [
                'modulo' => 'candidato',
                'rotulo' => 'Candidatos',
                'icone' => 'fa-user-group'
            ] : null,
            temPermissao('candidatura') ? [
                'modulo' => 'candidatura',
                'rotulo' => 'Candidaturas',
                'icone' => 'fa-file-signature'
            ] : null,
            temPermissao('vaga') ? [
                'modulo' => 'vaga',
                'rotulo' => 'Vagas',
                'icone' => 'fa-briefcase'
            ] : null
        ]))
    ],
    [
        'id' => 'processo-seletivo',
        'rotulo' => 'Processo seletivo',
        'icone' => 'fa-list-check',
        'itens' => array_values(array_filter([
            temPermissao('entrevista') ? [
                'modulo' => 'entrevista',
                'rotulo' => 'Entrevistas',
                'icone' => 'fa-calendar-days'
            ] : null,
            temPermissao('chamada') ? [
                'modulo' => 'chamada',
                'rotulo' => 'Chamadas',
                'icone' => 'fa-brands fa-whatsapp'
            ] : null,
            temPermissao('decisao') ? [
                'modulo' => 'decisao',
                'rotulo' => 'Centro de Decis&otilde;es',
                'icone' => 'fa-scale-balanced'
            ] : null,
            temPermissao('contratacao') ? [
                'modulo' => 'contratacao',
                'rotulo' => 'Contrata&ccedil;&otilde;es',
                'icone' => 'fa-user-check'
            ] : null
        ]))
    ],
    [
        'id' => 'cadastros',
        'rotulo' => 'Cadastros',
        'icone' => 'fa-folder-tree',
        'itens' => array_values(array_filter([
            temPermissao('categoria') ? [
                'modulo' => 'categoria',
                'rotulo' => 'Categorias',
                'icone' => 'fa-layer-group'
            ] : null,
            temPermissao('habilidade') ? [
                'modulo' => 'habilidade',
                'rotulo' => 'Habilidades',
                'icone' => 'fa-star'
            ] : null,
            podeGerenciarDepartamentos() ? [
                'modulo' => 'departamento',
                'rotulo' => 'Departamentos',
                'icone' => 'fa-building'
            ] : null
        ]))
    ],
    [
        'id' => 'administracao',
        'rotulo' => 'Administra&ccedil;&atilde;o',
        'icone' => 'fa-gear',
        'itens' => array_values(array_filter([
            temPermissao('usuario') ? [
                'modulo' => 'usuario',
                'rotulo' => 'Usu&aacute;rios',
                'icone' => 'fa-user-shield'
            ] : null,
            podeAcessarLog() ? [
                'modulo' => 'log',
                'rotulo' => 'Logs',
                'icone' => 'fa-clock-rotate-left'
            ] : null,
            podeAcessarTema() ? [
                'modulo' => 'configuracao',
                'rotulo' => 'Apar&ecirc;ncia',
                'icone' => 'fa-palette'
            ] : null,
            podeConfigurarEmail() ? [
                'modulo' => 'configuracaoEmail',
                'rotulo' => 'E-mail do Token',
                'icone' => 'fa-envelope-circle-check'
            ] : null
        ]))
    ]
];

$gruposMenu = array_values(array_filter(
    $gruposMenu,
    function ($grupo) {
        return !empty($grupo['itens']);
    }
));
?>

<aside class="sidebar">
    <div class="logo marca-sidebar">
        <span class="marca-sidebar-fundo">
            <img
                id="marcaSidebar"
                src="<?= htmlspecialchars($configuracaoSistema['iconeMarca']) ?>"
                alt="<?= htmlspecialchars($configuracaoSistema['nomeMarca']) ?>">
        </span>
        <span class="marca-sidebar-texto">
            Banco de Talentos
            <small><?= htmlspecialchars($configuracaoSistema['nomeMarca']) ?></small>
        </span>
    </div>

    <nav class="sidebar-nav" aria-label="Navega&ccedil;&atilde;o principal">
        <ul class="menu-principal">
            <?php if (temPermissao('dashboard')): ?>
                <li>
                    <a
                        href="?c=dashboard"
                        data-menu-modulo="dashboard">
                        <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php foreach ($gruposMenu as $grupo): ?>
                <?php
                $modulosGrupo = array_map(
                    function ($item) {
                        return strtolower($item['modulo']);
                    },
                    $grupo['itens']
                );
                $grupoAberto = in_array(
                    $moduloAtualMenu,
                    $modulosGrupo,
                    true
                );

                if (
                    $grupo['id'] === 'administracao'
                    && $moduloAtualMenu === 'usuario'
                    && in_array(
                        strtolower($metodoAtualMenu),
                        ['minhaconta', 'alterarminhasenha', 'sair'],
                        true
                    )
                ) {
                    $grupoAberto = false;
                }
                ?>
                <li
                    class="menu-grupo <?= $grupoAberto ? 'aberto possui-ativo' : '' ?>"
                    data-menu-grupo="<?= htmlspecialchars($grupo['id']) ?>">
                    <button
                        type="button"
                        class="menu-grupo-toggle"
                        aria-expanded="<?= $grupoAberto ? 'true' : 'false' ?>"
                        aria-controls="submenu-<?= htmlspecialchars($grupo['id']) ?>">
                        <i
                            class="fa-solid <?= htmlspecialchars($grupo['icone']) ?>"
                            aria-hidden="true"></i>
                        <span><?= $grupo['rotulo'] ?></span>
                        <i
                            class="fa-solid fa-chevron-down menu-grupo-seta"
                            aria-hidden="true"></i>
                    </button>

                    <ul
                        class="menu-subitens"
                        id="submenu-<?= htmlspecialchars($grupo['id']) ?>"
                        <?= $grupoAberto ? '' : 'hidden' ?>>
                        <?php foreach ($grupo['itens'] as $item): ?>
                            <?php
                            $classeIcone = strpos($item['icone'], 'fa-brands') === 0
                                ? $item['icone']
                                : 'fa-solid ' . $item['icone'];
                            ?>
                            <li>
                                <a
                                    href="?c=<?= urlencode($item['modulo']) ?>"
                                    data-menu-modulo="<?= htmlspecialchars(strtolower($item['modulo'])) ?>">
                                    <i
                                        class="<?= htmlspecialchars($classeIcone) ?>"
                                        aria-hidden="true"></i>
                                    <span><?= $item['rotulo'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>

        <ul class="menu-conta">
            <li>
                <a
                    href="?c=usuario&amp;m=minhaConta"
                    data-menu-modulo="usuario"
                    data-menu-metodo="minhaConta">
                    <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                    <span>Minha conta</span>
                </a>
            </li>
            <li>
                <a href="?c=usuario&amp;m=sair" class="menu-sair">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script>
    (function () {
        const parametros = new URLSearchParams(window.location.search);
        const moduloAtual = (parametros.get('c') || 'dashboard').toLowerCase();
        const metodoAtual = parametros.get('m') || 'index';
        const metodosConta = ['minhaConta', 'alterarMinhaSenha', 'sair'];
        const grupos = Array.from(document.querySelectorAll('.menu-grupo'));

        function definirGrupo(grupo, aberto) {
            const botao = grupo.querySelector('.menu-grupo-toggle');
            const submenu = grupo.querySelector('.menu-subitens');

            grupo.classList.toggle('aberto', aberto);
            botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
            submenu.hidden = !aberto;
        }

        function abrirSomente(grupoSelecionado) {
            grupos.forEach(function (grupo) {
                definirGrupo(grupo, grupo === grupoSelecionado);
            });
        }

        document.querySelectorAll('.sidebar a[data-menu-modulo]').forEach(function (link) {
            const moduloLink = link.dataset.menuModulo.toLowerCase();
            const metodoLink = link.dataset.menuMetodo || '';
            let ativo = moduloLink === moduloAtual;

            if (ativo && metodoLink) {
                ativo = metodoLink === metodoAtual;
            } else if (
                ativo
                && moduloLink === 'usuario'
                && metodosConta.includes(metodoAtual)
            ) {
                ativo = false;
            }

            if (!ativo) {
                return;
            }

            link.classList.add('ativo');
            link.setAttribute('aria-current', 'page');

            const grupo = link.closest('.menu-grupo');
            if (grupo) {
                grupo.classList.add('possui-ativo');
                abrirSomente(grupo);
            }
        });

        grupos.forEach(function (grupo) {
            grupo.querySelector('.menu-grupo-toggle').addEventListener('click', function () {
                const abrir = !grupo.classList.contains('aberto');

                if (abrir) {
                    abrirSomente(grupo);
                    localStorage.setItem('menuGrupoAberto', grupo.dataset.menuGrupo);
                } else {
                    definirGrupo(grupo, false);
                    localStorage.removeItem('menuGrupoAberto');
                }
            });
        });

        if (!document.querySelector('.menu-grupo.possui-ativo')) {
            const grupoSalvo = localStorage.getItem('menuGrupoAberto');
            const grupo = grupos.find(function (item) {
                return item.dataset.menuGrupo === grupoSalvo;
            });

            if (grupo) {
                abrirSomente(grupo);
            }
        }
    }());
</script>

<main class="content">
