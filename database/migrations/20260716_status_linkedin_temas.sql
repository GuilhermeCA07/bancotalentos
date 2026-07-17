ALTER TABLE candidatos
    ADD COLUMN IF NOT EXISTS linkedin VARCHAR(255) NULL AFTER email;

ALTER TABLE candidatos
    MODIFY status_candidato ENUM(
        'Em Análise',
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevista Realizada',
        'Aprovado',
        'Recusado',
        'Reprovado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Em Análise';

UPDATE candidatos
SET status_candidato = 'Aguardando Entrevista'
WHERE status_candidato = 'Em Análise';

ALTER TABLE candidatos
    MODIFY status_candidato ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Entrevista Realizada',
        'Aprovado',
        'Recusado',
        'Reprovado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';

ALTER TABLE candidaturas
    MODIFY status ENUM(
        'Em Análise',
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Aprovado',
        'Recusado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Em Análise';

UPDATE candidaturas
SET status = 'Aguardando Entrevista'
WHERE status = 'Em Análise';

UPDATE candidaturas c
INNER JOIN vagas v
    ON v.idVaga = c.vaga_id
INNER JOIN (
    SELECT vaga_id, COUNT(*) AS total
    FROM candidaturas
    WHERE status_contratacao = 'Contratado'
    GROUP BY vaga_id
) contratados
    ON contratados.vaga_id = c.vaga_id
SET
    c.status = 'Vaga Preenchida por Contratação',
    c.motivo_recusa = NULL,
    c.data_atualizacao = COALESCE(c.data_atualizacao, NOW())
WHERE c.status = 'Recusado'
AND c.motivo_recusa = 'Vaga encerrada por contratação de outro candidato.'
AND contratados.total >= v.quantidade_vagas;

ALTER TABLE candidaturas
    MODIFY status ENUM(
        'Aguardando Entrevista',
        'Entrevista Agendada',
        'Aprovado',
        'Recusado',
        'Vaga Preenchida por Contratação',
        'Vaga Fechada'
    ) NOT NULL DEFAULT 'Aguardando Entrevista';

CREATE TABLE IF NOT EXISTS configuracoes (
    idConfiguracao INT NOT NULL AUTO_INCREMENT,
    corPrimaria CHAR(7) NOT NULL DEFAULT '#0F4DB0',
    corSecundaria CHAR(7) NOT NULL DEFAULT '#FF6B00',
    atualizado_em DATETIME NULL,
    PRIMARY KEY (idConfiguracao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO configuracoes (corPrimaria, corSecundaria)
SELECT '#0F4DB0', '#FF6B00'
WHERE NOT EXISTS (
    SELECT 1 FROM configuracoes
);
