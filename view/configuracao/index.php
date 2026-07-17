<?php
$iconePadrao = 'public/img/branding/talentos-icon.svg';
$temas = [
    ['nome' => 'Oceano', 'icone' => 'fa-water', 'primaria' => '#2563EB', 'secundaria' => '#0B1220'],
    ['nome' => 'Esmeralda', 'icone' => 'fa-leaf', 'primaria' => '#059669', 'secundaria' => '#10251F'],
    ['nome' => 'Violeta', 'icone' => 'fa-gem', 'primaria' => '#7C3AED', 'secundaria' => '#1E1733'],
    [
        'nome' => 'Netcom',
        'identidade' => 'netcom',
        'imagem' => 'public/img/branding/netcom-icon.png',
        'primaria' => '#0F4DB0',
        'secundaria' => '#FF6B00'
    ],
    [
        'nome' => 'SumerNet',
        'identidade' => 'sumernet',
        'imagem' => 'public/img/branding/sumernet-icon.png',
        'primaria' => '#E2B725',
        'secundaria' => '#18498F'
    ],
    [
        'nome' => 'NetAki',
        'identidade' => 'netaki',
        'imagem' => 'public/img/branding/netaki-icon.png',
        'primaria' => '#FFFFFF',
        'secundaria' => '#B40404'
    ]
];
?>

<div class="topo configuracao-topo">
    <div>
        <span class="configuracao-eyebrow">Administração</span>
        <h1>Aparência do Sistema</h1>
        <p>Escolha a identidade visual e as cores de todo o sistema.</p>
    </div>
</div>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div class="configuracao-alerta sucesso">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($_SESSION['sucesso']) ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<form
    class="configuracao-form"
    method="POST"
    action="?c=configuracao&m=salvar"
    id="formTema">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($csrfToken) ?>">
    <input
        type="hidden"
        name="identidade"
        id="identidade"
        value="<?= htmlspecialchars($configuracao['identidade']) ?>">

    <section class="configuracao-secao">
        <div class="configuracao-secao-cabecalho">
            <div>
                <h2>Temas Prontos</h2>
                <p>As combinações são as mesmas usadas no sistema de portabilidade.</p>
            </div>
            <i class="fa-solid fa-palette"></i>
        </div>

        <div class="tema-grid">
            <?php foreach ($temas as $tema): ?>
                <button
                    type="button"
                    class="tema-opcao"
                    data-primary="<?= $tema['primaria'] ?>"
                    data-sidebar="<?= $tema['secundaria'] ?>"
                    data-identidade="<?= htmlspecialchars($tema['identidade'] ?? 'padrao') ?>"
                    data-icone="<?= htmlspecialchars($tema['imagem'] ?? $iconePadrao) ?>"
                    aria-label="Selecionar tema <?= htmlspecialchars($tema['nome']) ?>">

                    <span
                        class="tema-icone <?= !empty($tema['imagem']) ? 'tema-icone-marca' : '' ?>"
                        style="--tema-primary:<?= $tema['primaria'] ?>;--tema-sidebar:<?= $tema['secundaria'] ?>">
                        <?php if (!empty($tema['imagem'])): ?>
                            <img
                                src="<?= htmlspecialchars($tema['imagem']) ?>"
                                alt="">
                        <?php else: ?>
                            <i class="fa-solid <?= $tema['icone'] ?>"></i>
                        <?php endif; ?>
                    </span>

                    <span class="tema-nome">
                        <?= htmlspecialchars($tema['nome']) ?>
                    </span>

                    <span class="tema-amostras">
                        <i style="background:<?= $tema['primaria'] ?>"></i>
                        <i style="background:<?= $tema['secundaria'] ?>"></i>
                    </span>

                    <i class="fa-solid fa-circle-check tema-check"></i>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="configuracao-secao">
        <div class="configuracao-secao-cabecalho">
            <div>
                <h2>Cores Personalizadas</h2>
                <p>A alteração é pré-visualizada imediatamente nesta tela.</p>
            </div>
            <i class="fa-solid fa-eye"></i>
        </div>

        <div class="campos-cor">
            <label>
                <span>Cor principal</span>
                <span class="campo-cor">
                    <input
                        type="color"
                        id="corPrimaria"
                        name="corPrimaria"
                        value="<?= htmlspecialchars($configuracao['corPrimaria']) ?>">
                    <strong id="valorPrimaria"></strong>
                </span>
            </label>

            <label>
                <span>Cor do menu</span>
                <span class="campo-cor">
                    <input
                        type="color"
                        id="corSecundaria"
                        name="corSecundaria"
                        value="<?= htmlspecialchars($configuracao['corSecundaria']) ?>">
                    <strong id="valorSecundaria"></strong>
                </span>
            </label>
        </div>
    </section>

    <div class="configuracao-acoes">
        <a class="btn-configuracao-cancelar" href="<?= rotaInicial() ?>">
            Cancelar
        </a>
        <button class="btn" type="submit">
            <i class="fa-solid fa-check"></i>
            Salvar Aparência
        </button>
    </div>
