<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Passo 1: Renomear nome para nome_social
        Schema::table('moradores', function (Blueprint $table) {
            $table->renameColumn('nome', 'nome_social');
        });

        // Passo 2: Adicionar novos campos e remover sexo
        Schema::table('moradores', function (Blueprint $table) {
            $table->string('nome_registro')->nullable()->after('nome_social');
            $table->dropColumn('sexo');
            $table->string('genero')->nullable()->after('apelido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            $table->renameColumn('nome_social', 'nome');
            $table->dropColumn('nome_registro');
            $table->dropColumn('genero');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable()->after('apelido');
        });
    }
};
