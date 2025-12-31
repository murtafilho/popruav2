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
        // Verificar se as tabelas existem
        if (! Schema::hasTable('vistoria_fotos') || ! Schema::hasTable('vistorias')) {
            return;
        }

        $driver = DB::getDriverName();

        // Para SQLite, não precisamos adicionar foreign key se já existe
        if ($driver === 'sqlite') {
            return;
        }

        // Verificar se a foreign key já existe (MySQL/MariaDB)
        try {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'vistoria_fotos' 
                AND CONSTRAINT_NAME = 'vistoria_fotos_vistoria_id_foreign'
            ");

            if (empty($foreignKeys)) {
                // Primeiro, alterar o tipo da coluna para corresponder à tabela vistorias
                if (Schema::hasColumn('vistoria_fotos', 'vistoria_id')) {
                    DB::statement('ALTER TABLE vistoria_fotos MODIFY COLUMN vistoria_id INT UNSIGNED NOT NULL');

                    Schema::table('vistoria_fotos', function (Blueprint $table) {
                        $table->foreign('vistoria_id')->references('id')->on('vistorias')->onDelete('cascade');
                    });
                }
            }
        } catch (\Exception $e) {
            // Se houver erro, apenas ignora (pode ser que a foreign key já exista)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('vistoria_fotos')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        try {
            Schema::table('vistoria_fotos', function (Blueprint $table) {
                $table->dropForeign(['vistoria_id']);
            });
        } catch (\Exception $e) {
            // Se houver erro, apenas ignora
        }
    }
};
