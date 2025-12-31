<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE vistorias MODIFY data_abordagem DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE vistorias MODIFY data_abordagem DATE NULL');
    }
};
