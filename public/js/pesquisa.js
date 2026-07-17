const busca =
document.getElementById(
    "buscaTabela"
);

if (busca) {
    busca.addEventListener(
        "keyup",
        function()
        {
            const valor =
                this.value.toLowerCase();

            document
                .querySelectorAll(
                    "tbody tr"
                )
                .forEach(linha =>
                {
                    linha.style.display =
                        linha.innerText
                            .toLowerCase()
                            .includes(valor)
                        ? ""
                        : "none";
                });
        }
    );
}
