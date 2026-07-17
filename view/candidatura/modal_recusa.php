<div
    class="modal-recusa"
    id="modalRecusa"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalRecusaTitulo"
    aria-hidden="true">
    <button
        type="button"
        class="modal-recusa-fundo"
        data-fechar-modal-recusa
        aria-label="Fechar detalhes da entrevista"></button>

    <section class="modal-recusa-painel">
        <header class="modal-recusa-cabecalho">
            <div>
                <span class="modal-recusa-rotulo">Detalhes da decis&atilde;o</span>
                <h2 id="modalRecusaTitulo">Candidatura recusada</h2>
            </div>
            <button
                type="button"
                class="modal-recusa-fechar"
                data-fechar-modal-recusa
                aria-label="Fechar detalhes da entrevista"
                title="Fechar">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="modal-recusa-corpo">
            <div class="modal-recusa-carregando" id="recusaCarregando">
                <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
                Carregando detalhes...
            </div>
            <div class="modal-recusa-erro" id="recusaErro" hidden></div>

            <div id="recusaDetalhes" hidden>
                <p class="modal-recusa-contexto" id="recusaContexto"></p>
                <dl class="modal-recusa-grade">
                    <div>
                        <dt id="recusaDataRotulo">Data da decis&atilde;o</dt>
                        <dd id="recusaDataDecisao">-</dd>
                    </div>
                    <div>
                        <dt id="recusaResponsavelRotulo">Recusado por</dt>
                        <dd id="recusaResponsavel">-</dd>
                    </div>
                    <div id="recusaEntrevistaItem">
                        <dt>Data da entrevista</dt>
                        <dd id="recusaDataEntrevista">-</dd>
                    </div>
                </dl>

                <div class="modal-recusa-motivo">
                    <span id="recusaMotivoRotulo">Motivo da recusa</span>
                    <p id="recusaMotivo"></p>
                </div>
            </div>
        </div>
    </section>
</div>
