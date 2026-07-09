const categorias = [...new Set(
    habilidades.map(
        h => h.categoria_nome
    )
)];

const selectCategoria =
    document.getElementById("categoria");

const selectHabilidade =
    document.getElementById("habilidade");

const listaHabilidades =
    document.getElementById("listaHabilidades");

const inputsOcultos =
    document.getElementById("inputsOcultos");

const valorNivel =
    document.getElementById("valorNivel");

const slider =
    document.getElementById("nivel");

slider.addEventListener("input", () => {
    valorNivel.innerText = slider.value;
});

categorias.forEach(categoria => {

    const option =
        document.createElement("option");

    option.value = categoria;
    option.textContent = categoria;

    selectCategoria.appendChild(option);

});

selectCategoria.addEventListener("change", () => {

    selectHabilidade.innerHTML =
        '<option value="">Selecione...</option>';

    const categoria =
        selectCategoria.value;

    habilidades
        .filter(
            h => h.categoria_nome === categoria
        )
        .forEach(h => {

            const option =
                document.createElement("option");

            option.value =
                h.idHabilidade;

            option.textContent =
                h.nome;

            option.dataset.nome =
                h.nome;

            selectHabilidade.appendChild(option);

        });

});

function adicionarHabilidade() {

    const habilidadeId =
        Number(selectHabilidade.value);

    if (!habilidadeId) {

        alert("Selecione uma habilidade");

        return;

    }

    const nivel =
        Number(slider.value);

    const habilidadeNome =
        selectHabilidade.options[
            selectHabilidade.selectedIndex
        ].text;

    const descricao =
        document
            .getElementById(
                "descricaoPersonalizada"
            )
            .value
            .trim();

    /*
    |--------------------------------------------------------------------------
    | Habilidade personalizada
    |--------------------------------------------------------------------------
    */

    if (habilidadeNome === "Outra Habilidade") {

        if (descricao === "") {

            alert(
                "Informe a descrição da habilidade."
            );

            return;

        }

        const existente =
            habilidadesSelecionadas.find(h =>

                h.nomeOriginal === "Outra Habilidade"

                &&

                h.descricao.toLowerCase() === descricao.toLowerCase()

            );

        if (existente) {

            existente.nivel = nivel;

            renderizarHabilidades();

            document.getElementById(
                "descricaoPersonalizada"
            ).value = "";

            document.getElementById(
                "grupoDescricaoPersonalizada"
            ).style.display = "none";

            selectHabilidade.selectedIndex = 0;

            slider.value = 5;

            valorNivel.innerText = 5;

            return;

        }

        habilidadesSelecionadas.push({

            id: habilidadeId,

            nome: descricao,

            nomeOriginal: "Outra Habilidade",

            nomeExibicao: descricao,

            descricao: descricao,

            nivel: nivel,

            salva: false

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Habilidades normais
    |--------------------------------------------------------------------------
    */

    else {

        const existente =
            habilidadesSelecionadas.find(
                h => Number(h.id) === habilidadeId
            );

        if (existente) {

            existente.nivel = nivel;

            renderizarHabilidades();

            selectHabilidade.selectedIndex = 0;

            slider.value = 5;

            valorNivel.innerText = 5;

            return;

        }

        habilidadesSelecionadas.push({

            id: habilidadeId,

            nome: habilidadeNome,

            nomeOriginal: habilidadeNome,

            nomeExibicao: habilidadeNome,

            descricao: "",

            nivel: nivel,

            salva: false

        });

    }

    console.log(habilidadesSelecionadas);

    renderizarHabilidades();

    document.getElementById(
        "descricaoPersonalizada"
    ).value = "";

    document.getElementById(
        "grupoDescricaoPersonalizada"
    ).style.display = "none";

    selectHabilidade.selectedIndex = 0;

    slider.value = 5;

    valorNivel.innerText = 5;

}

function renderizarHabilidades() {

    listaHabilidades.innerHTML = "";

    inputsOcultos.innerHTML = "";

    habilidadesSelecionadas.forEach(
        (habilidade, index) => {

            const nivel =
                habilidade.nivel ??
                habilidade.nivelHabilidade ??
                habilidade.nivel_habilidade ??
                0;

            const div =
                document.createElement("div");

            div.className =
                "habilidade-tag";

            div.innerHTML = `
                <span>
                    ${habilidade.nomeExibicao}
                    - Nível ${nivel}
                </span>

                <button
                    type="button"
                    onclick="confirmarRemocaoHabilidade(${index})"
                >
                    ✕
                </button>
            `;

            listaHabilidades.appendChild(div);

            inputsOcultos.innerHTML += `
                <input
                    type="hidden"
                    name="habilidade[${index}]"
                    value="${habilidade.id}"
                >

                <input
                    type="hidden"
                    name="nivel[${index}]"
                    value="${nivel}"
                >

                <input
                    type="hidden"
                    name="nome_exibicao[${index}]"
                    value="${habilidade.nomeExibicao}"
                >

                <input
                    type="hidden"
                    name="habilidade_nome[${index}]"
                    value="${habilidade.nomeOriginal}"
                >
            `;

            if (habilidade.descricao) {

                inputsOcultos.innerHTML += `
                    <input
                        type="hidden"
                        name="descricao_personalizada[${index}]"
                        value="${habilidade.descricao}"
                    >
                `;
            }

        }
    );

}

function confirmarRemocaoHabilidade(index) {

    if (!confirm("Deseja realmente remover esta habilidade?")) {
        return;
    }

    const habilidade =
        habilidadesSelecionadas[index];

    /*
    |--------------------------------------------------
    | Ainda não foi salva no banco
    |--------------------------------------------------
    */

    if (!habilidade.salva) {

        removerHabilidade(index);

        return;

    }

    /*
    |--------------------------------------------------
    | Já existe no banco
    |--------------------------------------------------
    */

    fetch(
        "index.php?c=home&m=removerHabilidade",
        {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({

                habilidade_id: habilidade.id,

                nome_exibicao: habilidade.nomeExibicao,

                habilidade_nome: habilidade.nomeOriginal

            })
        }
    )
        .then(res => {

            return res.json();

        })
        .then(retorno => {

            if (!retorno.sucesso) {

                alert(retorno.mensagem || "Erro ao remover habilidade.");

                return;

            }

            carregarHabilidadesExistentes(
                retorno.habilidades
            );

        });

}

function removerHabilidade(index) {
    habilidadesSelecionadas.splice(
        index,
        1
    );

    renderizarHabilidades();
}

selectHabilidade.addEventListener(
    "change",
    verificarHabilidadePersonalizada
);

function verificarHabilidadePersonalizada() {
    const texto =
        selectHabilidade.options[
            selectHabilidade.selectedIndex
        ]?.text;

    const grupo =
        document.getElementById(
            "grupoDescricaoPersonalizada"
        );

    if (texto === "Outra Habilidade") {
        grupo.style.display = "block";
    }
    else {
        grupo.style.display = "none";
    }
}

renderizarHabilidades();

