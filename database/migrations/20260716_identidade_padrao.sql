ALTER TABLE configuracoes
    MODIFY identidade VARCHAR(20) NOT NULL DEFAULT 'padrao';

UPDATE configuracoes
SET identidade = 'padrao'
WHERE identidade NOT IN ('netcom', 'sumernet', 'netaki', 'padrao')
OR identidade = ''
OR (corPrimaria = '#2563EB' AND corSecundaria = '#0B1220')
OR (corPrimaria = '#059669' AND corSecundaria = '#10251F')
OR (corPrimaria = '#7C3AED' AND corSecundaria = '#1E1733');
