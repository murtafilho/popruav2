<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Converter VARCHAR para DECIMAL mantendo os dados
        DB::statement('ALTER TABLE pontos MODIFY lat DECIMAL(17,14) NULL');
        DB::statement('ALTER TABLE pontos MODIFY lng DECIMAL(17,14) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pontos MODIFY lat VARCHAR(30) NULL');
        DB::statement('ALTER TABLE pontos MODIFY lng VARCHAR(30) NULL');
    }
};
