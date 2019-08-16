<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class saneamentoController extends Controller
{
    public function executar(){

    /*        $up = DB::table('vistorias')
                ->where('casal', null)
                ->orWhere('casal','')
                ->orWhere('casal','Não')
                ->update(['casal' => 0]);
            echo $up;
            echo '<br>';



        $up = DB::table('vistorias')
        ->where('casal', 'Sim')
        ->orWhere('casal','on')
        ->orWhere('casal','Casal')
        ->update(['casal' => 1]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('num_reduzido', null)
        ->orWhere('num_reduzido','')
        ->update(['num_reduzido' => 0]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('catador_reciclados', null)
        ->orWhere('catador_reciclados','')
        ->update(['catador_reciclados' => 0]);
        echo $up;
        echo '<br>';
*/
        $up = DB::table('vistorias')
        ->where('resistencia', null)
        ->update(['resistencia' => 0]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('fixacao_antiga', null)
        ->update(['fixacao_antiga' => 0]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('estrutura_abrigo_provisorio', null)
        ->update(['estrutura_abrigo_provisorio' => 0]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('excesso_objetos', null)
        ->update(['excesso_objetos' => 0]);
        echo $up;
        echo '<br>';

        
        $up = DB::table('vistorias')
        ->where('trafico_ilicitos', null)
        ->update(['trafico_ilicitos' => 0]);
        echo $up;
        echo '<br>';

        
        $up = DB::table('vistorias')
        ->where('menores_idosos', null)
        ->update(['menores_idosos' => 0]);
        echo $up;
        echo '<br>';

                
        $up = DB::table('vistorias')
        ->where('deficiente', null)
        ->update(['deficiente' => 0]);
        echo $up;
        echo '<br>';

                
        $up = DB::table('vistorias')
        ->where('agrupamento_quimico', null)
        ->update(['agrupamento_quimico' => 0]);
        echo $up;
        echo '<br>';
                        
        $up = DB::table('vistorias')
        ->where('saude_mental', null)
        ->update(['saude_mental' => 0]);
        echo $up;
        echo '<br>';

        $up = DB::table('vistorias')
        ->where('animais', null)
        ->update(['animais' => 0]);
        echo $up;
        echo '<br>';

        DB::statement('ALTER TABLE vistorias MODIFY COLUMN casal tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN num_reduzido tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN catador_reciclados tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN resistencia tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN fixacao_antiga tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN estrutura_abrigo_provisorio tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN excesso_objetos tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN trafico_ilicitos(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN menores_idosos tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN deficiente tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN agrupamento_quimico(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN saude_mental tinyint(1) NULL DEFAULT 0;');
        DB::statement('ALTER TABLE vistorias MODIFY COLUMN animais tinyint(1) NULL DEFAULT 0;');




        
    }
}
