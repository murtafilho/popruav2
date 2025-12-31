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
        // Esta tabela não é mais necessária pois usamos Spatie Media Library
        // Mas mantemos a migration para não quebrar instalações existentes
        if (Schema::hasTable('vistoria_fotos')) {
            return;
        }

        // Só cria a tabela se a tabela vistorias existir
        if (! Schema::hasTable('vistorias')) {
            return;
        }

        Schema::create('vistoria_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vistoria_id');
            $table->string('caminho'); // Caminho do arquivo no storage
            $table->string('nome_original')->nullable(); // Nome original do arquivo
            $table->integer('tamanho')->nullable(); // Tamanho em bytes
            $table->string('mime_type')->nullable(); // Tipo MIME (image/jpeg, etc)
            $table->integer('ordem')->default(0); // Ordem de exibição
            $table->text('descricao')->nullable(); // Descrição opcional da foto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vistoria_fotos');
    }
};
