ALTER TABLE candidatos
    MODIFY status_candidato ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevista Realizada',
        'Entrevistada',
        'Entrevistado',
        'Aprovado',
        'Recusado',
        'Reprovado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';

ALTER TABLE candidaturas
    MODIFY status ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevistada',
        'Entrevistado',
        'Aprovado',
        'Recusado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';

UPDATE candidatos
SET status_candidato = 'Entrevistado'
WHERE status_candidato = 'Entrevistada';

UPDATE candidaturas
SET status = 'Entrevistado'
WHERE status = 'Entrevistada';

ALTER TABLE candidatos
    MODIFY status_candidato ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevista Realizada',
        'Entrevistado',
        'Aprovado',
        'Recusado',
        'Reprovado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';

ALTER TABLE candidaturas
    MODIFY status ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevistado',
        'Aprovado',
        'Recusado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';
