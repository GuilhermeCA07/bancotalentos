CREATE TABLE IF NOT EXISTS usuarios_dispositivos_confiaveis (
    idDispositivo INT NOT NULL AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    seletor CHAR(24) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_uso_em DATETIME NULL,
    expira_em DATETIME NOT NULL,
    PRIMARY KEY (idDispositivo),
    UNIQUE KEY uk_dispositivo_confiavel_seletor (seletor),
    KEY idx_dispositivo_confiavel_usuario (usuario_id, expira_em),
    CONSTRAINT fk_dispositivo_confiavel_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (idUsuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
