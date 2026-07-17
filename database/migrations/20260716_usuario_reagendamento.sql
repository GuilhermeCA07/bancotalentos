UPDATE historico_entrevistas h
SET h.usuario = (
    SELECT l.usuario_nome
    FROM logs_atividade l
    WHERE l.modulo = 'Entrevista'
    AND l.metodo = 'salvarReagendamento'
    AND JSON_VALID(l.dados)
    AND JSON_UNQUOTE(
        JSON_EXTRACT(l.dados, '$.post.idEntrevista')
    ) = CAST(h.entrevista_id AS CHAR)
    AND ABS(TIMESTAMPDIFF(SECOND, l.criado_em, h.data_registro)) <= 5
    ORDER BY l.criado_em DESC, l.idLog DESC
    LIMIT 1
)
WHERE h.usuario = 'Sistema'
AND EXISTS (
    SELECT 1
    FROM logs_atividade l
    WHERE l.modulo = 'Entrevista'
    AND l.metodo = 'salvarReagendamento'
    AND JSON_VALID(l.dados)
    AND JSON_UNQUOTE(
        JSON_EXTRACT(l.dados, '$.post.idEntrevista')
    ) = CAST(h.entrevista_id AS CHAR)
    AND ABS(TIMESTAMPDIFF(SECOND, l.criado_em, h.data_registro)) <= 5
);
