<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Banco de Talentos Netcom
    </title>

    <link
        rel="stylesheet"
        href="public/css/home.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" sizes="16x16" href="public/img/icon_logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/img/icon_logo.png">

</head>

<body>

    <header class="home-header">

        <div class="home-logo">
            <a href="https://netcom.tv.br">
                <img
                    src="public/img/logo.png"
                    alt="Netcom">
            </a>
        </div>

        <div>

            <a
                href="#"
                class="btn-admin"
                id="btnCurriculoLivre">
                <i class="fa-solid fa-file-arrow-up"></i>
                Deixe seu Currículo

            </a>
        </div>

    </header>

    <section class="home-hero">

        <h1>
            Banco de Talentos Netcom
        </h1>

        <p>

            Faça parte da equipe que conecta pessoas através da tecnologia.

        </p>

    </section>

    <?php if (isset($_SESSION['sucesso_curriculo'])): ?>

        <div class="toast toast-sucesso">

            <i class="fa-solid fa-file-circle-check"></i>

            <span>
                <?= $_SESSION['sucesso_curriculo'] ?>
            </span>

        </div>

    <?php unset($_SESSION['sucesso_curriculo']);
    endif; ?>


    <?php if (isset($_SESSION['curriculo_atualizado'])): ?>

        <div class="toast toast-info">

            <i class="fa-solid fa-arrows-rotate"></i>

            <span>
                <?= $_SESSION['curriculo_atualizado'] ?>
            </span>

        </div>

    <?php unset($_SESSION['curriculo_atualizado']);
    endif; ?>


    <?php if (isset($_SESSION['sucesso_candidatura'])): ?>

        <div class="toast toast-sucesso">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?= $_SESSION['sucesso_candidatura'] ?>
            </span>

        </div>

    <?php unset($_SESSION['sucesso_candidatura']);
    endif; ?>


    <?php if (isset($_SESSION['candidatura_atualizada'])): ?>

        <div class="toast toast-info">

            <i class="fa-solid fa-user-check"></i>

            <span>
                <?= $_SESSION['candidatura_atualizada'] ?>
            </span>

        </div>

    <?php unset($_SESSION['candidatura_atualizada']);
    endif; ?>


    <?php if (isset($_SESSION['ja_candidatado'])): ?>

        <div class="toast toast-erro">

            <i class="fa-solid fa-triangle-exclamation"></i>

            <span>
                <?= $_SESSION['ja_candidatado'] ?>
            </span>

        </div>

    <?php unset($_SESSION['ja_candidatado']);
    endif; ?>


    <?php if (isset($_SESSION['erro_curriculo'])): ?>

        <div class="toast toast-erro">

            <i class="fa-solid fa-circle-xmark"></i>

            <span>
                <?= $_SESSION['erro_curriculo'] ?>
            </span>

        </div>

    <?php unset($_SESSION['erro_curriculo']);
    endif; ?>

    <section class="home-vagas">

        <?php if (!empty($vagas)): ?>
            <?php foreach ($vagas as $vaga): ?>

                <div class="vaga-card">

                    <div class="vaga-header">

                        <span class="vaga-badge">
                            Vaga Aberta
                        </span>

                        <h2>
                            <?= $vaga['titulo'] ?>
                        </h2>

                        <p class="vaga-departamento">
                            <i class="fa-solid fa-briefcase"></i>
                            <?= $vaga['departamento'] ?>
                        </p>

                    </div>

                    <div class="vaga-body">

                        <p>

                            <?= mb_strimwidth(
                                strip_tags($vaga['descricao']),
                                0,
                                180,
                                '...'
                            ) ?>

                        </p>

                    </div>

                    <div class="vaga-footer">

                        <div class="vaga-acoes">

                            <button
                                class="btn-detalhes"

                                data-id="<?= $vaga['idVaga'] ?>"

                                data-titulo="<?= htmlspecialchars($vaga['titulo']) ?>"

                                data-departamento="<?= htmlspecialchars($vaga['departamento']) ?>"

                                data-cidade="<?= htmlspecialchars($vaga['cidade']) ?>"

                                data-modalidade="<?= htmlspecialchars($vaga['modalidade']) ?>"

                                data-tipo="<?= htmlspecialchars($vaga['tipo_contratacao']) ?>"

                                data-escala="<?= htmlspecialchars($vaga['escala']) ?>"

                                data-descricao="<?= htmlspecialchars($vaga['descricao']) ?>"

                                data-requisitos="<?= htmlspecialchars($vaga['requisitos']) ?>"

                                data-observacoes="<?= htmlspecialchars($vaga['observacoes']) ?>"

                                data-cnh="<?= htmlspecialchars($vaga['cnh_obrigatoria']) ?>">

                                Ver Detalhes

                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="sem-vagas">

                <i class="fa-solid fa-briefcase"></i>

                <h2>
                    Nenhuma vaga disponível no momento
                </h2>

                <p>
                    Não encontramos oportunidades abertas agora,
                    mas você pode deixar seu currículo em nosso
                    banco de talentos para futuras seleções.
                </p>

                <button
                    type="button"
                    class="btn-curriculo"
                    id='btnCurriculoLivre'>

                    <i class="fa-solid fa-file-arrow-up"></i>
                    Deixe seu Currículo

                </button>

            </div>

        <?php endif; ?>
    </section>

    <div id="modalEmail" class="modal-filtro">

        <div class="modal-filtro-content modal-email">

            <button
                type="button"
                class="fechar-modal"
                onclick="fecharModalEmail()">

                &times;

            </button>

            <div class="modal-email-icon">

                <i class="fa-solid fa-envelope"></i>

            </div>

            <h3>

                Bem-vindo!

            </h3>

            <p>

                Informe seu e-mail para continuar.

                Caso ele já esteja cadastrado,
                enviaremos um código de confirmação.

            </p>

            <input

                type="email"

                id="emailAcesso"

                placeholder="email@dominio.com">

            <div id="mensagemEmail"></div>

            <button

                type="button"

                class="btn"

                onclick="verificarEmail()">

                Continuar

            </button>

        </div>

    </div>

    <div
        id="modalDetalhes"
        class="modal">

        <div class="modal-content modal-vaga">

            <span
                class="fechar-detalhes">

                &times;

            </span>

            <h2 id="detalheTitulo"></h2>

            <div class="vaga-info-grid">

                <div>
                    <strong>Departamento</strong>
                    <span id="detalheDepartamento"></span>
                </div>

                <div>
                    <strong>Cidade</strong>
                    <span id="detalheCidade"></span>
                </div>

                <div>
                    <strong>Modalidade</strong>
                    <span id="detalheModalidade"></span>
                </div>

                <div>
                    <strong>Contratação</strong>
                    <span id="detalheTipo"></span>
                </div>

                <div>
                    <strong>Escala</strong>
                    <span id="detalheEscala"></span>
                </div>

                <div>
                    <strong>CNH Obrigatória</strong>
                    <span id="cnhObrigatoria"><?= $vaga['cnh_obrigatoria'] ?></span>
                </div>

            </div>

            <hr>

            <h3>Descrição</h3>

            <div id="detalheDescricao"></div>

            <h3>Requisitos</h3>

            <div id="detalheRequisitos"></div>

            <div id="blocoObservacoes">

                <h3>Observações</h3>

                <div id="detalheObservacoes"></div>

            </div>

            <button
                id="btnAbrirCandidatura"
                class="btn-candidatar">

                Candidatar-se

            </button>

        </div>

    </div>

    <div
        id="modalCandidatura"
        class="modal">

        <div class="modal-content">

            <span
                class="fechar-modal">

                &times;

            </span>

            <h2 id="tituloModal">
                Candidatar-se
            </h2>

            <form
                id="formCandidatura"
                method="POST"
                action="?c=home&m=candidatar"
                enctype="multipart/form-data">

                <div id="campoVaga">

                    <input
                        type="hidden"
                        name="vaga_id"
                        id="vaga_id">

                </div>

                <input
                    type="hidden"
                    name="atualizar_cadastro"
                    id="atualizar_cadastro"
                    value="0">

                <input
                    type="hidden"
                    name="tipo_cadastro"
                    id="tipo_cadastro"
                    value="vaga">

                <div class="form-group">

                    <label>
                        Nome Completo
                    </label>

                    <input
                        id="nome"
                        type="text"
                        name="nome"
                        required
                        pattern="^[A-Za-zÀ-ÿ\s]{5,100}$">

                </div>

                <div class="form-group">

                    <label>
                        Telefone
                    </label>

                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        required>

                </div>

                <div class="form-group checkbox">

                    <input
                        type="checkbox"
                        name="whatsapp"
                        id="whatsapp"
                        value="1">

                    <label>
                        Este número possui WhatsApp
                    </label>

                </div>

                <div class="form-group">

                    <label>
                        E-mail
                    </label>

                    <input

                        type="email"
                        name="email"
                        id="email"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Currículo
                    </label>

                    <input
                        type="file"
                        name="curriculo"
                        accept=".pdf,.doc,.docx">

                </div>
                <div
                    id="curriculo-info"
                    class="curriculo-info"
                    style="display:none;">
                </div>

                <h3>Habilidades</h3>

                <div class="habilidades-container">

                    <div class="form-group">

                        <label>Categoria</label>

                        <select id="categoria">
                            <option value="">
                                Selecione...
                            </option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label>Habilidade</label>

                        <select id="habilidade">

                            <option value="">
                                Selecione...
                            </option>

                        </select>

                    </div>

                    <div
                        class="form-group"
                        id="grupoDescricaoPersonalizada"
                        style="display:none;">

                        <label>
                            Descrição da Habilidade
                        </label>

                        <input
                            type="text"
                            id="descricaoPersonalizada">

                    </div>

                    <div class="form-group">

                        <div class="nivel-header">

                            <label>Nível</label>

                            <span id="valorNivel">
                                5
                            </span>

                        </div>

                        <input
                            type="range"
                            id="nivel"
                            min="0"
                            max="10"
                            value="5">

                    </div>

                    <div class="form-group btn-habilidade-group">

                        <button
                            type="button"
                            class="btn-habilidade"
                            onclick="adicionarHabilidade()">

                            Adicionar Habilidade

                        </button>

                    </div>

                </div>

                <h4>
                    Habilidades Adicionadas
                </h4>

                <div id="listaHabilidades"></div>

                <div id="inputsOcultos"></div>
                <br>

                <br>
                <div class="form-group termos-lgpd">

                    <div class="lgpd-container">

                        <input
                            type="checkbox"
                            id="aceite_lgpd"
                            name="aceite_lgpd"
                            required>

                        <label for="aceite_lgpd">

                            Li e concordo com os

                            <a href="#"
                                onclick="abrirTermos(); return false;">
                                Termos de Privacidade
                            </a>

                        </label>

                    </div>

                </div>
                <button
                    type="submit"
                    class="btn-candidatar"
                    id="btnEnviar">

                    Enviar Candidatura

                </button>

            </form>

        </div>

    </div>
    <div id="modalTermos" class="modal-termos">

        <div class="modal-termos-content">

            <div class="modal-termos-header">

                <h2>
                    Termo de Consentimento para Tratamento de Dados Pessoais
                </h2>

                <button
                    type="button"
                    class="btn-fechar-termos"
                    onclick="fecharTermos()">

                    ×

                </button>

            </div>

            <div class="modal-termos-body">

                <p>
                    Ao realizar seu cadastro no Banco de Talentos da Netcom TV,
                    enviar seu currículo ou se candidatar a uma vaga, você declara
                    estar ciente e de acordo com o tratamento dos seus dados pessoais
                    para fins de recrutamento, seleção, contratação e gestão de
                    oportunidades profissionais.
                </p>

                <h3>Dados coletados</h3>

                <ul>
                    <li>Nome completo;</li>
                    <li>Telefone e WhatsApp;</li>
                    <li>Endereço de e-mail;</li>
                    <li>Currículo profissional;</li>
                    <li>Informações profissionais e acadêmicas fornecidas pelo candidato;</li>
                    <li>Habilidades, experiências e qualificações;</li>
                    <li>Histórico de candidaturas, entrevistas e processos seletivos;</li>
                    <li>Demais informações fornecidas voluntariamente pelo candidato.</li>
                </ul>

                <h3>Finalidade do tratamento</h3>

                <ul>
                    <li>Cadastro no Banco de Talentos;</li>
                    <li>Análise de perfil profissional;</li>
                    <li>Participação em processos seletivos atuais e futuros;</li>
                    <li>Agendamento e gestão de entrevistas;</li>
                    <li>Comunicação entre a empresa e o candidato;</li>
                    <li>Registro de histórico de participação em processos seletivos;</li>
                    <li>Avaliação para contratação.</li>
                </ul>

                <h3>Compartilhamento de dados</h3>

                <p>
                    A Netcom TV não comercializa os dados pessoais dos candidatos.
                    Os dados poderão ser acessados apenas por colaboradores
                    autorizados envolvidos nos processos de recrutamento,
                    seleção e contratação.
                </p>

                <h3>Armazenamento e segurança</h3>

                <p>
                    A empresa adota medidas técnicas e administrativas para proteger
                    os dados pessoais contra acessos não autorizados, perda,
                    alteração ou divulgação indevida.
                </p>

                <h3>Tempo de retenção</h3>

                <p>
                    Os dados poderão permanecer armazenados enquanto houver interesse
                    legítimo da empresa em manter o candidato em seu Banco de Talentos
                    ou pelo período necessário para cumprimento de obrigações legais.
                </p>

                <h3>Direitos do titular</h3>

                <P>
                    Nos termos da Lei nº 13.709/2018 (Lei Geral de Proteção de Dados Pessoais - LGPD), o candidato poderá solicitar:
                </P>

                <ul>
                    <li>Confirmação da existência de tratamento;</li>
                    <li>Acesso aos dados armazenados;</li>
                    <li>Correção de dados desatualizados;</li>
                    <li>Atualização cadastral;</li>
                    <li>Exclusão dos dados quando legalmente aplicável;</li>
                    <li>Informações sobre o tratamento realizado.</li>
                </ul>

                <h3>Consentimento</h3>

                <p>
                    Ao marcar a opção de aceite e enviar seus dados,
                    você declara que leu, compreendeu e concorda com os termos acima,
                    autorizando a Netcom TV a realizar o tratamento das informações
                    fornecidas para as finalidades descritas neste documento.
                </p>

                <p>
                    <strong>Última atualização:</strong>
                    Junho/2026
                </p>

            </div>

        </div>

    </div>

    <!-- MODAL TOKEN -->
    <div id="modalToken" class="modal-filtro">

        <div class="modal-filtro-content modal-token">

            <button
                type="button"
                class="btn-fechar-modal"
                onclick="fecharModalToken()">

                &times;

            </button>

            <h3>Confirmação de Segurança</h3>

            <p>

                Enviamos um código de verificação para seu e-mail.

            </p>

            <div class="token-boxes">

                <?php for ($i = 0; $i < 6; $i++): ?>

                    <input
                        type="text"
                        maxlength="1"
                        class="token-input"
                        inputmode="numeric">

                <?php endfor; ?>

            </div>

            <div id="mensagemToken"></div>

            <div class="acoes-token">

                <button
                    type="button"
                    class="btn"
                    onclick="validarToken()">

                    Confirmar

                </button>

                <button
                    type="button"
                    class="btn btn-secundario"
                    onclick="reenviarToken()">

                    Reenviar código

                </button>

            </div>

        </div>

    </div>

