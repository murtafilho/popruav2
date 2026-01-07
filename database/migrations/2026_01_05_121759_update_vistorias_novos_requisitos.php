<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vistorias', function (Blueprint $table) {
            // 1. Desmembrar menores_idosos em dois campos
            $table->boolean('crianca_adolescente')->default(false)->after('trafico_ilicitos');
            $table->boolean('idosos')->default(false)->after('crianca_adolescente');

            // 2. Novos campos solicitados
            $table->boolean('gestante')->default(false)->after('idosos');
            $table->boolean('lgbtqiapn')->default(false)->after('gestante');
            $table->boolean('cena_uso_caracterizada')->default(false)->after('lgbtqiapn');

            // 3. Quantidade de abrigos provisórios com tipos (JSON)
            $table->unsignedInteger('qtd_abrigos_provisorios')->default(0)->after('cena_uso_caracterizada');
            $table->json('abrigos_tipos')->nullable()->after('qtd_abrigos_provisorios');

            // 4. Número de casais (quando casal=true)
            $table->unsignedInteger('qtd_casais')->default(0)->after('casal');

            // 5. Número de animais (quando animais=true)
            $table->unsignedInteger('qtd_animais')->default(0)->after('animais');

            // 6. Condução pelas Forças de Segurança
            $table->boolean('conducao_forcas_seguranca')->default(false)->after('qtd_animais');
            $table->text('conducao_forcas_observacao')->nullable()->after('conducao_forcas_seguranca');

            // 7. Apreensão Fiscal e Auto de Fiscalização
            $table->boolean('apreensao_fiscal')->default(false)->after('conducao_forcas_observacao');
            $table->boolean('auto_fiscalizacao_aplicado')->default(false)->after('apreensao_fiscal');
            $table->string('auto_fiscalizacao_numero')->nullable()->after('auto_fiscalizacao_aplicado');

            // 8. Alterar nomes_pessoas para TEXT (multilinha)
            $table->text('nomes_pessoas')->nullable()->change();

            // 9. Alterar observacao para TEXT maior (Relatório Descritivo)
            $table->text('observacao')->nullable()->change();
        });

        // 10. Migrar dados de menores_idosos para os novos campos
        // Se menores_idosos=1, marca ambos como true (será ajustado manualmente depois)
        DB::table('vistorias')
            ->where('menores_idosos', true)
            ->update([
                'crianca_adolescente' => true,
                'idosos' => true,
            ]);

        // 11. Remover campo menores_idosos (após migração)
        Schema::table('vistorias', function (Blueprint $table) {
            $table->dropColumn('menores_idosos');
        });

        // 12. Remover campo estrutura_abrigo_provisorio (duplicado)
        Schema::table('vistorias', function (Blueprint $table) {
            $table->dropColumn('estrutura_abrigo_provisorio');
        });

        // 13. Remover campo conformidade (duplicado com resultado_acao)
        Schema::table('vistorias', function (Blueprint $table) {
            $table->dropColumn('conformidade');
        });

        // 14. Atualizar terminologia "Fenomeno Extinto" -> "Fenômeno Deixou de Ocorrer"
        DB::table('resultados_acoes')
            ->where('id', 3)
            ->update(['resultado' => 'Fenômeno Deixou de Ocorrer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter terminologia
        DB::table('resultados_acoes')
            ->where('id', 3)
            ->update(['resultado' => 'Fenomeno Extinto']);

        Schema::table('vistorias', function (Blueprint $table) {
            // Restaurar campos removidos
            $table->boolean('conformidade')->default(false);
            $table->boolean('estrutura_abrigo_provisorio')->default(false);
            $table->boolean('menores_idosos')->default(false);

            // Remover novos campos
            $table->dropColumn([
                'crianca_adolescente',
                'idosos',
                'gestante',
                'lgbtqiapn',
                'cena_uso_caracterizada',
                'qtd_abrigos_provisorios',
                'abrigos_tipos',
                'qtd_casais',
                'qtd_animais',
                'conducao_forcas_seguranca',
                'conducao_forcas_observacao',
                'apreensao_fiscal',
                'auto_fiscalizacao_aplicado',
                'auto_fiscalizacao_numero',
            ]);

            // Reverter tipos de coluna
            $table->string('nomes_pessoas')->nullable()->change();
            $table->string('observacao')->nullable()->change();
        });
    }
};
