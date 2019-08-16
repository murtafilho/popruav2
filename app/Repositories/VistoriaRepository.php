<?php

namespace App\Repositories;

use App\Models\Vistoria;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

class VistoriaRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'data_abordagem',
        'nomes_pessoas',
        'quantidade_pessoas',
        'casal',
        'nivel_complexidade',
        'classificacao',
        'num_reduzido',
        'catador_reciclados',
        'resistencia',
        'fixacao_antiga',
        'estrutura_abrigo_provisorio',
        'excesso_objetos',
        'trafico_ilicitos',
        'menores_idosos',
        'deficiente',
        'agrupamento_quimico',
        'saude_mental',
        'animais',
        'e1',
        'e2',
        'e3',
        'e4',
        'material_apreendido',
        'material_descartado',
        'tipo_abrigo_desmontado',
        'qtd_kg',
        'resultado_acao',
        'movimento_migratorio',
        'observacao',
        'ponto_id'
    ];

    public function model()
    {
        return Vistoria::class;
    }

    public function listar(){
	    $vistorias = DB::table('qry_vistorias_v2')->paginate(10);
	    return $vistorias;
    }

    public function buscar($request){
    	$logradouro = $request->logradouro;
    	$numero = $request->numero;
    	$vistorias = DB::table('qry_vistorias_v2');
	    if($logradouro){
		    $vistorias = $vistorias ->where('logradouro','like','%'.$request->logradouro.'%');
	    }
	    if($numero){
		    $vistorias = $vistorias ->where('numero','=',$request->numero);
	    }
	    $vistorias = $vistorias->paginate(10);
	    return $vistorias;
    }

    public function listarById($id){
    	$vistorias = DB::table('qry_vistorias_v2')
    	->where('ponto_id','=',$id)->paginate(10);
    	return $vistorias;
    }

    public function visualizar($id){
	    $vistoria = DB::table('qry_vistorias_v2')->find($id);
	    return $vistoria;
    }
}
