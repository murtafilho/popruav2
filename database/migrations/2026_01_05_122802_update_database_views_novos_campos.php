<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remover views existentes
        DB::statement('DROP VIEW IF EXISTS v_pontos');
        DB::statement('DROP VIEW IF EXISTS v_vistorias');

        // Recriar view v_pontos com novos campos de complexidade
        DB::statement('
            CREATE VIEW v_pontos AS
            SELECT
                p.id,
                p.numero,
                p.complemento,
                p.endereco_id,
                p.updated_at,
                p.created_at,
                e.logradouro,
                e.bairro,
                e.regional,
                e.tipo,
                ca.caracteristica_abrigo,
                p.lat,
                p.lng,
                COALESCE(stats.soma_kg, 0) AS soma_kg,
                COALESCE(stats.contador, 0) AS contador,
                stats.data_ini,
                uv.data_abordagem AS data_a,
                tad.tipo_abrigo AS tipo_abrigo_desmontado,
                ra.resultado,
                (
                    IFNULL(uv.resistencia, 0) +
                    IFNULL(uv.num_reduzido, 0) +
                    IFNULL(uv.casal, 0) +
                    IFNULL(uv.catador_reciclados, 0) +
                    IFNULL(uv.fixacao_antiga, 0) +
                    IFNULL(uv.excesso_objetos, 0) +
                    IFNULL(uv.trafico_ilicitos, 0) +
                    IFNULL(uv.crianca_adolescente, 0) +
                    IFNULL(uv.idosos, 0) +
                    IFNULL(uv.gestante, 0) +
                    IFNULL(uv.lgbtqiapn, 0) +
                    IFNULL(uv.cena_uso_caracterizada, 0) +
                    IFNULL(uv.deficiente, 0) +
                    IFNULL(uv.agrupamento_quimico, 0) +
                    IFNULL(uv.saude_mental, 0) +
                    IFNULL(uv.animais, 0)
                ) AS complexidade
            FROM pontos p
            JOIN ender e ON e.id = p.endereco_id
            LEFT JOIN caracteristica_abrigo ca ON ca.id = p.caracteristica_abrigo_id
            LEFT JOIN (
                SELECT
                    ponto_id,
                    SUM(qtd_kg) AS soma_kg,
                    COUNT(*) AS contador,
                    MIN(data_abordagem) AS data_ini,
                    MAX(id) AS ultima_vistoria_id
                FROM vistorias
                GROUP BY ponto_id
            ) stats ON stats.ponto_id = p.id
            LEFT JOIN vistorias uv ON uv.id = stats.ultima_vistoria_id
            LEFT JOIN tipo_abrigo_desmontado tad ON tad.id = uv.tipo_abrigo_desmontado_id
            LEFT JOIN resultados_acoes ra ON ra.id = uv.resultado_acao_id
            ORDER BY uv.data_abordagem DESC, e.logradouro
        ');

        // Recriar view v_vistorias com novos campos de complexidade
        DB::statement('
            CREATE VIEW v_vistorias AS
            SELECT
                v.id,
                p.id AS ponto_id,
                p.numero,
                p.complemento,
                p.endereco_id,
                p.updated_at,
                p.created_at,
                e.logradouro,
                e.bairro,
                e.regional,
                e.tipo,
                p.lat,
                p.lng,
                v.data_abordagem,
                v.quantidade_pessoas,
                v.nomes_pessoas,
                ta.tipo AS tipo_abordagem,
                tad.tipo_abrigo AS tipo_abrigo_desmontado,
                ra.resultado,
                COALESCE(stats.contador, 0) AS contador,
                uv.data_abordagem AS data_a,
                uv.quantidade_pessoas AS num_pessoas,
                (
                    IFNULL(uv.resistencia, 0) +
                    IFNULL(uv.num_reduzido, 0) +
                    IFNULL(uv.casal, 0) +
                    IFNULL(uv.catador_reciclados, 0) +
                    IFNULL(uv.fixacao_antiga, 0) +
                    IFNULL(uv.excesso_objetos, 0) +
                    IFNULL(uv.trafico_ilicitos, 0) +
                    IFNULL(uv.crianca_adolescente, 0) +
                    IFNULL(uv.idosos, 0) +
                    IFNULL(uv.gestante, 0) +
                    IFNULL(uv.lgbtqiapn, 0) +
                    IFNULL(uv.cena_uso_caracterizada, 0) +
                    IFNULL(uv.deficiente, 0) +
                    IFNULL(uv.agrupamento_quimico, 0) +
                    IFNULL(uv.saude_mental, 0) +
                    IFNULL(uv.animais, 0)
                ) AS complexidade
            FROM vistorias v
            JOIN pontos p ON p.id = v.ponto_id
            JOIN ender e ON e.id = p.endereco_id
            LEFT JOIN tipo_abordagem ta ON ta.id = v.tipo_abordagem_id
            LEFT JOIN tipo_abrigo_desmontado tad ON tad.id = v.tipo_abrigo_desmontado_id
            LEFT JOIN resultados_acoes ra ON ra.id = v.resultado_acao_id
            LEFT JOIN (
                SELECT
                    ponto_id,
                    COUNT(*) AS contador,
                    MAX(id) AS ultima_vistoria_id
                FROM vistorias
                GROUP BY ponto_id
            ) stats ON stats.ponto_id = p.id
            LEFT JOIN vistorias uv ON uv.id = stats.ultima_vistoria_id
            ORDER BY v.data_abordagem DESC, e.logradouro, p.numero DESC
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover views atualizadas
        DB::statement('DROP VIEW IF EXISTS v_pontos');
        DB::statement('DROP VIEW IF EXISTS v_vistorias');

        // Recriar views originais com campos antigos
        DB::statement('
            CREATE VIEW v_pontos AS
            SELECT
                p.id,
                p.numero,
                p.complemento,
                p.endereco_id,
                p.updated_at,
                p.created_at,
                e.logradouro,
                e.bairro,
                e.regional,
                e.tipo,
                ca.caracteristica_abrigo,
                p.lat,
                p.lng,
                COALESCE(stats.soma_kg, 0) AS soma_kg,
                COALESCE(stats.contador, 0) AS contador,
                stats.data_ini,
                uv.data_abordagem AS data_a,
                tad.tipo_abrigo AS tipo_abrigo_desmontado,
                ra.resultado,
                (
                    IFNULL(uv.resistencia, 0) +
                    IFNULL(uv.num_reduzido, 0) +
                    IFNULL(uv.casal, 0) +
                    IFNULL(uv.catador_reciclados, 0) +
                    IFNULL(uv.fixacao_antiga, 0) +
                    IFNULL(uv.estrutura_abrigo_provisorio, 0) +
                    IFNULL(uv.excesso_objetos, 0) +
                    IFNULL(uv.trafico_ilicitos, 0) +
                    IFNULL(uv.menores_idosos, 0) +
                    IFNULL(uv.deficiente, 0) +
                    IFNULL(uv.agrupamento_quimico, 0) +
                    IFNULL(uv.saude_mental, 0) +
                    IFNULL(uv.animais, 0)
                ) AS complexidade
            FROM pontos p
            JOIN ender e ON e.id = p.endereco_id
            LEFT JOIN caracteristica_abrigo ca ON ca.id = p.caracteristica_abrigo_id
            LEFT JOIN (
                SELECT
                    ponto_id,
                    SUM(qtd_kg) AS soma_kg,
                    COUNT(*) AS contador,
                    MIN(data_abordagem) AS data_ini,
                    MAX(id) AS ultima_vistoria_id
                FROM vistorias
                GROUP BY ponto_id
            ) stats ON stats.ponto_id = p.id
            LEFT JOIN vistorias uv ON uv.id = stats.ultima_vistoria_id
            LEFT JOIN tipo_abrigo_desmontado tad ON tad.id = uv.tipo_abrigo_desmontado_id
            LEFT JOIN resultados_acoes ra ON ra.id = uv.resultado_acao_id
            ORDER BY uv.data_abordagem DESC, e.logradouro
        ');

        DB::statement('
            CREATE VIEW v_vistorias AS
            SELECT
                v.id,
                p.id AS ponto_id,
                p.numero,
                p.complemento,
                p.endereco_id,
                p.updated_at,
                p.created_at,
                e.logradouro,
                e.bairro,
                e.regional,
                e.tipo,
                p.lat,
                p.lng,
                v.data_abordagem,
                v.quantidade_pessoas,
                v.nomes_pessoas,
                ta.tipo AS tipo_abordagem,
                tad.tipo_abrigo AS tipo_abrigo_desmontado,
                ra.resultado,
                COALESCE(stats.contador, 0) AS contador,
                uv.data_abordagem AS data_a,
                uv.quantidade_pessoas AS num_pessoas,
                (
                    IFNULL(uv.resistencia, 0) +
                    IFNULL(uv.num_reduzido, 0) +
                    IFNULL(uv.casal, 0) +
                    IFNULL(uv.catador_reciclados, 0) +
                    IFNULL(uv.fixacao_antiga, 0) +
                    IFNULL(uv.estrutura_abrigo_provisorio, 0) +
                    IFNULL(uv.excesso_objetos, 0) +
                    IFNULL(uv.trafico_ilicitos, 0) +
                    IFNULL(uv.menores_idosos, 0) +
                    IFNULL(uv.deficiente, 0) +
                    IFNULL(uv.agrupamento_quimico, 0) +
                    IFNULL(uv.saude_mental, 0) +
                    IFNULL(uv.animais, 0)
                ) AS complexidade
            FROM vistorias v
            JOIN pontos p ON p.id = v.ponto_id
            JOIN ender e ON e.id = p.endereco_id
            LEFT JOIN tipo_abordagem ta ON ta.id = v.tipo_abordagem_id
            LEFT JOIN tipo_abrigo_desmontado tad ON tad.id = v.tipo_abrigo_desmontado_id
            LEFT JOIN resultados_acoes ra ON ra.id = v.resultado_acao_id
            LEFT JOIN (
                SELECT
                    ponto_id,
                    COUNT(*) AS contador,
                    MAX(id) AS ultima_vistoria_id
                FROM vistorias
                GROUP BY ponto_id
            ) stats ON stats.ponto_id = p.id
            LEFT JOIN vistorias uv ON uv.id = stats.ultima_vistoria_id
            ORDER BY v.data_abordagem DESC, e.logradouro, p.numero DESC
        ');
    }
};
