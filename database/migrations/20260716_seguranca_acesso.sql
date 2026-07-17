ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS troca_senha_obrigatoria TINYINT(1) NOT NULL DEFAULT 0
        AFTER perfil,
    ADD COLUMN IF NOT EXISTS dois_fatores_ativo TINYINT(1) NOT NULL DEFAULT 0
        AFTER troca_senha_obrigatoria,
    ADD COLUMN IF NOT EXISTS dois_fatores_segredo TEXT NULL
        AFTER dois_fatores_ativo,
    ADD COLUMN IF NOT EXISTS dois_fatores_ultimo_periodo BIGINT NULL
        AFTER dois_fatores_segredo;

ALTER TABLE usuarios
    MODIFY troca_senha_obrigatoria TINYINT(1) NOT NULL DEFAULT 1;
