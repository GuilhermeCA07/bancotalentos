ALTER TABLE usuarios
    MODIFY perfil ENUM(
        'Administrador',
        'Gerente',
        'Secretario',
        'Recrutador'
    ) NOT NULL;

UPDATE usuarios
SET perfil = 'Administrador'
WHERE LOWER(email) IN (
    'matheus.quelucci@netcom.tv.br',
    'guilherme@netcom.tv.br',
    'andre.gois@netcom.tv.br'
);

CREATE TABLE IF NOT EXISTS configuracoes_email (
    idConfiguracaoEmail INT NOT NULL AUTO_INCREMENT,
    smtp_host VARCHAR(255) NOT NULL,
    smtp_port INT NOT NULL,
    smtp_usuario VARCHAR(255) NOT NULL,
    smtp_senha TEXT NOT NULL,
    smtp_criptografia ENUM('tls', 'ssl', 'nenhuma') NOT NULL DEFAULT 'tls',
    email_remetente VARCHAR(255) NOT NULL,
    nome_remetente VARCHAR(150) NOT NULL,
    atualizado_por INT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    testado_em DATETIME NULL,
    PRIMARY KEY (idConfiguracaoEmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
