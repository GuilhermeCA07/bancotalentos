ALTER TABLE configuracoes
    ADD COLUMN IF NOT EXISTS identidade VARCHAR(20) NOT NULL DEFAULT 'netcom'
    AFTER corSecundaria;

UPDATE configuracoes
SET identidade = 'netcom'
WHERE identidade IS NULL OR identidade = '';
