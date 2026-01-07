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
        Schema::table('moradores', function (Blueprint $table) {
            $table->unsignedInteger('ponto_atual_id')->nullable()->after('id');
            $table->foreign('ponto_atual_id')->references('id')->on('pontos')->nullOnDelete();
            $table->index('ponto_atual_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            $table->dropForeign(['ponto_atual_id']);
            $table->dropColumn('ponto_atual_id');
        });
    }
};
