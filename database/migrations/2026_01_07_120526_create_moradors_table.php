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
        Schema::create('moradores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->enum('sexo', ['M', 'F', 'O'])->nullable()->comment('M=Masculino, F=Feminino, O=Outro');
            $table->text('observacoes')->nullable();
            $table->string('documento')->nullable();
            $table->string('contato')->nullable();
            $table->string('fotografia')->nullable()->comment('Caminho da foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moradores');
    }
};
