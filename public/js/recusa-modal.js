(function () {
    const modal = document.getElementById('modalRecusa');

    if (!modal) {
        return;
    }

    const carregando = document.getElementById('recusaCarregando');
    const erro = document.getElementById('recusaErro');
    const detalhes = document.getElementById('recusaDetalhes');
    const titulo = document.getElementById('modalRecusaTitulo');
    const contexto = document.getElementById('recusaContexto');
    const dataRotulo = document.getElementById('recusaDataRotulo');
    const dataDecisao = document.getElementById('recusaDataDecisao');
    const responsavelRotulo = document.getElementById('recusaResponsavelRotulo');
    const responsavel = document.getElementById('recusaResponsavel');
    const entrevistaItem = document.getElementById('recusaEntrevistaItem');
    const dataEntrevista = document.getElementById('recusaDataEntrevista');
    const motivoRotulo = document.getElementById('recusaMotivoRotulo');
    const motivo = document.getElementById('recusaMotivo');
    const botaoFechar = modal.querySelector('.modal-recusa-fechar');
    let ultimoAcionador = null;

    function formatarDataHora(valor) {
        const partes = String(valor || '').match(
            /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/
        );

        if (!partes) {
            return 'N\u00e3o informada';
        }

        const data = `${partes[3]}/${partes[2]}/${partes[1]}`;
        return partes[4]
            ? `${data} \u00e0s ${partes[4]}:${partes[5]}`
            : data;
    }

    function abrir() {
        modal.classList.add('aberto');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-recusa-aberto');
        botaoFechar.focus();
    }

    function fechar() {
        modal.classList.remove('aberto');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-recusa-aberto');

        if (ultimoAcionador) {
            ultimoAcionador.focus();
        }
    }

    function mostrarCarregamento() {
        carregando.hidden = false;
        erro.hidden = true;
        detalhes.hidden = true;
    }

    function mostrarErro(mensagem) {
        carregando.hidden = true;
        detalhes.hidden = true;
        erro.textContent = mensagem;
        erro.hidden = false;
    }

    function configurarRotulos(resultado) {
        const configuracoes = {
            Recusado: {
                titulo: 'Candidatura recusada',
                data: 'Data da decis\u00e3o',
                responsavel: 'Recusado por',
                detalhes: 'Motivo da recusa',
                usarMotivoRecusa: true
            },
            Entrevistado: {
                titulo: 'Entrevista realizada',
                data: 'Data da finaliza\u00e7\u00e3o',
                responsavel: 'Entrevistador',
                detalhes: 'Observa\u00e7\u00f5es da entrevista'
            },
            Aprovado: {
                titulo: 'Candidatura aprovada',
                data: 'Data da aprova\u00e7\u00e3o',
                responsavel: 'Aprovado por',
                detalhes: 'Observa\u00e7\u00f5es da entrevista'
            },
            Contratado: {
                titulo: 'Candidato contratado',
                data: 'Data da contrata\u00e7\u00e3o',
                responsavel: 'Contratado por',
                detalhes: 'Observa\u00e7\u00f5es da entrevista'
            }
        };
        const configuracao = configuracoes[resultado] || configuracoes.Recusado;

        modal.dataset.resultado = String(resultado || '').toLowerCase();
        titulo.textContent = configuracao.titulo;
        dataRotulo.textContent = configuracao.data;
        responsavelRotulo.textContent = configuracao.responsavel;
        motivoRotulo.textContent = configuracao.detalhes;

        return configuracao;
    }

    function preencher(dados) {
        const configuracao = configurarRotulos(dados.resultado);

        contexto.textContent = `${dados.candidato} - ${dados.vaga}`;
        dataDecisao.textContent = formatarDataHora(dados.data_decisao);
        responsavel.textContent = dados.responsavel_decisao || 'N\u00e3o identificado';
        motivo.textContent = configuracao.usarMotivoRecusa
            ? (dados.motivo_recusa
                || 'Motivo n\u00e3o informado (registro anterior \u00e0 obrigatoriedade).')
            : (dados.observacoes || 'Observa\u00e7\u00f5es n\u00e3o informadas.');

        if (dados.data_entrevista) {
            const horario = dados.hora_entrevista
                ? ` ${dados.hora_entrevista}`
                : '';
            dataEntrevista.textContent = formatarDataHora(
                `${dados.data_entrevista}${horario}`
            );
            entrevistaItem.hidden = false;
        } else {
            entrevistaItem.hidden = true;
        }

        carregando.hidden = true;
        erro.hidden = true;
        detalhes.hidden = false;
    }

    async function buscarDetalhes(idCandidatura, endpoint) {
        mostrarCarregamento();
        abrir();

        try {
            const urlBase = endpoint
                || '?c=candidatura&m=detalhesResultado';
            const separador = urlBase.includes('?') ? '&' : '?';
            const resposta = await fetch(
                `${urlBase}${separador}id=${encodeURIComponent(idCandidatura)}`,
                { headers: { Accept: 'application/json' } }
            );
            const retorno = await resposta.json();

            if (!resposta.ok || !retorno.sucesso) {
                throw new Error(
                    retorno.mensagem || 'N\u00e3o foi poss\u00edvel consultar os detalhes.'
                );
            }

            preencher(retorno.dados);
        } catch (falha) {
            mostrarErro(
                falha.message || 'N\u00e3o foi poss\u00edvel consultar os detalhes.'
            );
        }
    }

    document.addEventListener('click', function (evento) {
        const acionador = evento.target.closest('.badge-detalhes-interativo');

        if (!acionador) {
            return;
        }

        ultimoAcionador = acionador;
        configurarRotulos(acionador.dataset.resultado);
        buscarDetalhes(
            acionador.dataset.detalhesId,
            acionador.dataset.detalhesEndpoint
        );
    });

    modal.querySelectorAll('[data-fechar-modal-recusa]').forEach(function (item) {
        item.addEventListener('click', fechar);
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && modal.classList.contains('aberto')) {
            fechar();
        }
    });
}());