</body>
<script>
    //Modal Termos
    function abrirTermos() {

        document.getElementById(
            "modalTermos"
        ).style.display = "flex";
    }

    function fecharTermos() {

        document.getElementById(
            "modalTermos"
        ).style.display = "none";
    }

    window.addEventListener(
        "click",
        function(e) {

            const modal =
                document.getElementById(
                    "modalTermos"
                );

            if (e.target === modal) {

                fecharTermos();
            }
        }
    );

    document
        .getElementById("telefone")
        .addEventListener(
            "input",
            function(e) {

                let v =
                    e.target.value
                    .replace(/\D/g, '');

                if (v.length > 11) {

                    v =
                        v.substring(0, 11);
                }

                if (v.length > 10) {

                    v =
                        v.replace(
                            /^(\d{2})(\d{5})(\d{4}).*/,
                            '($1) $2-$3'
                        );
                } else {

                    v =
                        v.replace(
                            /^(\d{2})(\d{4})(\d{0,4}).*/,
                            '($1) $2-$3'
                        );
                }

                e.target.value = v;
            }
        );


    const modalDetalhes =
        document.getElementById(
            "modalDetalhes"
        );

    const modalCandidatura =
        document.getElementById(
            "modalCandidatura"
        );

    const fecharDetalhes =
        document.querySelector(
            ".fechar-detalhes"
        );

    const fecharCandidatura =
        document.querySelector(
            ".fechar-modal"
        );

    let vagaSelecionada = null;

    /*
    |--------------------------------------------------------------------------
    | ABRIR DETALHES
    |--------------------------------------------------------------------------
    */

    function formatar(valor) {
        if (valor == 1) {
            return 'Sim'
        } else {
            return 'Não'
        }
    }

    document
        .querySelectorAll(
            ".btn-detalhes"
        )



        .forEach(btn => {

            btn.addEventListener(
                "click",
                function() {

                    vagaSelecionada =
                        this.dataset.id;

                    document
                        .getElementById(
                            "detalheTitulo"
                        )
                        .innerText =
                        this.dataset.titulo;

                    document
                        .getElementById(
                            "detalheDepartamento"
                        )
                        .innerText =
                        this.dataset.departamento;

                    document
                        .getElementById(
                            "detalheCidade"
                        )
                        .innerText =
                        this.dataset.cidade;

                    document
                        .getElementById(
                            "detalheModalidade"
                        )
                        .innerText =
                        this.dataset.modalidade;

                    document
                        .getElementById(
                            "detalheTipo"
                        )
                        .innerText =
                        this.dataset.tipo;

                    document
                        .getElementById(
                            "detalheEscala"
                        )
                        .innerText =
                        this.dataset.escala;

                    document
                        .getElementById(
                            "cnhObrigatoria"
                        )
                        .innerText = formatar(this.dataset.cnh)

                    document
                        .getElementById(
                            "detalheDescricao"
                        )
                        .innerText =
                        this.dataset.descricao;

                    document
                        .getElementById(
                            "detalheRequisitos"
                        )
                        .innerText =
                        this.dataset.requisitos;

                    document
                        .getElementById(
                            "detalheObservacoes"
                        )
                        .innerText =
                        this.dataset.observacoes;

                    document
                        .getElementById(
                            "blocoObservacoes"
                        )
                        .style.display =
                        this.dataset.observacoes &&
                        this.dataset.observacoes.trim() ?
                        "block" :
                        "none";

                    modalDetalhes
                        .style.display =
                        "flex";
                }
            );

        });

    /*
    |--------------------------------------------------------------------------
    | FECHAR DETALHES
    |--------------------------------------------------------------------------
    */

    if (fecharDetalhes) {

        fecharDetalhes.onclick =
            function() {

                modalDetalhes
                    .style.display =
                    "none";
            };
    }

    /*
    |--------------------------------------------------------------------------
    | ABRIR FORMULÁRIO DE CANDIDATURA
    |--------------------------------------------------------------------------
    */

    document
        .getElementById("btnAbrirCandidatura")
        ?.addEventListener(
            "click",
            function() {
                iniciarFluxo("vaga");

            }
        );

    document
        .getElementById("btnCurriculoLivre")
        ?.addEventListener(
            "click",
            function() {
                iniciarFluxo("livre");

            }
        );

    /*
    |--------------------------------------------------------------------------
    | FECHAR FORMULÁRIO
    |--------------------------------------------------------------------------
    */

    if (fecharCandidatura) {

        fecharCandidatura.onclick =
            function() {

                modalCandidatura
                    .style.display =
                    "none";
            };
    }

    /*
    |--------------------------------------------------------------------------
    | FECHAR AO CLICAR FORA
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "click",
        function(event) {

            if (
                event.target ===
                modalDetalhes
            ) {

                modalDetalhes
                    .style.display =
                    "none";
            }

            if (
                event.target ===
                modalCandidatura
            ) {

                modalCandidatura
                    .style.display =
                    "none";
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | MÁSCARA TELEFONE
    |--------------------------------------------------------------------------
    */

    const telefone =
        document.getElementById(
            "telefone"
        );

    if (telefone) {

        telefone.addEventListener(
            "input",
            function(e) {

                let v =
                    e.target.value
                    .replace(/\D/g, '');

                if (
                    v.length > 11
                ) {

                    v =
                        v.substring(
                            0,
                            11
                        );
                }

                if (
                    v.length > 10
                ) {

                    v =
                        v.replace(
                            /^(\d{2})(\d{5})(\d{4}).*/,
                            '($1) $2-$3'
                        );

                } else {

                    v =
                        v.replace(
                            /^(\d{2})(\d{4})(\d{0,4}).*/,
                            '($1) $2-$3'
                        );
                }

                e.target.value =
                    v;
            }
        );
    }

    function abrirModalCandidatura() {

        modalDetalhes.style.display = "none";

        document
            .getElementById("tituloModal")
            .innerText =
            "Candidatar-se";

        document
            .getElementById("btnEnviar")
            .innerText =
            "Enviar Candidatura";

        document
            .getElementById("tipo_cadastro")
            .value =
            "vaga";

        document
            .getElementById("campoVaga")
            .style.display =
            "block";

        document
            .getElementById("formCandidatura")
            .action =
            "?c=home&m=candidatar";

        document
            .getElementById("vaga_id")
            .value =
            vagaSelecionada;

        modalCandidatura.style.display =
            "flex";

    }

    function abrirModalCurriculoLivre() {

        modalDetalhes.style.display = "none";

        document
            .getElementById("tituloModal")
            .innerText =
            "Cadastro de Currículo";

        document
            .getElementById("btnEnviar")
            .innerText =
            "Cadastrar Currículo";

        document
            .getElementById("tipo_cadastro")
            .value =
            "livre";

        document
            .getElementById("campoVaga")
            .style.display =
            "none";

        document
            .getElementById("formCandidatura")
            .action =
            "?c=home&m=candidatar";

        document
            .getElementById("vaga_id")
            .value =
            "";

        modalCandidatura.style.display =
            "flex";

    }

    const habilidades =
        <?= json_encode($habilidades) ?>;
    let habilidadesSelecionadas = [];


    document.addEventListener(
        "DOMContentLoaded",
        () => {

            document
                .querySelectorAll(".toast")
                .forEach(toast => {

                    setTimeout(() => {

                        toast.classList.add("show");

                    }, 100);

                    setTimeout(() => {

                        toast.classList.remove("show");

                        setTimeout(() => {

                            toast.remove();

                        }, 500);

                    }, 5000);

                });

        }
    );

    const inputs =
        document.querySelectorAll(".token-input");

    inputs.forEach((input, index) => {

        input.addEventListener("input", () => {

            input.value =
                input.value.replace(/\D/g, '');

            if (
                input.value &&
                index < inputs.length - 1
            ) {

                inputs[index + 1].focus();

            }

        });

        input.addEventListener("keydown", (e) => {

            if (
                e.key === "Backspace" &&
                !input.value &&
                index > 0
            ) {

                inputs[index - 1].focus();

            }

        });

    });

    inputs[0].addEventListener(
        "paste",
        function(e) {

            e.preventDefault();

            const codigo =
                (
                    e.clipboardData ||
                    window.clipboardData
                )
                .getData("text")
                .replace(/\D/g, '');

            codigo
                .split("")
                .forEach((numero, index) => {

                    if (inputs[index]) {

                        inputs[index].value =
                            numero;

                    }

                });

        });

    function obterToken() {

        let codigo = "";

        document
            .querySelectorAll(".token-input")
            .forEach(input => {

                codigo += input.value;

            });

        return codigo;

    }

    function validarToken() {

        const token =
            obterToken();

        if (token.length !== 6) {

            document
                .getElementById("mensagemToken")
                .innerHTML =
                "Informe os 6 dígitos.";

            return;

        }

        fetch(

                "index.php?c=home&m=validarToken",

                {

                    method: "POST",

                    headers: {

                        "Content-Type": "application/x-www-form-urlencoded"

                    },

                    body: new URLSearchParams({

                        token: token

                    })

                }

            )

            .then(res => res.json())

            .then(retorno => {

                if (!retorno.sucesso) {

                    document
                        .getElementById("mensagemToken")
                        .innerHTML =
                        retorno.mensagem;

                    return;

                }

                fecharModalToken();

                preencherFormulario(
                    retorno.dados
                );

                if (acaoCandidato === "vaga") {

                    abrirModalCandidatura();

                } else {

                    abrirModalCurriculoLivre();

                }

            })

            .catch(() => {

                document
                    .getElementById("mensagemToken")
                    .innerHTML =
                    "Erro ao validar o código.";

            });

    }

    function carregarCurriculo(candidato) {

        const info =
            document.getElementById(
                "curriculo-info"
            );

        if (!candidato.curriculo) {

            info.style.display = "none";

            return;

        }

        info.style.display = "block";

        info.innerHTML = `

        <div class="curriculo-alerta">

            <i class="fa-solid fa-file-pdf"></i>

            <div>

                <strong>
                    Currículo já cadastrado.
                </strong>

                <br>

                Envie outro arquivo apenas se desejar atualizá-lo.

            </div>

        </div>

    `;

    }

    function carregarHabilidadesExistentes(habilidadesCandidato) {

        habilidadesSelecionadas = [];

        habilidadesCandidato.forEach(h => {

            habilidadesSelecionadas.push({

                id: h.idHabilidade,

                nome: h.nome,

                nomeOriginal: h.nome,

                nomeExibicao: h.nome_exibicao ?? h.nome,

                descricao: h.nome === "Outra Habilidade" ?
                    h.nome_exibicao :
                    "",

                nivel: Number(h.nivel),

                salva: true

            });

        });

        renderizarHabilidades();

    }

    function preencherFormulario(candidato) {

        document.getElementById("nome").value =
            candidato.nome ?? "";

        document.getElementById("telefone").value =
            candidato.telefone ?? "";

        document.querySelector(
                "input[name='email']"
            ).value =
            candidato.email ?? "";

        const chkWhatsapp =
            document.getElementById("whatsapp");

        if (chkWhatsapp) {

            chkWhatsapp.checked =
                candidato.whatsapp == 1;

        }

        document.getElementById(
            "atualizar_cadastro"
        ).value = 1;

        carregarCurriculo(candidato);

        carregarHabilidadesExistentes(
            candidato.habilidades ?? []
        );

    }

    function abrirModalToken() {

        document
            .getElementById("modalToken")
            .style.display = "flex";

    }

    function fecharModalToken() {

        document
            .getElementById("modalToken")
            .style.display = "none";

    }

    window.addEventListener("click", function(e) {

        const modal =
            document.getElementById("modalToken");

        if (e.target === modal) {

            fecharModalToken();

        }

    });

    let acaoCandidato = "";

    function abrirModalEmail(acao) {

        acaoCandidato = acao;

        document
            .getElementById("emailAcesso")
            .value = "";

        document
            .getElementById("mensagemEmail")
            .innerHTML = "";

        document
            .getElementById("modalEmail")
            .style.display = "flex";

    }

    function fecharModalEmail() {

        document
            .getElementById("modalEmail")
            .style.display = "none";

    }

    window.addEventListener("click", function(e) {

        const modal =
            document.getElementById("modalEmail");

        if (e.target === modal) {

            fecharModalEmail();

        }

    });

    function reenviarToken() {

        alert("Em desenvolvimento");

    }


    function verificarEmail() {

        const email = document
            .getElementById("emailAcesso")
            .value
            .trim();

        if (email === "") {

            document
                .getElementById("mensagemEmail")
                .innerHTML =
                "Informe um e-mail.";

            return;
        }

        const botao =
            document.querySelector(
                "#modalEmail .btn"
            );

        botao.disabled = true;

        botao.innerHTML = "Verificando...";

        fetch("index.php?c=home&m=verificarEmail", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({

                    email: email

                })

            })

            .then(res => res.json())

            .then(retorno => {

                botao.disabled = false;
                botao.innerHTML = "Continuar";


                if (!retorno.sucesso) {

                    document
                        .getElementById("mensagemEmail")
                        .innerHTML =
                        retorno.mensagem;

                    return;
                }

                /*
                 * Candidato novo
                 */

                if (retorno.novo) {

                    fecharModalEmail();

                    if (acaoCandidato === "vaga") {

                        abrirModalCandidatura();

                    } else {

                        abrirModalCurriculoLivre();

                    }

                    return;

                }

                /*
                 * Já possui cadastro
                 */

                fecharModalEmail();

                abrirModalToken();

            })

            .catch(() => {

                botao.disabled = false;

                botao.innerHTML = "Continuar";

                document
                    .getElementById("mensagemEmail")
                    .innerHTML =
                    "Erro ao comunicar com o servidor.";

            });

    }

    function iniciarFluxo(acao) {

        acaoCandidato = acao;

        fetch(
                "index.php?c=home&m=verificarSessao"
            )

            .then(res => res.json())

            .then(retorno => {

                if (retorno.autenticado) {

                    preencherFormulario(
                        retorno.dados
                    );

                    if (acaoCandidato === "vaga") {

                        abrirModalCandidatura();

                    } else {

                        abrirModalCurriculoLivre();

                    }

                    return;

                }

                abrirModalEmail(acao);

            });

    }
</script>
<script src="public/js/habilidades.js"></script>

</html>