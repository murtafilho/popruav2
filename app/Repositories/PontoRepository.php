<?php

namespace App\Repositories;

use App\Models\Ponto;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\QryPonto;

class PontoRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'numero',
        'caracteristica_abrigo',
        'complemento',
        'endereco_id'
    ];

    public function model()
    {
        return Ponto::class;
    }

	public function DESCONSIDERAR(){
    	$raw2 = "(SELECT vistorias.resultado_acao from vistorias WHERE vistorias.ponto_id = pontos.id ORDER BY vistorias.id DESC LIMIT 1) as resultado";
		$pontos = $this->model->distinct()
			->join('ender','pontos.endereco_id','=','ender.id')
			->join('vistorias','pontos.id','=','vistorias.ponto_id')
			->select('pontos.*',
				'ender.logradouro','ender.bairro','ender.regional',
				DB::raw('(SELECT count(DISTINCT vistorias.id) from vistorias where vistorias.ponto_id = pontos.id) as contador'),
				DB::raw($raw2)
			)
			->orderBy('logradouro')
			->orderBy('numero')->take(10);
		return $pontos;
	}


	public function buscar($request){
		$logradouro = $request->logradouro;
		$numero = $request->numero;
		$tipo_busca = $request->tipo_busca;
		
		$pontos = DB::table('qry_pontos_v2');
		if($logradouro){
			if($tipo_busca == '0'){
				$pontos = $pontos ->where('logradouro','LIKE','%'.$request->logradouro.'%');
			}else{
				$pontos = $pontos ->where('logradouro','like',$request->logradouro.'%');
			}	
		}
		if($numero){
			$pontos = $pontos ->where('numero','=',$request->numero);
		}
	

		return  $pontos->paginate(10);
	}

	public function listar(){
		$pontos = DB::table('qry_pontos_v2')->paginate(10);
		return $pontos;
	}

}
