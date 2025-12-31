<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pontos')) {
            return;
        }

        $driver = DB::getDriverName();

        // SQLite não suporta MODIFY, então pulamos para SQLite
        if ($driver === 'sqlite') {
            return;
        }

        // Converter VARCHAR para DECIMAL mantendo os dados
        if (Schema::hasColumn('pontos', 'lat')) {
            DB::statement('ALTER TABLE pontos MODIFY lat DECIMAL(17,14) NULL');
        }

        if (Schema::hasColumn('pontos', 'lng')) {
            DB::statement('ALTER TABLE pontos MODIFY lng DECIMAL(17,14) NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pontos')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if (Schema::hasColumn('pontos', 'lat')) {
            DB::statement('ALTER TABLE pontos MODIFY lat VARCHAR(30) NULL');
        }

        if (Schema::hasColumn('pontos', 'lng')) {
            DB::statement('ALTER TABLE pontos MODIFY lng VARCHAR(30) NULL');
        }
    }
};