</form>

<script>
    (() => {
        const primaria = document.getElementById("corPrimaria");
        const secundaria = document.getElementById("corSecundaria");
        const identidade = document.getElementById("identidade");
        const opcoes = document.querySelectorAll(".tema-opcao");
        const raiz = document.documentElement;
        const marcaSidebar = document.getElementById("marcaSidebar");
        const iconePadrao = <?= json_encode($iconePadrao) ?>;

        function escurecer(hex, percentual) {
            const fator = (100 - percentual) / 100;
            const valor = hex.replace("#", "");
            const partes = [0, 2, 4].map(inicio =>
                Math.round(parseInt(valor.slice(inicio, inicio + 2), 16) * fator)
                    .toString(16)
                    .padStart(2, "0")
            );
            return `#${partes.join("")}`;
        }

        function contraste(hex) {
            const valor = hex.replace("#", "");
            const rgb = [0, 2, 4].map(inicio =>
                parseInt(valor.slice(inicio, inicio + 2), 16)
            );
            const luminancia =
                (.299 * rgb[0] + .587 * rgb[1] + .114 * rgb[2]) / 255;
            return luminancia > .58 ? "#0F172A" : "#FFFFFF";
        }

        function atualizarTema() {
            const destaque =
                contraste(primaria.value) === "#0F172A"
                ? secundaria.value
                : primaria.value;

            raiz.style.setProperty("--azul", destaque);
            raiz.style.setProperty("--laranja", secundaria.value);
            raiz.style.setProperty(
                "--laranjaescuro",
                escurecer(secundaria.value, 18)
            );
            raiz.style.setProperty(
                "--texto-menu",
                contraste(secundaria.value)
            );

            document.getElementById("valorPrimaria").textContent =
                primaria.value.toUpperCase();
            document.getElementById("valorSecundaria").textContent =
                secundaria.value.toUpperCase();

            opcoes.forEach(opcao => {
                const identidadeTema = opcao.dataset.identidade;
                const selecionada =
                    opcao.dataset.primary.toLowerCase() === primaria.value.toLowerCase()
                    && opcao.dataset.sidebar.toLowerCase() === secundaria.value.toLowerCase()
                    && (
                        !identidadeTema
                        || identidadeTema === identidade.value
                    );
                opcao.classList.toggle("selecionada", selecionada);
            });
        }

        opcoes.forEach(opcao => {
            opcao.addEventListener("click", () => {
                primaria.value = opcao.dataset.primary;
                secundaria.value = opcao.dataset.sidebar;

                if (opcao.dataset.identidade) {
                    identidade.value = opcao.dataset.identidade;
                    document.body.classList.remove(
                        "marca-padrao",
                        "marca-netcom",
                        "marca-sumernet",
                        "marca-netaki"
                    );
                    document.body.classList.add(
                        `marca-${opcao.dataset.identidade}`
                    );
                    document.body.dataset.identidade =
                        opcao.dataset.identidade;

                    if (marcaSidebar && opcao.dataset.icone) {
                        marcaSidebar.src = opcao.dataset.icone;
                        marcaSidebar.alt = opcao.querySelector(".tema-nome").textContent.trim();
                    }
                }

                atualizarTema();
            });
        });

        function usarIdentidadePadrao() {
            identidade.value = "padrao";
            document.body.classList.remove(
                "marca-netcom",
                "marca-sumernet",
                "marca-netaki"
            );
            document.body.classList.add("marca-padrao");
            document.body.dataset.identidade = "padrao";

            if (marcaSidebar) {
                marcaSidebar.src = iconePadrao;
                marcaSidebar.alt = "Talentos";
            }

            atualizarTema();
        }

        primaria.addEventListener("input", usarIdentidadePadrao);
        secundaria.addEventListener("input", usarIdentidadePadrao);
        atualizarTema();
    })();
</script>
