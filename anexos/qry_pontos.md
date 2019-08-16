SELECT
pontos.id AS id,
pontos.numero AS numero,
pontos.complemento AS complemento,
pontos.endereco_id AS endereco_id,
pontos.updated_at AS updated_at,
pontos.created_at AS created_at,
ender.logradouro AS logradouro,
ender.bairro AS bairro,
ender.regional AS regional,
( SELECT `vistorias`.`data_abordagem` FROM `vistorias` WHERE ( `vistorias`.`ponto_id` = `pontos`.`id` ) ORDER BY `vistorias`.`data_abordagem` DESC LIMIT 1 ) AS data_a,
( SELECT count( DISTINCT `vistorias`.`id` ) FROM `vistorias` WHERE ( `vistorias`.`ponto_id` = `pontos`.`id` ) ) AS contador,
( SELECT resultados_acoes.resultado FROM vistorias INNER JOIN resultados_acoes ON resultados_acoes.id = vistorias.resultado_acao_id WHERE ( `vistorias`.`ponto_id` = `pontos`.`id` ) ORDER BY `vistorias`.`id` DESC LIMIT 1 ) AS resultado,
(
SELECT
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	(
	( IF ( `vistorias`.`resistencia`, 1, 0 ) + IF ( `vistorias`.`num_reduzido`, 1, 0 ) ) +
IF
	( `vistorias`.`casal`, 1, 0 ) 
	) +
IF
	( `vistorias`.`catador_reciclados`, 1, 0 ) 
	) +
IF
	( `vistorias`.`resistencia`, 1, 0 ) 
	) +
IF
	( `vistorias`.`fixacao_antiga`, 1, 0 ) 
	) +
IF
	( `vistorias`.`excesso_objetos`, 1, 0 ) 
	) +
IF
	( `vistorias`.`resistencia`, 1, 0 ) 
	) +
IF
	( `vistorias`.`fixacao_antiga`, 1, 0 ) 
	) +
IF
	( `vistorias`.`excesso_objetos`, 1, 0 ) 
	) +
IF
	( `vistorias`.`trafico_ilicitos`, 1, 0 ) 
	) +
IF
	( `vistorias`.`menores_idosos`, 1, 0 ) 
	) +
IF
	( `vistorias`.`deficiente`, 1, 0 ) 
	) +
IF
	( `vistorias`.`agrupamento_quimico`, 1, 0 ) 
	) +
IF
	( `vistorias`.`saude_mental`, 1, 0 ) 
	) +
IF
	( `vistorias`.`animais`, 1, 0 ) 
	) +
IF
	( `vistorias`.`estrutura_abrigo_provisorio`, 1, 0 ) 
	) 
FROM
	`vistorias` 
WHERE
	( `vistorias`.`ponto_id` = `pontos`.`id` ) 
ORDER BY
	`vistorias`.`id` DESC 
	LIMIT 1 
	) AS complexidade,
ender.tipo AS tipo,
caracteristica_abrigo.caracteristica_abrigo
FROM
(pontos
JOIN ender ON ((ender.id = pontos.endereco_id)))
LEFT JOIN caracteristica_abrigo ON caracteristica_abrigo.id = pontos.caracteristica_abrigo_id
ORDER BY
'data_a' DESC,
logradouro ASC 
