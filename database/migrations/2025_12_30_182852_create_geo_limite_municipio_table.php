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
        Schema::create('geo_limite_municipio', function (Blueprint $table) {
            $table->id();
            $table->decimal('area_km2', 10, 5)->nullable();
            $table->decimal('perimetro_m', 12, 3)->nullable();
            $table->json('geometry');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_limite_municipio');
    }
};
