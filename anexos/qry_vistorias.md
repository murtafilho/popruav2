SELECT
	vistorias.id AS id,
	vistorias.data_abordagem AS data_abordagem,
	vistorias.nomes_pessoas AS nomes_pessoas,
	vistorias.quantidade_pessoas AS quantidade_pessoas,
	vistorias.casal AS casal,
	vistorias.classificacao AS classificacao,
	vistorias.num_reduzido AS num_reduzido,
	vistorias.catador_reciclados AS catador_reciclados,
	vistorias.resistencia AS resistencia,
	vistorias.fixacao_antiga AS fixacao_antiga,
	vistorias.estrutura_abrigo_provisorio AS estrutura_abrigo_provisorio,
	vistorias.excesso_objetos AS excesso_objetos,
	vistorias.trafico_ilicitos AS trafico_ilicitos,
	vistorias.menores_idosos AS menores_idosos,
	vistorias.deficiente AS deficiente,
	vistorias.agrupamento_quimico AS agrupamento_quimico,
	vistorias.saude_mental AS saude_mental,
	vistorias.animais AS animais,
	vistorias.material_apreendido AS material_apreendido,
	vistorias.material_descartado AS material_descartado,
	vistorias.qtd_kg AS qtd_kg,
	vistorias.movimento_migratorio AS movimento_migratorio,
	vistorias.observacao AS observacao,
	vistorias.ponto_id AS ponto_id,
	vistorias.created_at AS created_at,
	vistorias.updated_at AS updated_at,
	pontos.numero AS numero,
	ender.logradouro AS logradouro,
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
	) AS complexidade,
	ender.tipo,
	tipo_abordagem.tipo AS tipo_abordagem,
	resultados_acoes.resultado AS resultado_acao,
	tipo_abrigo_desmontado.tipo_abrigo AS tipo_abrigo_desmontado,
	e1.encaminhamento AS encaminhamento1,
	e2.encaminhamento AS encaminhamento2,
	e3.encaminhamento AS encaminhamento3,
	e4.encaminhamento AS encaminhamento4 
FROM
	(
	( vistorias JOIN pontos ON ( ( pontos.id = vistorias.ponto_id ) ) )
	JOIN ender ON ( ( ender.id = pontos.endereco_id ) ) 
	)
	LEFT JOIN tipo_abordagem ON tipo_abordagem.id = vistorias.tipo_abordagem_id
	LEFT JOIN resultados_acoes ON resultados_acoes.id = vistorias.resultado_acao_id
	LEFT JOIN tipo_abrigo_desmontado ON tipo_abrigo_desmontado.id = vistorias.tipo_abrigo_desmontado_id
	LEFT JOIN encaminhamentos AS e1 ON e1.id = vistorias.e1_id
	LEFT JOIN encaminhamentos AS e2 ON e2.id = vistorias.e2_id
	LEFT JOIN encaminhamentos AS e3 ON e3.id = vistorias.e3_id
	LEFT JOIN encaminhamentos AS e4 ON e4.id = vistorias.e4_id 
ORDER BY
	data_abordagem DESC,
	logradouro ASC,
	numero ASC
