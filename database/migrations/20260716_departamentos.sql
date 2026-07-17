CREATE TABLE departamentos (
    idDepartamento INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cor CHAR(7) NOT NULL DEFAULT '#6B7280',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idDepartamento),
    UNIQUE KEY uk_departamentos_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO departamentos (nome, cor) VALUES
    ('NOC', '#0F4DB0'),
    ('Financeiro', '#16A34A'),
    ('Comercial/Atendimento', '#FF6B00'),
    ('Suporte Técnico', '#7C3AED'),
    ('Infra', '#DC2626'),
    ('Técnico de Rua', '#0891B2'),
    ('RH', '#DB2777'),
    ('Outros', '#6B7280');

ALTER TABLE vagas
    ADD COLUMN departamento_id INT NULL AFTER titulo;

UPDATE vagas v
INNER JOIN departamentos d
    ON d.nome = v.departamento
SET v.departamento_id = d.idDepartamento;

ALTER TABLE vagas
    MODIFY departamento_id INT NOT NULL,
    DROP COLUMN departamento,
    ADD INDEX idx_vagas_departamento (departamento_id),
    ADD CONSTRAINT fk_vagas_departamento
        FOREIGN KEY (departamento_id)
        REFERENCES departamentos (idDepartamento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;
