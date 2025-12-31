<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('vistorias')) {
            return;
        }

        if (Schema::hasColumn('vistorias', 'tipo_abrigo_desmontado')) {
            Schema::table('vistorias', function (Blueprint $table) {
                $table->dropColumn('tipo_abrigo_desmontado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('vistorias')) {
            return;
        }

        if (! Schema::hasColumn('vistorias', 'tipo_abrigo_desmontado')) {
            Schema::table('vistorias', function (Blueprint $table) {
                $table->string('tipo_abrigo_desmontado', 100)->nullable()->after('tipo_abrigo_desmontado_id');
            });
        }
    }
};
