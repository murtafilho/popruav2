<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vistorias')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite não suporta MODIFY, então precisamos recriar a tabela
            // Por enquanto, apenas verificamos se a tabela existe
            return;
        }

        if (Schema::hasColumn('vistorias', 'data_abordagem')) {
            DB::statement('ALTER TABLE vistorias MODIFY data_abordagem DATETIME NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('vistorias')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if (Schema::hasColumn('vistorias', 'data_abordagem')) {
            DB::statement('ALTER TABLE vistorias MODIFY data_abordagem DATE NULL');
        }
    }
};
