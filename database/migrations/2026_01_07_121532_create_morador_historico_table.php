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
        Schema::create('morador_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('morador_id')->constrained('moradores')->cascadeOnDelete();
            $table->unsignedInteger('ponto_id');
            $table->unsignedInteger('vistoria_entrada_id')->nullable();
            $table->unsignedInteger('vistoria_saida_id')->nullable();
            $table->date('data_entrada');
            $table->date('data_saida')->nullable();
            $table->timestamps();

            $table->foreign('ponto_id')->references('id')->on('pontos')->cascadeOnDelete();
            $table->foreign('vistoria_entrada_id')->references('id')->on('vistorias')->nullOnDelete();
            $table->foreign('vistoria_saida_id')->references('id')->on('vistorias')->nullOnDelete();

            $table->index(['morador_id', 'data_saida']);
            $table->index('ponto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('morador_historico');
    }
};
